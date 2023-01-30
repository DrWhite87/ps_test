<?php

namespace App\Http\Controllers;

use App\Models\Lesson;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redirect;
use Inertia\Inertia;
use Inertia\Response;

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
                'duration' => $lesson->duration,
                'view_users_count' => $lesson->view_users_count,
            ]);

        return Inertia::render('Lessons/Index', [
            'response' => $lessons,
        ]);
    }

    /**
     * Display create lesson form.
     */
    public function create()
    {
        return Inertia::render('Lessons/Create');
    }

    /**
     * Store lesson.
     * @param Request $request
     * @return RedirectResponse
     */
    public function store(Request $request)
    {
        $input = $request->validate([
            'name' => ['required', 'max:150'],
            'description' => ['required', 'max:1000'],
            'duration' => ['required', 'integer'],
        ]);

        Lesson::create($input);

        return redirect()->route('lessons.index')->with(['success' => 'Added']);
    }

    /**
     * Display edit lesson form.
     * @param Request $request
     * @param $id
     * @return Response
     */
    public function edit(Request $request, $id): Response
    {
        return Inertia::render('Lessons/Edit', [
            'lesson' => Lesson::findOrFail($id),
        ]);
    }

    /**
     * Update lesson.
     * @param Request $request
     * @param $id
     * @return RedirectResponse
     */
    public function update(Request $request, $id): RedirectResponse
    {
        $lesson = Lesson::findOrFail($id);
        $lesson->fill($request->validate([
            'name' => ['required', 'max:150'],
            'description' => ['required', 'max:1000'],
            'duration' => ['required', 'integer'],
        ]));

        $lesson->save();

        return redirect()->back()->with(['success' => 'Updated']);
    }

    /**
     * Delete lesson
     * @param $id
     * @return RedirectResponse
     */
    public function destroy($id): RedirectResponse
    {
        $student = Lesson::findOrFail($id);

        $student->delete();

        return redirect()->back()->with(['success' => 'Deleted']);
    }
}
