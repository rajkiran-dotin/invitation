@extends('layouts.dashboard')

@section('title', 'My Invitations')

@section('content')
    @if ($invitations->isEmpty())
        <div class="empty-state large">No invitations yet. Create your first one!</div>
    @else
        <section class="invitation-grid">
            @foreach ($invitations as $invitation)
                @php
                    $data = $invitation->data ?? [];
                    $template = $invitation->dashboard_template;
                    $shareUrl = route('invitations.preview', $invitation->share_slug);
                @endphp
                <article class="invitation-card">
                    <div class="invitation-thumb">
                        @if ($template?->thumbnail_url)
                            <img src="{{ $template->thumbnail_url }}" alt="{{ $template->name }} thumbnail">
                        @else
                            <div class="thumb-fallback">{{ $template->name ?? 'InviteCraft' }}</div>
                        @endif
                    </div>
                    <div class="invitation-body">
                        <h2>{{ $data['groom_name'] ?? 'Groom' }} &amp; {{ $data['bride_name'] ?? 'Bride' }}</h2>
                        <p>{{ isset($data['wedding_date']) ? \Illuminate\Support\Carbon::parse($data['wedding_date'])->format('d M Y') : 'Wedding date' }}</p>
                        <div class="badge-row">
                            <span class="badge plan">{{ ucfirst($invitation->plan) }}</span>
                            <span class="badge {{ $invitation->status === 'published' ? 'success' : 'muted' }}">{{ ucfirst($invitation->status) }}</span>
                        </div>
                        <div class="views-count">{{ $invitation->views }} views</div>
                        <div class="card-actions">
                            <button type="button" data-copy="{{ $shareUrl }}">Copy Share Link</button>
                            <a href="{{ $shareUrl }}" target="_blank" rel="noreferrer">Preview</a>
                            <form method="POST" action="{{ route('dashboard.invitations.destroy', $invitation) }}" data-confirm-delete>
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="danger">Delete</button>
                            </form>
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
            setTimeout(function () { button.textContent = 'Copy Share Link'; }, 1600);
        });
    });

    document.querySelectorAll('[data-confirm-delete]').forEach(function (form) {
        form.addEventListener('submit', function (event) {
            if (!confirm('Delete this invitation?')) {
                event.preventDefault();
            }
        });
    });
</script>
@endpush
