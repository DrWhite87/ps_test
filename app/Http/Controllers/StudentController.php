<?php

namespace App\Http\Controllers;

use App\Models\Lesson;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;

class StudentController extends Controller
{
    public function index(Request $request)
    {
        DB::statement("SET @position = 0");
        DB::statement("SET @prev_rating = 0");

        $students = DB::query()->fromSub(function ($query) {
            $query->from('u')->fromSub(function ($query2) {
                $query2->from('users')->select(DB::raw("`users`.*,
            (
                select sum(`score`)
                from `user_lesson`
                where `users`.`id` = `user_lesson`.`user_id`
            ) as `score`")
                )->where('role', '=', User::USER_ROLE_STUDENT)->orderBy('score', 'desc');
            }, 'u')->select(DB::raw("u.*, if(u.score > 0, if(@prev_rating != u.score, @position := @position + 1, @position), null) as position, @prev_rating := u.score as prev_rating,
                (select count(*) from `lessons` inner join `user_lesson` on `lessons`.`id` = `user_lesson`.`lesson_id` where u.`id` = `user_lesson`.`user_id`) as `lessons_count`,
                (select count(*) from `lessons` inner join `user_lesson` on `lessons`.`id` = `user_lesson`.`lesson_id` where u.`id` = `user_lesson`.`user_id` and `user_lesson`.`view_at` is not null) as `view_lessons_count`"));
        }, 'us')
            ->when(!empty($request->query('sort')), function ($q) use ($request) {
                $sortAttribute = $request->query('sort');
                $sortDirection = 'ASC';
                if (strncmp($sortAttribute, '-', 1) === 0) {
                    $sortDirection = 'DESC';
                    $sortAttribute = substr($sortAttribute, 1);
                }
                if ($sortAttribute === 'position') {
                    $q->orderByRaw('ISNULL(position), position ' . $sortDirection);
                } else {
                    $q->orderBy($sortAttribute, $sortDirection);
                }

            }, function ($q) use ($request) {
                $q->orderBy('id', 'asc');
            })
            ->paginate(20)
            ->withQueryString()
            ->through(fn ($student) => [
                'id' => $student->id,
                'name' => $student->name,
                'email' => $student->email,
                'lessons_count' => $student->lessons_count,
                'view_lessons_count' => $student->view_lessons_count,
                'position' => $student->position,
                'score' => $student->score,
            ]);
        /*->each(function ($q) {
            $q->rating = $q->lessons->sum('pivot.score');
        });*/

//        dd($students);

        $allLessonsCount = Lesson::count();

        return Inertia::render('Users/Index', [
            'students' => $students,
            'allLessonsCount' => $allLessonsCount
        ]);
    }
}
