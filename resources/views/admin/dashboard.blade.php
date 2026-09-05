@extends('admin.layout')

@section('title', 'Dashboard')

@section('content')
    <section class="admin-stats">
        <article><span>Today's Visitors</span><strong>{{ number_format($todayUniqueVisitors) }}</strong></article>
        <article><span>Today's Page Views</span><strong>{{ number_format($todayPageViews) }}</strong></article>
        <article><span>New Users Today</span><strong>{{ number_format($newUsersToday) }}</strong></article>
        <article><span>Purchases Today</span><strong>{{ number_format($purchasesToday) }}</strong></article>
        <article><span>Today's Revenue</span><strong>₹{{ number_format($todayRevenue) }}</strong></article>
        <article><span>Total Users</span><strong>{{ number_format($totalUsers) }}</strong></article>
        <article><span>Total Purchases</span><strong>{{ number_format($totalPurchases) }}</strong></article>
        <article><span>Total Revenue</span><strong>₹{{ number_format($totalRevenue) }}</strong></article>
    </section>

    <section class="admin-panel analytics-panel">
        <div class="admin-panel-head">
            <h2>Visitors & Purchases - Last 7 Days</h2>
        </div>
        @php
            $chartMax = max([1, ...$chartData['visitors'], ...$chartData['purchases']]);
        @endphp
        <div class="analytics-chart" aria-label="Visitors and purchases for the last 7 days">
            @foreach ($chartData['labels'] as $index => $label)
                <div class="chart-day">
                    <div class="chart-bars">
                        <span class="visitor-bar" style="height: {{ max(6, ($chartData['visitors'][$index] / $chartMax) * 100) }}%" title="{{ $chartData['visitors'][$index] }} visitors"></span>
                        <span class="purchase-bar" style="height: {{ max(6, ($chartData['purchases'][$index] / $chartMax) * 100) }}%" title="{{ $chartData['purchases'][$index] }} purchases"></span>
                    </div>
                    <small>{{ $label }}</small>
                </div>
            @endforeach
        </div>
        <div class="chart-legend"><span><i class="visitor-key"></i>Visitors</span><span><i class="purchase-key"></i>Purchases</span></div>
    </section>

    <section class="admin-panel">
        <div class="admin-panel-head">
            <h2>Recent Purchases</h2>
            <a class="btn btn-outline" href="{{ route('admin.users.index') }}">View All</a>
        </div>
        <div class="admin-table-wrap">
            <table class="admin-table">
                <thead><tr><th>User</th><th>Product / Template</th><th>Plan</th><th>Amount</th><th>Payment Status</th><th>Purchased At</th></tr></thead>
                <tbody>
                    @forelse ($recentPurchases as $purchase)
                        <tr>
                            <td><strong>{{ $purchase->user?->name ?? 'Deleted user' }}</strong><small>{{ $purchase->user?->email }}</small></td>
                            <td>{{ $purchase->template?->name ?? $purchase->product_name ?? 'Invitation purchase' }}</td>
                            <td>{{ $purchase->plan?->name ?? 'No plan' }}</td>
                            <td>{{ $purchase->currency }} {{ number_format((float) $purchase->amount) }}</td>
                            <td><span @class(['status-pill', 'status-paid' => $purchase->payment_status === 'paid', 'status-pending' => $purchase->payment_status === 'pending', 'status-failed' => $purchase->payment_status === 'failed', 'status-refunded' => $purchase->payment_status === 'refunded', 'status-cancelled' => $purchase->payment_status === 'cancelled'])>{{ ucfirst($purchase->payment_status) }}</span></td>
                            <td>{{ $purchase->purchased_at?->format('d M Y') ?? $purchase->created_at->format('d M Y') }}</td>
                        </tr>
                    @empty
                        <tr><td colspan="6" class="empty-state">No purchases yet.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </section>

    <section class="admin-stats secondary-stats">
        <article><span>New Enquiries</span><strong>{{ number_format($newEnquiries) }}</strong></article>
        <article><span>Total Enquiries</span><strong>{{ number_format($totalEnquiries) }}</strong></article>
        <article><span>Templates</span><strong>{{ number_format($templates) }}</strong></article>
        <article><span>Plans</span><strong>{{ number_format($plans) }}</strong></article>
    </section>

    <section class="admin-panel">
        <div class="admin-panel-head">
            <h2>Recent Enquiries</h2>
            <a class="btn btn-outline" href="{{ route('admin.enquiries.index') }}">View All</a>
        </div>
        @include('admin.partials.enquiry-table', ['enquiries' => $recentEnquiries, 'compact' => true])
    </section>
@endsection
