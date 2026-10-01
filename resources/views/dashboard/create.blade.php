@extends('layouts.dashboard')

@section('title', 'Invitation Builder')

@section('content')
    <form class="create-flow builder-flow" method="POST" action="{{ route('invitations.update', $invitation) }}" enctype="multipart/form-data" data-google-key="{{ $googleMapsApiKey }}">
        @csrf
        @method('PUT')
        <input type="hidden" name="next_action" id="nextAction" value="save">

        <div class="stepper builder-stepper" aria-label="Invitation builder steps">
            <button type="button" class="active" data-step-button="1">1 Couple</button>
            <button type="button" data-step-button="2">2 Functions</button>
            <button type="button" data-step-button="3">3 Venues</button>
            <button type="button" data-step-button="4">4 Photos</button>
            <button type="button" data-step-button="5">5 Settings</button>
            <button type="button" data-step-button="6">6 Preview</button>
        </div>

        @if ($errors->any())
            <div class="error-box">
                @foreach ($errors->all() as $error)
                    <p>{{ $error }}</p>
                @endforeach
            </div>
        @endif

        <section class="flow-step active" data-step="1">
            <div class="section-heading"><h2>Couple Details</h2></div>
            <div class="form-grid">
                <label>Template
                    <select name="template_id" required>
                        @foreach ($templates as $template)
                            <option value="{{ $template->id }}" @selected(old('template_id', $invitation->template_id) == $template->id)>{{ $template->name }}</option>
                        @endforeach
                    </select>
                </label>
                <label>Wedding Date *<input required type="date" name="wedding_date" value="{{ old('wedding_date', optional($invitation->wedding_date)->format('Y-m-d')) }}"></label>
                <label>Bride Name *<input required name="bride_name" value="{{ old('bride_name', $invitation->bride_name) }}"></label>
                <label>Groom Name *<input required name="groom_name" value="{{ old('groom_name', $invitation->groom_name) }}"></label>
                <label>Wedding Time<input type="time" name="wedding_time" value="{{ old('wedding_time', $invitation->wedding_time) }}"></label>
                <label>Bride Photo<input type="file" name="bride_photo" accept="image/*"></label>
                <label>Groom Photo<input type="file" name="groom_photo" accept="image/*"></label>
                <label>Bride Father Name<input name="bride_father_name" value="{{ old('bride_father_name', $invitation->bride_father_name) }}"></label>
                <label>Bride Mother Name<input name="bride_mother_name" value="{{ old('bride_mother_name', $invitation->bride_mother_name) }}"></label>
                <label>Groom Father Name<input name="groom_father_name" value="{{ old('groom_father_name', $invitation->groom_father_name) }}"></label>
                <label>Groom Mother Name<input name="groom_mother_name" value="{{ old('groom_mother_name', $invitation->groom_mother_name) }}"></label>
                <label class="full">Invitation Message<textarea name="message" rows="4">{{ old('message', $invitation->message) }}</textarea></label>
            </div>
            <div class="flow-actions"><button type="button" class="primary-action" data-next-step="2">Save & Continue</button></div>
        </section>

        <section class="flow-step" data-step="2">
            @php
                $selectedWeddingSide = old('wedding_side', $invitation->wedding_side ?: 'both');
                $savedCeremonies = $invitation->ceremonies->map(fn ($ceremony) => $ceremony->toArray())->all();
                $ceremonies = old('ceremonies');

                if ($ceremonies === null) {
                    $ceremonies = $savedCeremonies ?: collect($weddingSides[$selectedWeddingSide] ?? [])->map(fn (array $ceremony, int $index) => [
                        'name' => $ceremony['name'],
                        'slug' => $ceremony['slug'],
                        'date' => optional($invitation->wedding_date)->format('Y-m-d'),
                        'sort_order' => $index + 1,
                    ])->all();
                }
            @endphp
            <div class="section-heading">
                <h2>Wedding Functions</h2>
                <button type="button" class="secondary-action" id="addCeremony">Add Function</button>
            </div>
            <fieldset class="settings-grid" id="weddingSideOptions">
                <legend>Whose side is this invitation for?</legend>
                <label class="toggle-row"><input type="radio" name="wedding_side" value="bride" @checked($selectedWeddingSide === 'bride')> Bride Side</label>
                <label class="toggle-row"><input type="radio" name="wedding_side" value="groom" @checked($selectedWeddingSide === 'groom')> Groom Side</label>
                <label class="toggle-row"><input type="radio" name="wedding_side" value="both" @checked($selectedWeddingSide === 'both')> Both Families</label>
            </fieldset>
            <div id="ceremonyList" class="builder-list">
                @foreach ($ceremonies as $index => $ceremony)
                    @include('invitations.partials.ceremony-fields', ['index' => $index, 'ceremony' => (array) $ceremony])
                @endforeach
            </div>
            <div class="flow-actions split">
                <button type="button" class="secondary-action" data-prev-step="1">Back</button>
                <button type="button" class="primary-action" data-next-step="3">Save & Continue</button>
            </div>
        </section>
        <section class="flow-step" data-step="3">
            <div class="section-heading"><h2>Main Venue & Locations</h2></div>
            <div class="form-grid">
                <label>Main Wedding Venue<input class="place-input" name="venue_name" value="{{ old('venue_name', $invitation->venue_name) }}" data-place-prefix="main" placeholder="Start typing venue..."></label>
                <label class="full">Formatted Address / selected location<textarea name="formatted_address" id="main_formatted_address" rows="3">{{ old('formatted_address', $invitation->formatted_address) }}</textarea></label>
                <input type="hidden" name="google_place_id" id="main_google_place_id" value="{{ old('google_place_id', $invitation->google_place_id) }}">
                <input type="hidden" name="latitude" id="main_latitude" value="{{ old('latitude', $invitation->latitude) }}">
                <input type="hidden" name="longitude" id="main_longitude" value="{{ old('longitude', $invitation->longitude) }}">
                <input type="hidden" name="google_maps_url" id="main_google_maps_url" value="{{ old('google_maps_url', $invitation->google_maps_url) }}">
            </div>
            <div class="map-preview" data-map-preview="main">
                <strong>{{ $invitation->venue_name ?: 'Venue preview' }}</strong>
                <p>{{ $invitation->formatted_address ?: 'Select a Google place to show map details.' }}</p>
                @if ($invitation->google_maps_url)
                    <a href="{{ $invitation->google_maps_url }}" target="_blank" rel="noreferrer">View on Google Maps</a>
                @endif
            </div>
            <p class="muted-note">Each function above can have a separate venue. Use Google suggestions in function venue fields too.</p>
            <div class="flow-actions split">
                <button type="button" class="secondary-action" data-prev-step="2">Back</button>
                <button type="button" class="primary-action" data-next-step="4">Save & Continue</button>
            </div>
        </section>

        <section class="flow-step" data-step="4">
            <div class="section-heading"><h2>Photos / Gallery</h2></div>
            <label class="gallery-upload">Upload Gallery Photos<input type="file" name="gallery_images[]" accept="image/*" multiple></label>
            @if ($invitation->galleryImages->isNotEmpty())
                <div class="gallery-manager">
                    @foreach ($invitation->galleryImages as $image)
                        <figure>
                            <img src="{{ Storage::url($image->image_path) }}" alt="Gallery image">
                            <figcaption>{{ $image->category ?? 'Gallery' }}</figcaption>
                        </figure>
                    @endforeach
                </div>
            @endif
            <div class="flow-actions split">
                <button type="button" class="secondary-action" data-prev-step="3">Back</button>
                <button type="button" class="primary-action" data-next-step="5">Save & Continue</button>
            </div>
        </section>

        <section class="flow-step" data-step="5">
            <div class="section-heading"><h2>Invitation Settings</h2></div>
            <div class="settings-grid">
                @foreach ([
                    'music_enabled' => 'Music Enabled',
                    'rsvp_enabled' => 'RSVP Enabled',
                    'gallery_enabled' => 'Gallery Enabled',
                    'story_enabled' => 'Story Enabled',
                    'family_enabled' => 'Family Section Enabled',
                ] as $key => $label)
                    <label class="toggle-row"><input type="checkbox" name="settings[{{ $key }}]" value="1" @checked(old("settings.$key", $settings[$key] ?? false))> {{ $label }}</label>
                @endforeach
            </div>
            <div class="flow-actions split">
                <button type="button" class="secondary-action" data-prev-step="4">Back</button>
                <button type="button" class="primary-action" data-next-step="6">Save & Continue</button>
            </div>
        </section>

        <section class="flow-step" data-step="6">
            <div class="section-heading"><h2>Preview & Publish</h2></div>
            <div class="empty-state">
                Save this draft, preview the actual invitation, then publish directly. Payment is bypassed.
            </div>
            <div class="flow-actions split">
                <button type="button" class="secondary-action" data-prev-step="5">Back</button>
                <button type="submit" class="secondary-action">Save Draft</button>
                <button type="submit" class="primary-action" data-submit-action="preview">Preview Invitation</button>
            </div>
        </section>
    </form>

    <form method="POST" action="{{ route('invitations.publish', $invitation) }}" class="publish-strip">
        @csrf
        <button class="primary-action" type="submit">Publish Invitation</button>
    </form>

    <template id="ceremonyTemplate">
        @include('invitations.partials.ceremony-fields', ['index' => '__INDEX__', 'ceremony' => []])
    </template>
