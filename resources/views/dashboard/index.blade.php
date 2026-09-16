@extends('layouts.dashboard')

@section('title', 'Dashboard')

@section('content')
    <section class="welcome-panel royal-panel">
        <div>
            <p class="eyebrow">Welcome back</p>
            <h2>{{ auth()->user()->name }}</h2>
            <p>Create, share, and track wedding invitations crafted with a royal Indian touch.</p>
        </div>
        <a class="primary-action" href="{{ route('dashboard.create') }}">Create New Invitation</a>
    </section>

    <section class="stats-grid" aria-label="Invitation statistics">
        <article class="stat-card">
            <span>Total Invitations</span>
            <strong>{{ $totalInvitations }}</strong>
        </article>
        <article class="stat-card">
            <span>Total Views</span>
            <strong>{{ $totalViews }}</strong>
        </article>
        <article class="stat-card">
            <span>Active Invitations</span>
            <strong>{{ $activeInvitations }}</strong>
        </article>
    </section>

    <section class="content-section">
        <div class="section-heading">
            <h2>Recent Invitations</h2>
            <a href="{{ route('dashboard.invitations') }}">View all</a>
        </div>

        @if ($recentInvitations->isEmpty())
            <div class="empty-state">No invitations yet. Create your first one!</div>
        @else
            <div class="recent-list">
                @foreach ($recentInvitations as $invitation)
                    @php($data = $invitation->data ?? [])
                    <article class="recent-item">
                        <div>
                            <strong>{{ $data['groom_name'] ?? 'Groom' }} &amp; {{ $data['bride_name'] ?? 'Bride' }}</strong>
                            <span>{{ optional($invitation->created_at)->format('d M Y') }} • {{ ucfirst($invitation->plan) }}</span>
                        </div>
                        <div class="recent-meta">
                            <span>{{ $invitation->views }} views</span>
                            <a href="{{ route('invitations.preview', $invitation->share_slug) }}" target="_blank" rel="noreferrer">Preview</a>
                        </div>
                    </article>
                @endforeach
            </div>
        @endif
    </section>
@endsection
