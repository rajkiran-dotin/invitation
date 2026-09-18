<?php

namespace Tests\Feature;

use App\Models\InvitationTemplate;
use App\Models\TemplateCategory;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TemplateCategoryLibraryTest extends TestCase
{
    use RefreshDatabase;

    public function test_public_templates_library_uses_active_database_categories(): void
    {
        TemplateCategory::query()->delete();

        $wedding = TemplateCategory::create([
            'name' => 'Wedding',
            'slug' => 'wedding',
            'is_active' => true,
            'sort_order' => 1,
        ]);

        $housewarming = TemplateCategory::create([
            'name' => 'Housewarming',
            'slug' => 'housewarming',
            'is_active' => true,
            'sort_order' => 2,
        ]);

        $haldi = TemplateCategory::create([
            'name' => 'Haldi',
            'slug' => 'haldi',
            'is_active' => false,
            'sort_order' => 3,
        ]);

        InvitationTemplate::create([
            'name' => 'Royal Wedding',
            'category' => $wedding->name,
            'category_id' => $wedding->id,
            'theme_class' => 'royal',
            'price' => 499,
            'is_active' => true,
            'sort_order' => 1,
        ]);

        InvitationTemplate::create([
            'name' => 'Warm Home',
            'category' => $housewarming->name,
            'category_id' => $housewarming->id,
            'theme_class' => 'pastel',
            'price' => 499,
            'is_active' => true,
            'sort_order' => 2,
        ]);

        InvitationTemplate::create([
            'name' => 'Haldi Glow',
            'category' => $haldi->name,
            'category_id' => $haldi->id,
            'theme_class' => 'floral',
            'price' => 499,
            'is_active' => true,
            'sort_order' => 3,
        ]);

        $this->get(route('templates.index'))
            ->assertOk()
            ->assertSee('Wedding')
            ->assertSee('Housewarming')
            ->assertDontSee('Haldi')
            ->assertSee('Royal Wedding')
            ->assertSee('Warm Home')
            ->assertDontSee('Haldi Glow');
    }

    public function test_public_templates_filter_by_category_slug(): void
    {
        TemplateCategory::query()->delete();

        $wedding = TemplateCategory::create(['name' => 'Wedding', 'slug' => 'wedding', 'is_active' => true, 'sort_order' => 1]);
        $birthday = TemplateCategory::create(['name' => 'Birthday', 'slug' => 'birthday', 'is_active' => true, 'sort_order' => 2]);

        InvitationTemplate::create(['name' => 'Royal Wedding', 'slug' => 'royal-wedding', 'category' => 'Wedding', 'category_id' => $wedding->id, 'theme_class' => 'royal', 'price' => 499, 'is_active' => true, 'sort_order' => 1]);
        InvitationTemplate::create(['name' => 'Modern Birthday', 'slug' => 'modern-birthday', 'category' => 'Birthday', 'category_id' => $birthday->id, 'theme_class' => 'modern', 'price' => 499, 'is_active' => true, 'sort_order' => 2]);

        $this->get(route('templates.index', ['category' => 'birthday']))
            ->assertOk()
            ->assertSee('Modern Birthday')
            ->assertDontSee('Royal Wedding');
    }

    public function test_template_category_admin_routes_require_admin_user(): void
    {
        $user = User::factory()->create(['is_admin' => false]);

        $this->actingAs($user)
            ->get(route('admin.template-categories.index'))
            ->assertForbidden();
    }

    public function test_admin_can_manage_template_categories(): void
    {
        $admin = User::factory()->create(['is_admin' => true]);

        $this->actingAs($admin)
            ->post(route('admin.template-categories.store'), [
                'name' => 'Housewarming',
                'slug' => 'House Warming',
                'description' => 'New home celebrations',
                'sort_order' => 10,
                'is_active' => '1',
            ])
            ->assertRedirect(route('admin.template-categories.index'));

        $category = TemplateCategory::where('slug', 'house-warming')->firstOrFail();

        $this->assertTrue($category->is_active);

        $this->actingAs($admin)
            ->patch(route('admin.template-categories.toggle', $category))
            ->assertSessionHas('status', 'Template category status updated.');

        $this->assertFalse($category->fresh()->is_active);
    }

    public function test_category_with_templates_cannot_be_deleted(): void
    {
        $admin = User::factory()->create(['is_admin' => true]);
        $category = TemplateCategory::where('slug', 'wedding')->firstOrFail();

        InvitationTemplate::create([
            'name' => 'Royal Wedding',
            'category' => 'Wedding',
            'category_id' => $category->id,
            'theme_class' => 'royal',
            'price' => 499,
            'is_active' => true,
            'sort_order' => 1,
        ]);

        $this->actingAs($admin)
            ->delete(route('admin.template-categories.destroy', $category))
            ->assertSessionHas('status', 'Cannot delete this category because 1 templates are assigned to it.');

        $this->assertDatabaseHas('template_categories', ['id' => $category->id]);
    }

    public function test_admin_template_form_uses_category_dropdown_and_saves_category_id(): void
    {
        $admin = User::factory()->create(['is_admin' => true]);
        $category = TemplateCategory::where('slug', 'reception')->firstOrFail();

        $this->actingAs($admin)
            ->get(route('admin.templates.create'))
            ->assertOk()
            ->assertSee('Reception')
            ->assertSee('name="category_id"', false);

        $this->actingAs($admin)
            ->post(route('admin.templates.store'), [
                'name' => 'Reception Classic',
                'category_id' => $category->id,
                'theme_class' => 'classic',
                'price' => 799,
                'sort_order' => 4,
                'is_active' => '1',
            ])
            ->assertRedirect(route('admin.templates.index'));

        $this->assertDatabaseHas('invitation_templates', [
            'name' => 'Reception Classic',
            'category_id' => $category->id,
            'category' => 'Reception',
        ]);
    }

    public function test_admin_can_update_template_status_inline(): void
    {
        $admin = User::factory()->create(['is_admin' => true]);
        $category = TemplateCategory::where('slug', 'wedding')->firstOrFail();
        $template = InvitationTemplate::create([
            'name' => 'Inline Status Wedding',
            'slug' => 'inline-status-wedding',
            'category' => 'Wedding',
            'category_id' => $category->id,
            'theme_class' => 'royal',
            'price' => 499,
            'is_active' => false,
            'sort_order' => 1,
        ]);

        $this->actingAs($admin)
            ->get(route('admin.templates.index'))
            ->assertOk()
            ->assertSee('name="csrf-token"', false)
            ->assertSee('data-template-status-select', false)
            ->assertSee('Change Inline Status Wedding status');

        $this->actingAs($admin)
            ->patchJson(route('admin.templates.status', $template), ['status' => 'active'])
            ->assertOk()
            ->assertJson([
                'success' => true,
                'status' => 'active',
            ]);

        $this->assertTrue($template->fresh()->is_active);

        $this->get(route('templates.index'))
            ->assertOk()
            ->assertSee('Inline Status Wedding');

        $this->actingAs($admin)
            ->patchJson(route('admin.templates.status', $template), ['status' => 'hidden'])
            ->assertOk()
            ->assertJson([
                'success' => true,
                'status' => 'hidden',
            ]);

        $this->assertFalse($template->fresh()->is_active);

        $this->get(route('templates.index'))
            ->assertOk()
            ->assertDontSee('Inline Status Wedding');
    }

    public function test_template_status_route_requires_admin_user(): void
    {
        $user = User::factory()->create(['is_admin' => false]);
        $template = InvitationTemplate::create([
            'name' => 'Protected Status Wedding',
            'category' => 'Wedding',
            'theme_class' => 'royal',
            'price' => 499,
            'is_active' => true,
            'sort_order' => 1,
        ]);

        $this->actingAs($user)
            ->patchJson(route('admin.templates.status', $template), ['status' => 'hidden'])
            ->assertForbidden();

        $this->assertTrue($template->fresh()->is_active);
    }

    public function test_admin_templates_index_filters_by_category_and_status(): void
    {
        $admin = User::factory()->create(['is_admin' => true]);
        $wedding = TemplateCategory::where('slug', 'wedding')->firstOrFail();
        $engagement = TemplateCategory::where('slug', 'engagement')->firstOrFail();

        InvitationTemplate::create([
            'name' => 'Admin Wedding Active',
            'slug' => 'admin-wedding-active',
            'category' => 'Wedding',
            'category_id' => $wedding->id,
            'theme_class' => 'royal',
            'price' => 499,
            'is_active' => true,
            'sort_order' => 1,
        ]);

        InvitationTemplate::create([
            'name' => 'Admin Wedding Hidden',
            'slug' => 'admin-wedding-hidden',
            'category' => 'Wedding',
            'category_id' => $wedding->id,
            'theme_class' => 'minimal',
            'price' => 499,
            'is_active' => false,
            'sort_order' => 2,
        ]);

        InvitationTemplate::create([
            'name' => 'Admin Engagement Active',
            'slug' => 'admin-engagement-active',
            'category' => 'Engagement',
            'category_id' => $engagement->id,
            'theme_class' => 'floral',
            'price' => 499,
            'is_active' => true,
            'sort_order' => 3,
        ]);

        $this->actingAs($admin)
            ->get(route('admin.templates.index'))
            ->assertOk()
            ->assertSee('All Categories')
            ->assertSee('Wedding')
            ->assertSee('Engagement')
            ->assertSee('Admin Wedding Active')
            ->assertSee('Admin Wedding Hidden')
            ->assertSee('Admin Engagement Active')
            ->assertSee('href="'.route('admin.templates.index').'"', false);

        $this->actingAs($admin)
            ->get(route('admin.templates.index', ['category_id' => $wedding->id]))
            ->assertOk()
            ->assertSee('Admin Wedding Active')
            ->assertSee('Admin Wedding Hidden')
            ->assertDontSee('Admin Engagement Active')
            ->assertSee('value="'.$wedding->id.'" selected', false);

        $this->actingAs($admin)
            ->get(route('admin.templates.index', ['category_id' => $wedding->id, 'status' => 'hidden']))
            ->assertOk()
            ->assertSee('Admin Wedding Hidden')
            ->assertDontSee('Admin Wedding Active')
            ->assertDontSee('Admin Engagement Active')
            ->assertSee('value="hidden" selected', false);
    }

    public function test_admin_templates_filter_preserves_query_string_in_pagination_and_empty_state(): void
    {
        $admin = User::factory()->create(['is_admin' => true]);
        $wedding = TemplateCategory::where('slug', 'wedding')->firstOrFail();
        $birthday = TemplateCategory::where('slug', 'birthday')->firstOrFail();

        foreach (range(1, 13) as $index) {
            InvitationTemplate::create([
                'name' => 'Paged Wedding '.$index,
                'slug' => 'paged-wedding-'.$index,
                'category' => 'Wedding',
                'category_id' => $wedding->id,
                'theme_class' => 'royal',
                'price' => 499,
                'is_active' => true,
                'sort_order' => $index,
            ]);
        }

        $this->actingAs($admin)
            ->get(route('admin.templates.index', ['category_id' => $wedding->id]))
            ->assertOk()
            ->assertSee('Paged Wedding 1')
            ->assertSee('category_id='.$wedding->id, false);

        $this->actingAs($admin)
            ->get(route('admin.templates.index', ['category_id' => $birthday->id, 'status' => 'hidden']))
            ->assertOk()
            ->assertSee('No templates found in this category.')
            ->assertSee('Clear Filter')
            ->assertSee('href="'.route('admin.templates.index').'"', false);
    }

    public function test_search_and_category_filters_work_together(): void
    {
        $wedding = TemplateCategory::where('slug', 'wedding')->firstOrFail();
        $birthday = TemplateCategory::where('slug', 'birthday')->firstOrFail();

        InvitationTemplate::create(['name' => 'Floral Wedding', 'slug' => 'floral-wedding', 'category' => 'Wedding', 'category_id' => $wedding->id, 'description' => 'Floral wedding style', 'theme_class' => 'floral', 'price' => 499, 'is_active' => true, 'sort_order' => 1]);
        InvitationTemplate::create(['name' => 'Floral Birthday', 'slug' => 'floral-birthday', 'category' => 'Birthday', 'category_id' => $birthday->id, 'description' => 'Floral birthday style', 'theme_class' => 'floral', 'price' => 499, 'is_active' => true, 'sort_order' => 2]);

        $this->get(route('templates.index', ['category' => 'wedding', 'search' => 'floral']))
            ->assertOk()
            ->assertSee('Floral Wedding')
            ->assertDontSee('Floral Birthday');
    }

    public function test_template_detail_page_and_use_template_flow(): void
    {
        $category = TemplateCategory::where('slug', 'wedding')->firstOrFail();
        $template = InvitationTemplate::create([
            'name' => 'Preview Wedding',
            'slug' => 'preview-wedding',
            'category' => 'Wedding',
            'category_id' => $category->id,
            'description' => 'A polished preview template.',
            'features' => ['Responsive invitation website'],
            'theme_class' => 'royal',
            'price' => 499,
            'is_premium' => true,
            'is_active' => true,
            'sort_order' => 1,
        ]);

        $this->get(route('templates.show', $template->slug))
            ->assertOk()
            ->assertSee('Preview Wedding')
            ->assertSee('Live Preview')
            ->assertSee('Use This Template');

        $this->get(route('templates.preview', $template->slug))
            ->assertOk()
            ->assertSee('InviteCraft Preview')
            ->assertSee('Preview Wedding')
            ->assertSee('Priya')
            ->assertSee('Rahul')
            ->assertSee('Haldi')
            ->assertSee('Use This Template');

        $this->get(route('templates.use', $template->slug))
            ->assertRedirect(route('login'))
            ->assertSessionHas('selected_template_slug', 'preview-wedding')
            ->assertSessionHas('url.intended', route('templates.use', $template->slug));

        $user = User::factory()->create(['is_admin' => false]);

        $this->actingAs($user)
            ->get(route('templates.use', $template->slug))
            ->assertRedirect(route('dashboard', ['template' => $template->slug]))
            ->assertSessionHas('selected_template_id', $template->id);
    }

    public function test_inactive_categories_and_templates_are_hidden_from_library(): void
    {
        $hiddenCategory = TemplateCategory::create(['name' => 'Hidden Category', 'slug' => 'hidden-category', 'is_active' => false, 'sort_order' => 99]);
        $activeCategory = TemplateCategory::where('slug', 'wedding')->firstOrFail();

        InvitationTemplate::create(['name' => 'Hidden Category Template', 'slug' => 'hidden-category-template', 'category' => 'Hidden Category', 'category_id' => $hiddenCategory->id, 'theme_class' => 'minimal', 'price' => 499, 'is_active' => true, 'sort_order' => 1]);
        InvitationTemplate::create(['name' => 'Inactive Wedding Template', 'slug' => 'inactive-wedding-template', 'category' => 'Wedding', 'category_id' => $activeCategory->id, 'theme_class' => 'minimal', 'price' => 499, 'is_active' => false, 'sort_order' => 2]);

        $this->get(route('templates.index'))
            ->assertOk()
            ->assertDontSee('Hidden Category')
            ->assertDontSee('Hidden Category Template')
            ->assertDontSee('Inactive Wedding Template');

        $this->get(route('templates.show', 'hidden-category-template'))->assertNotFound();
        $this->get(route('templates.show', 'inactive-wedding-template'))->assertNotFound();
        $this->get(route('templates.preview', 'hidden-category-template'))->assertNotFound();
        $this->get(route('templates.preview', 'inactive-wedding-template'))->assertNotFound();
    }

    public function test_royal_template_live_preview_uses_actual_dynamic_template(): void
    {
        $category = TemplateCategory::where('slug', 'wedding')->firstOrFail();
        $template = InvitationTemplate::create([
            'name' => 'Royal Saffron Vows',
            'slug' => 'royal-saffron-vows',
            'category' => 'Wedding',
            'category_id' => $category->id,
            'description' => 'Royal live invitation.',
            'theme_class' => 'royal',
            'view_name' => 'invitations.public.royal_saffron_vows',
            'price' => 499,
            'is_premium' => true,
            'is_active' => true,
            'sort_order' => 1,
        ]);

        $this->get(route('templates.preview', $template->slug))
            ->assertOk()
            ->assertSee('InviteCraft Preview')
            ->assertSee('Royal Saffron Vows')
            ->assertSee('window.InviteCraftWeddingData', false)
            ->assertSee('window.InviteCraftPreviewMode = true', false)
            ->assertSee('Open Invitation')
            ->assertSee('Priya')
            ->assertSee('Rahul')
            ->assertSee('Haldi')
            ->assertSee('Mehendi')
            ->assertSee('Sangeet')
            ->assertSee('Wedding')
            ->assertSee('Reception');
    }

    public function test_homepage_template_cards_are_full_clickable_links_for_active_templates(): void
    {
        TemplateCategory::query()->delete();

        $wedding = TemplateCategory::create(['name' => 'Wedding', 'slug' => 'wedding', 'is_active' => true, 'sort_order' => 1]);

        $activeTemplate = InvitationTemplate::create([
            'name' => 'Royal Saffron Vows',
            'slug' => 'royal-saffron-vows',
            'category' => 'Wedding',
            'category_id' => $wedding->id,
            'theme_class' => 'royal',
            'price' => 499,
            'is_active' => true,
            'sort_order' => 1,
        ]);

        InvitationTemplate::create([
            'name' => 'Hidden Saffron Vows',
            'slug' => 'hidden-saffron-vows',
            'category' => 'Wedding',
            'category_id' => $wedding->id,
            'theme_class' => 'royal',
            'price' => 499,
            'is_active' => false,
            'sort_order' => 2,
        ]);

        $this->get(route('home'))
            ->assertOk()
            ->assertSee('Beautiful Templates for You')
            ->assertSee('class="template-card-link"', false)
            ->assertSee('href="'.route('templates.show', $activeTemplate->slug).'"', false)
            ->assertSee('aria-label="View Royal Saffron Vows template"', false)
            ->assertSee('Royal Saffron Vows')
            ->assertDontSee('Hidden Saffron Vows');
    }

    public function test_homepage_event_cards_mark_non_wedding_events_as_coming_soon(): void
    {
        $response = $this->get(route('home'))
            ->assertOk()
            ->assertSee('View Wedding templates')
            ->assertSee('href="'.route('templates.index', ['category' => 'wedding']).'"', false)
            ->assertSee('Engagement - Coming Soon')
            ->assertSee('Birthday - Coming Soon')
            ->assertSee('Mundan Ceremony - Coming Soon')
            ->assertSee('Griha Pravesh - Coming Soon');

        $html = $response->getContent();

        $this->assertSame(4, substr_count($html, 'class="coming-soon-badge"'));
        $this->assertStringNotContainsString('Wedding - Coming Soon', $html);
    }
}
