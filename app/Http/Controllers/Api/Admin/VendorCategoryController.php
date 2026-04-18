<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\VendorCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class VendorCategoryController extends Controller
{
    public function index()
    {
        $categories = VendorCategory::latest()->get();
        return response()->json(["info" => "Point de terminaison API généré... Il faut y injecter vos variables de vue."]);
    }

    public function create()
    {
        return response()->json(["info" => "Point de terminaison API généré... Il faut y injecter vos variables de vue."]);
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

        return response()->json(["info" => "Action terminée (Ancien redirect).", "status" => "success"]);
    }

    public function edit(VendorCategory $vendorCategory)
    {
        return response()->json(["info" => "Point de terminaison API généré... Il faut y injecter vos variables de vue."]);
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

        return response()->json(["info" => "Action terminée (Ancien redirect).", "status" => "success"]);
    }

    public function destroy(VendorCategory $vendorCategory)
    {
        if ($vendorCategory->vendeurs()->count() > 0) {
            return response()->json(["info" => "Action terminée (Ancien redirect).", "status" => "success"]);
        }

        $vendorCategory->delete();

        return response()->json(["info" => "Action terminée (Ancien redirect).", "status" => "success"]);
    }
}
