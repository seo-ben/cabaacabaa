<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\CategoryPlat;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class CategoryController extends Controller
{
    /**
     * Display a listing of the categories.
     */
    public function index()
    {
        $categories = CategoryPlat::orderBy('ordre_affichage')->get();
        return response()->json(["info" => "Point de terminaison API généré... Il faut y injecter vos variables de vue."]);
    }

    /**
     * Show the form for creating a new category.
     */
    public function create()
    {
        return response()->json(["info" => "Point de terminaison API généré... Il faut y injecter vos variables de vue."]);
    }

    /**
     * Store a newly created category.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'nom_categorie' => 'required|string|max:255',
            'description' => 'nullable|string',
            'icone' => 'nullable|string',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:2048',
            'ordre_affichage' => 'nullable|integer',
            'actif' => 'required|boolean',
        ]);

        // Handle image upload
        if ($request->hasFile('image')) {
            $imagePath = $request->file('image')->store('categories', 'public');
            $validated['image'] = $imagePath;
        }

        CategoryPlat::create($validated);

        return response()->json(["info" => "Action terminée (Ancien redirect).", "status" => "success"]);
    }

    /**
     * Show the form for editing the specified category.
     */
    public function edit(CategoryPlat $category)
    {
        return response()->json(["info" => "Point de terminaison API généré... Il faut y injecter vos variables de vue."]);
    }

    /**
     * Update the specified category.
     */
    public function update(Request $request, CategoryPlat $category)
    {
        $validated = $request->validate([
            'nom_categorie' => 'required|string|max:255',
            'description' => 'nullable|string',
            'icone' => 'nullable|string',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:2048',
            'ordre_affichage' => 'nullable|integer',
            'actif' => 'required|boolean',
        ]);

        // Handle image upload
        if ($request->hasFile('image')) {
            // Delete old image if exists
            if ($category->image) {
                Storage::disk('public')->delete($category->image);
            }
            $imagePath = $request->file('image')->store('categories', 'public');
            $validated['image'] = $imagePath;
        }

        // Handle image removal
        if ($request->has('remove_image') && $request->remove_image) {
            if ($category->image) {
                Storage::disk('public')->delete($category->image);
            }
            $validated['image'] = null;
        }

        $category->update($validated);

        return response()->json(["info" => "Action terminée (Ancien redirect).", "status" => "success"]);
    }

    /**
     * Remove the specified category.
     */
    public function destroy(CategoryPlat $category)
    {
        // Delete image if exists
        if ($category->image) {
            Storage::disk('public')->delete($category->image);
        }
        
        $category->delete();

        return response()->json(["info" => "Action terminée (Ancien redirect).", "status" => "success"]);
    }
}
