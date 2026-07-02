<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\DailyMentalCheck;
use App\Models\MicroBreak;
use App\Models\User;
use App\Models\MentalHealthPoster;
use App\Models\PosterSetting;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Storage;

class DailyMentalCheck extends Component
{
    public $activeTab = 0;
    public $posterUrl = '';
    public $todayCheck = null;
    public $todayMicro = null;
    public $weekHistory = [];
    public $compliancePercent = 0;
    public $reportData = null;
    public $posters = [];
    public $rotation = 'daily';
    public $quotes = [];

    protected function getListeners()
    {
        return ['notify', 'refreshData' => 'loadToday'];
    }

    public function mount()
    {
        $this->posterUrl = $this->resolvePoster();
        $this->loadToday();
        $this->quotes = [
            ['text' => 'Kesehatan mental bukanlah tujuan, tapi sebuah proses.', 'author' => 'Unknown'],
            ['text' => 'Istirahat bukanlah kemalasan, tapi bagian dari produktivitas.', 'author' => 'Unknown'],
            ['text' => 'Kamu tidak harus selalu baik-baik saja.', 'author' => 'Unknown'],
            ['text' => 'Jaga dirimu sebagaimana kamu menjaga orang lain.', 'author' => 'Unknown'],
            ['text' => 'Setiap langkah kecil adalah kemajuan.', 'author' => 'Unknown'],
        ];
    }

    public function loadActiveTab($tab)
    {
        $this->activeTab = $tab;
        if ($tab == 2) {
            $this->loadHistory();
        } elseif ($tab == 3) {
            $this->loadReport();
        }
    }

    public function resolvePoster(): string
    {
        $rotation = PosterSetting::getRotation();
        $now = Carbon::now();
        $query = MentalHealthPoster::where('is_active', true);

        if ($rotation === 'daily') {
            $poster = (clone $query)->whereDate('created_at', $now->toDateString())->latest()->first();
        } else {
            $poster = (clone $query)->whereBetween('created_at', [$now->startOfWeek()->toDateString(), $now->copy()->endOfWeek()->toDateString()])->latest()->first();
        }

        return ($poster ?? $query->latest()->first())?->url ?? '';
    }

    public function loadToday()
    {
        $user = auth()->user();
        $today = Carbon::today();

        $check = DailyMentalCheck::where('user_id', $user->id)->whereDate('check_date', $today)->first();
        $micro = MicroBreak::where('user_id', $user->id)->whereDate('check_date', $today)->first();

        $this->todayCheck = $check ? $check->toArray() : null;
        $this->todayMicro = $micro ? $micro->toArray() : null;
    }

    public function storeDailyCheck($answers, $needHelp, $helpNote = null)
    {
        $user = auth()->user();
        $today = Carbon::today();
        $totalScore = array_sum($answers);

        if ($totalScore >= 5 && $totalScore <= 7) {
            $category = 'baik';
        } elseif ($totalScore >= 8 && $totalScore <= 11) {
            $category = 'perlu_perhatian';
        } else {
            $category = 'perlu_pendampingan';
        }

        DailyMentalCheck::updateOrCreate(
            ['user_id' => $user->id, 'check_date' => $today],
            [
                'answers' => $answers,
                'total_score' => $totalScore,
                'category' => $category,
                'need_help' => $needHelp === 'ya',
                'help_note' => $helpNote,
            ]
        );

        $this->loadToday();
        $this->dispatch('notify', type: 'success', message: 'Cek harian berhasil disimpan');

        return ['success' => true];
    }

    public function storeMicroBreak($checklist, $eval, $catatanMembantu = null, $catatanKendala = null)
    {
        $user = auth()->user();
        $today = Carbon::today();
        $score = array_sum($checklist);

        if ($score >= 7) {
            $level = 'tinggi';
        } elseif ($score >= 4) {
            $level = 'sedang';
        } else {
            $level = 'rendah';
        }

        MicroBreak::updateOrCreate(
            ['user_id' => $user->id, 'check_date' => $today],
            [
                'checklist' => $checklist,
                'score' => $score,
                'level' => $level,
                'eval' => $eval,
                'catatan_membantu' => $catatanMembantu,
                'catatan_kendala' => $catatanKendala,
            ]
        );

        $this->loadToday();
        $this->dispatch('notify', type: 'success', message: 'Micro break berhasil dicatat');

        return ['success' => true];
    }

    public function loadHistory()
    {
        $user = auth()->user();
        $today = Carbon::today();
        $weekAgo = $today->copy()->subDays(6);

        $dailyChecks = DailyMentalCheck::where('user_id', $user->id)
            ->whereBetween('check_date', [$weekAgo, $today])
            ->get()->keyBy(fn($r) => $r->check_date->format('Y-m-d'));

        $microBreaks = MicroBreak::where('user_id', $user->id)
            ->whereBetween('check_date', [$weekAgo, $today])
            ->get()->keyBy(fn($r) => $r->check_date->format('Y-m-d'));

        $dayLabels = ['Min', 'Sen', 'Sel', 'Rab', 'Kam', 'Jum', 'Sab'];
        $history = [];
        for ($i = 6; $i >= 0; $i--) {
            $date = $today->copy()->subDays($i);
            $dateStr = $date->format('Y-m-d');
            $daily = $dailyChecks->get($dateStr);
            $micro = $microBreaks->get($dateStr);
            $history[] = [
                'date' => $dateStr,
                'label' => $dayLabels[$date->dayOfWeek],
                'daily_check' => $daily ? ['category' => $daily->category, 'score' => $daily->total_score] : null,
                'micro_break' => $micro ? ['score' => $micro->score, 'level' => $micro->level] : null,
            ];
        }

        $this->weekHistory = $history;
        $this->compliancePercent = min(round((count($microBreaks) / 7) * 100), 100);
    }

