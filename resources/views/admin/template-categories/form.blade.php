@extends('admin.layout')

@section('title', $category->exists ? 'Edit Template Category' : 'Add Template Category')

@section('content')
    <form class="admin-form admin-panel" method="POST" action="{{ $category->exists ? route('admin.template-categories.update', $category) : route('admin.template-categories.store') }}">
        @csrf
        @if ($category->exists) @method('PUT') @endif

        <div class="form-grid">
            <label>Category Name<input name="name" value="{{ old('name', $category->name) }}" required></label>
            <label>Slug<input name="slug" value="{{ old('slug', $category->slug) }}" placeholder="Auto-generates from name if left blank"></label>
            <label class="wide">Description<textarea name="description" rows="4">{{ old('description', $category->description) }}</textarea></label>
            <label>Sort Order<input type="number" min="0" name="sort_order" value="{{ old('sort_order', $category->sort_order ?: 0) }}" required></label>
            <label class="check-row"><input type="checkbox" name="is_active" value="1" @checked(old('is_active', $category->exists ? $category->is_active : true))> Active</label>
        </div>

        @if ($errors->any())
            <div class="form-error">{{ $errors->first() }}</div>
        @endif

        <div class="form-actions">
            <a class="btn btn-light" href="{{ route('admin.template-categories.index') }}">Cancel</a>
            <button class="btn btn-primary" type="submit">Save Category</button>
        </div>
    </form>
@endsection