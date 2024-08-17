<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Quiz;
use Illuminate\Http\Request;

class QuizController extends Controller
{
    // List all quizzes for a lesson or course
    public function index(Request $request)
    {
        if ($request->has('lesson_id')) {
            $quizzes = Quiz::where('lesson_id', $request->lesson_id)->with('questions')->get();
        } elseif ($request->has('course_id')) {
            $quizzes = Quiz::where('course_id', $request->course_id)->with('questions')->get();
        } else {
            $quizzes = Quiz::with('questions')->get();
        }

        return response()->json($quizzes);
    }

    // Show a specific quiz
    public function show($id)
    {
        $quiz = Quiz::with('questions.choices')->findOrFail($id);
        return response()->json($quiz);
    }

    // Create a new quiz
    public function store(Request $request)
    {
        $validated = $request->validate([
            'lesson_id' => 'nullable|exists:lessons,lesson_id',
            'course_id' => 'nullable|exists:courses,course_id',
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
        ]);

        $quiz = Quiz::create($validated);
        return response()->json($quiz, 201);
    }

    // Update an existing quiz
    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
        ]);

        $quiz = Quiz::findOrFail($id);
        $quiz->update($validated);
        return response()->json($quiz);
    }

    // Delete a quiz
    public function destroy($id)
    {
        $quiz = Quiz::findOrFail($id);
        $quiz->delete();
        return response()->json(null, 204);
    }
}
