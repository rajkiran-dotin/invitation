@extends('layouts.dashboard')

@section('title', 'Preview Invitation')

@section('content')
    <section class="content-section">
        <div class="section-heading">
            <h2>Preview Invitation</h2>
            <div class="preview-actions">
                <a class="secondary-action" href="{{ route('invitations.edit', $invitation) }}">Back & Edit</a>
                <form method="POST" action="{{ route('invitations.publish', $invitation) }}">
                    @csrf
                    <button class="primary-action" type="submit">Publish Invitation</button>
                </form>
            </div>
        </div>
    </section>

    @include($publicView, ['invitation' => $invitation, 'isPreview' => true])
@endsection
