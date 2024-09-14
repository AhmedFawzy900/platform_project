<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Validation\ValidationException;

class CategoryController extends Controller
{
    // List all categories
    public function index()
    {
        try {
            $categories = Category::all();
            return response()->json($categories);
        } catch (\Exception $e) {
            return response()->json(['error' => 'An unexpected error occurred', 'message' => $e->getMessage()], 500);
        }
    }

    // Show a specific category
    public function show($id)
    {
        try {
            $category = Category::findOrFail($id);
            return response()->json($category);
        } catch (ModelNotFoundException $e) {
            return response()->json(['error' => 'Category not found'], 404);
        } catch (\Exception $e) {
            return response()->json(['error' => 'An unexpected error occurred', 'message' => $e->getMessage()], 500);
        }
    }

    // Create a new category
    public function store(Request $request)
    {
        try {
            $validatedData = $request->validate([
                'name' => 'required|string|max:255',
                'description' => 'nullable|string',
                'cover_image' => 'nullable|string',
            ]);

            $category = Category::create($validatedData);
            return response()->json($category, 201);
        } catch (ValidationException $e) {
            return response()->json([
                'error' => 'Validation failed',
                'messages' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            return response()->json(['error' => 'An unexpected error occurred', 'message' => $e->getMessage()], 500);
        }
    }

    // Update an existing category
    public function update(Request $request, $id)
    {
        try {
            $category = Category::findOrFail($id);

            $validatedData = $request->validate([
                'name' => 'sometimes|string|max:255',
                'description' => 'nullable|string',
                'cover_image' => 'nullable|string',
            ]);

            $category->update($validatedData);
            return response()->json($category);
        } catch (ModelNotFoundException $e) {
            return response()->json(['error' => 'Category not found'], 404);
        } catch (ValidationException $e) {
            return response()->json([
                'error' => 'Validation failed',
                'messages' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            return response()->json(['error' => 'An unexpected error occurred', 'message' => $e->getMessage()], 500);
        }
    }

    // Soft delete a category
    public function destroy($id)
    {
        try {
            $category = Category::findOrFail($id);
            $category->delete(); // Soft delete

            return response()->json(['message' => 'Category soft deleted successfully']);
        } catch (ModelNotFoundException $e) {
            return response()->json(['error' => 'Category not found'], 404);
        } catch (\Exception $e) {
            return response()->json(['error' => 'An unexpected error occurred', 'message' => $e->getMessage()], 500);
        }
    }

    // Get all trashed categories
    public function trash()
    {
        try {
            $categories = Category::onlyTrashed()->get();
            return response()->json($categories);
        } catch (\Exception $e) {
            return response()->json(['error' => 'An unexpected error occurred', 'message' => $e->getMessage()], 500);
        }
    }

    // Restore a soft-deleted category
    public function restore($id)
    {
        try {
            $category = Category::onlyTrashed()->findOrFail($id);
            $category->restore();

            return response()->json(['message' => 'Category restored successfully']);
        } catch (ModelNotFoundException $e) {
            return response()->json(['error' => 'Category not found'], 404);
        } catch (\Exception $e) {
            return response()->json(['error' => 'An unexpected error occurred', 'message' => $e->getMessage()], 500);
        }
    }

    // Permanently delete a category
    public function forceDelete($id)
    {
        try {
            $category = Category::onlyTrashed()->findOrFail($id);
            $category->forceDelete();

            return response()->json(['message' => 'Category permanently deleted']);
        } catch (ModelNotFoundException $e) {
            return response()->json(['error' => 'Category not found'], 404);
        } catch (\Exception $e) {
            return response()->json(['error' => 'An unexpected error occurred', 'message' => $e->getMessage()], 500);
        }
    }
}
