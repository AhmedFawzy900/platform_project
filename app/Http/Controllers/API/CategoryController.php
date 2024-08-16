<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Category;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
       // List all categories
       public function index()
       {
           $categories = Category::all();
           return response()->json($categories);
       }
   
       // Show a specific category
       public function show($id)
       {
           $category = Category::findOrFail($id);
           return response()->json($category);
       }
   
       // Create a new category
       public function store(Request $request)
       {
           $validatedData = $request->validate([
               'name' => 'required|string|max:255',
               'description' => 'nullable|string',
               'cover_image' => 'nullable|string',
           ]);
   
           $category = Category::create($validatedData);
           return response()->json($category, 201);
       }
   
       // Update an existing category
       public function update(Request $request, $id)
       {
           $category = Category::findOrFail($id);
   
           $validatedData = $request->validate([
               'name' => 'sometimes|string|max:255',
               'description' => 'nullable|string',
               'cover_image' => 'nullable|string',
           ]);
   
           $category->update($validatedData);
           return response()->json($category);
       }
   
       // Soft delete a category
       public function destroy($id)
       {
           $category = Category::findOrFail($id);
           $category->delete(); // Soft delete
   
           return response()->json(['message' => 'Category soft deleted successfully']);
       }
        //    get all trashed categories
       public function trash()
       {
           $categories = Category::onlyTrashed()->get();
           return response()->json($categories);
       }
   
       // Restore a soft-deleted category
       public function restore($id)
       {
           $category = Category::onlyTrashed()->findOrFail($id);
           $category->restore();
   
           return response()->json(['message' => 'Category restored successfully']);
       }
   
       // Permanently delete a category
       public function forceDelete($id)
       {
           $category = Category::onlyTrashed()->findOrFail($id);
           $category->forceDelete();
   
           return response()->json(['message' => 'Category permanently deleted']);
       }
}
