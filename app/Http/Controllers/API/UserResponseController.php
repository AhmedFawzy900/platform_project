<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Grade;
use App\Models\Question;
use App\Models\UserResponse;
use Illuminate\Http\Request;

class UserResponseController extends Controller
{
    public function index()
    {
        $responses = UserResponse::with(['user', 'question', 'choice'])->get();
        return response()->json($responses);
    }

    // Show a specific user response
    public function show($id)
    {
        $response = UserResponse::with(['user', 'question', 'choice'])->findOrFail($id);
        return response()->json($response);
    }

    // Store a new user response
    public function store(Request $request)
    {
        $validated = $request->validate([
            'user_id' => 'required|exists:users,id',
            'question_id' => 'required|exists:questions,question_id',
            'choice_id' => 'nullable|exists:choices,choice_id',
            'written_response' => 'nullable|string',
        ]);

        // Ensure only one type of response is provided
        if (empty($validated['choice_id']) && empty($validated['written_response'])) {
            return response()->json(['error' => 'Either choice_id or written_response must be provided.'], 400);
        }

        $response = UserResponse::create($validated);
        return response()->json($response, 201);
    }

    // Update an existing user response
    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'user_id' => 'required|exists:users,id',
            'question_id' => 'required|exists:questions,question_id',
            'choice_id' => 'nullable|exists:choices,choice_id',
            'written_response' => 'nullable|string',
        ]);

        $response = UserResponse::findOrFail($id);
        $response->update($validated);
        return response()->json($response);
    }

    // Delete a user response
    public function destroy($id)
    {
        $response = UserResponse::findOrFail($id);
        $response->delete();
        return response()->json(null, 204);
    }
    // AUTOMATICALLY CALCULATE AND STORE GRADE
    // public function calculateAndStoreGrade($userId, $quizId)
    // {
    //     $questions = Question::where('quiz_id', $quizId)->get();
    //     $correctAnswers = 0;

    //     foreach ($questions as $question) {
    //         $userResponse = UserResponse::where('user_id', $userId)
    //                                     ->where('question_id', $question->question_id)
    //                                     ->first();

    //         if ($question->type == 'multiple_choice' && $userResponse) {
    //             if ($userResponse->choice && $userResponse->choice->is_correct) {
    //                 $correctAnswers++;
    //             }
    //         } else if ($question->type == 'written') {
    //             // Implement written response grading logic here, or manually grade
    //         }
    //     }

    //     $totalQuestions = count($questions);
    //     $score = ($totalQuestions > 0) ? ($correctAnswers / $totalQuestions) * 100 : 0;

    //     Grade::updateOrCreate(
    //         ['user_id' => $userId, 'quiz_id' => $quizId],
    //         ['score' => $score]
    //     );
    // }

}
