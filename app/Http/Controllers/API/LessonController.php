<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Lesson;
use Illuminate\Http\Request;

class LessonController extends Controller
{
    // List all lessons in a course
    public function index($courseId)
    {
        $lessons = Lesson::where('course_id', $courseId)->with('materials')->get();
        return response()->json($lessons);
    }

    // Show a specific lesson
    public function show($id)
    {
        $lesson = Lesson::with('materials')->findOrFail($id);
        return response()->json($lesson);
    }

    // Create a new lesson
    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'course_id' => 'required|exists:courses,course_id',
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'order' => 'nullable|integer',
        ]);

        $lesson = Lesson::create($validatedData);
        return response()->json($lesson, 201);
    }

    // Update an existing lesson
    public function update(Request $request, $id)
    {
        $lesson = Lesson::findOrFail($id);

        $validatedData = $request->validate([
            'course_id' => 'sometimes|exists:courses,course_id',
            'title' => 'sometimes|string|max:255',
            'description' => 'nullable|string',
            'order' => 'nullable|integer',
        ]);

        $lesson->update($validatedData);
        return response()->json($lesson);
    }

    // Soft delete a lesson
    public function destroy($id)
    {
        $lesson = Lesson::findOrFail($id);
        $lesson->delete(); // Soft delete

        return response()->json(['message' => 'Lesson soft deleted successfully']);
    }
    // get all trashed lessons
    public function trash()
    {
        $lessons = Lesson::onlyTrashed()->get();
        return response()->json($lessons);
    }

    // Restore a soft-deleted lesson
    public function restore($id)
    {
        $lesson = Lesson::onlyTrashed()->findOrFail($id);
        $lesson->restore();

        return response()->json(['message' => 'Lesson restored successfully']);
    }

    // Permanently delete a lesson
    public function forceDelete($id)
    {
        $lesson = Lesson::onlyTrashed()->findOrFail($id);
        $lesson->forceDelete();

        return response()->json(['message' => 'Lesson permanently deleted']);
    }
}
