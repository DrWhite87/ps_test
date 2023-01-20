<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Lesson;
use Illuminate\Http\Request;

class ApiLessonController extends Controller
{
    public function students(Request $request, $id)
    {
        $lesson = Lesson::findOrFail($id);
        $response = collect();

        if ($lesson) {
            $students = $lesson->users();
            if(!empty($request->viewOnly)){
                $students->wherePivot('view_at', '<>', null);;
            }

            $response = $students
                ->when(!empty($request->query('sort')), function ($q) use ($request) {
                    $sortAttribute = $request->query('sort');
                    $sortDirection = 'ASC';
                    if (strncmp($sortAttribute, '-', 1) === 0) {
                        $sortDirection = 'DESC';
                        $sortAttribute = substr($sortAttribute, 1);
                    }
                    $q->orderBy($sortAttribute, $sortDirection);
                })->paginate(20)
                ->withQueryString()
                ->through(fn($lesson) => [
                    'id' => $lesson->id,
                    'name' => $lesson->name,
                    'email' => $lesson->email,
                    'pivot_progress' => $lesson->pivot->progress,
                    'pivot_score' => $lesson->pivot->score,
                ]);
        }

        return response()->json($response);
    }
}
