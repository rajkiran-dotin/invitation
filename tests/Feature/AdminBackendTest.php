<?php

namespace Tests\Feature;

use App\Models\InvitationEnquiry;
use App\Models\InvitationTemplate;
use App\Models\PricingPlan;
use App\Models\Purchase;
use App\Models\SiteVisit;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Laravel\Socialite\Contracts\User as SocialiteUser;
use Laravel\Socialite\Facades\Socialite;
use Mockery;
use Tests\TestCase;

class AdminBackendTest extends TestCase
{
    use RefreshDatabase;

    public function test_public_login_page_is_available(): void
    {
        $this->get(route('login'))
            ->assertOk()
            ->assertSee('Forgot Password?')
            ->assertSee('Remember me')
            ->assertSee('Continue with Google')
            ->assertSee('Create Account')
            ->assertDontSee('Continue with Facebook')
            ->assertDontSee('Default: admin@invitecraft.test / password');
    }

    public function test_admin_login_remains_admin_only_and_hides_default_credentials(): void
    {
        $this->get(route('admin.login'))
            ->assertOk()
            ->assertSee('Login to Admin')
            ->assertDontSee('Create Account')
            ->assertDontSee('Continue with Google')
            ->assertDontSee('Continue with Facebook')
            ->assertDontSee('Default: admin@invitecraft.test / password');
    }

    public function test_landing_login_button_opens_login_page(): void
    {
        $this->get(route('home'))
            ->assertOk()
            ->assertSee('href="'.route('login').'"', false)
            ->assertSee('Login')
            ->assertSee('Get Started')
            ->assertSee('class="btn btn-light"', false);
    }

    public function test_create_account_link_opens_registration(): void
    {
        $this->get(route('login'))
            ->assertOk()
            ->assertSee('href="'.route('register').'"', false);

        $this->get(route('register'))
            ->assertOk()
            ->assertSee('Create Account')
            ->assertSee('Continue with Google')
            ->assertDontSee('Continue with Facebook')
            ->assertSee('href="'.route('auth.google.redirect').'"', false)
            ->assertSee('Already have an account?');
    }

    public function test_normal_user_can_register_and_is_not_admin(): void
    {
        $this->post(route('register.store'), [
            'name' => 'Priya Sharma',
            'email' => 'priya@example.com',
            'password' => 'secret-password',
            'password_confirmation' => 'secret-password',
        ])->assertRedirect(route('home'));

        $this->assertAuthenticated();
        $this->assertDatabaseHas(User::class, [
            'email' => 'priya@example.com',
            'is_admin' => false,
        ]);
        $this->assertTrue(Hash::check('secret-password', User::where('email', 'priya@example.com')->first()->password));
    }

    public function test_duplicate_email_cannot_register(): void
    {
        User::factory()->create(['email' => 'priya@example.com']);

        $this->from(route('register'))->post(route('register.store'), [
            'name' => 'Priya Sharma',
            'email' => 'priya@example.com',
            'password' => 'secret-password',
            'password_confirmation' => 'secret-password',
        ])
            ->assertRedirect(route('register'))
            ->assertSessionHasErrors('email');
    }

    public function test_registered_user_can_login_and_is_redirected_home(): void
    {
        User::factory()->create([
            'email' => 'customer@example.com',
            'password' => 'secret-password',
            'is_admin' => false,
        ]);

        $this->post(route('login.store'), [
            'email' => 'customer@example.com',
            'password' => 'secret-password',
        ])->assertRedirect(route('home'));

        $this->get(route('home'))
            ->assertOk()
            ->assertSee('Hi,')
            ->assertSee('Logout')
            ->assertDontSee('href="'.route('login').'"', false);
    }

    public function test_invalid_credentials_show_error_and_preserve_email(): void
    {
        $this->from(route('login'))->post(route('login.store'), [
            'email' => 'admin@example.com',
            'password' => 'wrong-password',
        ])
            ->assertRedirect(route('login'))
            ->assertSessionHasErrors('email')
            ->assertSessionHasInput('email', 'admin@example.com');
    }

    public function test_admin_can_login_from_admin_page_and_view_admin_dashboard(): void
    {
        User::factory()->create([
            'email' => 'admin@example.com',
            'password' => 'secret-password',
            'is_admin' => true,
        ]);

        $this->post(route('admin.login.store'), [
            'email' => 'admin@example.com',
            'password' => 'secret-password',
        ])->assertRedirect(route('admin.dashboard'));

        $this->get(route('admin.dashboard'))->assertOk()->assertSee('Dashboard');
    }

    public function test_admin_login_rejects_normal_users(): void
    {
        User::factory()->create([
            'email' => 'customer@example.com',
            'password' => 'secret-password',
            'is_admin' => false,
        ]);

        $this->post(route('admin.login.store'), [
            'email' => 'customer@example.com',
            'password' => 'secret-password',
        ])->assertSessionHasErrors('email');

        $this->assertGuest();
    }

