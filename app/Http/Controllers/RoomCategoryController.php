<?php

namespace App\Http\Controllers;

use App\Models\RoomCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class RoomCategoryController extends Controller
{
    public function index()
    {
        $categories = RoomCategory::withCount('studyRooms')->get();
        return view('room-categories.index', compact('categories'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'icon' => 'nullable|string'
        ]);

        $validated['slug'] = Str::slug($validated['name']);

        RoomCategory::create($validated);

        return redirect()->route('room-categories.index')
            ->with('success', 'Category created successfully.');
    }

    public function update(Request $request, RoomCategory $category)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'icon' => 'nullable|string'
        ]);

        $validated['slug'] = Str::slug($validated['name']);

        $category->update($validated);

        return redirect()->route('room-categories.index')
            ->with('success', 'Category updated successfully.');
    }

    public function destroy(RoomCategory $category)
    {
        $category->delete();

        return redirect()->route('room-categories.index')
            ->with('success', 'Category deleted successfully.');
    }
} 