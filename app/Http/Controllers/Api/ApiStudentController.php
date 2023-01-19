<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Lesson;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;

class ApiStudentController extends Controller
{
    public function lessons(Request $request, $id)
    {
        $user = User::findOrFail($id);
        $lessons = collect();

        if ($user) {
            $lessons = $user->lessons();
            if(!empty($request->viewOnly)){
                $lessons->wherePivot('view_at', '<>', null);;
            }

            $lessons = $lessons->get();
        }

        return response()->json($lessons);
    }
}
