<?php

namespace Tests\Feature;

use App\Models\Invitation;
use App\Models\InvitationCeremony;
use App\Models\InvitationTemplate;
use App\Models\TemplateCategory;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\TestCase;

class InvitationWeddingFunctionPresetTest extends TestCase
{
    use RefreshDatabase;

    #[DataProvider('weddingSidePresets')]
    public function test_empty_builder_renders_selected_wedding_side_preset(string $side, array $expectedNames): void
    {
        $user = User::factory()->create(['is_admin' => false]);
        $template = $this->createWeddingTemplate();
        $invitation = Invitation::create([
            'user_id' => $user->id,
            'template_id' => $template->id,
            'wedding_side' => $side,
            'wedding_date' => '2036-12-25',
            'status' => Invitation::Draft,
            'settings' => [],
        ]);

        $response = $this->actingAs($user)->get(route('invitations.edit', $invitation));

        $response->assertOk()
            ->assertSeeInOrder(array_map(fn (string $name): string => 'value="'.$name.'"', $expectedNames), false)
            ->assertDontSee('value="Engagement"', false)
            ->assertDontSee('value="custom"', false);
    }

    public function test_builder_has_side_selector_confirmation_and_no_custom_side_option(): void
    {
        $user = User::factory()->create(['is_admin' => false]);
        $template = $this->createWeddingTemplate();
        $invitation = Invitation::create([
            'user_id' => $user->id,
            'template_id' => $template->id,
            'status' => Invitation::Draft,
            'settings' => [],
        ]);

        $this->actingAs($user)
            ->get(route('invitations.edit', $invitation))
            ->assertOk()
            ->assertSee('Whose side is this invitation for?')
            ->assertSee('Bride Side')
            ->assertSee('Groom Side')
            ->assertSee('Both Families')
            ->assertSee('Add Function')
            ->assertSee('Changing the wedding side will replace the current function list. Continue?')
            ->assertDontSee('value="custom"', false);
    }

    public function test_update_saves_custom_removed_and_reordered_ceremonies_with_sort_order(): void
    {
        $user = User::factory()->create(['is_admin' => false]);
        $template = $this->createWeddingTemplate();
        $invitation = Invitation::create([
            'user_id' => $user->id,
            'template_id' => $template->id,
            'status' => Invitation::Draft,
            'settings' => [],
        ]);

        $this->actingAs($user)
            ->put(route('invitations.update', $invitation), $this->payload($template, [
                ['name' => 'Haldi', 'slug' => 'haldi', 'date' => '2036-12-23', 'sort_order' => 1],
                ['name' => 'Welcome Dinner', 'date' => '2036-12-22', 'sort_order' => 2],
                ['name' => 'Wedding', 'slug' => 'wedding', 'date' => '2036-12-25', 'sort_order' => 3],
            ], ['wedding_side' => 'bride']))
            ->assertRedirect(route('invitations.edit', $invitation));

        $invitation->refresh()->load('ceremonies');

        $this->assertSame('bride', $invitation->wedding_side);
        $this->assertSame(['Haldi', 'Welcome Dinner', 'Wedding'], $invitation->ceremonies->pluck('name')->all());
        $this->assertSame(['haldi', 'welcome-dinner', 'wedding'], $invitation->ceremonies->pluck('slug')->all());
        $this->assertSame([1, 2, 3], $invitation->ceremonies->pluck('sort_order')->all());
        $this->assertDatabaseMissing(InvitationCeremony::class, [
            'invitation_id' => $invitation->id,
            'name' => 'Reception',
        ]);
    }

    public function test_existing_invitation_edit_does_not_reset_saved_ceremonies(): void
    {
        $user = User::factory()->create(['is_admin' => false]);
        $template = $this->createWeddingTemplate();
        $invitation = Invitation::create([
            'user_id' => $user->id,
            'template_id' => $template->id,
            'wedding_side' => 'groom',
            'wedding_date' => '2036-12-25',
            'status' => Invitation::Draft,
            'settings' => [],
        ]);
        $invitation->ceremonies()->createMany([
            ['name' => 'Bhaat', 'slug' => 'bhaat', 'date' => '2036-12-21', 'sort_order' => 1],
            ['name' => 'Wedding', 'slug' => 'wedding', 'date' => '2036-12-25', 'sort_order' => 2],
        ]);

        $this->actingAs($user)
            ->get(route('invitations.edit', $invitation))
            ->assertOk()
            ->assertSee('value="Bhaat"', false)
            ->assertSee('value="Wedding"', false)
            ->assertDontSee('name="ceremonies[0][name]" value="Tilak"', false)
            ->assertDontSee('name="ceremonies[5][name]" value="Reception"', false);
    }

