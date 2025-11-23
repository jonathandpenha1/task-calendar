<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Category;

class CategoryController extends Controller
{
    // Show form to create category
    public function create()
    {
        $categories = Category::all();
        return view('categories.create', compact('categories'));
    }

    // Store the newly created category
    public function store(Request $request)
    {
        // Validate the category name and color
        $request->validate([
            'name' => 'required|string|max:255|unique:categories,name',
            'color' => 'required|string|size:7',
        ]);

        // Create the category
        $category = Category::create([
            'name' => $request->name,
            'color' => $request->color,
        ]);

        if ($request->ajax()) {
            return response()->json($category);
        }
        return redirect()->route('categories.create')->with('success', 'Category created successfully!');
    }


    // Show all categories
    public function index()
    {
        $categories = Category::all();
        return view('categories.create', compact('categories'));
    }

    // Delete category
    public function destroy($id)
    {
        $category = Category::findOrFail($id);

        $category->delete();
        return redirect()->route('categories.index')->with('success', 'Category deleted successfully.');
    }
}
