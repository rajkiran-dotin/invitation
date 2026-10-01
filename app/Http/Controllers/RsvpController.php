<?php

namespace App\Http\Controllers;

use App\Models\Invitation;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class RsvpController extends Controller
{
    public function store(Request $request, string $slug): RedirectResponse|JsonResponse
    {
        $invitation = Invitation::query()
            ->with('ceremonies')
            ->where('slug', $slug)
            ->where('status', Invitation::Published)
            ->firstOrFail();

        $validCeremonyIds = $invitation->ceremonies->pluck('id')->map(fn (int $id): string => (string) $id)->all();

        $validated = $request->validate([
            'guest_name' => ['required', 'string', 'max:255'],
            'attending' => ['required', 'boolean'],
            'functions_attending' => ['nullable', 'array'],
            'functions_attending.*' => ['string', 'max:255', Rule::in($validCeremonyIds)],
            'message' => ['nullable', 'string', 'max:2000'],
        ]);

        $attending = $request->boolean('attending');

        $invitation->rsvps()->create([
            'guest_name' => $validated['guest_name'],
            'attending' => $attending,
            'functions_attending' => $attending ? ($validated['functions_attending'] ?? null) : null,
            'message' => $validated['message'] ?? null,
        ]);

        $message = 'Thank you! Your RSVP has been received.';

        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'message' => $message,
            ]);
        }

        return back()->with('status', $message);
    }
}
