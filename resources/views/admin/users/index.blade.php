@extends('admin.layout')

@section('title', 'New Users')

@section('content')
    <section class="admin-panel">
        <div class="admin-panel-head">
            <h2>Customers</h2>
        </div>

        <form class="admin-filters" method="GET" action="{{ route('admin.users.index') }}">
            <label>Search<input name="search" value="{{ request('search') }}" placeholder="Name or email"></label>
            <label>Registration
                <select name="registration">
                    <option value="">All</option>
                    <option value="today" @selected(request('registration') === 'today')>Today</option>
                    <option value="7" @selected(request('registration') === '7')>Last 7 Days</option>
                    <option value="30" @selected(request('registration') === '30')>Last 30 Days</option>
                </select>
            </label>
            <label>Payment
                <select name="payment_status">
                    <option value="">All</option>
                    @foreach ($paymentStatuses as $status)
                        <option value="{{ $status }}" @selected(request('payment_status') === $status)>{{ ucfirst($status) }}</option>
                    @endforeach
                </select>
            </label>
            <label>Plan
                <select name="plan_id">
                    <option value="">All</option>
                    @foreach ($plans as $plan)
                        <option value="{{ $plan->id }}" @selected((int) request('plan_id') === $plan->id)>{{ $plan->name }}</option>
                    @endforeach
                </select>
            </label>
            <label>Template
                <select name="template_id">
                    <option value="">All</option>
                    @foreach ($templates as $template)
                        <option value="{{ $template->id }}" @selected((int) request('template_id') === $template->id)>{{ $template->name }}</option>
                    @endforeach
                </select>
            </label>
            <div class="filter-actions">
                <button class="btn btn-primary" type="submit">Filter</button>
                <a class="btn btn-light" href="{{ route('admin.users.index') }}">Reset</a>
            </div>
        </form>

        <div class="admin-table-wrap">
            <table class="admin-table">
                <thead><tr><th>User</th><th>Email</th><th>Joined</th><th>Total Purchases</th><th>Total Spent</th><th>Latest Purchase</th><th>Payment Status</th><th>Action</th></tr></thead>
                <tbody>
                    @forelse ($users as $user)
                        @php($latestPurchase = $user->latestPurchase)
                        <tr>
                            <td><strong>{{ $user->name }}</strong></td>
                            <td>{{ $user->email }}</td>
                            <td>{{ $user->created_at->format('d M Y') }}</td>
                            <td>{{ $user->purchases_count }}</td>
                            <td>₹{{ number_format((float) ($user->total_spent ?? 0)) }}</td>
                            <td>
                                {{ $latestPurchase?->template?->name ?? $latestPurchase?->product_name ?? 'No purchases' }}
                                @if ($latestPurchase?->plan)
                                    <small>{{ $latestPurchase->plan->name }}</small>
                                @endif
                            </td>
                            <td>
                                @if ($latestPurchase)
                                    <span @class(['status-pill', 'status-paid' => $latestPurchase->payment_status === 'paid', 'status-pending' => $latestPurchase->payment_status === 'pending', 'status-failed' => $latestPurchase->payment_status === 'failed', 'status-refunded' => $latestPurchase->payment_status === 'refunded', 'status-cancelled' => $latestPurchase->payment_status === 'cancelled'])>{{ ucfirst($latestPurchase->payment_status) }}</span>
                                @else
                                    <span class="status-pill">None</span>
                                @endif
                            </td>
                            <td><a href="{{ route('admin.users.show', $user) }}">View</a></td>
                        </tr>
                    @empty
                        <tr><td colspan="8" class="empty-state">No users registered yet.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{ $users->links() }}
    </section>
@endsection
