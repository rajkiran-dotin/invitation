@php
    use Illuminate\Support\Carbon;
    use Illuminate\Support\Facades\Storage;
    use Illuminate\Support\Js;

    $assetBase = 'assets/royal_saffron_vows';
    $storageUrl = fn (?string $path): ?string => $path ? Storage::url($path) : null;
    $settings = array_merge([
        'music_enabled' => false,
        'rsvp_enabled' => true,
        'gallery_enabled' => true,
        'story_enabled' => true,
        'family_enabled' => true,
    ], $invitation->settings ?? []);

    $weddingDateTime = null;

    if ($invitation->wedding_date) {
        $dateValue = $invitation->wedding_date->format('Y-m-d');
        $timeValue = $invitation->wedding_time ?: '00:00';
        $weddingDateTime = Carbon::parse($dateValue.' '.$timeValue)->toIso8601String();
    }

    $familyGroups = [
        'brideSide' => [
            'title' => "Bride's Family",
            'members' => collect([
                ['name' => $invitation->bride_father_name, 'relation' => 'Father of the Bride'],
                ['name' => $invitation->bride_mother_name, 'relation' => 'Mother of the Bride'],
            ])->filter(fn (array $member): bool => filled($member['name']))->values()->all(),
        ],
        'groomSide' => [
            'title' => "Groom's Family",
            'members' => collect([
                ['name' => $invitation->groom_father_name, 'relation' => 'Father of the Groom'],
                ['name' => $invitation->groom_mother_name, 'relation' => 'Mother of the Groom'],
            ])->filter(fn (array $member): bool => filled($member['name']))->values()->all(),
        ],
    ];

    $ceremonies = $invitation->ceremonies->map(function ($ceremony) use ($storageUrl): array {
        return [
            'title' => $ceremony->name,
            'dateTime' => collect([
                optional($ceremony->date)->format('d F Y'),
                $ceremony->start_time,
            ])->filter()->implode(', '),
            'venue' => collect([$ceremony->venue_name, $ceremony->formatted_address])->filter()->implode(', '),
            'description' => $ceremony->description ?: $ceremony->dress_code,
            'image' => $storageUrl($ceremony->image_path) ?: 'https://images.unsplash.com/photo-1623039405147-547794f92e9e?auto=format&fit=crop&w=1000&q=80',
            'imageAlt' => $ceremony->name.' ceremony',
        ];
    })->values()->all();

    $gallery = $invitation->galleryImages->map(fn ($image): array => [
        'src' => $storageUrl($image->image_path),
        'alt' => $image->caption ?: 'Invitation gallery image',
    ])->filter(fn (array $image): bool => filled($image['src']))->values()->all();

    $mapQuery = trim(collect([$invitation->venue_name, $invitation->formatted_address])->filter()->implode(', '));
    $mapUrl = $invitation->google_maps_url ?: ($mapQuery ? 'https://www.google.com/maps/search/?'.http_build_query(['api' => '1', 'query' => $mapQuery], '', '&', PHP_QUERY_RFC3986) : '#');
    $embedMapUrl = $mapQuery ? 'https://www.google.com/maps?q='.rawurlencode($mapQuery).'&output=embed' : 'about:blank';

    $weddingData = [
        'sectionVisibility' => [
            'hero' => true,
            'couple' => true,
            'story' => (bool) $settings['story_enabled'],
            'family' => (bool) $settings['family_enabled'],
            'ceremonies' => $invitation->ceremonies->isNotEmpty(),
            'gallery' => (bool) $settings['gallery_enabled'] && $invitation->galleryImages->isNotEmpty(),
            'countdown' => filled($weddingDateTime),
            'venue' => filled($invitation->venue_name) || filled($invitation->formatted_address),
            'rsvp' => (bool) $settings['rsvp_enabled'],
            'footer' => true,
        ],
        'bride' => [
            'name' => $invitation->bride_name ?: 'Bride',
            'initial' => mb_substr($invitation->bride_name ?: 'B', 0, 1),
            'photo' => $storageUrl($invitation->bride_photo_path) ?: 'https://images.unsplash.com/photo-1583391733981-849840c5d628?auto=format&fit=crop&w=900&q=80',
            'photoAlt' => ($invitation->bride_name ?: 'Bride').' portrait',
            'bio' => 'With love and blessings from family and friends.',
        ],
        'groom' => [
            'name' => $invitation->groom_name ?: 'Groom',
            'initial' => mb_substr($invitation->groom_name ?: 'G', 0, 1),
            'photo' => $storageUrl($invitation->groom_photo_path) ?: 'https://images.unsplash.com/photo-1610173827043-62b52d7c2300?auto=format&fit=crop&w=900&q=80',
            'photoAlt' => ($invitation->groom_name ?: 'Groom').' portrait',
            'bio' => 'Together, beginning a beautiful new chapter.',
        ],
        'wedding' => [
            'templateName' => 'InviteCraft',
            'invitationLabel' => 'Wedding Invitation',
            'openingMessage' => 'invite you to celebrate their wedding',
            'date' => $weddingDateTime,
            'displayDate' => $invitation->wedding_date ? $invitation->wedding_date->format('d F Y').($invitation->wedding_time ? ' at '.$invitation->wedding_time : '') : 'Wedding date',
            'message' => $invitation->message ?: 'Together with their families, they invite you to an evening of blessings, rituals, music, and love.',
            'storyTitle' => 'A story written in little moments',
            'story' => $invitation->message ?: 'Every celebration begins with love, family, and the joy of coming together.',
            'heroImage' => $storageUrl($invitation->hero_photo_path) ?: ($storageUrl($invitation->bride_photo_path) ?: 'https://images.unsplash.com/photo-1587271636175-90d58cdad458?auto=format&fit=crop&w=1800&q=80'),
            'storyImage' => $storageUrl($invitation->hero_photo_path) ?: 'https://images.unsplash.com/photo-1606800052052-a08af7148866?auto=format&fit=crop&w=1800&q=80',
            'footerText' => 'Made with love for their wedding celebration',
            'backgroundMusic' => $settings['music_enabled'] ? asset($assetBase.'/audio/traditional-invitation-wedding-invitation-rajasthani-einvite.mp3') : null,
        ],
        'storyMilestones' => [
            ['title' => 'The Beginning', 'date' => 'With love', 'description' => 'A beautiful bond grew into a promise for life.'],
            ['title' => 'Family Blessings', 'date' => 'Together', 'description' => 'Two families come together to bless this celebration.'],
            ['title' => 'The Wedding', 'date' => $invitation->wedding_date ? $invitation->wedding_date->format('d M Y') : 'Soon', 'description' => 'A new journey begins with sacred vows and joyful hearts.'],
        ],
        'ceremonies' => $ceremonies,
        'family' => $familyGroups,
        'gallery' => $gallery,
        'venue' => [
            'name' => $invitation->venue_name ?: 'Venue',
            'city' => $invitation->formatted_address,
            'address' => $invitation->formatted_address ?: $invitation->venue_name,
            'mapUrl' => $mapUrl,
            'embedMapUrl' => $embedMapUrl,
        ],
        'contact' => [
            'name' => 'Event Desk',
            'phone' => '',
            'phoneHref' => '#',
        ],
    ];
