<?php

namespace App\Http\Controllers;

use App\Models\Invitation;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(Request $request): View|RedirectResponse
    {
        if ($request->user()->is_admin) {
            return redirect()->route('admin.dashboard');
        }

        if ($request->filled('template')) {
            return redirect()->route('invitations.create', ['template' => $request->string('template')->toString()]);
        }

        $invitations = Invitation::query()->where('user_id', $request->user()->id);

        return view('dashboard.index', [
            'pageTitle' => 'Dashboard',
            'totalInvitations' => (clone $invitations)->count(),
            'totalViews' => (clone $invitations)->sum('views'),
            'activeInvitations' => (clone $invitations)->where('status', Invitation::Published)->count(),
            'recentInvitations' => (clone $invitations)->with('template')->latest()->take(5)->get(),
        ]);
    }

    public function invitations(Request $request): View
    {
        return view('dashboard.invitations', [
            'pageTitle' => 'My Invitations',
            'invitations' => Invitation::query()
                ->with('template')
                ->withCount('rsvps')
                ->where('user_id', $request->user()->id)
                ->latest()
                ->get(),
        ]);
    }

    public function rsvps(Request $request, Invitation $invitation): View
    {
        abort_unless((int) $invitation->user_id === (int) $request->user()->id, 403);

        $invitation->load([
            'ceremonies',
            'rsvps' => fn ($query) => $query->latest(),
        ]);

        return view('dashboard.rsvps', [
            'pageTitle' => 'RSVPs',
            'invitation' => $invitation,
        ]);
    }

    public function profile(Request $request): View
    {
        return view('dashboard.profile', [
            'pageTitle' => 'Profile',
            'user' => $request->user(),
        ]);
    }

    public function updateProfile(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:120'],
            'email' => ['nullable', 'email', 'max:255', Rule::unique('users', 'email')->ignore($request->user()->id)],
        ]);

        $request->user()->update($validated);

        return back()->with('status', 'Profile updated successfully.');
    }
}
