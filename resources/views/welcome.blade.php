@php
    $events = [
        [
            'icon' => 'rings',
            'title' => 'Wedding',
            'copy' => 'Make your special day even more memorable',
            'tone' => 'rose',
        ],
        ['icon' => 'gem', 'title' => 'Engagement', 'copy' => 'Celebrate your beautiful beginning', 'tone' => 'violet'],
        ['icon' => 'cake', 'title' => 'Birthday', 'copy' => 'Create magical birthday invitations', 'tone' => 'amber'],
        [
            'icon' => 'baby',
            'title' => 'Mundan Ceremony',
            'copy' => 'Traditional ceremonies made digital',
            'tone' => 'mint',
        ],
        [
            'icon' => 'home',
            'title' => 'Griha Pravesh',
            'copy' => 'Welcome your new home with blessings',
            'tone' => 'sky',
        ],
    ];

    $steps = [
        ['icon' => 'calendar', 'title' => 'Select Event', 'copy' => 'Choose the type of event you are inviting for'],
        ['icon' => 'layout', 'title' => 'Choose Template', 'copy' => 'Pick a beautiful template you love'],
        ['icon' => 'edit', 'title' => 'Customize Details', 'copy' => 'Add event details, photos, schedule and more'],
        ['icon' => 'card', 'title' => 'Make Payment', 'copy' => 'Secure payment through multiple options'],
        ['icon' => 'send', 'title' => 'Share & Celebrate', 'copy' => 'Get your unique link and share instantly'],
    ];

    $templates = [
        ['title' => 'Royal Wedding', 'class' => 'royal'],
        ['title' => 'Floral Bliss', 'class' => 'floral'],
        ['title' => 'Classic Elegance', 'class' => 'classic'],
        ['title' => 'Minimal Love', 'class' => 'minimal'],
        ['title' => 'Modern Chic', 'class' => 'modern'],
        ['title' => 'Pastel Dream', 'class' => 'pastel'],
    ];

    $plans = [
        [
            'name' => 'Basic',
            'price' => '499',
            'features' => [
                '1 Invitation Website',
                'Premium Templates',
                'Basic Customization',
                'Gallery (Up to 20 Photos)',
                'QR Code & WhatsApp Share',
            ],
        ],
        [
            'name' => 'Pro',
            'price' => '999',
            'popular' => true,
            'features' => [
                '1 Invitation Website',
                'Premium Templates',
                'Advanced Customization',
                'Gallery (Up to 100 Photos)',
                'Custom Domain',
                'Remove InviteCraft Branding',
                'QR Code & WhatsApp Share',
                'Priority Support',
            ],
        ],
        [
            'name' => 'Business',
            'price' => '1999',
            'features' => [
                '5 Invitation Websites',
                'Premium Templates',
                'Advanced Customization',
                'Unlimited Photos',
                'Custom Domain',
                'Remove Branding',
                'Priority Support',
            ],
        ],
    ];

    if (isset($dbTemplates) && $dbTemplates->isNotEmpty()) {
        $templates = $dbTemplates
            ->map(fn($template) => ['title' => $template->name, 'class' => $template->theme_class])
            ->all();
    }

    if (isset($dbPlans) && $dbPlans->isNotEmpty()) {
        $plans = $dbPlans
            ->map(
                fn($plan) => [
                    'name' => $plan->name,
                    'price' => number_format((float) $plan->price, 0),
                    'popular' => $plan->is_popular,
                    'features' => $plan->features ?? [],
                ],
            )
            ->all();
    }

    $testimonials =
        isset($dbTestimonials) && $dbTestimonials->isNotEmpty()
            ? $dbTestimonials
                ->map(
                    fn($testimonial) => [
                        'name' => $testimonial->customer_name,
                        'message' => $testimonial->message,
                        'rating' => $testimonial->rating,
                    ],
                )
                ->all()
            : [
                [
                    'name' => 'Neha & Rohit',
                    'message' =>
                        'InviteCraft made our wedding invitations so beautiful and easy to share. Everyone loved the design!',
                    'rating' => 5,
                ],
                [
                    'name' => 'Anjali Sharma',
                    'message' => 'The templates are stunning and the platform is super easy to use. Worth every penny!',
                    'rating' => 5,
                ],
                [
                    'name' => 'Vikram Patel',
                    'message' => 'We created our Griha Pravesh invitation in just 10 minutes. Amazing experience!',
                    'rating' => 5,
                ],
            ];

    $faqs =
        isset($dbFaqs) && $dbFaqs->isNotEmpty()
            ? $dbFaqs->map(fn($faq) => ['question' => $faq->question, 'answer' => $faq->answer])->all()
            : [
                [
                    'question' => 'How long does it take to create an invitation?',
                    'answer' => 'Most customers finish their invitation website in less than 10 minutes.',
                ],
                [
                    'question' => 'Can I share my invitation on WhatsApp?',
                    'answer' => 'Yes, every plan includes a shareable link and QR code.',
                ],
                [
                    'question' => 'Do you provide custom domain?',
                    'answer' => 'Custom domains are available with Pro and Business plans.',
                ],
            ];

    $icons = [
        'rings' =>
            '<path d="M10.2 8.8a4.9 4.9 0 1 0 4.9 4.9 4.9 4.9 0 0 0-4.9-4.9Zm7.6 0a4.9 4.9 0 1 0 4.9 4.9 4.9 4.9 0 0 0-4.9-4.9ZM14 9.4l2-3h-4l2 3Zm3.8 0 2-3h-4l2 3Z"/>',
        'gem' => '<path d="m12 3 7 5-7 13L5 8l7-5Zm-7 5h14M8.4 8 12 21 15.6 8"/>',
        'cake' => '<path d="M6 12h12v8H6v-8Zm0 4h12M8 8h8v4H8V8Zm4-5v5"/>',
        'baby' => '<path d="M12 13a4 4 0 1 0 0-8 4 4 0 0 0 0 8Zm-6 8a6 6 0 0 1 12 0M9.2 11.7 7 14m7.8-2.3L17 14"/>',
        'home' => '<path d="M4 11 12 4l8 7v9H6v-9Zm5 9v-6h6v6"/>',
        'calendar' => '<path d="M7 3v4m10-4v4M5 8h14M5 5h14v16H5V5Zm4 7h2m2 0h2m-6 4h2m2 0h2"/>',
        'layout' => '<path d="M4 5h16v14H4V5Zm0 5h16M10 10v9"/>',
        'edit' => '<path d="M4 20h4L19 9l-4-4L4 16v4Zm11-15 4 4"/>',
        'card' => '<path d="M4 6h16v12H4V6Zm0 4h16m3 4h5"/>',
        'send' => '<path d="m4 4 17 8-17 8 3-8-3-8Zm3 8h8"/>',
        'heart' => '<path d="M12 20s-7-4.5-7-10a4 4 0 0 1 7-2 4 4 0 0 1 7 2c0 5.5-7 10-7 10Z"/>',
        'arrow' => '<path d="M5 12h14m-5-5 5 5-5 5"/>',
        'spark' => '<path d="M12 3 9.8 9.8 3 12l6.8 2.2L12 21l2.2-6.8L21 12l-6.8-2.2L12 3Z"/>',
    ];
