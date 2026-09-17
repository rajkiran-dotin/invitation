@extends('layouts.dashboard')

@section('title', 'Dashboard')

@section('content')
    <section class="welcome-panel royal-panel">
        <div>
            <p class="eyebrow">Welcome back</p>
            <h2>{{ auth()->user()->name }}</h2>
            <p>Create, share, and track wedding invitations crafted with a royal Indian touch.</p>
        </div>
        <a class="primary-action" href="{{ route('templates.index') }}">Create New Invitation</a>
    </section>

    <section class="stats-grid" aria-label="Invitation statistics">
        <article class="stat-card"><span>Total Invitations</span><strong>{{ $totalInvitations }}</strong></article>
        <article class="stat-card"><span>Total Views</span><strong>{{ $totalViews }}</strong></article>
        <article class="stat-card"><span>Active Invitations</span><strong>{{ $activeInvitations }}</strong></article>
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
                    <article class="recent-item">
                        <div>
                            <strong>{{ $invitation->bride_name ?? 'Bride' }} &amp; {{ $invitation->groom_name ?? 'Groom' }}</strong>
                            <span>{{ optional($invitation->wedding_date)->format('d M Y') ?? 'Wedding date pending' }} · {{ ucfirst($invitation->status) }}</span>
                        </div>
                        <div class="recent-meta">
                            <span>{{ $invitation->views }} views</span>
                            <a href="{{ route('invitations.edit', $invitation) }}">Edit</a>
                            <a href="{{ route('invitations.preview', $invitation) }}" target="_blank" rel="noreferrer">Preview</a>
                        </div>
                    </article>
                @endforeach
            </div>
        @endif
    </section>
@endsection
