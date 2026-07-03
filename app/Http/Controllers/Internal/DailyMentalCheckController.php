<?php

namespace App\Http\Controllers\Internal;

use App\Http\Controllers\Controller;
use App\Http\Requests\UploadPosterRequest;
use App\Http\Requests\UpdateRotationRequest;
use App\Http\Requests\StoreDailyCheckRequest;
use App\Http\Requests\StoreMicroBreakRequest;
use App\Models\DailyMentalCheck;
use App\Models\MentalHealthPoster;
use App\Models\MicroBreak;
use App\Models\PosterSetting;
use App\Models\User;
use App\Services\ImageService;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Storage;

class DailyMentalCheckController extends Controller
{
    public function index()
    {
        $posterUrl = $this->resolvePoster();

        return view('internal.daily-mental-check', compact('posterUrl'));
    }

    private function resolvePoster(): string
    {
        $rotation = PosterSetting::getRotation();
        $now = Carbon::now();

        $query = MentalHealthPoster::where('is_active', true);

        if ($rotation === 'daily') {
            $poster = (clone $query)->whereDate('created_at', $now->toDateString())
                ->latest()
                ->first();
        } else {
            $poster = (clone $query)->whereBetween('created_at', [
                    $now->startOfWeek()->toDateString(),
                    $now->copy()->endOfWeek()->toDateString(),
                ])
                ->latest()
                ->first();
        }

        if (!$poster) {
            $poster = $query->latest()->first();
        }

        return $poster?->url ?? '';
    }

    public function listPosters()
    {
        $posters = MentalHealthPoster::with('uploader')
            ->latest()
            ->get()
            ->map(fn ($p) => [
                'id'          => $p->id,
                'url'         => $p->url,
                'is_active'   => $p->is_active,
                'uploaded_by' => $p->uploader?->name,
                'created_at'  => $p->created_at->diffForHumans(),
            ]);

        return response()->json([
            'posters'  => $posters,
            'rotation' => PosterSetting::getRotation(),
        ]);
    }

    public function uploadPoster(UploadPosterRequest $request)
    {
        $path = app(ImageService::class)->compressAndStore(
            $request->file('image'), 'posters', quality: 70
        );

        $poster = MentalHealthPoster::create([
            'image_path'  => $path,
            'is_active'   => true,
            'uploaded_by' => $request->user()->id,
        ]);

        return response()->json([
            'success' => true,
            'poster'  => [
                'id'    => $poster->id,
                'url'   => $poster->url,
                'created_at' => $poster->created_at->diffForHumans(),
            ],
        ]);
    }

    public function deletePoster($id)
    {
        $poster = MentalHealthPoster::findOrFail($id);

        Storage::disk('public')->delete($poster->image_path);
        $poster->delete();

        return response()->json(['success' => true]);
    }

    public function updateRotation(UpdateRotationRequest $request)
    {
        PosterSetting::setRotation($request->rotation);

        // Re-resolve poster URL after rotation change
        $posterUrl = $this->resolvePoster();

        return response()->json([
            'success'   => true,
            'posterUrl' => $posterUrl,
        ]);
    }

    public function getToday(Request $request)
    {
        $user = $request->user();
        $today = Carbon::today();

        $dailyCheck = DailyMentalCheck::where('user_id', $user->id)
            ->whereDate('check_date', $today)
            ->first();

        $microBreak = MicroBreak::where('user_id', $user->id)
            ->whereDate('check_date', $today)
            ->first();

        return response()->json([
            'daily_check' => $dailyCheck,
            'micro_break' => $microBreak,
        ]);
    }

    public function storeDailyCheck(StoreDailyCheckRequest $request)
    {
        $validated = $request->validated();

        $user = $request->user();
        $today = Carbon::today();

        $answers = $validated['answers'];
        $totalScore = array_sum($answers);

        if ($totalScore >= 5 && $totalScore <= 7) {
            $category = 'baik';
        } elseif ($totalScore >= 8 && $totalScore <= 11) {
            $category = 'perlu_perhatian';
        } else {
            $category = 'perlu_pendampingan';
        }

        $record = DailyMentalCheck::updateOrCreate(
            ['user_id' => $user->id, 'check_date' => $today],
            [
                'answers' => $answers,
                'total_score' => $totalScore,
                'category' => $category,
                'need_help' => $validated['need_help'] === 'ya',
                'help_note' => $validated['help_note'] ?? null,
            ]
        );

        return response()->json(['success' => true, 'data' => $record]);
    }

    public function storeMicroBreak(StoreMicroBreakRequest $request)
    {
        $validated = $request->validated();

        $user = $request->user();
        $today = Carbon::today();

        $score = array_sum($validated['checklist']);

        if ($score >= 7) {
            $level = 'tinggi';
        } elseif ($score >= 4) {
            $level = 'sedang';
        } else {
            $level = 'rendah';
        }

        $record = MicroBreak::updateOrCreate(
            ['user_id' => $user->id, 'check_date' => $today],
            [
                'checklist' => $validated['checklist'],
                'score' => $score,
                'level' => $level,
                'eval' => $validated['eval'],
                'catatan_membantu' => $validated['catatan_membantu'] ?? null,
                'catatan_kendala' => $validated['catatan_kendala'] ?? null,
            ]
        );

        return response()->json(['success' => true, 'data' => $record]);
    }