    public function loadReport()
    {
        $today = Carbon::today();
        $weekAgo = $today->copy()->subDays(6);

        $staff = User::whereHas('role', fn($q) => $q->whereIn('name', \App\Models\Role::internalNames()))
            ->with('role')->get();
        $staffIds = $staff->pluck('id');

        $todayChecks = DailyMentalCheck::whereIn('user_id', $staffIds)->whereDate('check_date', $today)->get()->keyBy('user_id');
        $todayMicros = MicroBreak::whereIn('user_id', $staffIds)->whereDate('check_date', $today)->get()->keyBy('user_id');

        $checkedToday = 0;
        $needAttention = 0;
        $staffToday = [];

        foreach ($staff as $s) {
            $check = $todayChecks->get($s->id);
            $micro = $todayMicros->get($s->id);
            if ($check) $checkedToday++;
            if ($check && ($check->category !== 'baik' || $check->need_help)) $needAttention++;
            $staffToday[] = [
                'user_id' => $s->id, 'name' => $s->name, 'role' => $s->role->name,
                'avatar' => $s->avatar,
                'daily_check' => $check ? ['category' => $check->category, 'score' => $check->total_score, 'need_help' => $check->need_help, 'help_note' => $check->help_note] : null,
                'micro_break' => $micro ? ['score' => $micro->score, 'level' => $micro->level] : null,
            ];
        }

        $dayLabels = ['Min', 'Sen', 'Sel', 'Rab', 'Kam', 'Jum', 'Sat'];
        $weekSummary = [];
        for ($i = 6; $i >= 0; $i--) {
            $date = $today->copy()->subDays($i);
            $dayChecks = DailyMentalCheck::whereIn('user_id', $staffIds)->whereDate('check_date', $date)->get();
            $dayMicros = MicroBreak::whereIn('user_id', $staffIds)->whereDate('check_date', $date)->get();
            $catCounts = $dayChecks->groupBy('category')->map->count();
            $weekSummary[] = [
                'date' => $date->format('Y-m-d'), 'label' => $dayLabels[$date->dayOfWeek],
                'total_filled' => $dayChecks->count(),
                'avg_score' => $dayChecks->avg('total_score') ? round($dayChecks->avg('total_score'), 1) : null,
                'baik' => $catCounts->get('baik', 0),
                'perlu_perhatian' => $catCounts->get('perlu_perhatian', 0),
                'perlu_pendampingan' => $catCounts->get('perlu_pendampingan', 0),
            ];
        }

        $staffStats = $staff->map(function ($s) use ($weekAgo, $today) {
            $checks = DailyMentalCheck::where('user_id', $s->id)->whereBetween('check_date', [$weekAgo, $today])->get();
            $micros = MicroBreak::where('user_id', $s->id)->whereBetween('check_date', [$weekAgo, $today])->get();
            return [
                'user_id' => $s->id, 'name' => $s->name, 'role' => $s->role->name,
                'avatar' => $s->avatar, 'total_days' => $checks->count(),
                'avg_score' => $checks->avg('total_score') ? round($checks->avg('total_score'), 1) : null,
                'worst_category' => $checks->sortByDesc('total_score')->first()?->category,
                'micro_days' => $micros->count(),
            ];
        });

        $this->reportData = [
            'today_summary' => ['total_staff' => $staff->count(), 'checked' => $checkedToday, 'unchecked' => $staff->count() - $checkedToday, 'need_attention' => $needAttention],
            'staff_today' => $staffToday,
            'week_summary' => $weekSummary,
            'staff_stats' => $staffStats,
        ];

        return $this->reportData;
    }

    public function listPosters()
    {
        $posters = MentalHealthPoster::with('uploader')->latest()->get()->map(fn($p) => [
            'id' => $p->id, 'url' => $p->url, 'is_active' => $p->is_active,
            'uploaded_by' => $p->uploader?->name, 'created_at' => $p->created_at->diffForHumans(),
        ]);
        $this->posters = $posters->toArray();
        $this->rotation = PosterSetting::getRotation();
        return ['posters' => $posters, 'rotation' => $this->rotation];
    }

    public function uploadPoster()
    {
        // File upload via Livewire is complex here — keep using fetch for poster
        // This method is a placeholder, poster management stays Alpine+fetch
    }

    public function deletePoster($id)
    {
        $poster = MentalHealthPoster::findOrFail($id);
        Storage::disk('public')->delete($poster->image_path);
        $poster->delete();
        $this->dispatch('notify', type: 'success', message: 'Poster berhasil dihapus');
        return ['success' => true];
    }

    public function updateRotation($rotation)
    {
        PosterSetting::setRotation($rotation);
        $this->rotation = $rotation;
        $this->posterUrl = $this->resolvePoster();
        $this->dispatch('notify', type: 'success', message: 'Rotasi poster diperbarui');
        return ['success' => true, 'posterUrl' => $this->posterUrl];
    }

    public function render()
    {
        return view('livewire.daily-mental-check');
    }
}
