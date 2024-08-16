<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Course;
use Illuminate\Http\Request;

class CourseController extends Controller
{
    // List all courses
    public function index()
    {
        $courses = Course::with('category')->get();
        return response()->json($courses);
    }

    // Show a specific course
    public function show($id)
    {
        $course = Course::with('category')->findOrFail($id);
        return response()->json($course);
    }

    // Create a new course
    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'category_id' => 'required|exists:categories,category_id',
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'cover_image' => 'nullable|string',
            'price' => 'nullable|numeric',
        ]);

        $course = Course::create($validatedData);
        return response()->json($course, 201);
    }

    // Update an existing course
    public function update(Request $request, $id)
    {
        $course = Course::findOrFail($id);

        $validatedData = $request->validate([
            'category_id' => 'sometimes|exists:categories,category_id',
            'title' => 'sometimes|string|max:255',
            'description' => 'nullable|string',
            'cover_image' => 'nullable|string',
            'price' => 'nullable|numeric',
        ]);

        $course->update($validatedData);
        return response()->json($course);
    }

    // Soft delete a course
    public function destroy($id)
    {
        $course = Course::findOrFail($id);
        $course->delete(); // Soft delete

        return response()->json(['message' => 'Course soft deleted successfully']);
    }
    //    get all trashed categories
    public function trash()
    {
        $courses = Course::onlyTrashed()->get();
        return response()->json($courses);
    }

    // Restore a soft-deleted course
    public function restore($id)
    {
        $course = Course::onlyTrashed()->findOrFail($id);
        $course->restore();

        return response()->json(['message' => 'Course restored successfully']);
    }

    // Permanently delete a course
    public function forceDelete($id)
    {
        $course = Course::onlyTrashed()->findOrFail($id);
        $course->forceDelete();

        return response()->json(['message' => 'Course permanently deleted']);
    }
}
