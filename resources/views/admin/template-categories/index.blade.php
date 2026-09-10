@extends('admin.layout')

@section('title', 'Template Categories')

@section('content')
    <section class="admin-panel">
        <div class="admin-panel-head">
            <h2>Template Categories</h2>
            <a class="btn btn-primary" href="{{ route('admin.template-categories.create') }}">Add Category</a>
        </div>
        <div class="admin-table-wrap">
            <table class="admin-table">
                <thead><tr><th>Name</th><th>Slug</th><th>Templates Count</th><th>Status</th><th>Sort Order</th><th>Actions</th></tr></thead>
                <tbody>
                    @forelse ($categories as $category)
                        <tr>
                            <td><strong>{{ $category->name }}</strong><small>{{ $category->description }}</small></td>
                            <td>{{ $category->slug }}</td>
                            <td>{{ $category->templates_count }}</td>
                            <td><span class="status-pill">{{ $category->is_active ? 'Active' : 'Hidden' }}</span></td>
                            <td>{{ $category->sort_order }}</td>
                            <td>
                                <a href="{{ route('admin.template-categories.edit', $category) }}">Edit</a>
                                <form method="POST" action="{{ route('admin.template-categories.toggle', $category) }}" class="inline-form">
                                    @csrf
                                    @method('PATCH')
                                    <button type="submit">{{ $category->is_active ? 'Deactivate' : 'Activate' }}</button>
                                </form>
                                <form method="POST" action="{{ route('admin.template-categories.destroy', $category) }}" class="inline-form">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit">Delete</button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="6" class="empty-state">No template categories found.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        {{ $categories->links() }}
    </section>
@endsection