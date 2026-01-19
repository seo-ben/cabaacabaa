<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\VendorCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class VendorCategoryController extends Controller
{
    public function index()
    {
        $categories = VendorCategory::latest()->get();
        return view('admin.vendor_categories.index', compact('categories'));
    }

    public function create()
    {
        return view('admin.vendor_categories.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'icon' => 'nullable|string',
            'is_active' => 'boolean',
        ]);

        $validated['slug'] = Str::slug($validated['name']);

        // Ensure unique slug
        $originalSlug = $validated['slug'];
        $count = 1;
        while (VendorCategory::where('slug', $validated['slug'])->exists()) {
            $validated['slug'] = $originalSlug . '-' . $count++;
        }

        VendorCategory::create($validated);

        return redirect()->route('admin.vendor-categories.index')
            ->with('success', 'Catégorie de vendeur créée avec succès.');
    }

    public function edit(VendorCategory $vendorCategory)
    {
        return view('admin.vendor_categories.edit', ['category' => $vendorCategory]);
    }

    public function update(Request $request, VendorCategory $vendorCategory)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'icon' => 'nullable|string',
            'is_active' => 'boolean',
        ]);

        if ($vendorCategory->name !== $validated['name']) {
            $validated['slug'] = Str::slug($validated['name']);
            $originalSlug = $validated['slug'];
            $count = 1;
            while (VendorCategory::where('slug', $validated['slug'])->where('id_category_vendeur', '!=', $vendorCategory->id_category_vendeur)->exists()) {
                $validated['slug'] = $originalSlug . '-' . $count++;
            }
        }

        $vendorCategory->update($validated);

        return redirect()->route('admin.vendor-categories.index')
            ->with('success', 'Catégorie de vendeur mise à jour avec succès.');
    }

    public function destroy(VendorCategory $vendorCategory)
    {
        if ($vendorCategory->vendeurs()->count() > 0) {
            return redirect()->back()->with('error', 'Impossible de supprimer cette catégorie car elle est utilisée par des boutiques.');
        }

        $vendorCategory->delete();

        return redirect()->route('admin.vendor-categories.index')
            ->with('success', 'Catégorie de vendeur supprimée avec succès.');
    }
}
