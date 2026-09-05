@extends('admin.layout')

@section('title', $plan->exists ? 'Edit Plan' : 'Add Plan')

@section('content')
    <form class="admin-form admin-panel" method="POST" action="{{ $plan->exists ? route('admin.plans.update', $plan) : route('admin.plans.store') }}">
        @csrf
        @if ($plan->exists) @method('PUT') @endif

        <div class="form-grid">
            <label>Name<input name="name" value="{{ old('name', $plan->name) }}" required></label>
            <label>Price<input type="number" min="0" step="1" name="price" value="{{ old('price', $plan->price ?: 999) }}" required></label>
            <label>Sort Order<input type="number" min="0" name="sort_order" value="{{ old('sort_order', $plan->sort_order ?: 0) }}" required></label>
            <label class="check-row"><input type="checkbox" name="is_popular" value="1" @checked(old('is_popular', $plan->is_popular))> Most Popular</label>
            <label class="check-row"><input type="checkbox" name="is_active" value="1" @checked(old('is_active', $plan->exists ? $plan->is_active : true))> Active</label>
            <label class="wide">Features, one per line
                <textarea name="features_text" rows="9" required>{{ old('features_text', implode(PHP_EOL, $plan->features ?? [])) }}</textarea>
            </label>
        </div>

        @if ($errors->any())
            <div class="form-error">{{ $errors->first() }}</div>
        @endif

        <div class="form-actions">
            <a class="btn btn-light" href="{{ route('admin.plans.index') }}">Cancel</a>
            <button class="btn btn-primary" type="submit">Save Plan</button>
        </div>
    </form>
@endsection
