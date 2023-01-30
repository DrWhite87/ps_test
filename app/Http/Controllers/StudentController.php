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

class StudentController extends Controller
{
    public function index(Request $request)
    {
        $perPage = 20;
        $page = $request->input("page", 0);
        $skip = ($page - 1) * $perPage;
        if ($perPage < 1) {
            $perPage = 1;
        }
        if ($skip < 0) {
            $skip = 0;
        }

        $totalCount = User::students()->count();

        DB::statement("SET @position = 0");
        DB::statement("SET @prev_rating = 0");
        $results = DB::query()->fromSub(function ($query) {
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
            ->take($perPage)
            ->skip($skip)
            ->get();

        $params = $request->except("page");

        $students = new \Illuminate\Pagination\LengthAwarePaginator($results, $totalCount, $perPage, $page, [
            'path' => url()->current() . (!empty($params) ? '?' : '') . http_build_query($params),
        ]);
        $students = $students->through(fn($student) => [
            'id' => $student->id,
            'name' => $student->name,
            'email' => $student->email,
            'lessons_count' => $student->lessons_count,
            'view_lessons_count' => $student->view_lessons_count,
            'position' => $student->position,
            'score' => $student->score,
        ]);


        /* $queries = DB::getQueryLog();
         dd($queries);*/
        $allLessonsCount = Lesson::count();

        return Inertia::render('Users/Index',[
            'response' => $students,
            'allLessonsCount' => $allLessonsCount
        ]);
    }

    /**
     * Display create student form.
     */
    public function create()
    {
        return Inertia::render('Users/Create');
    }

    /**
     * Store student.
     * @param Request $request
     * @return RedirectResponse
     */
    public function store(Request $request)
    {
        $input = $request->validate([
            'name' => ['required', 'max:150'],
            'email' => ['required', 'max:50', 'email'],
        ]);

        $input['role'] = User::USER_ROLE_STUDENT;
        $input['password'] = '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi';

        User::create($input);

        return redirect()->route('students.index')->with(['success' => 'Added']);
    }

    /**
     * Display edit student form.
     * @param Request $request
     * @param $id
     * @return Response
     */
    public function edit(Request $request, $id): Response
    {
        return Inertia::render('Users/Edit', [
            'student' => User::students()->findOrFail($id),
        ]);
    }

    /**
     * Update student.
     * @param Request $request
     * @param $id
     * @return RedirectResponse
     */
    public function update(Request $request, $id): RedirectResponse
    {
        $student = User::students()->findOrFail($id);
        $student->fill($request->validate([
            'name' => ['required', 'max:150'],
            'email' => ['required', 'max:50', 'email'],
        ]));

        $student->save();

        return redirect()->back()->with(['success' => 'Updated']);
    }

    /**
     * Delete student
     * @param $id
     * @return RedirectResponse
     */
    public function destroy($id): RedirectResponse
    {
        $student = User::students()->findOrFail($id);

        $student->delete();

        return redirect()->route('students.index')->with(['success' => 'Deleted']);
    }
}
