@php
    $prefix = "ceremonies[$index]";
    $fieldId = "ceremony_{$index}";
    $venueName = $ceremony['venue_name'] ?? '';
    $formattedAddress = $ceremony['formatted_address'] ?? '';
    $placeId = $ceremony['google_place_id'] ?? '';
    $mapUrl = $ceremony['google_maps_url'] ?? '';

    if (! $mapUrl && ($venueName || $formattedAddress)) {
        $params = ['api' => '1', 'query' => trim(collect([$venueName, $formattedAddress])->filter()->implode(', '))];

        if ($placeId) {
            $params['query_place_id'] = $placeId;
        }

        $mapUrl = 'https://www.google.com/maps/search/?'.http_build_query($params, '', '&', PHP_QUERY_RFC3986);
    }
@endphp
<article class="ceremony-card">
    <input type="hidden" name="{{ $prefix }}[id]" value="{{ $ceremony['id'] ?? '' }}">
    <div class="section-heading compact">
        <h3>Function</h3>
        <button type="button" class="secondary-action" data-remove-ceremony>Remove</button>
    </div>
    <div class="form-grid">
        <label>Function Name *<input required name="{{ $prefix }}[name]" value="{{ $ceremony['name'] ?? '' }}" placeholder="Haldi, Mehendi, Wedding, Reception"></label>
        <label>Date *<input required type="date" name="{{ $prefix }}[date]" value="{{ isset($ceremony['date']) ? \Illuminate\Support\Carbon::parse($ceremony['date'])->format('Y-m-d') : '' }}"></label>
        <label>Dress Code<input name="{{ $prefix }}[dress_code]" value="{{ $ceremony['dress_code'] ?? '' }}"></label>
        <label>Image<input type="file" name="{{ $prefix }}[image]" accept="image/*"></label>
        <label class="full">Description<textarea name="{{ $prefix }}[description]" rows="3">{{ $ceremony['description'] ?? '' }}</textarea></label>
        <label>Venue<input class="place-input" name="{{ $prefix }}[venue_name]" value="{{ $venueName }}" data-place-prefix="{{ $fieldId }}" placeholder="Start typing venue..."></label>
        <label class="full">Formatted Address / selected location<textarea id="{{ $fieldId }}_formatted_address" name="{{ $prefix }}[formatted_address]" rows="2">{{ $formattedAddress }}</textarea></label>
        <input type="hidden" id="{{ $fieldId }}_google_place_id" name="{{ $prefix }}[google_place_id]" value="{{ $placeId }}">
        <input type="hidden" id="{{ $fieldId }}_latitude" name="{{ $prefix }}[latitude]" value="{{ $ceremony['latitude'] ?? '' }}">
        <input type="hidden" id="{{ $fieldId }}_longitude" name="{{ $prefix }}[longitude]" value="{{ $ceremony['longitude'] ?? '' }}">
        <input type="hidden" id="{{ $fieldId }}_google_maps_url" name="{{ $prefix }}[google_maps_url]" value="{{ $mapUrl }}">
    </div>
    <div class="map-preview small" data-map-preview="{{ $fieldId }}">
        <strong>{{ $venueName ?: 'Function venue' }}</strong>
        <p>{{ $formattedAddress ?: 'Select a Google place or enter address for this function.' }}</p>
        @if ($mapUrl)
            <a href="{{ $mapUrl }}" target="_blank" rel="noreferrer">View on Google Maps</a>
            <button type="button" data-change-location="{{ $fieldId }}">Change Location</button>
        @endif
    </div>
</article>