<?php

namespace App\Http\Controllers\Internal;

use App\Http\Controllers\Controller;
use App\Http\Requests\UploadPosterRequest;
use App\Http\Requests\UpdateRotationRequest;
use App\Http\Requests\StoreDailyCheckRequest;
use App\Http\Requests\StoreMicroBreakRequest;
use App\Models\DailyMentalCheck;
use App\Models\MentalHealthPoster;
use App\Models\Role;
use App\Models\MicroBreak;
use App\Models\PosterSetting;
use App\Models\Setting;
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
        $reminderTimes = json_decode(Setting::get('reminder_times', '["10:00","13:00","15:00"]'));

        return view('internal.daily-mental-check', compact('posterUrl', 'reminderTimes'));
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

    public function updateReminderTimes(Request $request)
    {
        $request->validate([
            'times' => 'required|array|min:1',
            'times.*' => 'required|string|date_format:H:i',
        ]);

        Setting::set('reminder_times', json_encode($request->times));

        return response()->json([
            'success' => true,
            'message' => 'Waktu reminder berhasil diperbarui.',
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
        return response()->json($this->buildReportData());
    }

    public function exportReportCsv(Request $request)
    {
        $data = $this->buildReportData();

        $filename = 'mental-check-report-' . now()->format('Ymd-His') . '.csv';
        $headers = [
            'Content-Type'        => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
        ];

        $callback = function () use ($data) {
            $file = fopen('php://output', 'w');
            fputs($file, "\xEF\xBB\xBF");

            fputcsv($file, ['LAPORAN DAILY MENTAL CHECK', '', '']);
            fputcsv($file, ['Periode', now()->subDays(6)->format('d/m/Y') . ' s/d ' . now()->format('d/m/Y'), '']);
            fputcsv($file, ['', '', '']);

            fputcsv($file, ['A. Ringkasan Hari Ini', '', '']);
            fputcsv($file, ['Metrik', 'Nilai', '']);
            fputcsv($file, ['Total Staff', $data['today_summary']['total_staff'], '']);
            fputcsv($file, ['Sudah Check-in', $data['today_summary']['checked'], '']);
            fputcsv($file, ['Belum Check-in', $data['today_summary']['unchecked'], '']);
            fputcsv($file, ['Perlu Perhatian', $data['today_summary']['need_attention'], '']);
            fputcsv($file, ['', '', '']);

            fputcsv($file, ['B. Kondisi Staff Hari Ini', '', '', '', '', '']);
            fputcsv($file, ['Staff', 'Role', 'Daily Check', 'Skor', 'Butuh Bantuan', 'Micro-Break']);
            foreach ($data['staff_today'] as $s) {
                fputcsv($file, [
                    $s['name'],
                    $s['role'],
                    $s['daily_check'] ? ($s['daily_check']['category'] === 'baik' ? 'Baik' : ($s['daily_check']['category'] === 'perlu_perhatian' ? 'Perhatian' : 'Pendampingan')) : '-',
                    $s['daily_check'] ? $s['daily_check']['score'] : '-',
                    $s['daily_check'] && $s['daily_check']['need_help'] ? 'Ya' : '-',
                    $s['micro_break'] ? $s['micro_break']['level'] : '-',
                ]);
            }
            fputcsv($file, ['', '', '', '', '', '']);

            fputcsv($file, ['C. Ringkasan 7 Hari', '', '', '', '', '']);
            fputcsv($file, ['Hari', 'Diisi', 'Rata-rata', 'Baik', 'Perhatian', 'Pendampingan']);
            foreach ($data['week_summary'] as $day) {
                fputcsv($file, [
                    $day['label'],
                    $day['total_filled'] . '/' . $data['today_summary']['total_staff'],
                    $day['avg_score'] ?? '-',
                    $day['baik'],
                    $day['perlu_perhatian'],
                    $day['perlu_pendampingan'],
                ]);
            }
            fputcsv($file, ['', '', '', '', '', '']);

            fputcsv($file, ['D. Statistik Staff (7 Hari)', '', '', '', '', '']);
            fputcsv($file, ['Staff', 'Role', 'Hari', 'Rata-rata', 'Kategori Terburuk', 'Micro']);
            foreach ($data['staff_stats'] as $s) {
                $worst = $s['worst_category']
                    ? ($s['worst_category'] === 'baik' ? 'Baik' : ($s['worst_category'] === 'perlu_perhatian' ? 'Perhatian' : 'Pendampingan'))
                    : '-';
                fputcsv($file, [
                    $s['name'],
                    $s['role'],
                    $s['total_days'] . '/7',
                    $s['avg_score'] ?? '-',
                    $worst,
                    $s['micro_days'] . '/7',
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    public function exportReportExcel(Request $request)
    {
        $data = $this->buildReportData();

        $filename = 'mental-check-report-' . now()->format('Ymd-His') . '.xls';
        $headers = [
            'Content-Type'        => 'application/vnd.ms-excel',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
        ];

        $callback = function () use ($data) {
            echo '<html xmlns:o="urn:schemas-microsoft-com:office:office" xmlns:x="urn:schemas-microsoft-com:office:excel" xmlns="http://www.w3.org/TR/REC-html40">';
            echo '<head><meta charset="UTF-8"><!--[if gte mso 9]><xml><x:ExcelWorkbook><x:ExcelWorksheets><x:ExcelWorksheet><x:Name>MentalCheck</x:Name></x:ExcelWorksheet></x:ExcelWorksheets></x:ExcelWorkbook></xml><![endif]--></head>';
            echo '<body>';
            echo '<table border="1">';

            echo '<tr><th colspan="6" style="font-size:14px;text-align:center;background:#1a237e;color:white;">LAPORAN DAILY MENTAL CHECK</th></tr>';
            echo '<tr><td colspan="6" style="text-align:center;font-weight:bold;">Periode: ' . now()->subDays(6)->format('d/m/Y') . ' s/d ' . now()->format('d/m/Y') . '</td></tr>';
            echo '<tr><td colspan="6"></td></tr>';

            echo '<tr><th colspan="6" style="text-align:left;background:#e5e7eb;font-weight:bold;">A. Ringkasan Hari Ini</th></tr>';
            echo '<tr><th style="font-weight:bold;">Metrik</th><th style="font-weight:bold;">Nilai</th><th colspan="4"></th></tr>';
            echo '<tr><td>Total Staff</td><td style="font-weight:bold;">' . $data['today_summary']['total_staff'] . '</td><td colspan="4"></td></tr>';
            echo '<tr><td>Sudah Check-in</td><td style="font-weight:bold;color:#16a34a;">' . $data['today_summary']['checked'] . '</td><td colspan="4"></td></tr>';
            echo '<tr><td>Belum Check-in</td><td style="font-weight:bold;color:#9ca3af;">' . $data['today_summary']['unchecked'] . '</td><td colspan="4"></td></tr>';
            echo '<tr><td>Perlu Perhatian</td><td style="font-weight:bold;color:#dc2626;">' . $data['today_summary']['need_attention'] . '</td><td colspan="4"></td></tr>';
            echo '<tr><td colspan="6"></td></tr>';

            echo '<tr><th colspan="6" style="text-align:left;background:#e5e7eb;font-weight:bold;">B. Kondisi Staff Hari Ini</th></tr>';
            echo '<tr><th>Staff</th><th>Role</th><th>Daily Check</th><th>Skor</th><th>Butuh Bantuan</th><th>Micro-Break</th></tr>';
            foreach ($data['staff_today'] as $s) {
                $daily = $s['daily_check']
                    ? ($s['daily_check']['category'] === 'baik' ? 'Baik' : ($s['daily_check']['category'] === 'perlu_perhatian' ? 'Perhatian' : 'Pendampingan'))
                    : '-';
                $skor = $s['daily_check'] ? $s['daily_check']['score'] : '-';
                $bantuan = ($s['daily_check'] && $s['daily_check']['need_help']) ? 'Ya' : '-';
                $micro = $s['micro_break'] ? $s['micro_break']['level'] : '-';
                echo '<tr><td>' . e($s['name']) . '</td><td>' . e($s['role']) . '</td><td>' . $daily . '</td><td>' . $skor . '</td><td>' . $bantuan . '</td><td>' . $micro . '</td></tr>';
            }
            echo '<tr><td colspan="6"></td></tr>';

            echo '<tr><th colspan="6" style="text-align:left;background:#e5e7eb;font-weight:bold;">C. Ringkasan 7 Hari</th></tr>';
            echo '<tr><th>Hari</th><th>Diisi</th><th>Rata-rata</th><th>Baik</th><th>Perhatian</th><th>Pendampingan</th></tr>';
            foreach ($data['week_summary'] as $day) {
                echo '<tr><td>' . e($day['label']) . '</td><td>' . $day['total_filled'] . '/' . $data['today_summary']['total_staff'] . '</td><td>' . ($day['avg_score'] ?? '-') . '</td><td>' . $day['baik'] . '</td><td>' . $day['perlu_perhatian'] . '</td><td>' . $day['perlu_pendampingan'] . '</td></tr>';
            }
            echo '<tr><td colspan="6"></td></tr>';

            echo '<tr><th colspan="6" style="text-align:left;background:#e5e7eb;font-weight:bold;">D. Statistik Staff (7 Hari)</th></tr>';
            echo '<tr><th>Staff</th><th>Role</th><th>Hari</th><th>Rata-rata</th><th>Kategori Terburuk</th><th>Micro</th></tr>';
            foreach ($data['staff_stats'] as $s) {
                $worst = $s['worst_category']
                    ? ($s['worst_category'] === 'baik' ? 'Baik' : ($s['worst_category'] === 'perlu_perhatian' ? 'Perhatian' : 'Pendampingan'))
                    : '-';
                echo '<tr><td>' . e($s['name']) . '</td><td>' . e($s['role']) . '</td><td>' . $s['total_days'] . '/7</td><td>' . ($s['avg_score'] ?? '-') . '</td><td>' . $worst . '</td><td>' . $s['micro_days'] . '/7</td></tr>';
            }

            echo '</table>';
            echo '</body></html>';
        };

        return response()->stream($callback, 200, $headers);
    }

    private function buildReportData(): array
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

        return [
            'today_summary' => [
                'total_staff'    => $staff->count(),
                'checked'        => $checkedToday,
                'unchecked'      => $staff->count() - $checkedToday,
                'need_attention' => $needAttention,
            ],
            'staff_today'  => $todayStaff,
            'week_summary' => $weekSummary,
            'staff_stats'  => $staffStats,
        ];
    }
}
