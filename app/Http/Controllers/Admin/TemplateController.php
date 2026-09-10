<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\InvitationTemplate;
use App\Models\TemplateCategory;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class TemplateController extends Controller
{
    public function index(): View
    {
        return view('admin.templates.index', [
            'templates' => InvitationTemplate::query()->with('templateCategory')->orderBy('sort_order')->paginate(12),
        ]);
    }

    public function create(): View
    {
        return view('admin.templates.form', [
            'template' => new InvitationTemplate,
            'categories' => $this->categoryOptions(),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        InvitationTemplate::create($this->validated($request));

        return redirect()->route('admin.templates.index')->with('status', 'Template created.');
    }

    public function edit(InvitationTemplate $template): View
    {
        return view('admin.templates.form', [
            'template' => $template,
            'categories' => $this->categoryOptions($template),
        ]);
    }

    public function update(Request $request, InvitationTemplate $template): RedirectResponse
    {
        $template->update($this->validated($request, $template));

        return redirect()->route('admin.templates.index')->with('status', 'Template updated.');
    }

    public function destroy(InvitationTemplate $template): RedirectResponse
    {
        $template->delete();

        return back()->with('status', 'Template deleted.');
    }

    private function validated(Request $request, ?InvitationTemplate $template = null): array
    {
        $request->merge([
            'slug' => Str::slug($request->filled('slug') ? $request->string('slug')->toString() : $request->string('name')->toString()),
        ]);

        $data = $request->validate([
            'name' => ['required', 'string', 'max:120'],
            'slug' => ['required', 'string', 'max:140', 'alpha_dash', Rule::unique('invitation_templates', 'slug')->ignore($template?->id)],
            'category_id' => ['required', Rule::exists('template_categories', 'id')],
            'description' => ['nullable', 'string', 'max:1000'],
            'preview_image' => ['nullable', 'string', 'max:255'],
            'demo_url' => ['nullable', 'url', 'max:255'],
            'features_text' => ['nullable', 'string', 'max:2000'],
            'theme_class' => ['required', 'in:royal,floral,classic,minimal,modern,pastel'],
            'price' => ['required', 'numeric', 'min:0'],
            'sort_order' => ['required', 'integer', 'min:0'],
            'is_premium' => ['nullable', 'boolean'],
            'is_active' => ['nullable', 'boolean'],
        ]);

        $data['category'] = TemplateCategory::query()->whereKey($data['category_id'])->value('name');
        $data['features'] = collect(preg_split('/\r\n|\r|\n/', $data['features_text'] ?? ''))
            ->map(fn (string $feature): string => trim($feature))
            ->filter()
            ->values()
            ->all();

        unset($data['features_text']);

        return $data + ['is_premium' => false, 'is_active' => false];
    }

    private function categoryOptions(?InvitationTemplate $template = null): Collection
    {
        return TemplateCategory::query()
            ->where(function ($query) use ($template): void {
                $query->where('is_active', true);

                if ($template?->category_id) {
                    $query->orWhereKey($template->category_id);
                }
            })
            ->orderBy('sort_order')
            ->orderBy('name')
            ->get();
    }
}
