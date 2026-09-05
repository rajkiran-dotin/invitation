<?php

namespace Tests\Feature;

use App\Models\InvitationEnquiry;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminBackendTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_login_page_is_available(): void
    {
        $this->get(route('admin.login'))->assertOk();
    }

    public function test_admin_can_login_and_view_dashboard(): void
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
}
