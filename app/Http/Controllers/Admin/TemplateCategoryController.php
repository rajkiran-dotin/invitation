<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\TemplateCategory;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class TemplateCategoryController extends Controller
{
    public function index(): View
    {
        return view('admin.template-categories.index', [
            'categories' => TemplateCategory::query()
                ->withCount('templates')
                ->orderBy('sort_order')
                ->orderBy('name')
                ->paginate(12),
        ]);
    }

    public function create(): View
    {
        return view('admin.template-categories.form', ['category' => new TemplateCategory]);
    }

    public function store(Request $request): RedirectResponse
    {
        TemplateCategory::create($this->validated($request));

        return redirect()->route('admin.template-categories.index')->with('status', 'Template category created.');
    }

    public function edit(TemplateCategory $templateCategory): View
    {
        return view('admin.template-categories.form', ['category' => $templateCategory]);
    }

    public function update(Request $request, TemplateCategory $templateCategory): RedirectResponse
    {
        $templateCategory->update($this->validated($request, $templateCategory));

        return redirect()->route('admin.template-categories.index')->with('status', 'Template category updated.');
    }

    public function toggle(TemplateCategory $templateCategory): RedirectResponse
    {
        $templateCategory->update(['is_active' => ! $templateCategory->is_active]);

        return back()->with('status', 'Template category status updated.');
    }

    public function destroy(TemplateCategory $templateCategory): RedirectResponse
    {
        $templatesCount = $templateCategory->templates()->count();

        if ($templatesCount > 0) {
            return back()->with('status', "Cannot delete this category because {$templatesCount} templates are assigned to it.");
        }

        $templateCategory->delete();

        return back()->with('status', 'Template category deleted.');
    }

    private function validated(Request $request, ?TemplateCategory $category = null): array
    {
        $request->merge([
            'slug' => Str::slug($request->filled('slug') ? $request->string('slug')->toString() : $request->string('name')->toString()),
        ]);

        $categoryId = $category?->id;

        $data = $request->validate([
            'name' => ['required', 'string', 'max:120'],
            'slug' => ['required', 'string', 'max:140', 'alpha_dash', Rule::unique('template_categories', 'slug')->ignore($categoryId)],
            'description' => ['nullable', 'string', 'max:1000'],
            'sort_order' => ['required', 'integer', 'min:0'],
            'is_active' => ['nullable', 'boolean'],
        ]);

        $data['slug'] = Str::slug($data['slug']);

        return $data + ['is_active' => false];
    }
}
