@extends('admin.layout')

@section('title', 'Templates')

@section('content')
    <section class="admin-panel">
        <div class="admin-panel-head">
            <h2>Invitation Templates</h2>
            <a class="btn btn-primary" href="{{ route('admin.templates.create') }}">Add Template</a>
        </div>
        <form class="admin-compact-filters" method="GET" action="{{ route('admin.templates.index') }}">
            <label>
                Category
                <select name="category_id" onchange="this.form.submit()">
                    <option value="">All Categories</option>
                    @foreach ($categories as $category)
                        <option value="{{ $category->id }}" @selected((string) request('category_id') === (string) $category->id)>{{ $category->name }}</option>
                    @endforeach
                </select>
            </label>
            <label>
                Status
                <select name="status" onchange="this.form.submit()">
                    <option value="">All Status</option>
                    <option value="active" @selected(request('status') === 'active')>Active</option>
                    <option value="hidden" @selected(request('status') === 'hidden')>Hidden</option>
                </select>
            </label>
            <a class="filter-reset-link" href="{{ route('admin.templates.index') }}">Reset</a>
        </form>
        <div class="admin-table-wrap">
            <table class="admin-table">
                <thead><tr><th>Name</th><th>Slug</th><th>Category</th><th>Type</th><th>Theme</th><th>Status</th><th>Sort</th><th>Action</th></tr></thead>
                <tbody>
                    @forelse ($templates as $template)
                        <tr>
                            <td><strong>{{ $template->name }}</strong><small>{{ $template->description }}</small></td>
                            <td>{{ $template->slug }}</td>
                            <td>{{ $template->templateCategory?->name ?? $template->category }}</td>
                            <td>{{ $template->is_premium ? 'Premium' : 'Free' }}</td>
                            <td>{{ ucfirst($template->theme_class) }}</td>
                            <td>
                                <label class="template-status-control" data-template-status-control>
                                    <span class="status-pill {{ $template->is_active ? 'status-active' : 'status-hidden' }}" data-template-status-label>
                                        {{ $template->is_active ? 'Active' : 'Hidden' }}
                                    </span>
                                    <select
                                        aria-label="Change {{ $template->name }} status"
                                        data-template-status-select
                                        data-status-url="{{ route('admin.templates.status', $template) }}"
                                    >
                                        <option value="active" @selected($template->is_active)>Active</option>
                                        <option value="hidden" @selected(! $template->is_active)>Hidden</option>
                                    </select>
                                </label>
                            </td>
                            <td>{{ $template->sort_order }}</td>
                            <td>
                                <a href="{{ route('admin.templates.edit', $template) }}">Edit</a>
                                <form method="POST" action="{{ route('admin.templates.destroy', $template) }}" class="inline-form">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit">Delete</button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        @php($hasTemplateFilters = request()->filled('category_id') || request()->filled('status'))
                        <tr>
                            <td colspan="8" class="empty-state">
                                {{ $hasTemplateFilters ? 'No templates found in this category.' : 'No templates found.' }}
                                @if ($hasTemplateFilters)
                                    <a class="filter-clear-link" href="{{ route('admin.templates.index') }}">Clear Filter</a>
                                @endif
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        {{ $templates->links() }}
    </section>

    <div class="admin-toast" data-admin-toast role="status" aria-live="polite" hidden></div>
@endsection

@push('scripts')
    <script>
        const templateStatusToast = document.querySelector('[data-admin-toast]');
        let templateStatusToastTimer;

        function showTemplateStatusToast(message, type = 'success') {
            if (!templateStatusToast) return;

            window.clearTimeout(templateStatusToastTimer);
            templateStatusToast.textContent = message;
            templateStatusToast.dataset.type = type;
            templateStatusToast.hidden = false;

            templateStatusToastTimer = window.setTimeout(() => {
                templateStatusToast.hidden = true;
            }, 2800);
        }

        function setTemplateStatusState(control, status) {
            const label = control.querySelector('[data-template-status-label]');
            if (!label) return;

            label.textContent = status === 'active' ? 'Active' : 'Hidden';
            label.classList.toggle('status-active', status === 'active');
            label.classList.toggle('status-hidden', status === 'hidden');
        }

        document.querySelectorAll('[data-template-status-select]').forEach((select) => {
            select.dataset.previousStatus = select.value;

            select.addEventListener('change', async () => {
                const previousStatus = select.dataset.previousStatus;
                const nextStatus = select.value;
                const control = select.closest('[data-template-status-control]');

                select.disabled = true;
                setTemplateStatusState(control, nextStatus);

                try {
                    const response = await fetch(select.dataset.statusUrl, {
                        method: 'PATCH',
                        headers: {
                            'Accept': 'application/json',
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        },
                        body: JSON.stringify({ status: nextStatus }),
                    });

                    const data = await response.json();

                    if (!response.ok || !data.success) {
                        throw new Error(data.message || 'Unable to update template status.');
                    }

                    select.value = data.status;
                    select.dataset.previousStatus = data.status;
                    setTemplateStatusState(control, data.status);
                    showTemplateStatusToast('Template status updated');
                } catch (error) {
                    select.value = previousStatus;
                    setTemplateStatusState(control, previousStatus);
                    showTemplateStatusToast('Could not update template status', 'error');
                } finally {
                    select.disabled = false;
                }
            });
        });
    </script>
@endpush
