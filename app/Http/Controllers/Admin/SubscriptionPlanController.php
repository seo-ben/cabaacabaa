<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SubscriptionPlan;
use Illuminate\Http\Request;

class SubscriptionPlanController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $plans = SubscriptionPlan::all();
        return view('admin.subscriptions.index', compact('plans'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('admin.subscriptions.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:50',
            'price' => 'required|integer|min:0',
            'product_limit' => 'required|integer|min:0',
            'staff_limit' => 'required|integer|min:0',
            'coupon_limit' => 'required|integer|min:0',
            'has_gallery' => 'boolean',
            'has_socials' => 'boolean',
            'is_boosted' => 'boolean',
            'can_recruit_drivers' => 'boolean',
        ]);

        $validated['has_gallery'] = $request->has('has_gallery');
        $validated['has_socials'] = $request->has('has_socials');
        $validated['is_boosted'] = $request->has('is_boosted');
        $validated['can_recruit_drivers'] = $request->has('can_recruit_drivers');

        SubscriptionPlan::create($validated);

        return redirect()->route('admin.subscriptions.index')->with('success', 'Plan créé avec succès.');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        $plan = SubscriptionPlan::findOrFail($id);
        return view('admin.subscriptions.edit', compact('plan'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $plan = SubscriptionPlan::findOrFail($id);

        $validated = $request->validate([
            'name' => 'required|string|max:50',
            'price' => 'required|integer|min:0',
            'product_limit' => 'required|integer|min:0',
            'staff_limit' => 'required|integer|min:0',
            'coupon_limit' => 'required|integer|min:0',
            'has_gallery' => 'boolean',
            'has_socials' => 'boolean',
            'is_boosted' => 'boolean',
            'can_recruit_drivers' => 'boolean',
        ]);

        $validated['has_gallery'] = $request->has('has_gallery');
        $validated['has_socials'] = $request->has('has_socials');
        $validated['is_boosted'] = $request->has('is_boosted');
        $validated['can_recruit_drivers'] = $request->has('can_recruit_drivers');

        $plan->update($validated);

        return redirect()->route('admin.subscriptions.index')->with('success', 'Plan mis à jour.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $plan = SubscriptionPlan::findOrFail($id);
        $plan->delete();

        return redirect()->route('admin.subscriptions.index')->with('success', 'Plan supprimé.');
    }
}