@endphp

<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Cormorant+Garamond:wght@500;600;700&family=Inter:wght@400;500;600;700&family=Marcellus&display=swap" rel="stylesheet">
<link rel="stylesheet" href="https://unpkg.com/aos@2.3.4/dist/aos.css">
<link rel="stylesheet" href="{{ asset($assetBase.'/css/style.css') }}">

<a class="skip-link" href="#main-content">Skip to content</a>
<div id="petal-layer" aria-hidden="true"></div>

<div class="opening-screen" data-opening-screen role="dialog" aria-modal="true" aria-labelledby="opening-title">
    <div class="opening-door opening-door-left" aria-hidden="true"></div>
    <div class="opening-door opening-door-right" aria-hidden="true"></div>
    <div class="opening-card">
        <p class="eyebrow">Together with our families</p>
        <h2 id="opening-title"><span data-bind="bride.name">Bride</span><span>&amp;</span><span data-bind="groom.name">Groom</span></h2>
        <p data-bind="wedding.openingMessage">invite you to celebrate their wedding</p>
        <button class="button-primary open-invitation-button" type="button" data-open-invitation>Open Invitation</button>
    </div>
</div>

<button class="floating-music-control" type="button" data-floating-music-toggle aria-pressed="false" hidden>
    <span class="music-equalizer" aria-hidden="true"><i></i><i></i><i></i></span>
    <span data-floating-music-label>Play Music</span>
</button>

<header class="site-header" data-component="site-header">
    <nav class="nav-shell" aria-label="Wedding invitation navigation">
        <button class="nav-toggle" type="button" aria-expanded="false" aria-controls="primary-menu">
            <span class="nav-toggle-bars" aria-hidden="true"></span>
            <span class="sr-only">Open navigation</span>
        </button>
        <ul id="primary-menu" class="nav-menu">
            <li><a href="#couple">Couple</a></li>
            <li><a href="#story">Story</a></li>
            <li><a href="#ceremonies">Ceremonies</a></li>
            <li><a href="#gallery">Gallery</a></li>
            <li><a href="#venue">Venue</a></li>
        </ul>
    </nav>
