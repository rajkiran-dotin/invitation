@extends('admin.layout')

@section('title', $template->exists ? 'Edit Template' : 'Add Template')

@section('content')
    <form class="admin-form admin-panel" method="POST" action="{{ $template->exists ? route('admin.templates.update', $template) : route('admin.templates.store') }}">
        @csrf
        @if ($template->exists) @method('PUT') @endif

        <div class="form-grid">
            <label>Name<input name="name" value="{{ old('name', $template->name) }}" required></label>
            <label>Category<input name="category" value="{{ old('category', $template->category ?: 'Wedding') }}" required></label>
            <label>Theme
                <select name="theme_class" required>
                    @foreach (['royal', 'floral', 'classic', 'minimal', 'modern', 'pastel'] as $theme)
                        <option value="{{ $theme }}" @selected(old('theme_class', $template->theme_class ?: 'floral') === $theme)>{{ ucfirst($theme) }}</option>
                    @endforeach
                </select>
            </label>
            <label>Price<input type="number" min="0" step="1" name="price" value="{{ old('price', $template->price ?: 499) }}" required></label>
            <label>Sort Order<input type="number" min="0" name="sort_order" value="{{ old('sort_order', $template->sort_order ?: 0) }}" required></label>
            <label class="check-row"><input type="checkbox" name="is_active" value="1" @checked(old('is_active', $template->exists ? $template->is_active : true))> Active</label>
        </div>

        @if ($errors->any())
            <div class="form-error">{{ $errors->first() }}</div>
        @endif

        <div class="form-actions">
            <a class="btn btn-light" href="{{ route('admin.templates.index') }}">Cancel</a>
            <button class="btn btn-primary" type="submit">Save Template</button>
        </div>
    </form>
@endsection