    public function test_authenticated_admin_is_redirected_away_from_login_page(): void
    {
        $admin = User::factory()->create(['is_admin' => true]);

        $this->actingAs($admin)
            ->get(route('admin.login'))
            ->assertRedirect(route('admin.dashboard'));
    }

    public function test_guest_cannot_access_dashboard(): void
    {
        $this->get(route('dashboard'))->assertRedirect(route('login'));
    }

    public function test_logout_logs_user_out_and_redirects_home(): void
    {
        $user = User::factory()->create(['is_admin' => false]);

        $this->actingAs($user)
            ->post(route('logout'))
            ->assertRedirect(route('home'));

        $this->assertGuest();

        $this->get(route('home'))
            ->assertOk()
            ->assertSee('Login')
            ->assertSee('Get Started');
    }

    public function test_google_button_starts_oauth(): void
    {
        $provider = Mockery::mock();
        $provider->shouldReceive('redirect')->once()->andReturn(redirect('https://socialite.fake/google/authorize'));
        Socialite::shouldReceive('driver')->once()->with('google')->andReturn($provider);

        $this->get(route('auth.google.redirect'))
            ->assertRedirect('https://socialite.fake/google/authorize');
    }

    public function test_google_callback_logs_in_existing_user(): void
    {
        User::factory()->create([
            'email' => 'customer@example.com',
            'is_admin' => false,
        ]);

        $this->mockSocialiteUser('google', 'google-123', 'Customer User', 'customer@example.com', 'https://example.com/avatar.jpg');

        $this->get(route('auth.google.callback'))->assertRedirect(route('home'));

        $this->assertAuthenticated();
        $this->assertDatabaseHas(User::class, [
            'email' => 'customer@example.com',
            'provider' => 'google',
            'provider_id' => 'google-123',
            'is_admin' => false,
        ]);
    }

    public function test_google_callback_creates_normal_user(): void
    {
        $this->mockSocialiteUser('google', 'google-456', 'New Customer', 'new@example.com');

        $this->get(route('auth.google.callback'))->assertRedirect(route('home'));

        $this->assertAuthenticated();
        $this->assertDatabaseHas(User::class, [
            'email' => 'new@example.com',
            'provider' => 'google',
            'provider_id' => 'google-456',
            'is_admin' => false,
        ]);
    }

    public function test_facebook_auth_is_removed(): void
    {
        $this->assertFalse(\Illuminate\Support\Facades\Route::has('auth.facebook.redirect'));
        $this->assertFalse(\Illuminate\Support\Facades\Route::has('auth.facebook.callback'));
        $this->assertArrayNotHasKey('facebook', config('services'));

        $this->get('/auth/facebook/redirect')->assertNotFound();
        $this->get('/auth/facebook/callback')->assertNotFound();
    }

    public function test_public_enquiry_is_saved(): void
    {
        $this->post(route('enquiry.store'), [
            'name' => 'Raj',
            'email' => 'raj@example.com',
            'phone' => '9876543210',
            'event_type' => 'Wedding',
            'event_date' => '2036-12-25',
            'plan' => 'Pro',
            'message' => 'Need a wedding invite.',
        ])->assertSessionHas('status');

        $this->assertDatabaseHas(InvitationEnquiry::class, [
            'email' => 'raj@example.com',
            'event_type' => 'Wedding',
            'status' => 'new',
        ]);
    }

    public function test_frontend_visit_tracking_records_page_views_without_raw_ip(): void
    {
        $this->get(route('home'))->assertOk();
        $this->get(route('home'))->assertOk();

        $this->assertDatabaseCount(SiteVisit::class, 2);
        $visit = SiteVisit::first();

        $this->assertSame('/', $visit->path);
        $this->assertSame('home', $visit->route_name);
        $this->assertNotNull($visit->session_id);
        $this->assertNotNull($visit->ip_hash);
        $this->assertNotSame('127.0.0.1', $visit->ip_hash);
    }

    public function test_admin_routes_are_excluded_from_visit_tracking(): void
    {
        $admin = User::factory()->create(['is_admin' => true]);

        $this->actingAs($admin)->get(route('admin.dashboard'))->assertOk();
        $this->get(route('admin.login'))->assertRedirect(route('admin.dashboard'));

        $this->assertDatabaseCount(SiteVisit::class, 0);
    }

