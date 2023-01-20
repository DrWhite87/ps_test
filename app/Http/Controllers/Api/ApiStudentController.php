<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Inertia\Inertia;

class ApiStudentController extends Controller
{
    public function lessons(Request $request, $id)
    {
        $user = User::findOrFail($id);
        $response = collect();

        if ($user) {
            $lessons = $user->lessons();
            if (!empty($request->viewOnly)) {
                $lessons->wherePivot('view_at', '<>', null);
            }

            $response = $lessons
                ->when(!empty($request->query('sort')), function ($q) use ($request) {
                    $sortAttribute = $request->query('sort');
                    $sortDirection = 'ASC';
                    if (strncmp($sortAttribute, '-', 1) === 0) {
                        $sortDirection = 'DESC';
                        $sortAttribute = substr($sortAttribute, 1);
                    }
                    $q->orderBy($sortAttribute, $sortDirection);
                })->paginate(6)
                ->withQueryString()
                ->through(fn($lesson) => [
                    'id' => $lesson->id,
                    'name' => $lesson->name,
                    'description' => $lesson->description,
                    'pivot_progress' => $lesson->pivot->progress,
                    'pivot_score' => $lesson->pivot->score,
                ]);
        }

        return response()->json($response);
    }
}
