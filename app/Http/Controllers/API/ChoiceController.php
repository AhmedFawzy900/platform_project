<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Choice;
use Illuminate\Http\Request;

class ChoiceController extends Controller
{
     // List all choices for a specific question
     public function index($question_id)
     {
         $choices = Choice::where('question_id', $question_id)->get();
         return response()->json($choices);
     }
 
     // Show a specific choice
     public function show($id)
     {
         $choice = Choice::findOrFail($id);
         return response()->json($choice);
     }
 
     // Create a new choice
     public function store(Request $request, $question_id)
     {
         $validated = $request->validate([
             'choice_text' => 'required|string',
             'is_correct' => 'required|boolean',
         ]);
 
         $choice = Choice::create(array_merge($validated, ['question_id' => $question_id]));
         return response()->json($choice, 201);
     }
 
     // Update an existing choice
     public function update(Request $request, $id)
     {
         $validated = $request->validate([
             'choice_text' => 'required|string',
             'is_correct' => 'required|boolean',
         ]);
 
         $choice = Choice::findOrFail($id);
         $choice->update($validated);
         return response()->json($choice);
     }
 
     // Delete a choice
     public function destroy($id)
     {
         $choice = Choice::findOrFail($id);
         $choice->delete();
         return response()->json(null, 204);
     }
}
