<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\CategoryPlat;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    /**
     * Display a listing of the categories.
     */
    public function index()
    {
        $categories = CategoryPlat::orderBy('ordre_affichage')->get();
        return view('admin.categories.index', compact('categories'));
    }

    /**
     * Show the form for creating a new category.
     */
    public function create()
    {
        return view('admin.categories.create');
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
            'ordre_affichage' => 'nullable|integer',
            'actif' => 'required|boolean',
        ]);

        CategoryPlat::create($validated);

        return redirect()->route('admin.categories.index')
            ->with('success', 'Catégorie créée avec succès.');
    }

    /**
     * Show the form for editing the specified category.
     */
    public function edit(CategoryPlat $category)
    {
        return view('admin.categories.edit', compact('category'));
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
            'ordre_affichage' => 'nullable|integer',
            'actif' => 'required|boolean',
        ]);

        $category->update($validated);

        return redirect()->route('admin.categories.index')
            ->with('success', 'Catégorie mise à jour avec succès.');
    }

    /**
     * Remove the specified category.
     */
    public function destroy(CategoryPlat $category)
    {
        $category->delete();

        return redirect()->route('admin.categories.index')
            ->with('success', 'Catégorie supprimée avec succès.');
    }
}
