<?php

namespace App\Http\Controllers;

use App\Models\Plan;
use Illuminate\Http\Request;

class PlanController extends Controller
{
    public function index()
    {
        $plans = Plan::orderBy('is_custom')->orderBy('name')->get();
        return view('plans.index', compact('plans'));
    }

    public function create()
    {
        return view('plans.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name'              => 'required|string|max:255',
            'specs'             => 'nullable|string|max:500',
            'is_custom'         => 'boolean',
            'deposit_per_unit'  => 'required|numeric|min:0',
            'daily_rate'        => 'required|numeric|min:0',
            'weekly_rate'       => 'required|numeric|min:0',
            'monthly_rate'      => 'required|numeric|min:0',
            'active'            => 'boolean',
        ]);

        $data['is_custom'] = $request->boolean('is_custom');
        $data['active']    = $request->boolean('active', true);

        Plan::create($data);
        return redirect()->route('plans.index')->with('success', 'Plan created.');
    }

    public function edit(Plan $plan)
    {
        return view('plans.edit', compact('plan'));
    }

    public function update(Request $request, Plan $plan)
    {
        $data = $request->validate([
            'name'              => 'required|string|max:255',
            'specs'             => 'nullable|string|max:500',
            'is_custom'         => 'boolean',
            'deposit_per_unit'  => 'required|numeric|min:0',
            'daily_rate'        => 'required|numeric|min:0',
            'weekly_rate'       => 'required|numeric|min:0',
            'monthly_rate'      => 'required|numeric|min:0',
            'active'            => 'boolean',
        ]);

        $data['is_custom'] = $request->boolean('is_custom');
        $data['active']    = $request->boolean('active');

        $plan->update($data);
        return redirect()->route('plans.index')->with('success', 'Plan updated.');
    }

    public function destroy(Plan $plan)
    {
        $plan->delete();
        return redirect()->route('plans.index')->with('success', 'Plan deleted.');
    }
}
