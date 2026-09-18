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
                <article>
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
</section>
