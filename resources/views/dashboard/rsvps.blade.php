@extends('layouts.dashboard')

@section('title', 'RSVPs')

@section('content')
    @php
        $ceremonyNames = $invitation->ceremonies->pluck('name', 'id');
    @endphp

    <div class="section-heading">
        <div>
            <h2>{{ $invitation->bride_name ?? 'Bride' }} &amp; {{ $invitation->groom_name ?? 'Groom' }}</h2>
            <p class="muted-note">{{ $invitation->rsvps->count() }} RSVP{{ $invitation->rsvps->count() === 1 ? '' : 's' }}</p>
        </div>
        <a href="{{ route('dashboard.invitations') }}">Back to Invitations</a>
    </div>

    @if ($invitation->rsvps->isEmpty())
        <div class="empty-state large">No RSVPs yet.</div>
    @else
        <div class="dashboard-table-wrap">
            <table class="dashboard-table">
                <thead>
                    <tr>
                        <th>Guest</th>
                        <th>Attending</th>
                        <th>Functions</th>
                        <th>Message</th>
                        <th>Submitted</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($invitation->rsvps as $rsvp)
                        <tr>
                            <td>{{ $rsvp->guest_name }}</td>
                            <td>{{ $rsvp->attending ? 'Yes' : 'No' }}</td>
                            <td>
                                @if ($rsvp->attending && filled($rsvp->functions_attending))
                                    {{ collect($rsvp->functions_attending)->map(fn ($id) => $ceremonyNames[(int) $id] ?? $id)->implode(', ') }}
                                @else
                                    -
                                @endif
                            </td>
                            <td>{{ $rsvp->message ?: '-' }}</td>
                            <td>{{ $rsvp->created_at->format('d M Y, h:i A') }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @endif
@endsection
