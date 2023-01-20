<?php

namespace App\Http\Controllers;

use App\Models\Lesson;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;

class LessonController extends Controller
{
    public function index(Request $request)
    {
        $lessons = Lesson::withCount(['users', 'users as view_users_count' => function ($q) {
            $q->where('user_lesson.view_at', '<>', null);
        }])->when(!empty($request->query('sort')), function ($q) use ($request) {
            $sortAttribute = $request->query('sort');
            $sortDirection = 'ASC';
            if (strncmp($sortAttribute, '-', 1) === 0) {
                $sortDirection = 'DESC';
                $sortAttribute = substr($sortAttribute, 1);
            }

            $q->orderBy($sortAttribute, $sortDirection);
        })
            ->paginate(20)
            ->withQueryString()
            ->through(fn($lesson) => [
                'id' => $lesson->id,
                'name' => $lesson->name,
                'description' => $lesson->description,
                'view_users_count' => $lesson->view_users_count,
            ]);

        return Inertia::render('Lessons/Index', [
            'response' => $lessons
        ]);
    }
}
