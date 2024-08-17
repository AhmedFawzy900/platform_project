<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Grade;
use Illuminate\Http\Request;

class GradeController extends Controller
{
     // List all grades for a specific user or quiz
     public function index(Request $request)
     {
         $query = Grade::query();
 
         if ($request->has('user_id')) {
             $query->where('user_id', $request->user_id);
         }
 
         if ($request->has('quiz_id')) {
             $query->where('quiz_id', $request->quiz_id);
         }
 
         $grades = $query->with(['user', 'quiz'])->get();
         return response()->json($grades);
     }
 
     // Show a specific grade
     public function show($id)
     {
         $grade = Grade::with(['user', 'quiz'])->findOrFail($id);
         return response()->json($grade);
     }
 
     // Store a new grade
     public function store(Request $request)
     {
         $validated = $request->validate([
             'user_id' => 'required|exists:users,id',
             'quiz_id' => 'required|exists:quizzes,quiz_id',
             'score' => 'required|numeric|min:0|max:100',
         ]);
 
         $grade = Grade::create($validated);
         return response()->json($grade, 201);
     }
 
     // Update an existing grade
     public function update(Request $request, $id)
     {
         $validated = $request->validate([
             'score' => 'required|numeric|min:0|max:100',
         ]);
 
         $grade = Grade::findOrFail($id);
         $grade->update($validated);
         return response()->json($grade);
     }
 
     // Delete a grade
     public function destroy($id)
     {
         $grade = Grade::findOrFail($id);
         $grade->delete();
         return response()->json(null, 204);
     }
}