    public function test_public_template_renders_saved_order_and_custom_ceremony(): void
    {
        $template = $this->createWeddingTemplate();
        $invitation = Invitation::create([
            'user_id' => User::factory()->create(['is_admin' => false])->id,
            'template_id' => $template->id,
            'bride_name' => 'Priya',
            'groom_name' => 'Rahul',
            'wedding_date' => '2036-12-25',
            'slug' => 'priya-rahul',
            'status' => Invitation::Published,
            'published_at' => now(),
            'settings' => [],
        ]);
        $invitation->ceremonies()->createMany([
            ['name' => 'Haldi', 'slug' => 'haldi', 'date' => '2036-12-23', 'sort_order' => 2],
            ['name' => 'Cocktail Night', 'slug' => 'cocktail-night', 'date' => '2036-12-22', 'sort_order' => 1],
            ['name' => 'Wedding', 'slug' => 'wedding', 'date' => '2036-12-25', 'sort_order' => 3],
        ]);

        $this->get(route('invitations.public', 'priya-rahul'))
            ->assertOk()
            ->assertSeeInOrder(['Cocktail Night', 'Haldi', 'Wedding'])
            ->assertSee('event-card--cocktail-night', false);
    }

    public function test_preview_publish_and_public_flow_continue_to_work_with_presets(): void
    {
        $user = User::factory()->create(['is_admin' => false]);
        $template = $this->createWeddingTemplate();
        $invitation = Invitation::create([
            'user_id' => $user->id,
            'template_id' => $template->id,
            'status' => Invitation::Draft,
            'settings' => [],
        ]);

        $this->actingAs($user)
            ->put(route('invitations.update', $invitation), $this->payload($template, [
                ['name' => 'Mehendi', 'slug' => 'mehendi', 'date' => '2036-12-23', 'sort_order' => 1],
                ['name' => 'Wedding', 'slug' => 'wedding', 'date' => '2036-12-25', 'sort_order' => 2],
            ], ['wedding_side' => 'both', 'next_action' => 'preview']))
            ->assertRedirect(route('invitations.preview', $invitation));

        $this->actingAs($user)
            ->get(route('invitations.preview', $invitation))
            ->assertOk()
            ->assertSee('Mehendi')
            ->assertSee('Wedding');

        $this->actingAs($user)
            ->post(route('invitations.publish', $invitation))
            ->assertRedirect(route('invitations.success', $invitation));

        $this->get(route('invitations.public', $invitation->fresh()->slug))
            ->assertOk()
            ->assertSee('Priya')
            ->assertSee('Rahul')
            ->assertSeeInOrder(['Mehendi', 'Wedding']);
    }

    public static function weddingSidePresets(): array
    {
        return [
            'bride side' => ['bride', ['Mehendi', 'Haldi', 'Sangeet', 'Wedding', 'Reception']],
            'groom side' => ['groom', ['Tilak', 'Haldi', 'Mehendi', 'Sangeet', 'Wedding', 'Reception']],
            'both families' => ['both', ['Mehendi', 'Haldi', 'Sangeet', 'Wedding', 'Reception']],
        ];
    }

    private function createWeddingTemplate(): InvitationTemplate
    {
        $category = TemplateCategory::firstOrCreate(
            ['slug' => 'wedding'],
            ['name' => 'Wedding', 'is_active' => true, 'sort_order' => 1],
        );

        return InvitationTemplate::create([
            'name' => 'Preset Wedding',
            'slug' => 'preset-wedding-'.InvitationTemplate::query()->count(),
            'category' => 'Wedding',
            'category_id' => $category->id,
            'theme_class' => 'royal',
            'view_name' => 'invitations.public.default',
            'price' => 499,
            'is_active' => true,
            'sort_order' => 1,
        ]);
    }

    private function payload(InvitationTemplate $template, array $ceremonies, array $overrides = []): array
    {
        return array_merge([
            'template_id' => $template->id,
            'bride_name' => 'Priya',
            'groom_name' => 'Rahul',
            'wedding_date' => '2036-12-25',
            'ceremonies' => $ceremonies,
        ], $overrides);
    }
}
