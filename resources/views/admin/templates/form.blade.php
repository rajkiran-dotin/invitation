@extends('admin.layout')

@section('title', $template->exists ? 'Edit Template' : 'Add Template')

@section('content')
    <form class="admin-form admin-panel" method="POST" action="{{ $template->exists ? route('admin.templates.update', $template) : route('admin.templates.store') }}">
        @csrf
        @if ($template->exists) @method('PUT') @endif

        <div class="form-grid">
            <label>Name<input name="name" value="{{ old('name', $template->name) }}" required></label>
            <label>Slug<input name="slug" value="{{ old('slug', $template->slug) }}" placeholder="Auto-generates from name if left blank"></label>
            <label>Category
                <select name="category_id" required>
                    <option value="">Select category</option>
                    @foreach ($categories as $category)
                        <option value="{{ $category->id }}" @selected((int) old('category_id', $template->category_id) === $category->id)>{{ $category->name }}{{ $category->is_active ? '' : ' (Hidden)' }}</option>
                    @endforeach
                </select>
            </label>
            <label>Theme
                <select name="theme_class" required>
                    @foreach (['royal', 'floral', 'classic', 'minimal', 'modern', 'pastel'] as $theme)
                        <option value="{{ $theme }}" @selected(old('theme_class', $template->theme_class ?: 'floral') === $theme)>{{ ucfirst($theme) }}</option>
                    @endforeach
                </select>
            </label>
            <label>Price<input type="number" min="0" step="1" name="price" value="{{ old('price', $template->price ?: 499) }}" required></label>
            <label>Sort Order<input type="number" min="0" name="sort_order" value="{{ old('sort_order', $template->sort_order ?: 0) }}" required></label>
            <label class="wide">Description<textarea name="description" rows="4">{{ old('description', $template->description) }}</textarea></label>
            <label>Preview Image URL<input name="preview_image" value="{{ old('preview_image', $template->preview_image) }}" placeholder="/images/templates/example.svg"></label>
            <label>Preview/Demo URL<input name="demo_url" value="{{ old('demo_url', $template->demo_url) }}" placeholder="https://example.com/demo"></label>
            <label class="wide">Features, one per line<textarea name="features_text" rows="5">{{ old('features_text', implode(PHP_EOL, $template->features ?? [])) }}</textarea></label>
            <label class="check-row"><input type="checkbox" name="is_premium" value="1" @checked(old('is_premium', $template->exists ? $template->is_premium : true))> Premium</label>
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