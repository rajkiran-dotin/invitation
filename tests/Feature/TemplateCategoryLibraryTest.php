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
    }
}
