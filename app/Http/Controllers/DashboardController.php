<?php

namespace App\Http\Controllers;

use Illuminate\Database\Query\Builder;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(Request $request): View|RedirectResponse
    {
        if ($request->user()->is_admin) {
            return redirect()->route('admin.dashboard');
        }

        if (! $this->hasInvitationsTable()) {
            return view('dashboard.index', [
                'pageTitle' => 'Dashboard',
                'totalInvitations' => 0,
                'totalViews' => 0,
                'activeInvitations' => 0,
                'recentInvitations' => collect(),
            ]);
        }

        $invitations = $this->userInvitations($request);

        return view('dashboard.index', [
            'pageTitle' => 'Dashboard',
            'totalInvitations' => (clone $invitations)->count(),
            'totalViews' => (clone $invitations)->sum('views'),
            'activeInvitations' => (clone $invitations)->where('status', 'published')->count(),
            'recentInvitations' => $this->withTemplates((clone $invitations)->latest()->take(5)->get()),
        ]);
    }

    public function invitations(Request $request): View
    {
        return view('dashboard.invitations', [
            'pageTitle' => 'My Invitations',
            'invitations' => $this->hasInvitationsTable()
                ? $this->withTemplates($this->userInvitations($request)->latest()->get())
                : collect(),
        ]);
    }

    public function create(Request $request): View
    {
        return view('dashboard.create', [
            'pageTitle' => 'Create Invitation',
            'templates' => $this->activeTemplates(),
            'razorpayKey' => config('services.razorpay.key'),
            'plans' => $this->plans(),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'template_id' => ['required', 'integer'],
            'groom_name' => ['required', 'string', 'max:120'],
            'bride_name' => ['required', 'string', 'max:120'],
            'wedding_date' => ['required', 'date'],
            'wedding_time' => ['nullable', 'string', 'max:40'],
            'venue_name' => ['required', 'string', 'max:180'],
            'venue_address' => ['nullable', 'string', 'max:500'],
            'city' => ['required', 'string', 'max:120'],
            'family_names' => ['nullable', 'string', 'max:1000'],
            'rsvp_phone' => ['nullable', 'string', 'max:30'],
            'plan' => ['required', Rule::in(['silver', 'gold', 'platinum'])],
            'razorpay_payment_id' => ['nullable', 'string', 'max:120'],
            'razorpay_order_id' => ['nullable', 'string', 'max:120'],
            'razorpay_signature' => ['nullable', 'string', 'max:255'],
        ]);

        if (! $this->hasInvitationsTable()) {
            return back()->withErrors(['invitation' => 'Invitations table is missing. Please run migrations first.'])->withInput();
        }

        $shareSlug = $this->uniqueShareSlug($validated['groom_name'], $validated['bride_name']);

        DB::table('invitations')->insert([
            'user_id' => $request->user()->id,
            'template_id' => $validated['template_id'],
            'share_slug' => $shareSlug,
            'data' => json_encode([
                'groom_name' => $validated['groom_name'],
                'bride_name' => $validated['bride_name'],
                'wedding_date' => $validated['wedding_date'],
                'wedding_time' => $validated['wedding_time'] ?? null,
                'venue_name' => $validated['venue_name'],
                'venue_address' => $validated['venue_address'] ?? null,
                'city' => $validated['city'],
                'family_names' => $validated['family_names'] ?? null,
                'rsvp_phone' => $validated['rsvp_phone'] ?? null,
                'payment' => [
                    'gateway' => 'razorpay',
                    'payment_id' => $validated['razorpay_payment_id'] ?? null,
                    'order_id' => $validated['razorpay_order_id'] ?? null,
                    'signature' => $validated['razorpay_signature'] ?? null,
                ],
            ]),
            'plan' => $validated['plan'],
            'views' => 0,
            'status' => 'published',
            'created_at' => now(),
        ]);

        return redirect()->route('invitations.preview', $shareSlug);
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

    public function destroy(Request $request, int $invitation): RedirectResponse
    {
        if (! $this->hasInvitationsTable()) {
            return back()->with('status', 'No invitations found.');
        }

        DB::table('invitations')
            ->where('id', $invitation)
            ->where('user_id', $request->user()->id)
            ->delete();

        return back()->with('status', 'Invitation deleted successfully.');
    }

    public function preview(string $shareSlug): Response
    {
        abort_unless($this->hasInvitationsTable(), 404);

        $invitation = DB::table('invitations')->where('share_slug', $shareSlug)->first();
        abort_unless($invitation, 404);

        DB::table('invitations')->where('id', $invitation->id)->increment('views');

        $data = $this->decodeData($invitation->data ?? null);
        $date = isset($data['wedding_date']) ? Carbon::parse($data['wedding_date'])->format('l, d F Y') : 'Wedding Date';
        $time = ! empty($data['wedding_time']) ? ' at '.$data['wedding_time'] : '';

        $groom = e($data['groom_name'] ?? 'Groom');
        $bride = e($data['bride_name'] ?? 'Bride');
        $venue = e($data['venue_name'] ?? 'Venue');
        $address = e($data['venue_address'] ?? '');
        $city = e($data['city'] ?? '');
        $families = e($data['family_names'] ?? '');
        $rsvp = e($data['rsvp_phone'] ?? '');

        return response()->make(<<<HTML
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{$groom} &amp; {$bride} - InviteCraft</title>
    <link rel="stylesheet" href="/css/dashboard.css">
</head>
<body class="preview-body">
    <main class="public-invite">
        <p class="eyebrow">Wedding Invitation</p>
        <h1>{$groom} <span>&amp;</span> {$bride}</h1>
        <p class="invite-date">{$date}{$time}</p>
        <section><h2>{$venue}</h2><p>{$address} {$city}</p></section>
        <section><h2>With Blessings From</h2><p>{$families}</p></section>
        <a class="primary-action" href="tel:{$rsvp}">RSVP {$rsvp}</a>
    </main>
</body>
</html>
HTML);
    }

    private function hasInvitationsTable(): bool
    {
        return Schema::hasTable('invitations');
    }

    private function userInvitations(Request $request): Builder
    {
        return DB::table('invitations')->where('user_id', $request->user()->id);
    }

    private function withTemplates(Collection $invitations): Collection
    {
        $invitations = $this->normalizeInvitations($invitations);
        $templates = $this->templatesByIds($invitations->pluck('template_id')->filter()->unique()->values());

        return $invitations->map(function (object $invitation) use ($templates): object {
            $invitation->dashboard_template = $templates->get((int) $invitation->template_id);

            return $invitation;
        });
    }

    private function normalizeInvitations(Collection $invitations): Collection
    {
        return $invitations->map(function (object $invitation): object {
            $invitation->data = $this->decodeData($invitation->data ?? null);
            $invitation->created_at = isset($invitation->created_at) ? Carbon::parse($invitation->created_at) : null;

            return $invitation;
        });
    }

    private function decodeData(mixed $data): array
    {
        if (is_array($data)) {
            return $data;
        }

        if (! is_string($data) || $data === '') {
            return [];
        }

        $decoded = json_decode($data, true);

        return is_array($decoded) ? $decoded : [];
    }

    private function activeTemplates(): Collection
    {
        $table = $this->templateTable();

        if (! $table) {
            return collect();
        }

        $query = DB::table($table)->orderBy($this->hasColumn($table, 'sort_order') ? 'sort_order' : 'id');

        if ($this->hasColumn($table, 'status')) {
            $query->where('status', 'active');
        } elseif ($this->hasColumn($table, 'is_active')) {
            $query->where('is_active', true);
        }

        return $query->get()->map(fn (object $template): object => $this->normalizeTemplate($template));
    }

    private function templatesByIds(Collection $ids): Collection
    {
        $table = $this->templateTable();

        if (! $table || $ids->isEmpty()) {
            return collect();
        }

        return DB::table($table)
            ->whereIn('id', $ids)
            ->get()
            ->map(fn (object $template): object => $this->normalizeTemplate($template))
            ->keyBy('id');
    }

    private function normalizeTemplate(object $template): object
    {
        $template->thumbnail_url = $template->thumbnail ?? $template->preview_image ?? null;
        $template->premium = (bool) ($template->is_premium ?? false);
        $template->active_slug = $template->slug ?? (string) $template->id;

        return $template;
    }

    private function templateTable(): ?string
    {
        if (Schema::hasTable('templates')) {
            return 'templates';
        }

        if (Schema::hasTable('invitation_templates')) {
            return 'invitation_templates';
        }

        return null;
    }

    private function hasColumn(string $table, string $column): bool
    {
        return Schema::hasColumn($table, $column);
    }

    private function uniqueShareSlug(string $groom, string $bride): string
    {
        $base = Str::slug($groom.' '.$bride) ?: 'invitation';
        $slug = $base;
        $suffix = 2;

        while ($this->hasInvitationsTable() && DB::table('invitations')->where('share_slug', $slug)->exists()) {
            $slug = $base.'-'.$suffix;
            $suffix++;
        }

        return $slug;
    }

    private function plans(): array
    {
        return [
            'silver' => ['name' => 'Silver', 'price' => 399, 'features' => ['Digital invite page', 'Shareable link', 'Basic RSVP details']],
            'gold' => ['name' => 'Gold', 'price' => 699, 'features' => ['Everything in Silver', 'Premium template access', 'Venue and family sections']],
            'platinum' => ['name' => 'Platinum', 'price' => 999, 'features' => ['Everything in Gold', 'Priority styling support', 'Extended event details']],
        ];
    }
}
