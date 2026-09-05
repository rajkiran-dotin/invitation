@extends('admin.layout')

@section('title', 'Customer Details')

@section('content')
    <section class="admin-stats secondary-stats">
        <article><span>Total Purchases</span><strong>{{ number_format($totalPurchases) }}</strong></article>
        <article><span>Total Amount Spent</span><strong>₹{{ number_format($totalSpent) }}</strong></article>
    </section>

    <section class="admin-panel customer-profile">
        <div class="admin-panel-head">
            <h2>Customer Information</h2>
            <a class="btn btn-light" href="{{ route('admin.users.index') }}">Back</a>
        </div>
        <dl>
            <div><dt>Name</dt><dd>{{ $user->name }}</dd></div>
            <div><dt>Email</dt><dd>{{ $user->email }}</dd></div>
            <div><dt>Joined Date</dt><dd>{{ $user->created_at->format('d M Y') }}</dd></div>
        </dl>
    </section>

    <section class="admin-panel">
        <div class="admin-panel-head">
            <h2>Purchase History</h2>
        </div>
        <div class="admin-table-wrap">
            <table class="admin-table">
                <thead><tr><th>Product / Template</th><th>Plan</th><th>Amount</th><th>Payment Status</th><th>Gateway</th><th>Transaction ID</th><th>Purchase Date</th></tr></thead>
                <tbody>
                    @forelse ($purchases as $purchase)
                        <tr>
                            <td>{{ $purchase->template?->name ?? $purchase->product_name ?? 'Invitation purchase' }}</td>
                            <td>{{ $purchase->plan?->name ?? 'No plan' }}</td>
                            <td>{{ $purchase->currency }} {{ number_format((float) $purchase->amount) }}</td>
                            <td><span @class(['status-pill', 'status-paid' => $purchase->payment_status === 'paid', 'status-pending' => $purchase->payment_status === 'pending', 'status-failed' => $purchase->payment_status === 'failed', 'status-refunded' => $purchase->payment_status === 'refunded', 'status-cancelled' => $purchase->payment_status === 'cancelled'])>{{ ucfirst($purchase->payment_status) }}</span></td>
                            <td>{{ $purchase->gateway ?? 'Not recorded' }}</td>
                            <td>{{ $purchase->transaction_id ?? 'Not recorded' }}</td>
                            <td>{{ $purchase->purchased_at?->format('d M Y') ?? $purchase->created_at->format('d M Y') }}</td>
                        </tr>
                    @empty
                        <tr><td colspan="7" class="empty-state">No purchases yet.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{ $purchases->links() }}
    </section>
@endsection
