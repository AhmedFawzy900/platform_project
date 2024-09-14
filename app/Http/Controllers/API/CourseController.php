<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Course;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class CourseController extends Controller
{
    // List all courses
    public function index()
    {
        try {
            $courses = Course::with('category')->get();
            return response()->json($courses);
        } catch (\Exception $e) {
            return response()->json(['error' => 'An unexpected error occurred', 'message' => $e->getMessage()], 500);
        }
    }

    // Show a specific course
    public function show($id)
    {
        try {
            $course = Course::with('category')->findOrFail($id);
            return response()->json($course);
        } catch (ModelNotFoundException $e) {
            return response()->json(['error' => 'Course not found'], 404);
        } catch (\Exception $e) {
            return response()->json(['error' => 'An unexpected error occurred', 'message' => $e->getMessage()], 500);
        }
    }

    // Create a new course
    public function store(Request $request)
    {
        try {
            $validatedData = $request->validate([
                'category_id' => 'required|exists:categories,id',  // corrected from category_id to id
                'title' => 'required|string|max:255',
                'description' => 'nullable|string',
                'cover_image' => 'nullable|string',
                'price' => 'nullable|numeric',
                'teacher_id' => 'required|exists:users,id',
            ]);

            $course = Course::create($validatedData);
            return response()->json($course, 201);
        } catch (ValidationException $e) {
            return response()->json([
                'error' => 'Validation failed',
                'messages' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            return response()->json(['error' => 'An unexpected error occurred', 'message' => $e->getMessage()], 500);
        }
    }

    // Update an existing course
    public function update(Request $request, $id)
    {
        try {
            $course = Course::findOrFail($id);

            $validatedData = $request->validate([
                'category_id' => 'sometimes|exists:categories,id',  // corrected from category_id to id
                'title' => 'sometimes|string|max:255',
                'description' => 'nullable|string',
                'cover_image' => 'nullable|string',
                'price' => 'nullable|numeric',
                'teacher_id' => 'sometimes|exists:users,id',
            ]);

            $course->update($validatedData);
            return response()->json($course);
        } catch (ModelNotFoundException $e) {
            return response()->json(['error' => 'Course not found'], 404);
        } catch (ValidationException $e) {
            return response()->json([
                'error' => 'Validation failed',
                'messages' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            return response()->json(['error' => 'An unexpected error occurred', 'message' => $e->getMessage()], 500);
        }
    }

    // Soft delete a course
    public function destroy($id)
    {
        try {
            $course = Course::findOrFail($id);
            $course->delete(); // Soft delete

            return response()->json(['message' => 'Course soft deleted successfully']);
        } catch (ModelNotFoundException $e) {
            return response()->json(['error' => 'Course not found'], 404);
        } catch (\Exception $e) {
            return response()->json(['error' => 'An unexpected error occurred', 'message' => $e->getMessage()], 500);
        }
    }

    // Get all trashed courses
    public function trash()
    {
        try {
            $courses = Course::onlyTrashed()->get();
            return response()->json($courses);
        } catch (\Exception $e) {
            return response()->json(['error' => 'An unexpected error occurred', 'message' => $e->getMessage()], 500);
        }
    }

    // Restore a soft-deleted course
    public function restore($id)
    {
        try {
            $course = Course::onlyTrashed()->findOrFail($id);
            $course->restore();

            return response()->json(['message' => 'Course restored successfully']);
        } catch (ModelNotFoundException $e) {
            return response()->json(['error' => 'Course not found'], 404);
        } catch (\Exception $e) {
            return response()->json(['error' => 'An unexpected error occurred', 'message' => $e->getMessage()], 500);
        }
    }

    // Permanently delete a course
    public function forceDelete($id)
    {
        try {
            $course = Course::onlyTrashed()->findOrFail($id);
            $course->forceDelete();

            return response()->json(['message' => 'Course permanently deleted']);
        } catch (ModelNotFoundException $e) {
            return response()->json(['error' => 'Course not found'], 404);
        } catch (\Exception $e) {
            return response()->json(['error' => 'An unexpected error occurred', 'message' => $e->getMessage()], 500);
        }
    }
}