@endphp

<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>InviteCraft - Digital Invitation Websites</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body>
    <div class="site-shell">
        <header class="topbar" id="home">
            <a href="#home" class="brand" aria-label="InviteCraft home">
                <span class="brand-mark">
                    <svg viewBox="0 0 24 24" aria-hidden="true">
                        <path d="M4 8.5 12 3l8 5.5v11H4v-11Zm0 0 8 6 8-6M9 12l-5 7.5m11-7.5 5 7.5" />
                        <path d="m12 8 1 1.9 2.1.3-1.5 1.5.4 2.1-2-1-2 1 .4-2.1-1.5-1.5 2.1-.3L12 8Z" />
                    </svg>
                </span>
                <span><strong>InviteCraft</strong><small>Make Every Moment Memorable</small></span>
            </a>

            <nav class="nav-links" aria-label="Primary navigation">
                <a href="#home" class="active">Home</a>
                <a href="#templates">Templates</a>
                <a href="#pricing">Pricing</a>
                <a href="#process">How It Works</a>
                <a href="#features">Features</a>
                <a href="#blog">Blog</a>
                <a href="#contact">Contact</a>
            </nav>

            <div class="nav-actions">
                @guest
                    <a class="btn btn-light" href="{{ route('login') }}">Login</a>
                    <a class="btn btn-primary" href="#pricing">Get Started</a>
                @endguest
                @auth
                    <a class="btn btn-light" href="{{ route('dashboard') }}">Hi, {{ Auth::user()->name }}</a>
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button class="btn btn-primary" type="submit">Logout</button>
                    </form>
                @endauth
            </div>
        </header>

        <main>
            <section class="hero">
                <span class="petal petal-one"></span>
                <span class="petal petal-two"></span>
                <span class="petal petal-three"></span>

                <div class="hero-copy">
                    <p class="eyebrow">
                        <svg viewBox="0 0 24 24" aria-hidden="true">{!! $icons['heart'] !!}</svg>
                        Digital invitations, made beautiful
                    </p>
                    <h1>Create Beautiful <span>Digital Invitations</span> For Every Celebration</h1>
                    <p class="hero-text">Design stunning invitation websites in minutes. Share instantly with your loved
                        ones via link, WhatsApp, QR Code and more.</p>

                    <div class="feature-strip" id="features">
                        <span><svg viewBox="0 0 24 24">{!! $icons['spark'] !!}</svg><strong>500+</strong> Premium
                            Templates</span>
                        <span><svg viewBox="0 0 24 24">{!! $icons['edit'] !!}</svg><strong>Easy</strong> to
                            Customize</span>
                        <span><svg viewBox="0 0 24 24">{!! $icons['send'] !!}</svg><strong>Instant</strong>
                            Sharing</span>
                        <span><svg viewBox="0 0 24 24">{!! $icons['card'] !!}</svg><strong>Secure</strong>
                            Payments</span>
                    </div>

                    <div class="hero-actions">
                        <a class="btn btn-primary btn-lg" href="#pricing">Create Invitation <svg
                                viewBox="0 0 24 24">{!! $icons['arrow'] !!}</svg></a>
                        <a class="btn btn-light btn-lg" href="#templates">Explore Templates <svg
                                viewBox="0 0 24 24">{!! $icons['spark'] !!}</svg></a>
                    </div>
                </div>

                <div class="hero-preview" aria-label="Invitation preview">
                    <article class="invite-card main-card">
                        <div class="floral-corner top-left"></div>
                        <div class="floral-corner bottom-right"></div>
                        <p>Together<br>with their families</p>
                        <h2>Rahul<br><span>&</span><br>Priya</h2>
                        <p>Invite you to celebrate<br>their wedding</p>
                        <div class="date-row"><b>25</b><b>DEC</b><b>2036</b></div>
                        <small>The Grand Palace, Jaipur, Rajasthan</small>
                        <button>RSVP</button>
                    </article>

                    <article class="phone-card">
                        <div class="phone-notch"></div>
                        <div class="phone-screen">
                            <div class="phone-brand">InviteCraft</div>
                            <h3>Rahul<br>&<br>Priya</h3>
                            <div class="date-row compact"><b>25</b><b>DEC</b><b>2036</b></div>
                            <button>RSVP</button>
                            <p>Our Story</p>
                            <div class="couple-photo"></div>
                        </div>
                    </article>
                </div>
            </section>

            <section class="trusted">
                <p>Trusted by 10,000+ happy customers</p>
                <div>
                    <span>Google</span><span>Paytm</span><span>PhonePe</span><span>Razorpay</span><span>AWS</span><span>Cloudflare</span>
                </div>
            </section>

            <section class="section events">
                <p class="section-kicker">Perfect for every occasion</p>
                <h2>Choose Your Event</h2>
                <div class="event-grid">
                    @foreach ($events as $event)
                        <article class="event-card {{ $event['tone'] }}">
                            <span><svg viewBox="0 0 24 24" aria-hidden="true">{!! $icons[$event['icon']] !!}</svg></span>
                            <h3>{{ $event['title'] }}</h3>
                            <p>{{ $event['copy'] }}</p>
                        </article>
                    @endforeach
                </div>
            </section>

            <section class="section process" id="process">
                <p class="section-kicker">Simple process</p>
                <h2>How InviteCraft Works</h2>
                <div class="process-grid">
                    @foreach ($steps as $index => $step)
                        <article class="process-step">
                            <span class="step-icon"><svg viewBox="0 0 24 24"
                                    aria-hidden="true">{!! $icons[$step['icon']] !!}</svg></span>
                            <b>{{ $index + 1 }}</b>
                            <h3>{{ $step['title'] }}</h3>
                            <p>{{ $step['copy'] }}</p>
                        </article>
                    @endforeach
                </div>
            </section>

            <section class="section templates" id="templates">
                <div class="section-head">
                    <div>
                        <p class="section-kicker">Stunning templates</p>
                        <h2>Beautiful Templates for You</h2>
                    </div>
                    <a class="btn btn-outline" href="{{ route('templates.index') }}">View All Templates</a>
                </div>
                <div class="template-grid">
                    @foreach ($templates as $template)
                        <article>
                            <div class="template-preview {{ $template['class'] }}">
                                <small>Together forever</small>
                                <h3>Rahul<br>&<br>Priya</h3>
                                <span>25 DEC 2036</span>
                            </div>
                            <h4>{{ $template['title'] }}</h4>
                        </article>
                    @endforeach
                </div>
            </section>

            <section class="section pricing" id="pricing">
                <p class="section-kicker">Affordable pricing</p>
                <h2>Choose the Perfect Plan</h2>
                <div class="pricing-grid">
                    @foreach ($plans as $plan)
                        <article class="price-card {{ !empty($plan['popular']) ? 'popular' : '' }}">
                            @if (!empty($plan['popular']))
                                <span class="badge">Most Popular</span>
                            @endif
                            <h3>{{ $plan['name'] }}</h3>
                            <p class="price">â‚¹ {{ $plan['price'] }} <small>/one time</small></p>
                            <ul>
                                @foreach ($plan['features'] as $feature)
                                    <li>{{ $feature }}</li>
                                @endforeach
                            </ul>
                            <a class="btn {{ !empty($plan['popular']) ? 'btn-primary' : 'btn-outline' }}"
                                href="#contact">Get Started <svg viewBox="0 0 24 24">{!! $icons['arrow'] !!}</svg></a>
                        </article>
                    @endforeach
                </div>
            </section>

            <section class="section testimonials">
                <p class="section-kicker">Happy customers</p>
                <h2>Loved by Thousands of Families</h2>
                <div class="testimonial-grid">
                    @foreach ($testimonials as $testimonial)
                        <article><span>{{ str_repeat('â˜…', (int) $testimonial['rating']) }}</span>
                            <p>{{ $testimonial['message'] }}</p><b>- {{ $testimonial['name'] }}</b>
                        </article>
                    @endforeach
                </div>
                <div class="dots"><span></span><span></span><span></span></div>
            </section>

            <section class="faq section" id="blog">
                <div class="faq-list">
                    <p class="section-kicker">FAQ</p>
                    <h2>Frequently Asked Questions</h2>
                    @foreach ($faqs as $index => $faq)
                        <details @if ($index === 0) open @endif>
                            <summary>{{ $faq['question'] }}</summary>
                            <p>{{ $faq['answer'] }}</p>
                        </details>
                    @endforeach
                </div>
                <div class="faq-art" aria-hidden="true">
                    <span>?</span>
                    <i></i>
                    <b></b>
                </div>
            </section>
        </main>

        <footer class="footer" id="footer">
            <div class="footer-brand">
                <a href="#home" class="brand">
                    <span class="brand-mark"><svg viewBox="0 0 24 24" aria-hidden="true">
                            <path d="M4 8.5 12 3l8 5.5v11H4v-11Zm0 0 8 6 8-6" />
                        </svg></span>
                    <span><strong>InviteCraft</strong><small>Make Every Moment Memorable</small></span>
                </a>
                <p>The easiest way to create, customize and share beautiful digital invitations for every special
                    occasion.</p>
                <div class="socials"><a href="#">f</a><a href="#">ig</a><a href="#">wa</a><a
                        href="#">in</a></div>
            </div>
            <div>
                <h3>Quick Links</h3>
                <a href="#home">Home</a><a href="#templates">Templates</a><a href="#pricing">Pricing</a><a
                    href="#process">How It Works</a><a href="#features">Features</a><a href="#contact">Contact Us</a>
            </div>
            <div>
                <h3>Resources</h3>
                <a href="#blog">Blog</a><a href="#contact">Help Center</a><a href="#blog">FAQ</a><a
                    href="#contact">Privacy Policy</a><a href="#contact">Terms & Conditions</a>
            </div>
            <div>
                <h3>Support</h3>
                <p>+91 12345 67890</p>
                <p>support@invitecraft.com</p>
                <p>Mon - Sat: 9:00 AM - 7:00 PM</p>
            </div>
            <form>
                <h3>Newsletter</h3>
                <p>Subscribe to get updates and exclusive offers</p>
                <label>
                    <span>Email address</span>
                    <input type="email" placeholder="Enter your email">
                    <button type="submit"><svg viewBox="0 0 24 24">{!! $icons['send'] !!}</svg></button>
                </label>
            </form>
            <small class="copyright">Â© 2026 InviteCraft. All rights reserved.</small>
        </footer>
    </div>
</body>

</html>
