@extends('layouts.dashboard')

@section('title', 'Profile')

@section('content')
    <section class="profile-wrap royal-panel">
        <div class="profile-summary">
            <div class="profile-avatar">{{ strtoupper(substr($user->name ?? 'U', 0, 1)) }}</div>
            <h2>{{ $user->name }}</h2>
            <p>{{ $user->phone ?? 'Phone not added' }}</p>
            <p>{{ $user->email ?? 'Email not added' }}</p>
        </div>

        <form class="profile-form" method="POST" action="{{ route('dashboard.profile.update') }}">
            @csrf
            @method('PUT')
            <label>Name<input required name="name" value="{{ old('name', $user->name) }}"></label>
            <label>Email<input type="email" name="email" value="{{ old('email', $user->email) }}"></label>
            <label>Phone<input value="{{ $user->phone }}" disabled><small>Phone cannot be changed because it is OTP based.</small></label>
            <button class="primary-action" type="submit">Save Profile</button>
        </form>
    </section>
@endsection
