<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\PricingPlan;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class PlanController extends Controller
{
    public function index(): View
    {
        return view('admin.plans.index', [
            'plans' => PricingPlan::query()->orderBy('sort_order')->paginate(12),
        ]);
    }

    public function create(): View
    {
        return view('admin.plans.form', ['plan' => new PricingPlan()]);
    }

    public function store(Request $request): RedirectResponse
    {
        PricingPlan::create($this->validated($request));

        return redirect()->route('admin.plans.index')->with('status', 'Plan created.');
    }

    public function edit(PricingPlan $plan): View
    {
        return view('admin.plans.form', compact('plan'));
    }

    public function update(Request $request, PricingPlan $plan): RedirectResponse
    {
        $plan->update($this->validated($request));

        return redirect()->route('admin.plans.index')->with('status', 'Plan updated.');
    }

    public function destroy(PricingPlan $plan): RedirectResponse
    {
        $plan->delete();

        return back()->with('status', 'Plan deleted.');
    }

    private function validated(Request $request): array
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:120'],
            'price' => ['required', 'numeric', 'min:0'],
            'features_text' => ['required', 'string'],
            'sort_order' => ['required', 'integer', 'min:0'],
            'is_popular' => ['nullable', 'boolean'],
            'is_active' => ['nullable', 'boolean'],
        ]);

        $data['features'] = collect(preg_split('/\r\n|\r|\n/', $data['features_text']))
            ->map(fn (string $feature): string => trim($feature))
            ->filter()
            ->values()
            ->all();

        unset($data['features_text']);

        return $data + ['is_popular' => false, 'is_active' => false];
    }
}