</header>

<main id="main-content">
    <section id="hero" class="hero-section" data-section="hero" aria-labelledby="hero-title">
        <canvas id="mandala-canvas" class="mandala-canvas" aria-hidden="true"></canvas>
        <div id="particles-js" class="particle-layer" aria-hidden="true"></div>
        <div class="hero-media" data-dynamic-bg="wedding.heroImage"></div>
        <div class="hero-overlay"></div>
        <div class="hero-floral-mark" aria-hidden="true"></div>
        <div class="hero-content">
            <p class="eyebrow hero-kicker" data-bind="wedding.invitationLabel">Wedding Invitation</p>
            <h1 id="hero-title"><span data-bind="bride.name">Bride</span><span class="ampersand" aria-hidden="true">&amp;</span><span data-bind="groom.name">Groom</span></h1>
            <p class="hero-message" data-bind="wedding.message">Together with their families, they invite you to celebrate their wedding.</p>
        </div>
    </section>
    <div class="section-divider" aria-hidden="true"><span></span></div>

    <section id="couple" class="couple-section patterned-section" data-section="couple" aria-labelledby="couple-title">
        <div class="section-heading" data-aos="fade-up"><p class="eyebrow">The Couple</p><h2 id="couple-title">Two hearts, one celebration</h2></div>
        <div class="couple-layout">
            <article class="person-profile" data-person-card="bride" data-aos="fade-right">
                <img data-bind-src="bride.photo" data-bind-alt="bride.photoAlt" src="" alt="">
                <div><p class="profile-role">Bride</p><h3 data-bind="bride.name">Bride name</h3><p data-bind="bride.bio">Bride introduction</p></div>
            </article>
            <div class="couple-monogram" aria-hidden="true"><span data-bind="bride.initial">B</span><span>&amp;</span><span data-bind="groom.initial">G</span></div>
            <article class="person-profile" data-person-card="groom" data-aos="fade-left">
                <img data-bind-src="groom.photo" data-bind-alt="groom.photoAlt" src="" alt="">
                <div><p class="profile-role">Groom</p><h3 data-bind="groom.name">Groom name</h3><p data-bind="groom.bio">Groom introduction</p></div>
            </article>
        </div>
    </section>
    <div class="section-divider" aria-hidden="true"><span></span></div>

    <section id="countdown" class="countdown-section patterned-section" data-section="countdown" aria-labelledby="countdown-title">
        <div class="section-heading" data-aos="fade-up"><p class="eyebrow">Until The Wedding</p><h2 id="countdown-title">Counting every heartbeat</h2></div>
        <div class="scratch-wrapper" data-countdown-scratch-wrapper>
            <div class="countdown-burst-layer" data-countdown-burst-layer aria-hidden="true"></div>
            <canvas id="countdownScratch" class="countdown-scratch-canvas" data-countdown-scratch aria-label="Scratch to reveal the countdown"></canvas>
            <div class="countdown-content" data-countdown-content>
                <p class="countdown-date" data-bind="wedding.displayDate">Wedding date</p>
                <div class="countdown-grid" data-countdown aria-live="polite">
                    <div><strong data-countdown-days>00</strong><span>Days</span></div>
                    <div><strong data-countdown-hours>00</strong><span>Hours</span></div>
                    <div><strong data-countdown-minutes>00</strong><span>Minutes</span></div>
                    <div><strong data-countdown-seconds>00</strong><span>Seconds</span></div>
                </div>
            </div>
        </div>
    </section>
    <div class="section-divider" aria-hidden="true"><span></span></div>

    <section id="story" class="story-section" data-section="story" aria-labelledby="story-title">
        <div class="story-copy" data-aos="fade-up"><p class="eyebrow">Our Story</p><h2 id="story-title" data-bind="wedding.storyTitle">A story written in little moments</h2><p data-bind="wedding.story">Couple story placeholder.</p></div>
        <div class="story-timeline" data-story-timeline><div class="story-line" data-story-line></div><div class="story-milestones" data-story-milestones></div></div>
        <div class="decorative-divider" aria-hidden="true"></div>
    </section>
    <div class="section-divider" aria-hidden="true"><span></span></div>

    <section id="family" class="family-section patterned-section" data-section="family" aria-labelledby="family-title">
        <div class="section-heading" data-aos="fade-up"><p class="eyebrow">With Blessings From</p><h2 id="family-title">Families</h2></div>
        <div class="family-columns" data-family-list></div>
    </section>
    <div class="section-divider" aria-hidden="true"><span></span></div>

    <section id="ceremonies" class="ceremony-section" data-section="ceremonies" aria-labelledby="ceremonies-title">
        <div class="section-heading" data-aos="fade-up"><p class="eyebrow">Wedding Events</p><h2 id="ceremonies-title">Ceremonies</h2></div>
        <div class="ceremony-list" data-ceremony-list></div>
    </section>
    <div class="section-divider" aria-hidden="true"><span></span></div>

    <section id="gallery" class="gallery-section" data-section="gallery" aria-labelledby="gallery-title">
        <div class="section-heading" data-aos="fade-up"><p class="eyebrow">Moments</p><h2 id="gallery-title">Gallery</h2></div>
        <div class="gallery-grid" data-gallery-list></div>
    </section>
    <div class="section-divider" aria-hidden="true"><span></span></div>

    <section id="venue" class="venue-section" data-section="venue" aria-labelledby="venue-title">
        <div class="venue-content" data-aos="fade-right">
            <p class="eyebrow">Venue</p>
            <h2 id="venue-title" data-bind="venue.name">Venue name</h2>
            <p data-bind="venue.address">Venue address</p>
            <p class="contact-line"><span data-bind="contact.name">Contact person</span><a data-bind="contact.phone" data-bind-href="contact.phoneHref" href="#">Phone</a></p>
            <a class="button-secondary" data-bind-href="venue.mapUrl" href="#" target="_blank" rel="noopener">Open map</a>
        </div>
        <div class="map-frame" data-map-container data-aos="fade-left">
            <iframe title="Wedding venue map" data-bind-src="venue.embedMapUrl" src="about:blank" loading="lazy" referrerpolicy="no-referrer-when-downgrade"></iframe>
        </div>
    </section>
    <div class="section-divider" aria-hidden="true"><span></span></div>

    <section id="rsvp" class="rsvp-section patterned-section" data-section="rsvp" aria-labelledby="rsvp-title">
        <div class="section-heading" data-aos="fade-up"><p class="eyebrow">RSVP</p><h2 id="rsvp-title">Bless us with your presence</h2></div>
        <form class="rsvp-form" data-rsvp-form novalidate>
            <div class="form-row"><label for="guest-name">Full name</label><input id="guest-name" name="name" type="text" autocomplete="name" required></div>
            <div class="form-row"><label for="guest-phone">Phone number</label><input id="guest-phone" name="phone" type="tel" autocomplete="tel" required></div>
            <div class="form-row"><label for="guest-count">Guests attending</label><input id="guest-count" name="guestCount" type="number" min="1" max="10" value="1" required></div>
            <div class="form-row"><label for="guest-attendance">Will you attend?</label><select id="guest-attendance" name="attendance" required><option value="">Select</option><option value="yes">Joyfully attending</option><option value="no">Unable to attend</option></select></div>
            <div class="form-row form-row-full"><label for="guest-message">Message</label><textarea id="guest-message" name="message" rows="4"></textarea></div>
            <button class="button-primary" type="submit">Submit RSVP</button>
            <p class="form-feedback" data-rsvp-feedback role="status" aria-live="polite"></p>
        </form>
    </section>
