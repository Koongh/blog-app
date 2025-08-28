<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Category;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\Log;

class CategoryController extends Controller
{
    public function index()
    {
        try {
            $categories = Category::paginate(10);
            return response()->json($categories);
        } catch (\Throwable $e) {
            Log::error('Category index failed: ' . $e->getMessage());
            return response()->json(['message' => 'Failed to fetch categories', 'error' => $e->getMessage()], 500);
        }
    }

    public function show($id)
    {
        try {
            $category = Category::findOrFail($id);
            return response()->json($category);
        } catch (ModelNotFoundException $e) {
            return response()->json(['message' => 'Category not found'], 404);
        } catch (\Throwable $e) {
            Log::error('Category show failed: ' . $e->getMessage());
            return response()->json(['message' => 'Failed to fetch category', 'error' => $e->getMessage()], 500);
        }
    }

    public function store(Request $request)
    {
        try {
            $request->validate([
                'name' => 'required|string|unique:categories,name',
            ]);

            $category = Category::create($request->only('name'));

            return response()->json([
                'message' => 'Category created successfully',
                'category' => $category,
            ], 201);

        } catch (ValidationException $e) {
            return response()->json(['message' => 'Validation failed', 'errors' => $e->errors()], 422);
        } catch (\Throwable $e) {
            Log::error('Category store failed: ' . $e->getMessage());
            return response()->json(['message' => 'Failed to create category', 'error' => $e->getMessage()], 500);
        }
    }

    public function update(Request $request, $id)
    {
        try {
            $category = Category::findOrFail($id);

            $request->validate([
                'name' => 'required|unique:categories,name,' . $id,
            ]);

            $category->update($request->only('name'));

            return response()->json($category);

        } catch (ModelNotFoundException $e) {
            return response()->json(['message' => 'Category not found'], 404);
        } catch (ValidationException $e) {
            return response()->json(['message' => 'Validation failed', 'errors' => $e->errors()], 422);
        } catch (\Throwable $e) {
            Log::error('Category update failed: ' . $e->getMessage());
            return response()->json(['message' => 'Failed to update category', 'error' => $e->getMessage()], 500);
        }
    }

    public function destroy($id)
    {
        try {
            $category = Category::findOrFail($id);
            $category->delete();
            return response()->json(['message' => 'Category deleted']);
        } catch (ModelNotFoundException $e) {
            return response()->json(['message' => 'Category not found'], 404);
        } catch (\Throwable $e) {
            Log::error('Category delete failed: ' . $e->getMessage());
            return response()->json(['message' => 'Failed to delete category', 'error' => $e->getMessage()], 500);
        }
    }

    public function restore($id)
    {
        try {
            $category = Category::withTrashed()->findOrFail($id);
            $category->restore();
            return response()->json(['message' => 'Category restored']);
        } catch (ModelNotFoundException $e) {
            return response()->json(['message' => 'Category not found'], 404);
        } catch (\Throwable $e) {
            Log::error('Category restore failed: ' . $e->getMessage());
            return response()->json(['message' => 'Failed to restore category', 'error' => $e->getMessage()], 500);
        }
    }

}
