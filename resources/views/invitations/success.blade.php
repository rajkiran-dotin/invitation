@extends('layouts.dashboard')

@section('title', 'Invitation Published')

@section('content')
    @php
        $publicUrl = route('invitations.public', $invitation->slug);
        $shareText = rawurlencode("You're invited to celebrate {$invitation->bride_name} & {$invitation->groom_name}'s wedding 💍 {$publicUrl}");
    @endphp
    <section class="royal-panel success-panel">
        <p class="eyebrow">Your invitation is live</p>
        <h2>{{ $invitation->bride_name }} &amp; {{ $invitation->groom_name }}</h2>
        <p>{{ $publicUrl }}</p>
        <div class="success-actions">
            <a class="primary-action" href="{{ $publicUrl }}" target="_blank" rel="noreferrer">View Invitation</a>
            <button class="secondary-action" type="button" data-copy="{{ $publicUrl }}">Copy Link</button>
            <a class="secondary-action" href="https://wa.me/?text={{ $shareText }}" target="_blank" rel="noreferrer">Share on WhatsApp</a>
            <a class="secondary-action" href="{{ route('dashboard') }}">Go to Dashboard</a>
        </div>
    </section>
@endsection

@push('scripts')
<script>
    document.querySelector('[data-copy]')?.addEventListener('click', async function () {
        await navigator.clipboard.writeText(this.dataset.copy);
        this.textContent = 'Copied';
    });
</script>
@endpush