</main>

<footer class="site-footer" data-section="footer">
    <p><span data-bind="bride.name">Bride</span> <span aria-hidden="true">&amp;</span> <span data-bind="groom.name">Groom</span></p>
    <small data-bind="wedding.footerText">Made with love</small>
</footer>

<div class="gallery-lightbox" data-gallery-lightbox hidden aria-modal="true" role="dialog" aria-label="Gallery image preview">
    <button class="lightbox-close" type="button" data-lightbox-close aria-label="Close gallery preview">x</button>
    <button class="lightbox-nav lightbox-prev" type="button" data-lightbox-prev aria-label="Previous image">&lt;</button>
    <img data-lightbox-image src="" alt="">
    <button class="lightbox-nav lightbox-next" type="button" data-lightbox-next aria-label="Next image">&gt;</button>
</div>

<audio id="weddingMusic" loop preload="metadata">
    <source src="{{ asset($assetBase.'/audio/traditional-invitation-wedding-invitation-rajasthani-einvite.mp3') }}" type="audio/mpeg">
</audio>

<script>
    window.InviteCraftWeddingData = {{ Js::from($weddingData) }};
</script>
<script src="https://unpkg.com/aos@2.3.4/dist/aos.js" defer></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/gsap/3.12.5/gsap.min.js" defer></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/gsap/3.12.5/ScrollTrigger.min.js" defer></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/three.js/r128/three.min.js" defer></script>
<script src="https://cdn.jsdelivr.net/npm/particles.js@2.0.0/particles.min.js" defer></script>
<script src="{{ asset($assetBase.'/js/main.js') }}" defer></script>
