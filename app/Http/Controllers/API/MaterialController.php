<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Material;
use Illuminate\Http\Request;

class MaterialController extends Controller
{
    // List all materials in a lesson
    public function index($lessonId)
    {
        $materials = Material::where('lesson_id', $lessonId)->get();
        return response()->json($materials);
    }

    // Show a specific material
    public function show($id)
    {
        $material = Material::findOrFail($id);
        return response()->json($material);
    }

    // Create a new material
    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'lesson_id' => 'required|exists:lessons,lesson_id',
            'type' => 'required|string|max:255',
            'url' => 'required|string|max:255',
        ]);

        $material = Material::create($validatedData);
        return response()->json($material, 201);
    }

    // Update an existing material
    public function update(Request $request, $id)
    {
        $material = Material::findOrFail($id);

        $validatedData = $request->validate([
            'lesson_id' => 'sometimes|exists:lessons,lesson_id',
            'type' => 'sometimes|string|max:255',
            'url' => 'sometimes|string|max:255',
        ]);

        $material->update($validatedData);
        return response()->json($material);
    }

    // Soft delete a material
    public function destroy($id)
    {
        $material = Material::findOrFail($id);
        $material->delete(); // Soft delete

        return response()->json(['message' => 'Material soft deleted successfully']);
    }

    // Restore a soft-deleted material
    public function restore($id)
    {
        $material = Material::onlyTrashed()->findOrFail($id);
        $material->restore();

        return response()->json(['message' => 'Material restored successfully']);
    }

    // Permanently delete a material
    public function forceDelete($id)
    {
        $material = Material::onlyTrashed()->findOrFail($id);
        $material->forceDelete();

        return response()->json(['message' => 'Material permanently deleted']);
    }
}
