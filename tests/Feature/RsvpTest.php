<?php

namespace Tests\Feature;

use App\Models\Invitation;
use App\Models\InvitationTemplate;
use App\Models\Rsvp;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RsvpTest extends TestCase
{
    use RefreshDatabase;

    public function test_published_invitation_accepts_rsvp_and_stores_payload(): void
    {
        $invitation = $this->publishedInvitation();
        $haldi = $invitation->ceremonies()->create(['name' => 'Haldi', 'date' => '2036-12-23', 'sort_order' => 1]);
        $wedding = $invitation->ceremonies()->create(['name' => 'Wedding', 'date' => '2036-12-25', 'sort_order' => 2]);

        $this->post(route('invitations.rsvp.store', $invitation->slug), [
            'guest_name' => 'Ananya Sharma',
            'attending' => '1',
            'functions_attending' => [(string) $haldi->id, (string) $wedding->id],
            'message' => 'Blessings to you both.',
        ])->assertRedirect()
            ->assertSessionHas('status', 'Thank you! Your RSVP has been received.');

        $rsvp = Rsvp::query()->firstOrFail();

        $this->assertSame($invitation->id, $rsvp->invitation_id);
        $this->assertSame('Ananya Sharma', $rsvp->guest_name);
        $this->assertTrue($rsvp->attending);
        $this->assertSame([(string) $haldi->id, (string) $wedding->id], $rsvp->functions_attending);
        $this->assertSame('Blessings to you both.', $rsvp->message);
    }

    public function test_not_attending_clears_functions_attending(): void
    {
        $invitation = $this->publishedInvitation();
        $ceremony = $invitation->ceremonies()->create(['name' => 'Reception', 'date' => '2036-12-26']);

        $this->post(route('invitations.rsvp.store', $invitation->slug), [
            'guest_name' => 'Rohan Mehta',
            'attending' => '0',
            'functions_attending' => [(string) $ceremony->id],
            'message' => 'Sorry to miss it.',
        ])->assertRedirect();

        $rsvp = Rsvp::query()->firstOrFail();

        $this->assertFalse($rsvp->attending);
        $this->assertNull($rsvp->functions_attending);
    }

    public function test_invalid_rsvp_request_fails_validation(): void
    {
        $invitation = $this->publishedInvitation();

        $this->post(route('invitations.rsvp.store', $invitation->slug), [
            'guest_name' => '',
            'attending' => 'maybe',
            'message' => str_repeat('a', 2001),
        ])->assertSessionHasErrors(['guest_name', 'attending', 'message']);

        $this->assertDatabaseCount('rsvps', 0);
    }

    public function test_rsvp_cannot_be_submitted_to_unpublished_or_invalid_invitation(): void
    {
        $draft = $this->draftInvitation();

        $payload = [
            'guest_name' => 'Guest',
            'attending' => '1',
        ];

        $this->post(route('invitations.rsvp.store', $draft->slug), $payload)->assertNotFound();
        $this->post('/invite/missing-invitation/rsvp', $payload)->assertNotFound();

        $this->assertDatabaseCount('rsvps', 0);
    }

    public function test_owner_can_view_rsvp_list_with_persisted_records(): void
    {
        $owner = User::factory()->create(['is_admin' => false]);
        $invitation = $this->publishedInvitation($owner);
        $haldi = $invitation->ceremonies()->create(['name' => 'Haldi', 'date' => '2036-12-23']);
        $invitation->rsvps()->create([
            'guest_name' => 'Meera Kapoor',
            'attending' => true,
            'functions_attending' => [(string) $haldi->id],
            'message' => 'Can not wait to celebrate.',
        ]);

        $this->actingAs($owner)
            ->get(route('dashboard.invitations.rsvps', $invitation))
            ->assertOk()
            ->assertSee('Meera Kapoor')
            ->assertSee('Yes')
            ->assertSee('Haldi')
            ->assertSee('Can not wait to celebrate.');
    }

    public function test_another_user_cannot_view_invitation_rsvp_list(): void
    {
        $owner = User::factory()->create(['is_admin' => false]);
        $otherUser = User::factory()->create(['is_admin' => false]);
        $invitation = $this->publishedInvitation($owner);

        $this->actingAs($otherUser)
            ->get(route('dashboard.invitations.rsvps', $invitation))
            ->assertForbidden();
    }

    public function test_invitation_rsvps_relationship_works(): void
    {
        $invitation = $this->publishedInvitation();

        $invitation->rsvps()->create([
            'guest_name' => 'Kabir Singh',
            'attending' => true,
        ]);

        $this->assertCount(1, $invitation->fresh()->rsvps);
        $this->assertSame('Kabir Singh', $invitation->fresh()->rsvps->first()->guest_name);
    }

    public function test_existing_invitation_public_page_still_loads_with_rsvp_form(): void
    {
        $invitation = $this->publishedInvitation();

        $this->get(route('invitations.public', $invitation->slug))
            ->assertOk()
            ->assertSee('Priya')
            ->assertSee('Rahul')
            ->assertSee('action="'.route('invitations.rsvp.store', $invitation->slug).'"', false)
            ->assertSee('name="guest_name"', false);
    }

    private function publishedInvitation(?User $user = null): Invitation
    {
        return $this->invitation([
            'user_id' => ($user ?? User::factory()->create(['is_admin' => false]))->id,
            'status' => Invitation::Published,
            'slug' => 'priya-rahul-'.Invitation::query()->count(),
            'published_at' => now(),
        ]);
    }

    private function draftInvitation(): Invitation
    {
        return $this->invitation([
            'status' => Invitation::Draft,
            'slug' => 'draft-invitation',
        ]);
    }

    private function invitation(array $overrides = []): Invitation
    {
        $template = InvitationTemplate::create([
            'name' => 'RSVP Wedding',
            'slug' => 'rsvp-wedding-'.InvitationTemplate::query()->count(),
            'category' => 'Wedding',
            'theme_class' => 'royal',
            'view_name' => 'invitations.public.default',
            'price' => 499,
            'is_active' => true,
            'sort_order' => 1,
        ]);

        return Invitation::create(array_merge([
            'user_id' => User::factory()->create(['is_admin' => false])->id,
            'template_id' => $template->id,
            'bride_name' => 'Priya',
            'groom_name' => 'Rahul',
            'wedding_date' => '2036-12-25',
            'settings' => ['rsvp_enabled' => true],
        ], $overrides));
    }
}