@endsection

@push('scripts')
<script>
    const form = document.querySelector('.builder-flow');
    const nextAction = document.getElementById('nextAction');
    const ceremonyList = document.getElementById('ceremonyList');
    const ceremonyTemplate = document.getElementById('ceremonyTemplate');
    const weddingPresets = @json($weddingSides);
    const defaultWeddingDate = @json(old('wedding_date', optional($invitation->wedding_date)->format('Y-m-d')));
    const sideChangeMessage = 'Changing the wedding side will replace the current function list. Continue?';
    let selectedWeddingSide = document.querySelector('input[name="wedding_side"]:checked')?.value || 'both';

    function showStep(step) {
        document.querySelectorAll('[data-step]').forEach(panel => panel.classList.toggle('active', panel.dataset.step === String(step)));
        document.querySelectorAll('[data-step-button]').forEach(button => button.classList.toggle('active', button.dataset.stepButton === String(step)));
    }

    document.querySelectorAll('[data-next-step]').forEach(button => button.addEventListener('click', () => showStep(button.dataset.nextStep)));
    document.querySelectorAll('[data-prev-step]').forEach(button => button.addEventListener('click', () => showStep(button.dataset.prevStep)));
    document.querySelectorAll('[data-submit-action]').forEach(button => button.addEventListener('click', () => nextAction.value = button.dataset.submitAction));

    function slugify(value) {
        return String(value || '')
            .toLowerCase()
            .trim()
            .replace(/[^a-z0-9]+/g, '-')
            .replace(/^-+|-+$/g, '');
    }

    function addCeremony(ceremony = {}) {
        const index = ceremonyList.querySelectorAll('.ceremony-card').length;
        ceremonyList.insertAdjacentHTML('beforeend', ceremonyTemplate.innerHTML.replaceAll('__INDEX__', index));

        const card = ceremonyList.lastElementChild;
        const nameInput = card.querySelector('[data-ceremony-name]');
        const slugInput = card.querySelector('[data-ceremony-slug]');
        const dateInput = card.querySelector('input[type="date"]');

        if (nameInput) nameInput.value = ceremony.name || '';
        if (slugInput) slugInput.value = ceremony.slug || slugify(ceremony.name || '');
        if (dateInput) dateInput.value = ceremony.date || defaultWeddingDate || '';

        reindexCeremonies();
        bindLocationInputs();
    }

    function reindexCeremonies() {
        ceremonyList.querySelectorAll('.ceremony-card').forEach((card, index) => {
            const prefix = `ceremony_${index}`;

            card.querySelectorAll('[name]').forEach(input => {
                input.name = input.name.replace(/ceremonies\[[^\]]+\]/, `ceremonies[${index}]`);
            });

            card.querySelectorAll('[id]').forEach(input => {
                input.id = input.id.replace(/ceremony_(?:\d+|__INDEX__)/g, prefix);
            });

            card.querySelectorAll('[data-place-prefix]').forEach(input => {
                input.dataset.placePrefix = prefix;
                delete input.dataset.locationBound;
                delete input.dataset.googleBound;
            });

            card.querySelectorAll('[data-map-preview]').forEach(preview => {
                preview.dataset.mapPreview = prefix;
            });

            card.querySelectorAll('[data-change-location]').forEach(button => {
                button.dataset.changeLocation = prefix;
            });

            const sortOrderInput = card.querySelector('[data-ceremony-sort-order]');
            if (sortOrderInput) sortOrderInput.value = index + 1;
        });
    }

    function replaceCeremoniesForSide(side) {
        ceremonyList.innerHTML = '';
        (weddingPresets[side] || []).forEach(ceremony => addCeremony(ceremony));
        reindexCeremonies();
    }

    document.getElementById('addCeremony').addEventListener('click', function () {
        addCeremony();
    });

    document.querySelectorAll('input[name="wedding_side"]').forEach(radio => {
        radio.addEventListener('change', function () {
            if (this.value === selectedWeddingSide) return;

            if (ceremonyList.querySelectorAll('.ceremony-card').length > 0 && !window.confirm(sideChangeMessage)) {
                const previous = document.querySelector(`input[name="wedding_side"][value="${selectedWeddingSide}"]`);
                if (previous) previous.checked = true;
                return;
            }

            selectedWeddingSide = this.value;
            replaceCeremoniesForSide(this.value);
        });
    });

    ceremonyList.addEventListener('input', function (event) {
        if (!event.target.matches('[data-ceremony-name]')) return;

        const card = event.target.closest('.ceremony-card');
        const slugInput = card?.querySelector('[data-ceremony-slug]');
        if (slugInput) slugInput.value = slugify(event.target.value);
    });

    ceremonyList.addEventListener('click', function (event) {
        if (event.target.matches('[data-remove-ceremony]')) {
            event.target.closest('.ceremony-card').remove();
            reindexCeremonies();
        }

        if (event.target.matches('[data-move-ceremony]')) {
            const card = event.target.closest('.ceremony-card');
            const direction = event.target.dataset.moveCeremony;

            if (direction === 'up' && card.previousElementSibling) {
                ceremonyList.insertBefore(card, card.previousElementSibling);
            }

            if (direction === 'down' && card.nextElementSibling) {
                ceremonyList.insertBefore(card.nextElementSibling, card);
            }

            reindexCeremonies();
            bindLocationInputs();
        }
    });

    form.addEventListener('submit', reindexCeremonies);

    document.addEventListener('click', function (event) {
        if (!event.target.matches('[data-change-location]')) return;

        const input = document.querySelector(`[data-place-prefix="${event.target.dataset.changeLocation}"]`);
        if (input) input.focus();
    });

    function escapeHtml(value) {
        return String(value || '').replace(/[&<>'"]/g, character => ({ '&': '&amp;', '<': '&lt;', '>': '&gt;', "'": '&#039;', '"': '&quot;' }[character]));
    }

    function buildMapsUrl(venueName, formattedAddress, placeId = '') {
        const query = [venueName, formattedAddress].filter(Boolean).join(', ').trim();
        if (!query) return '';

        const params = new URLSearchParams({ api: '1', query });
        if (placeId) params.set('query_place_id', placeId);

        return `https://www.google.com/maps/search/?${params.toString()}`;
    }

    function field(prefix, suffix) {
        return document.getElementById(`${prefix}_${suffix}`);
    }

    function setField(prefix, suffix, value) {
        const target = field(prefix, suffix);
        if (target) target.value = value || '';
    }

    function updateLocationSummary(prefix) {
        const input = document.querySelector(`[data-place-prefix="${prefix}"]`);
        const venueName = input?.value || '';
        const address = field(prefix, 'formatted_address')?.value || '';
        const mapsUrl = field(prefix, 'google_maps_url')?.value || buildMapsUrl(venueName, address, field(prefix, 'google_place_id')?.value || '');
        const preview = document.querySelector(`[data-map-preview="${prefix}"]`);
        if (!preview) return;

        preview.innerHTML = `<strong>${escapeHtml(venueName || 'Venue preview')}</strong><p>${escapeHtml(address || 'Select a Google place or enter address to show map details.')}</p>${mapsUrl ? `<a href="${escapeHtml(mapsUrl)}" target="_blank" rel="noreferrer">View on Google Maps</a><button type="button" data-change-location="${escapeHtml(prefix)}">Change Location</button>` : ''}`;
    }

    function generateFallbackMapUrl(prefix) {
        const input = document.querySelector(`[data-place-prefix="${prefix}"]`);
        const venueName = input?.value || '';
        const address = field(prefix, 'formatted_address')?.value || '';
        const placeId = field(prefix, 'google_place_id')?.value || '';
        const mapsUrl = buildMapsUrl(venueName, address, placeId);

        setField(prefix, 'google_maps_url', mapsUrl);
        updateLocationSummary(prefix);
    }

    function bindLocationInputs() {
        document.querySelectorAll('.place-input').forEach(input => {
            const prefix = input.dataset.placePrefix;

            if (!input.dataset.locationBound) {
                input.dataset.locationBound = 'true';
                input.addEventListener('blur', () => generateFallbackMapUrl(prefix));
                field(prefix, 'formatted_address')?.addEventListener('blur', () => generateFallbackMapUrl(prefix));
                updateLocationSummary(prefix);
            }

            if (input.dataset.googleBound || !window.google?.maps?.places) return;

            input.dataset.googleBound = 'true';
            const autocomplete = new google.maps.places.Autocomplete(input, { fields: ['name', 'formatted_address', 'place_id', 'geometry'] });
            autocomplete.addListener('place_changed', function () {
                const place = autocomplete.getPlace();
                const lat = place.geometry?.location?.lat();
                const lng = place.geometry?.location?.lng();
                const venueName = place.name || input.value;
                const address = place.formatted_address || '';
                const mapsUrl = buildMapsUrl(venueName, address, place.place_id || '');

                input.value = venueName;
                setField(prefix, 'formatted_address', address);
                setField(prefix, 'google_place_id', place.place_id);
                setField(prefix, 'latitude', lat);
                setField(prefix, 'longitude', lng);
                setField(prefix, 'google_maps_url', mapsUrl);
                updateLocationSummary(prefix);
            });
        });
    }

    reindexCeremonies();
    bindLocationInputs();
    window.initInvitationPlaces = bindLocationInputs;
</script>
@if ($googleMapsApiKey)
    <script async defer src="https://maps.googleapis.com/maps/api/js?key={{ $googleMapsApiKey }}&libraries=places&callback=initInvitationPlaces"></script>
@endif
@endpush
