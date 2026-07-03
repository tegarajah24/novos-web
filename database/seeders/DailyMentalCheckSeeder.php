<?php

namespace Database\Seeders;

use App\Models\DailyMentalCheck;
use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;

class DailyMentalCheckSeeder extends Seeder
{
    public function run(): void
    {
        $staff = User::whereHas('role', fn($q) => $q->whereIn('name', Role::internalNames()))->get();

        if ($staff->isEmpty()) {
            $this->command?->warn('Tidak ada staff internal untuk di-seed.');
            return;
        }

        $today = Carbon::today();
        $created = 0;

        for ($day = 6; $day >= 0; $day--) {
            $date = $today->copy()->subDays($day);

            foreach ($staff as $user) {
                $answers = [];
                for ($i = 0; $i < 5; $i++) {
                    $answers[] = rand(1, 3);
                }

                $totalScore = array_sum($answers);

                if ($totalScore >= 5 && $totalScore <= 7) {
                    $category = 'baik';
                } elseif ($totalScore >= 8 && $totalScore <= 11) {
                    $category = 'perlu_perhatian';
                } else {
                    $category = 'perlu_pendampingan';
                }

                $needHelp = fake()->boolean(20);
                $helpNote = $needHelp ? fake()->sentence() : null;

                DailyMentalCheck::firstOrCreate(
                    ['user_id' => $user->id, 'check_date' => $date->toDateString()],
                    [
                        'answers' => $answers,
                        'total_score' => $totalScore,
                        'category' => $category,
                        'need_help' => $needHelp,
                        'help_note' => $helpNote,
                    ]
                );

                $created++;
            }
        }

        $this->command?->info("✅ Daily mental check seeded: {$created} records");
    }
}
