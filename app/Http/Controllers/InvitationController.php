<?php

namespace App\Http\Controllers;

use App\Models\Invitation;
use App\Models\InvitationGalleryImage;
use App\Models\InvitationTemplate;
use App\Services\InvitationPublishingService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class InvitationController extends Controller
{
    public function create(Request $request): RedirectResponse
    {
        $template = $this->resolveTemplate($request);

        if (! $template) {
            return redirect()->route('templates.index')->withErrors(['template' => 'Please choose a template first.']);
        }

        $invitation = Invitation::create([
            'user_id' => $request->user()->id,
            'template_id' => $template->id,
            'status' => Invitation::Draft,
            'settings' => $this->defaultSettings(),
        ]);

        $request->session()->forget(['selected_template_id', 'selected_template_slug']);

        return redirect()->route('invitations.edit', $invitation);
    }

    public function edit(Request $request, Invitation $invitation): View
    {
        $this->authorizeOwner($request, $invitation);

        $invitation->load(['template', 'ceremonies', 'galleryImages']);

        return view('dashboard.create', [
            'pageTitle' => 'Invitation Builder',
            'invitation' => $invitation,
            'templates' => InvitationTemplate::query()->where('is_active', true)->orderBy('sort_order')->orderBy('name')->get(),
            'googleMapsApiKey' => config('services.google.maps_api_key'),
            'settings' => array_merge($this->defaultSettings(), $invitation->settings ?? []),
        ]);
    }

    public function update(Request $request, Invitation $invitation): RedirectResponse
    {
        $this->authorizeOwner($request, $invitation);

        $validated = $request->validate($this->rules($invitation));

        $this->storeInvitationImages($request, $validated, $invitation);

        $invitation->update([
            'template_id' => $validated['template_id'],
            'bride_name' => $validated['bride_name'],
            'groom_name' => $validated['groom_name'],
            'bride_father_name' => $validated['bride_father_name'] ?? null,
            'bride_mother_name' => $validated['bride_mother_name'] ?? null,
            'groom_father_name' => $validated['groom_father_name'] ?? null,
            'groom_mother_name' => $validated['groom_mother_name'] ?? null,
            'wedding_date' => $validated['wedding_date'],
            'wedding_time' => $validated['wedding_time'] ?? null,
            'message' => $validated['message'] ?? null,
            'venue_name' => $validated['venue_name'] ?? null,
            'formatted_address' => $validated['formatted_address'] ?? null,
            'google_place_id' => $validated['google_place_id'] ?? null,
            'latitude' => $validated['latitude'] ?? null,
            'longitude' => $validated['longitude'] ?? null,
            'google_maps_url' => $this->googleMapsUrl($validated['venue_name'] ?? null, $validated['formatted_address'] ?? null, $validated['google_place_id'] ?? null),
            'settings' => $this->normalizeSettings($validated['settings'] ?? []),
            'bride_photo_path' => $validated['bride_photo_path'] ?? $invitation->bride_photo_path,
            'groom_photo_path' => $validated['groom_photo_path'] ?? $invitation->groom_photo_path,
        ]);

        $this->syncCeremonies($request, $invitation, $validated['ceremonies'] ?? []);
        $this->storeGalleryImages($request, $invitation);

        return redirect()
            ->route($request->input('next_action') === 'preview' ? 'invitations.preview' : 'invitations.edit', $invitation)
            ->with('status', 'Invitation draft saved.');
    }

    public function preview(Request $request, Invitation $invitation): View
    {
        $this->authorizeOwner($request, $invitation);

        $invitation->load(['template', 'ceremonies', 'galleryImages']);

        return view('invitations.preview', [
            'invitation' => $invitation,
            'publicView' => $this->publicView($invitation),
        ]);
    }

    public function publish(Request $request, Invitation $invitation, InvitationPublishingService $publisher): RedirectResponse
    {
        $this->authorizeOwner($request, $invitation);

        $request->validate([
            'confirm_publish' => ['nullable', 'string'],
        ]);

        if (! $invitation->bride_name || ! $invitation->groom_name || ! $invitation->wedding_date) {
            return redirect()->route('invitations.edit', $invitation)->withErrors(['publish' => 'Bride, groom and wedding date are required before publishing.']);
        }

        $publisher->publish($invitation);

        return redirect()->route('invitations.success', $invitation);
    }

    public function unpublish(Request $request, Invitation $invitation, InvitationPublishingService $publisher): RedirectResponse
    {
        $this->authorizeOwner($request, $invitation);
        $publisher->unpublish($invitation);

        return back()->with('status', 'Invitation unpublished.');
    }

    public function success(Request $request, Invitation $invitation): View
    {
        $this->authorizeOwner($request, $invitation);

        return view('invitations.success', ['invitation' => $invitation]);
    }

    public function showPublic(string $slug): View
    {
        $invitation = Invitation::query()
            ->with(['template', 'ceremonies', 'galleryImages'])
            ->where('slug', $slug)
            ->where('status', Invitation::Published)
            ->firstOrFail();

        $invitation->increment('views');

        return view('invitations.public.show', [
            'invitation' => $invitation,
            'publicView' => $this->publicView($invitation),
        ]);
    }

    public function destroyGalleryImage(Request $request, Invitation $invitation, InvitationGalleryImage $image): RedirectResponse
    {
        $this->authorizeOwner($request, $invitation);
        abort_unless((int) $image->invitation_id === (int) $invitation->id, 404);

        Storage::disk('public')->delete($image->image_path);
        $image->delete();

        return back()->with('status', 'Gallery image removed.');
    }

    private function resolveTemplate(Request $request): ?InvitationTemplate
    {
        $slug = $request->query('template') ?: $request->session()->get('selected_template_slug');
        $id = $request->session()->get('selected_template_id');

        return InvitationTemplate::query()
            ->where('is_active', true)
            ->when($slug, fn ($query) => $query->where('slug', $slug))
            ->when(! $slug && $id, fn ($query) => $query->whereKey($id))
            ->first();
    }

    private function authorizeOwner(Request $request, Invitation $invitation): void
    {
        abort_unless((int) $invitation->user_id === (int) $request->user()->id, 403);
    }

    private function rules(Invitation $invitation): array
    {
        return [
            'template_id' => ['required', 'integer', Rule::exists('invitation_templates', 'id')],
            'bride_name' => ['required', 'string', 'max:120'],
            'groom_name' => ['required', 'string', 'max:120'],
            'bride_photo' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:3072'],
            'groom_photo' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:3072'],
            'bride_father_name' => ['nullable', 'string', 'max:120'],
            'bride_mother_name' => ['nullable', 'string', 'max:120'],
            'groom_father_name' => ['nullable', 'string', 'max:120'],
            'groom_mother_name' => ['nullable', 'string', 'max:120'],
            'wedding_date' => ['required', 'date'],
            'wedding_time' => ['nullable', 'date_format:H:i'],
            'message' => ['nullable', 'string', 'max:2000'],
            'venue_name' => ['nullable', 'string', 'max:255'],
            'formatted_address' => ['nullable', 'string', 'max:500'],
            'google_place_id' => ['nullable', 'string', 'max:255'],
            'latitude' => ['nullable', 'numeric', 'between:-90,90'],
            'longitude' => ['nullable', 'numeric', 'between:-180,180'],
            'google_maps_url' => ['nullable', 'url', 'max:2048'],
            'settings' => ['nullable', 'array'],
            'settings.music_enabled' => ['nullable', 'boolean'],
            'settings.rsvp_enabled' => ['nullable', 'boolean'],
            'settings.gallery_enabled' => ['nullable', 'boolean'],
            'settings.story_enabled' => ['nullable', 'boolean'],
            'settings.family_enabled' => ['nullable', 'boolean'],
            'ceremonies' => ['nullable', 'array'],
            'ceremonies.*.id' => ['nullable', 'integer'],
            'ceremonies.*.name' => ['required_with:ceremonies', 'string', 'max:120'],
            'ceremonies.*.date' => ['required_with:ceremonies', 'date'],
            'ceremonies.*.description' => ['nullable', 'string', 'max:1000'],
            'ceremonies.*.dress_code' => ['nullable', 'string', 'max:120'],
            'ceremonies.*.venue_name' => ['nullable', 'string', 'max:255'],
            'ceremonies.*.formatted_address' => ['nullable', 'string', 'max:500'],
            'ceremonies.*.google_place_id' => ['nullable', 'string', 'max:255'],
            'ceremonies.*.latitude' => ['nullable', 'numeric', 'between:-90,90'],
            'ceremonies.*.longitude' => ['nullable', 'numeric', 'between:-180,180'],
            'ceremonies.*.google_maps_url' => ['nullable', 'url', 'max:2048'],
            'ceremonies.*.image' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:3072'],
            'gallery_images.*' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:4096'],
        ];
    }

    private function storeInvitationImages(Request $request, array &$validated, Invitation $invitation): void
    {
        foreach (['bride_photo' => 'bride_photo_path', 'groom_photo' => 'groom_photo_path'] as $input => $column) {
            if (! $request->hasFile($input)) {
                continue;
            }

            if ($invitation->{$column}) {
                Storage::disk('public')->delete($invitation->{$column});
            }

            $validated[$column] = $request->file($input)->store('invitations/'.$invitation->id, 'public');
            $invitation->{$column} = $validated[$column];
        }
    }

    private function syncCeremonies(Request $request, Invitation $invitation, array $ceremonies): void
    {
        $keptIds = [];

        foreach (array_values($ceremonies) as $index => $ceremonyData) {
            $ceremony = $invitation->ceremonies()->whereKey($ceremonyData['id'] ?? null)->first() ?? $invitation->ceremonies()->make();

            if ($request->hasFile("ceremonies.{$index}.image")) {
                if ($ceremony->image_path) {
                    Storage::disk('public')->delete($ceremony->image_path);
                }

                $ceremonyData['image_path'] = $request->file("ceremonies.{$index}.image")->store('invitations/'.$invitation->id.'/ceremonies', 'public');
            }

            $ceremony->fill([
                'name' => $ceremonyData['name'],
                'date' => $ceremonyData['date'],
                'start_time' => null,
                'end_time' => null,
                'description' => $ceremonyData['description'] ?? null,
                'dress_code' => $ceremonyData['dress_code'] ?? null,
                'image_path' => $ceremonyData['image_path'] ?? $ceremony->image_path,
                'venue_name' => $ceremonyData['venue_name'] ?? null,
                'formatted_address' => $ceremonyData['formatted_address'] ?? null,
                'google_place_id' => $ceremonyData['google_place_id'] ?? null,
                'latitude' => $ceremonyData['latitude'] ?? null,
                'longitude' => $ceremonyData['longitude'] ?? null,
                'google_maps_url' => $this->googleMapsUrl($ceremonyData['venue_name'] ?? null, $ceremonyData['formatted_address'] ?? null, $ceremonyData['google_place_id'] ?? null),
                'sort_order' => $index,
            ]);
            $ceremony->save();
            $keptIds[] = $ceremony->id;
        }

        $invitation->ceremonies()->whereNotIn('id', $keptIds ?: [0])->get()->each(function ($ceremony): void {
            if ($ceremony->image_path) {
                Storage::disk('public')->delete($ceremony->image_path);
            }
            $ceremony->delete();
        });
    }

    private function storeGalleryImages(Request $request, Invitation $invitation): void
    {
        if (! $request->hasFile('gallery_images')) {
            return;
        }

        $nextOrder = (int) $invitation->galleryImages()->max('sort_order') + 1;

        foreach ($request->file('gallery_images') as $image) {
            $invitation->galleryImages()->create([
                'image_path' => $image->store('invitations/'.$invitation->id.'/gallery', 'public'),
                'category' => 'Gallery',
                'sort_order' => $nextOrder++,
            ]);
        }
    }

    private function googleMapsUrl(?string $venueName, ?string $formattedAddress, ?string $placeId = null): ?string
    {
        $query = trim(collect([$venueName, $formattedAddress])->filter()->implode(', '));

        if ($query === '') {
            return null;
        }

        $parameters = [
            'api' => '1',
            'query' => $query,
        ];

        if ($placeId) {
            $parameters['query_place_id'] = $placeId;
        }

        return 'https://www.google.com/maps/search/?'.http_build_query($parameters, '', '&', PHP_QUERY_RFC3986);
    }

    private function normalizeSettings(array $settings): array
    {
        return collect($this->defaultSettings())
            ->mapWithKeys(fn ($default, $key) => [$key => (bool) ($settings[$key] ?? false)])
            ->all();
    }

    private function defaultSettings(): array
    {
        return [
            'music_enabled' => false,
            'rsvp_enabled' => true,
            'gallery_enabled' => true,
            'story_enabled' => true,
            'family_enabled' => true,
        ];
    }

    private function publicView(Invitation $invitation): string
    {
        $view = $invitation->template?->view_name ?: 'invitations.public.default';

        return view()->exists($view) ? $view : 'invitations.public.default';
    }
}
