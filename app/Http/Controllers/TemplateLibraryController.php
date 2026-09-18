<?php

namespace App\Http\Controllers;

use App\Models\InvitationTemplate;
use App\Models\TemplateCategory;
use App\Services\TemplatePreviewService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class TemplateLibraryController extends Controller
{
    public function index(Request $request): View
    {
        $categories = $this->activeCategories();
        $selectedCategory = $request->filled('category')
            ? $categories->firstWhere('slug', $request->string('category')->toString())
            : null;
        $search = trim($request->string('search')->toString());
        $sort = $request->string('sort')->toString();

        $templates = InvitationTemplate::query()
            ->with('templateCategory')
            ->where('is_active', true)
            ->whereHas('templateCategory', fn ($query) => $query->where('is_active', true))
            ->when($selectedCategory, fn ($query) => $query->where('category_id', $selectedCategory->id))
            ->when($search !== '', function ($query) use ($search): void {
                $query->where(function ($query) use ($search): void {
                    $query->where('name', 'like', "%{$search}%")
                        ->orWhere('description', 'like', "%{$search}%")
                        ->orWhereHas('templateCategory', fn ($query) => $query->where('name', 'like', "%{$search}%"));
                });
            })
            ->when($sort === 'newest', fn ($query) => $query->latest(), fn ($query) => $sort === 'name' ? $query->orderBy('name') : $query->orderBy('sort_order')->orderBy('name'))
            ->paginate(12)
            ->withQueryString();

        return view('templates.index', compact('categories', 'selectedCategory', 'templates', 'search', 'sort'));
    }

    public function show(InvitationTemplate $template): View
    {
        $template->load('templateCategory');

        abort_unless($template->is_active && $template->templateCategory?->is_active, 404);

        $relatedTemplates = InvitationTemplate::query()
            ->with('templateCategory')
            ->where('is_active', true)
            ->where('category_id', $template->category_id)
            ->whereKeyNot($template->id)
            ->orderBy('sort_order')
            ->take(4)
            ->get();

        return view('templates.show', compact('template', 'relatedTemplates'));
    }

    public function preview(InvitationTemplate $template, TemplatePreviewService $previewService): View
    {
        $template->load('templateCategory');

        abort_unless($template->is_active && $template->templateCategory?->is_active, 404);

        return view('templates.preview', [
            'template' => $template,
            'invitation' => $previewService->invitationFor($template),
            'publicView' => $previewService->publicViewFor($template),
            'isPreview' => true,
        ]);
    }

    public function select(InvitationTemplate $template, Request $request): RedirectResponse
    {
        $template->load('templateCategory');

        abort_unless($template->is_active && $template->templateCategory?->is_active, 404);

        $request->session()->put('selected_template_id', $template->id);
        $request->session()->put('selected_template_slug', $template->slug);

        if (! Auth::check()) {
            $request->session()->put('url.intended', route('templates.use', $template->slug));

            return redirect()->route('login')->with('status', 'Log in or register to use this template.');
        }

        return redirect()->route('dashboard', ['template' => $template->slug]);
    }

    private function activeCategories()
    {
        return TemplateCategory::query()
            ->where('is_active', true)
            ->orderBy('sort_order')
            ->orderBy('name')
            ->get();
    }
}
