<?php

namespace Tests\Feature;

use App\Models\InvitationTemplate;
use App\Models\TemplateCategory;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Socialite\Facades\Socialite;
use Laravel\Socialite\Two\User as SocialiteUser;
use Tests\TestCase;

class GoogleAuthTest extends TestCase
{
    use RefreshDatabase;

    public function test_google_redirect_route_sends_user_to_provider(): void
    {
        Socialite::fake('google');

        $this->get(route('auth.google.redirect'))->assertRedirect();
    }

    public function test_google_callback_logs_in_existing_email_user_without_duplicate(): void
    {
        $user = User::factory()->create([
            'email' => 'priya@example.com',
            'provider' => null,
            'provider_id' => null,
            'avatar' => null,
            'is_admin' => false,
        ]);

        Socialite::fake('google', SocialiteUser::fake([
            'id' => 'google-123',
            'name' => 'Priya Sharma',
            'email' => 'priya@example.com',
            'avatar' => 'https://example.com/priya.jpg',
        ]));

        $this->get(route('auth.google.callback'))
            ->assertRedirect(route('home'));

        $this->assertAuthenticatedAs($user);
        $this->assertDatabaseCount('users', 1);
        $this->assertDatabaseHas('users', [
            'id' => $user->id,
            'email' => 'priya@example.com',
            'provider' => 'google',
            'provider_id' => 'google-123',
            'avatar' => 'https://example.com/priya.jpg',
        ]);
    }

    public function test_google_callback_creates_new_customer_user(): void
    {
        Socialite::fake('google', SocialiteUser::fake([
            'id' => 'google-456',
            'name' => 'Rahul Verma',
            'email' => 'rahul@example.com',
            'avatar' => 'https://example.com/rahul.jpg',
        ]));

        $this->get(route('auth.google.callback'))
            ->assertRedirect(route('home'));

        $user = User::query()->where('email', 'rahul@example.com')->firstOrFail();

        $this->assertAuthenticatedAs($user);
        $this->assertFalse($user->is_admin);
        $this->assertNotEmpty($user->password);
        $this->assertSame('google', $user->provider);
        $this->assertSame('google-456', $user->provider_id);
        $this->assertSame('https://example.com/rahul.jpg', $user->avatar);
    }

    public function test_selected_template_session_is_preserved_after_google_callback(): void
    {
        $category = TemplateCategory::firstOrCreate(
            ['slug' => 'wedding'],
            ['name' => 'Wedding', 'is_active' => true, 'sort_order' => 1],
        );

        $template = InvitationTemplate::create([
            'name' => 'Royal Wedding',
            'slug' => 'royal-wedding',
            'category' => 'Wedding',
            'category_id' => $category->id,
            'theme_class' => 'royal',
            'price' => 499,
            'is_active' => true,
            'sort_order' => 1,
        ]);

        Socialite::fake('google', SocialiteUser::fake([
            'id' => 'google-789',
            'name' => 'Template User',
            'email' => 'template@example.com',
        ]));

        $this->withSession(['selected_template_slug' => $template->slug])
            ->get(route('auth.google.callback'))
            ->assertRedirect(route('invitations.create', ['template' => $template->slug]));

        $this->assertAuthenticated();
    }

    public function test_google_login_does_not_authenticate_admin_users(): void
    {
        $admin = User::factory()->create([
            'email' => 'admin@example.com',
            'is_admin' => true,
        ]);

        Socialite::fake('google', SocialiteUser::fake([
            'id' => 'google-admin',
            'name' => 'Admin User',
            'email' => 'admin@example.com',
        ]));

        $this->get(route('auth.google.callback'))
            ->assertRedirect(route('login'))
            ->assertSessionHasErrors('email');

        $this->assertGuest();
        $this->assertNull($admin->fresh()->provider);
        $this->assertNull($admin->fresh()->provider_id);
    }
}
