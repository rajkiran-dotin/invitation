@extends('admin.layout')

@section('title', 'Templates')

@section('content')
    <section class="admin-panel">
        <div class="admin-panel-head">
            <h2>Invitation Templates</h2>
            <a class="btn btn-primary" href="{{ route('admin.templates.create') }}">Add Template</a>
        </div>
        <div class="admin-table-wrap">
            <table class="admin-table">
                <thead><tr><th>Name</th><th>Category</th><th>Theme</th><th>Price</th><th>Status</th><th>Sort</th><th>Action</th></tr></thead>
                <tbody>
                    @forelse ($templates as $template)
                        <tr>
                            <td><strong>{{ $template->name }}</strong></td>
                            <td>{{ $template->category }}</td>
                            <td>{{ ucfirst($template->theme_class) }}</td>
                            <td>₹ {{ number_format((float) $template->price) }}</td>
                            <td><span class="status-pill">{{ $template->is_active ? 'Active' : 'Hidden' }}</span></td>
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
                        <tr><td colspan="7" class="empty-state">No templates found.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        {{ $templates->links() }}
    </section>
@endsection