    public function getHistory(Request $request)
    {
        $user = $request->user();
        $today = Carbon::today();
        $weekAgo = $today->copy()->subDays(6);

        $dailyChecks = DailyMentalCheck::where('user_id', $user->id)
            ->whereBetween('check_date', [$weekAgo, $today])
            ->get()
            ->keyBy(fn($r) => $r->check_date->format('Y-m-d'));

        $microBreaks = MicroBreak::where('user_id', $user->id)
            ->whereBetween('check_date', [$weekAgo, $today])
            ->get()
            ->keyBy(fn($r) => $r->check_date->format('Y-m-d'));

        $weekHistory = [];
        $dayLabels = ['Min', 'Sen', 'Sel', 'Rab', 'Kam', 'Jum', 'Sab'];
        for ($i = 6; $i >= 0; $i--) {
            $date = $today->copy()->subDays($i);
            $dateStr = $date->format('Y-m-d');
            $daily = $dailyChecks->get($dateStr);
            $micro = $microBreaks->get($dateStr);

            $weekHistory[] = [
                'date' => $dateStr,
                'label' => $dayLabels[$date->dayOfWeek],
                'daily_check' => $daily ? [
                    'category' => $daily->category,
                    'score' => $daily->total_score,
                    'emoji' => $daily->category === 'baik' ? '😊' : ($daily->category === 'perlu_perhatian' ? '😐' : '😟'),
                ] : null,
                'micro_break' => $micro ? [
                    'score' => $micro->score,
                    'level' => $micro->level,
                ] : null,
            ];
        }

        $totalMicroDays = count($microBreaks);
        $compliancePercent = $totalMicroDays > 0 ? round(($totalMicroDays / 7) * 100) : 0;

        return response()->json([
            'week_history' => $weekHistory,
            'compliance_percent' => min($compliancePercent, 100),
        ]);
    }

    public function getReport(Request $request)
    {
        $today = Carbon::today();
        $weekAgo = $today->copy()->subDays(6);

        $staff = User::whereHas('role', fn($q) => $q->whereIn('name', Role::internalNames()))
            ->with('role')
            ->get();

        $staffIds = $staff->pluck('id');

        $todayChecks = DailyMentalCheck::whereIn('user_id', $staffIds)
            ->whereDate('check_date', $today)
            ->get()
            ->keyBy('user_id');

        $todayMicros = MicroBreak::whereIn('user_id', $staffIds)
            ->whereDate('check_date', $today)
            ->get()
            ->keyBy('user_id');

        $todayStaff = [];
        $checkedToday = 0;
        $needAttention = 0;

        foreach ($staff as $s) {
            $check = $todayChecks->get($s->id);
            $micro = $todayMicros->get($s->id);

            if ($check) $checkedToday++;
            if ($check && ($check->category !== 'baik' || $check->need_help)) $needAttention++;

            $todayStaff[] = [
                'user_id'     => $s->id,
                'name'        => $s->name,
                'role'        => $s->role->name,
                'avatar'      => $s->avatar,
                'daily_check' => $check ? [
                    'category'  => $check->category,
                    'score'     => $check->total_score,
                    'need_help' => $check->need_help,
                    'help_note' => $check->help_note,
                ] : null,
                'micro_break' => $micro ? [
                    'score' => $micro->score,
                    'level' => $micro->level,
                ] : null,
            ];
        }

        $dayLabels = ['Min', 'Sen', 'Sel', 'Rab', 'Kam', 'Jum', 'Sab'];
        $weekSummary = [];
        for ($i = 6; $i >= 0; $i--) {
            $date = $today->copy()->subDays($i);
            $dateStr = $date->format('Y-m-d');

            $dayChecks = DailyMentalCheck::whereIn('user_id', $staffIds)
                ->whereDate('check_date', $date)
                ->get();

            $dayMicros = MicroBreak::whereIn('user_id', $staffIds)
                ->whereDate('check_date', $date)
                ->get();

            $catCounts = $dayChecks->groupBy('category')->map->count();

            $weekSummary[] = [
                'date'               => $dateStr,
                'label'              => $dayLabels[$date->dayOfWeek],
                'total_filled'       => $dayChecks->count(),
                'avg_score'          => $dayChecks->avg('total_score') ? round($dayChecks->avg('total_score'), 1) : null,
                'baik'               => $catCounts->get('baik', 0),
                'perlu_perhatian'    => $catCounts->get('perlu_perhatian', 0),
                'perlu_pendampingan' => $catCounts->get('perlu_pendampingan', 0),
                'micro_avg_score'    => $dayMicros->avg('score') ? round($dayMicros->avg('score'), 1) : null,
            ];
        }

        $staffStats = $staff->map(function ($s) use ($weekAgo, $today) {
            $checks = DailyMentalCheck::where('user_id', $s->id)
                ->whereBetween('check_date', [$weekAgo, $today])
                ->get();

            $micros = MicroBreak::where('user_id', $s->id)
                ->whereBetween('check_date', [$weekAgo, $today])
                ->get();

            return [
                'user_id'         => $s->id,
                'name'            => $s->name,
                'role'            => $s->role->name,
                'avatar'          => $s->avatar,
                'total_days'      => $checks->count(),
                'avg_score'       => $checks->avg('total_score') ? round($checks->avg('total_score'), 1) : null,
                'worst_category'  => $checks->sortByDesc('total_score')->first()?->category ?? null,
                'micro_days'      => $micros->count(),
                'avg_micro_score' => $micros->avg('score') ? round($micros->avg('score'), 1) : null,
            ];
        });

        return response()->json([
            'today_summary' => [
                'total_staff'    => $staff->count(),
                'checked'        => $checkedToday,
                'unchecked'      => $staff->count() - $checkedToday,
                'need_attention' => $needAttention,
            ],
            'staff_today'  => $todayStaff,
            'week_summary' => $weekSummary,
            'staff_stats'  => $staffStats,
        ]);
    }
}
