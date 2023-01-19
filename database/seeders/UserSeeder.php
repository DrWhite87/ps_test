<?php

namespace Database\Seeders;

use App\Models\Lesson;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use JetBrains\PhpStorm\Pure;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        User::factory()->create([
            'name' => 'Admin',
            'email' => 'admin@admin.com',
            'role' => 'admin',
        ]);
        User::factory(3)->create([
            'role' => 'admin'
        ]);

        User::factory(2000)->create();

        $lessons = Lesson::all();

        User::whereRole(User::USER_ROLE_STUDENT)->get()->each(function ($user) use ($lessons) {
            $lessons->random(rand(1, 20))->pluck('id')->each(function ($lessonID) use ($user) {
                $score = 0;
                $viewAt = rand(1, 100) < rand(20, 60) ? Carbon::now()->addDays(rand(1, 150))->addMinutes(rand(1, 55)) : null;
                if ($viewAt) {
                    $score = rand(3, 10);
                }
                $user->lessons()->attach($lessonID,
                    [
                        'progress' => $this->getRandomProgress(),
                        'score' => $score,
                        'view_at' => $viewAt
                    ]
                );
            });
        });
    }

    #[Pure]
    private function getRandomProgress(): int
    {
        $data = [25, 40, 50, 75, 90, 100];
        return $data[rand(0, count($data) - 1)];
    }
}
