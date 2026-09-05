@extends('admin.layout')

@section('title', 'Plans')

@section('content')
    <section class="admin-panel">
        <div class="admin-panel-head">
            <h2>Pricing Plans</h2>
            <a class="btn btn-primary" href="{{ route('admin.plans.create') }}">Add Plan</a>
        </div>
        <div class="admin-table-wrap">
            <table class="admin-table">
                <thead><tr><th>Name</th><th>Price</th><th>Features</th><th>Status</th><th>Sort</th><th>Action</th></tr></thead>
                <tbody>
                    @forelse ($plans as $plan)
                        <tr>
                            <td><strong>{{ $plan->name }}</strong><small>{{ $plan->is_popular ? 'Most popular' : '' }}</small></td>
                            <td>₹ {{ number_format((float) $plan->price) }}</td>
                            <td>{{ count($plan->features ?? []) }} features</td>
                            <td><span class="status-pill">{{ $plan->is_active ? 'Active' : 'Hidden' }}</span></td>
                            <td>{{ $plan->sort_order }}</td>
                            <td>
                                <a href="{{ route('admin.plans.edit', $plan) }}">Edit</a>
                                <form method="POST" action="{{ route('admin.plans.destroy', $plan) }}" class="inline-form">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit">Delete</button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="6" class="empty-state">No plans found.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        {{ $plans->links() }}
    </section>
@endsection
