@php
    use Illuminate\Support\Facades\Storage;
    use Illuminate\Support\Str;

    $mediaUrl = fn (?string $path): ?string => $path ? (Str::startsWith($path, ['http://', 'https://', '//']) ? $path : Storage::url($path)) : null;
@endphp

<section class="public-invite invitation-render">
    @if ($invitation->hero_photo_path)
        <img class="public-hero" src="{{ $mediaUrl($invitation->hero_photo_path) }}" alt="{{ $invitation->bride_name }} and {{ $invitation->groom_name }}">
    @endif
    <p class="eyebrow">Wedding Invitation</p>
    <h1>{{ $invitation->bride_name ?: 'Bride' }} <span>&amp;</span> {{ $invitation->groom_name ?: 'Groom' }}</h1>
    <p class="invite-date">{{ optional($invitation->wedding_date)->format('l, d F Y') }} {{ $invitation->wedding_time ? 'at '.$invitation->wedding_time : '' }}</p>
    @if ($invitation->message)
        <p>{{ $invitation->message }}</p>
    @endif
    @if (($invitation->settings['family_enabled'] ?? true) && ($invitation->bride_father_name || $invitation->groom_father_name))
        <section><h2>With Blessings From</h2><p>{{ $invitation->bride_father_name }} {{ $invitation->bride_mother_name }}<br>{{ $invitation->groom_father_name }} {{ $invitation->groom_mother_name }}</p></section>
    @endif
    @if ($invitation->venue_name || $invitation->formatted_address)
        @php
            $mainMapsUrl = $invitation->google_maps_url;

            if (! $mainMapsUrl) {
                $query = trim(collect([$invitation->venue_name, $invitation->formatted_address])->filter()->implode(', '));
                $mainMapsUrl = $query ? 'https://www.google.com/maps/search/?'.http_build_query(['api' => '1', 'query' => $query], '', '&', PHP_QUERY_RFC3986) : null;
            }
        @endphp
        <section>
            <h2>{{ $invitation->venue_name ?: 'Venue' }}</h2>
            <p>{{ $invitation->formatted_address }}</p>
            @if ($mainMapsUrl)
                <a class="secondary-action" href="{{ $mainMapsUrl }}" target="_blank" rel="noreferrer">Get Directions</a>
            @endif
        </section>
    @endif
    @if ($invitation->ceremonies->isNotEmpty())
        <section class="public-functions">
            <h2>Wedding Functions</h2>
            @foreach ($invitation->ceremonies as $ceremony)
                @php
                    $ceremonyMapsUrl = $ceremony->google_maps_url;

                    if (! $ceremonyMapsUrl) {
                        $query = trim(collect([$ceremony->venue_name, $ceremony->formatted_address])->filter()->implode(', '));
                        $params = ['api' => '1', 'query' => $query];

                        if ($ceremony->google_place_id) {
                            $params['query_place_id'] = $ceremony->google_place_id;
                        }

                        $ceremonyMapsUrl = $query ? 'https://www.google.com/maps/search/?'.http_build_query($params, '', '&', PHP_QUERY_RFC3986) : null;
                    }
                @endphp
                <article class="event-card event-card--{{ $ceremony->slug ?: Str::slug($ceremony->name) }}">
                    <h3>{{ $ceremony->name }}</h3>
                    <p>{{ optional($ceremony->date)->format('d M Y') }}</p>
                    <p>{{ $ceremony->description }}</p>
                    @if ($ceremony->venue_name || $ceremony->formatted_address)
                        <small>{{ $ceremony->venue_name }} {{ $ceremony->formatted_address }}</small>
                        @if ($ceremonyMapsUrl)
                            <a href="{{ $ceremonyMapsUrl }}" target="_blank" rel="noreferrer">Get Directions</a>
                        @endif
                    @endif
                </article>
            @endforeach
        </section>
    @endif
    @if (($invitation->settings['gallery_enabled'] ?? true) && $invitation->galleryImages->isNotEmpty())
        <section class="public-gallery">
            <h2>Gallery</h2>
            <div>
                @foreach ($invitation->galleryImages as $image)
                    <img src="{{ $mediaUrl($image->image_path) }}" alt="Invitation gallery image">
                @endforeach
            </div>
        </section>
    @endif
    @if ($invitation->settings['rsvp_enabled'] ?? true)
        <section class="public-rsvp">
            <h2>RSVP</h2>
            @if (session('status'))
                <p class="rsvp-status">{{ session('status') }}</p>
            @endif
            <form method="POST" action="{{ route('invitations.rsvp.store', $invitation->slug) }}">
                @csrf
                <label>
                    Guest Name
                    <input type="text" name="guest_name" value="{{ old('guest_name') }}" required maxlength="255">
                    @error('guest_name')
                        <small>{{ $message }}</small>
                    @enderror
                </label>
                <fieldset>
                    <legend>Will you attend?</legend>
                    <label class="rsvp-option">
                        <input type="radio" name="attending" value="1" @checked(old('attending', '1') === '1')>
                        Yes
                    </label>
                    <label class="rsvp-option">
                        <input type="radio" name="attending" value="0" @checked(old('attending') === '0')>
                        No
                    </label>
                    @error('attending')
                        <small>{{ $message }}</small>
                    @enderror
                </fieldset>
                @if ($invitation->ceremonies->isNotEmpty())
                    <fieldset data-rsvp-functions>
                        <legend>Functions Attending</legend>
                        @foreach ($invitation->ceremonies as $ceremony)
                            <label class="rsvp-option">
                                <input type="checkbox" name="functions_attending[]" value="{{ $ceremony->id }}" @checked(in_array((string) $ceremony->id, (array) old('functions_attending', []), true))>
                                {{ $ceremony->name }}
                            </label>
                        @endforeach
                        @error('functions_attending')
                            <small>{{ $message }}</small>
                        @enderror
                        @error('functions_attending.*')
                            <small>{{ $message }}</small>
                        @enderror
                    </fieldset>
                @endif
                <label>
                    Message/Blessings
                    <textarea name="message" rows="4" maxlength="2000">{{ old('message') }}</textarea>
                    @error('message')
                        <small>{{ $message }}</small>
                    @enderror
                </label>
                <button type="submit">Send RSVP</button>
            </form>
        </section>
    @endif
</section>

<script>
    document.querySelectorAll('.public-rsvp').forEach(function (section) {
        const functions = section.querySelector('[data-rsvp-functions]');
        const attendingInputs = section.querySelectorAll('input[name="attending"]');

        function syncFunctions() {
            const isAttending = section.querySelector('input[name="attending"]:checked')?.value !== '0';

            if (! functions) {
                return;
            }

            functions.hidden = ! isAttending;
            functions.querySelectorAll('input').forEach(function (input) {
                input.disabled = ! isAttending;
            });
        }

        attendingInputs.forEach(function (input) {
            input.addEventListener('change', syncFunctions);
        });

        syncFunctions();
    });
</script>
