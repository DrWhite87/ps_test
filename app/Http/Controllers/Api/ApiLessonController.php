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
        $students = collect();

        if ($lesson) {
            $students = $lesson->users();
            if(!empty($request->viewOnly)){
                $students->wherePivot('view_at', '<>', null);;
            }

            $students = $students->get();
        }

        return response()->json($students);
    }
}
