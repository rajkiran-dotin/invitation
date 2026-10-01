@extends('layouts.dashboard')

@section('title', 'My Invitations')

@section('content')
    @if ($invitations->isEmpty())
        <div class="empty-state large">No invitations yet. Create your first one!</div>
    @else
        <section class="invitation-grid">
            @foreach ($invitations as $invitation)
                @php
                    $template = $invitation->template;
                    $publicUrl = $invitation->slug ? route('invitations.public', $invitation->slug) : null;
                    $shareText = rawurlencode("You're invited to celebrate {$invitation->bride_name} & {$invitation->groom_name}'s wedding 💍".($publicUrl ? " {$publicUrl}" : ''));
                @endphp
                <article class="invitation-card">
                    <div class="invitation-thumb">
                        @if ($template?->preview_image)
                            <img src="{{ $template->preview_image }}" alt="{{ $template->name }} thumbnail">
                        @else
                            <div class="thumb-fallback">{{ $template->name ?? 'InviteCraft' }}</div>
                        @endif
                    </div>
                    <div class="invitation-body">
                        <h2>{{ $invitation->bride_name ?? 'Bride' }} &amp; {{ $invitation->groom_name ?? 'Groom' }}</h2>
                        <p>{{ optional($invitation->wedding_date)->format('d M Y') ?? 'Wedding date pending' }}</p>
                        <div class="badge-row">
                            <span class="badge plan">{{ $template->name ?? 'Template' }}</span>
                            <span class="badge {{ $invitation->status === 'published' ? 'success' : 'muted' }}">{{ ucfirst($invitation->status) }}</span>
                        </div>
                        <div class="views-count">{{ $invitation->views }} views</div>
                        <div class="views-count">RSVPs: {{ $invitation->rsvps_count }}</div>
                        <div class="card-actions invitation-actions">
                            @if ($invitation->status === 'published' && $publicUrl)
                                <a href="{{ $publicUrl }}" target="_blank" rel="noreferrer">View Live</a>
                                <a href="{{ route('dashboard.invitations.rsvps', $invitation) }}">View RSVPs</a>
                                <button type="button" data-copy="{{ $publicUrl }}">Copy Link</button>
                                <a href="https://wa.me/?text={{ $shareText }}" target="_blank" rel="noreferrer">Share</a>
                                <form method="POST" action="{{ route('invitations.unpublish', $invitation) }}">
                                    @csrf
                                    <button type="submit" class="danger">Unpublish</button>
                                </form>
                            @elseif ($invitation->status === 'unpublished')
                                <a href="{{ route('invitations.preview', $invitation) }}" target="_blank" rel="noreferrer">Preview</a>
                                <a href="{{ route('invitations.edit', $invitation) }}">Edit</a>
                                <form method="POST" action="{{ route('invitations.publish', $invitation) }}">
                                    @csrf
                                    <button type="submit">Publish Again</button>
                                </form>
                            @else
                                <a href="{{ route('invitations.edit', $invitation) }}">Continue</a>
                                <a href="{{ route('invitations.preview', $invitation) }}" target="_blank" rel="noreferrer">Preview</a>
                            @endif
                            <a href="{{ route('invitations.edit', $invitation) }}">Edit</a>
                        </div>
                    </div>
                </article>
            @endforeach
        </section>
    @endif
@endsection

@push('scripts')
<script>
    document.querySelectorAll('[data-copy]').forEach(function (button) {
        button.addEventListener('click', async function () {
            await navigator.clipboard.writeText(button.dataset.copy);
            button.textContent = 'Copied';
            setTimeout(function () { button.textContent = 'Copy Link'; }, 1600);
        });
    });
</script>
@endpush
