<?php

namespace App\Http\Controllers\Internal;

use App\Http\Controllers\Controller;
use App\Models\DailyMentalCheck;
use App\Models\MicroBreak;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;

class DailyMentalCheckController extends Controller
{
    public function index()
    {
        return view('internal.daily-mental-check');
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

    public function storeDailyCheck(Request $request)
    {
        $validated = $request->validate([
            'answers' => 'required|array|size:5',
            'answers.*' => 'required|integer|in:1,2,3',
            'need_help' => 'required|in:ya,tidak',
            'help_note' => 'nullable|string|max:500',
        ]);

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

    public function storeMicroBreak(Request $request)
    {
        $validated = $request->validate([
            'checklist' => 'required|array|size:8',
            'checklist.*' => 'required|integer|in:0,1',
            'eval' => 'required|array|size:3',
            'eval.stres' => 'required|in:lebih_baik,sama,lebih_buruk',
            'eval.fokus' => 'required|in:lebih_baik,sama,lebih_buruk',
            'eval.kenyamanan' => 'required|in:lebih_baik,sama,lebih_buruk',
            'catatan_membantu' => 'nullable|string|max:500',
            'catatan_kendala' => 'nullable|string|max:500',
        ]);

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
        for ($i = 6; $i >= 0; $i--) {
            $date = $today->copy()->subDays($i);
            $dateStr = $date->format('Y-m-d');
            $daily = $dailyChecks->get($dateStr);
            $micro = $microBreaks->get($dateStr);

            $weekHistory[] = [
                'date' => $dateStr,
                'label' => $date->format('D'),
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
}