    public function test_admin_dashboard_uses_real_analytics_and_paid_revenue_only(): void
    {
        $admin = User::factory()->create(['is_admin' => true]);
        $customer = User::factory()->create(['name' => 'Rahul Sharma', 'is_admin' => false]);
        $template = InvitationTemplate::create([
            'name' => 'Royal Wedding Template',
            'category' => 'Wedding',
            'theme_class' => 'royal',
            'price' => 2999,
            'is_active' => true,
            'sort_order' => 1,
        ]);
        $plan = PricingPlan::create([
            'name' => 'Premium Plan',
            'price' => 2999,
            'features' => ['Custom domain'],
            'is_popular' => true,
            'is_active' => true,
            'sort_order' => 1,
        ]);

        SiteVisit::create([
            'session_id' => 'visitor-session',
            'ip_hash' => 'hashed-ip',
            'path' => '/',
            'route_name' => 'home',
            'visited_at' => now(),
        ]);
        Purchase::create([
            'user_id' => $customer->id,
            'invitation_template_id' => $template->id,
            'pricing_plan_id' => $plan->id,
            'amount' => 2999,
            'currency' => 'INR',
            'payment_status' => 'paid',
            'gateway' => 'razorpay',
            'transaction_id' => 'pay_123',
            'purchased_at' => now(),
        ]);
        Purchase::create([
            'user_id' => $customer->id,
            'invitation_template_id' => $template->id,
            'pricing_plan_id' => $plan->id,
            'amount' => 4999,
            'currency' => 'INR',
            'payment_status' => 'failed',
            'gateway' => 'razorpay',
            'transaction_id' => 'pay_failed',
            'purchased_at' => now(),
        ]);

        $this->actingAs($admin)
            ->get(route('admin.dashboard'))
            ->assertOk()
            ->assertSee("Today's Visitors", false)
            ->assertSee("Today's Page Views", false)
            ->assertSee('New Users Today')
            ->assertSee('Purchases Today')
            ->assertSee("Today's Revenue", false)
            ->assertSee('Total Revenue')
            ->assertSee('Royal Wedding Template')
            ->assertSee('Premium Plan')
            ->assertSee('₹2,999')
            ->assertSee('Failed');
    }

    public function test_new_users_page_filters_and_displays_latest_purchase_summary(): void
    {
        $admin = User::factory()->create(['is_admin' => true]);
        $matchingUser = User::factory()->create(['name' => 'Rahul Sharma', 'email' => 'rahul@example.com', 'is_admin' => false]);
        User::factory()->create(['name' => 'Priya Sharma', 'email' => 'priya@example.com', 'is_admin' => false]);
        $template = InvitationTemplate::create([
            'name' => 'Floral Bliss',
            'category' => 'Wedding',
            'theme_class' => 'floral',
            'price' => 999,
            'is_active' => true,
            'sort_order' => 1,
        ]);
        $plan = PricingPlan::create([
            'name' => 'Pro',
            'price' => 999,
            'features' => ['Priority support'],
            'is_popular' => true,
            'is_active' => true,
            'sort_order' => 1,
        ]);

        Purchase::create([
            'user_id' => $matchingUser->id,
            'invitation_template_id' => $template->id,
            'pricing_plan_id' => $plan->id,
            'amount' => 999,
            'currency' => 'INR',
            'payment_status' => 'paid',
            'purchased_at' => now(),
        ]);

        $this->actingAs($admin)
            ->get(route('admin.users.index', ['search' => 'rahul', 'payment_status' => 'paid', 'plan_id' => $plan->id]))
            ->assertOk()
            ->assertSee('Rahul Sharma')
            ->assertSee('rahul@example.com')
            ->assertSee('Floral Bliss')
            ->assertSee('Pro')
            ->assertSee('₹999')
            ->assertSee('Paid')
            ->assertDontSee('priya@example.com');
    }

    public function test_user_detail_page_shows_purchase_history(): void
    {
        $admin = User::factory()->create(['is_admin' => true]);
        $customer = User::factory()->create(['name' => 'Rahul Sharma', 'email' => 'rahul@example.com', 'is_admin' => false]);

        Purchase::create([
            'user_id' => $customer->id,
            'product_name' => 'Custom Invitation',
            'amount' => 1499,
            'currency' => 'INR',
            'payment_status' => 'refunded',
            'gateway' => 'razorpay',
            'transaction_id' => 'pay_refunded',
            'purchased_at' => now(),
        ]);

        $this->actingAs($admin)
            ->get(route('admin.users.show', $customer))
            ->assertOk()
            ->assertSee('Customer Information')
            ->assertSee('Rahul Sharma')
            ->assertSee('Custom Invitation')
            ->assertSee('Refunded')
            ->assertSee('pay_refunded');
    }

    public function test_normal_user_cannot_access_admin_customer_pages(): void
    {
        $user = User::factory()->create(['is_admin' => false]);

        $this->actingAs($user)->get(route('admin.users.index'))->assertForbidden();
        $this->actingAs($user)->get(route('admin.users.show', $user))->assertForbidden();
    }

    private function mockSocialiteUser(
        string $providerName,
        string $id,
        string $name,
        ?string $email,
        ?string $avatar = null,
    ): void {
        $provider = Mockery::mock();
        $provider->shouldReceive('user')->once()->andReturn(new class ($id, $name, $email, $avatar) implements SocialiteUser {
            public function __construct(
                private readonly string $id,
                private readonly string $name,
                private readonly ?string $email,
                private readonly ?string $avatar,
            ) {}

            public function getId(): string
            {
                return $this->id;
            }

            public function getNickname(): ?string
            {
                return null;
            }

            public function getName(): string
            {
                return $this->name;
            }

            public function getEmail(): ?string
            {
                return $this->email;
            }

            public function getAvatar(): ?string
            {
                return $this->avatar;
            }
        });

        Socialite::shouldReceive('driver')->once()->with($providerName)->andReturn($provider);
    }
}
