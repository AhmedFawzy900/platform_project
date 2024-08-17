<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Question;
use Illuminate\Http\Request;

class QuestionController extends Controller
{
    // List all questions for a specific quiz
    public function index($quiz_id)
    {
        $questions = Question::where('quiz_id', $quiz_id)->with('choices')->get();
        return response()->json($questions);
    }

    // Show a specific question
    public function show($id)
    {
        $question = Question::with('choices')->findOrFail($id);
        return response()->json($question);
    }

    // Create a new question
    public function store(Request $request, $quiz_id)
    {
        $validated = $request->validate([
            'question' => 'required|string',
            'type' => 'required|in:multiple_choice,written',
        ]);

        $question = Question::create(array_merge($validated, ['quiz_id' => $quiz_id]));
        return response()->json($question, 201);
    }

    // Update an existing question
    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'question' => 'required|string',
            'type' => 'required|in:multiple_choice,written',
        ]);

        $question = Question::findOrFail($id);
        $question->update($validated);
        return response()->json($question);
    }

    // Delete a question
    public function destroy($id)
    {
        $question = Question::findOrFail($id);
        $question->delete();
        return response()->json(null, 204);
    }
}
