<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\InvitationTemplate;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class TemplateController extends Controller
{
    public function index(): View
    {
        return view('admin.templates.index', [
            'templates' => InvitationTemplate::query()->orderBy('sort_order')->paginate(12),
        ]);
    }

    public function create(): View
    {
        return view('admin.templates.form', ['template' => new InvitationTemplate()]);
    }

    public function store(Request $request): RedirectResponse
    {
        InvitationTemplate::create($this->validated($request));

        return redirect()->route('admin.templates.index')->with('status', 'Template created.');
    }

    public function edit(InvitationTemplate $template): View
    {
        return view('admin.templates.form', compact('template'));
    }

    public function update(Request $request, InvitationTemplate $template): RedirectResponse
    {
        $template->update($this->validated($request));

        return redirect()->route('admin.templates.index')->with('status', 'Template updated.');
    }

    public function destroy(InvitationTemplate $template): RedirectResponse
    {
        $template->delete();

        return back()->with('status', 'Template deleted.');
    }

    private function validated(Request $request): array
    {
        return $request->validate([
            'name' => ['required', 'string', 'max:120'],
            'category' => ['required', 'string', 'max:80'],
            'theme_class' => ['required', 'in:royal,floral,classic,minimal,modern,pastel'],
            'price' => ['required', 'numeric', 'min:0'],
            'sort_order' => ['required', 'integer', 'min:0'],
            'is_active' => ['nullable', 'boolean'],
        ]) + ['is_active' => false];
    }
}
