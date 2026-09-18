<?php

namespace Database\Seeders;

use App\Models\Faq;
use App\Models\InvitationTemplate;
use App\Models\PricingPlan;
use App\Models\TemplateCategory;
use App\Models\Testimonial;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        User::updateOrCreate(
            ['email' => 'admin@invitecraft.test'],
            [
                'name' => 'InviteCraft Admin',
                'password' => Hash::make('password'),
                'is_admin' => true,
            ],
        );

        $categoryIds = collect([
            ['Wedding', 1],
            ['Engagement', 2],
            ['Birthday', 3],
            ['Mundan', 4],
            ['Griha Pravesh', 5],
            ['Anniversary', 6],
            ['Baby Shower', 7],
            ['Naming Ceremony', 8],
            ['Reception', 9],
        ])->mapWithKeys(function (array $category): array {
            $model = TemplateCategory::updateOrCreate(
                ['slug' => Str::slug($category[0])],
                ['name' => $category[0], 'sort_order' => $category[1], 'is_active' => true],
            );

            return [$category[0] => $model->id];
        });

        TemplateCategory::query()
            ->whereIn('slug', ['haldi', 'mehendi', 'sangeet'])
            ->withCount('templates')
            ->get()
            ->each(function (TemplateCategory $category): void {
                if ($category->templates_count === 0) {
                    $category->delete();

                    return;
                }

                $category->update(['is_active' => false]);
            });

        collect([
            ['Royal Wedding', 'Wedding', 'royal', 1, true, 'Elegant royal maroon and gold wedding invitation template.'],
            ['Floral Bliss', 'Wedding', 'floral', 2, true, 'Soft floral wedding template with warm pastel details.'],
            ['Classic Elegance', 'Wedding', 'classic', 3, true, 'Timeless wedding invitation with a refined ceremonial look.'],
            ['Modern Wedding', 'Wedding', 'modern', 4, true, 'Clean contemporary layout for modern wedding celebrations.'],
            ['Traditional Indian Wedding', 'Wedding', 'royal', 5, true, 'Rich traditional Indian wedding design with festive detailing.'],
            ['Minimal Wedding', 'Wedding', 'minimal', 6, false, 'Simple and graceful wedding invitation for intimate events.'],
            ['Minimal Love', 'Engagement', 'minimal', 7, true, 'Minimal engagement invitation with romantic spacing and typography.'],
            ['Elegant Engagement', 'Engagement', 'pastel', 8, true, 'Premium engagement design for ring ceremonies and family gatherings.'],
            ['Ring Ceremony', 'Engagement', 'floral', 9, true, 'Celebratory ring ceremony invite with polished floral accents.'],
            ['Modern Engagement', 'Engagement', 'modern', 10, false, 'Stylish engagement template with a sharp modern finish.'],
            ['Modern Chic', 'Birthday', 'modern', 11, true, 'Bold birthday invitation template for stylish celebrations.'],
            ['Kids Birthday', 'Birthday', 'pastel', 12, false, 'Playful birthday invite made for colorful kids parties.'],
            ['Elegant Birthday', 'Birthday', 'floral', 13, true, 'Soft and refined birthday design for milestone celebrations.'],
            ['First Birthday', 'Birthday', 'pastel', 14, true, 'Sweet first birthday invitation with gentle pastel styling.'],
            ['Modern Birthday', 'Birthday', 'modern', 15, false, 'Fresh birthday template with high contrast and clean sections.'],
            ['Traditional Mundan', 'Mundan', 'classic', 16, true, 'Traditional mundan ceremony template with warm family details.'],
            ['Floral Mundan', 'Mundan', 'floral', 17, false, 'Gentle floral mundan invitation for a sacred celebration.'],
            ['Pastel Dream', 'Griha Pravesh', 'pastel', 18, true, 'Pastel housewarming invitation for a graceful new beginning.'],
            ['Sacred Home', 'Griha Pravesh', 'classic', 19, true, 'Blessed griha pravesh invite with traditional home ceremony styling.'],
            ['New Beginnings', 'Griha Pravesh', 'minimal', 20, false, 'Clean new-home celebration template with calm visual rhythm.'],
            ['Traditional Griha Pravesh', 'Griha Pravesh', 'royal', 21, true, 'Festive griha pravesh template with ceremonial accents.'],
            ['Golden Anniversary', 'Anniversary', 'classic', 22, true, 'Warm golden anniversary invitation for memorable milestones.'],
            ['Romantic Anniversary', 'Anniversary', 'floral', 23, true, 'Romantic anniversary invite with soft color and elegant details.'],
            ['Little Star', 'Baby Shower', 'pastel', 24, false, 'Sweet baby shower template with soft star-inspired details.'],
            ['Pastel Baby Shower', 'Baby Shower', 'minimal', 25, true, 'Premium pastel baby shower design with gentle spacing.'],
            ['Sweet Beginnings', 'Naming Ceremony', 'floral', 26, false, 'Delicate naming ceremony invitation for family celebrations.'],
            ['Baby Naming Invitation', 'Naming Ceremony', 'pastel', 27, true, 'Elegant baby naming invite with warm celebratory details.'],
            ['Royal Reception', 'Reception', 'royal', 28, true, 'Royal reception template with polished evening celebration styling.'],
            ['Elegant Reception', 'Reception', 'classic', 29, true, 'Sophisticated reception invitation with refined classic styling.'],
        ])->each(function (array $template) use ($categoryIds): void {
            $slug = Str::slug($template[0]);
            $attributes = [
                'name' => $template[0],
                'slug' => $slug,
                'category' => $template[1],
                'category_id' => $categoryIds[$template[1]],
                'description' => $template[5],
                'preview_image' => '/images/templates/'.$slug.'.svg',
                'features' => ['Responsive invitation website', 'Photo gallery ready', 'WhatsApp share link'],
                'is_premium' => $template[4],
                'theme_class' => $template[2],
                'price' => $template[4] ? 499 : 0,
                'sort_order' => $template[3],
                'is_active' => true,
            ];

            if ($slug === 'royal-wedding') {
                $attributes['view_name'] = 'invitations.public.royal_saffron_vows';
                $attributes['demo_url'] = '/templates/royal-wedding';
            }

            $model = InvitationTemplate::firstOrCreate(['slug' => $slug], $attributes);
            $missingAttributes = collect($attributes)
                ->filter(fn (mixed $value, string $key): bool => $model->{$key} === null || $model->{$key} === '' || $model->{$key} === [])
                ->all();

            if ($missingAttributes !== []) {
                $model->update($missingAttributes);
            }
        });

        collect([
            ['Basic', 499, ['1 Invitation Website', 'Premium Templates', 'Basic Customization', 'Gallery (Up to 20 Photos)', 'QR Code & WhatsApp Share'], false, 1],
            ['Pro', 999, ['1 Invitation Website', 'Premium Templates', 'Advanced Customization', 'Gallery (Up to 100 Photos)', 'Custom Domain', 'Remove InviteCraft Branding', 'QR Code & WhatsApp Share', 'Priority Support'], true, 2],
            ['Business', 1999, ['5 Invitation Websites', 'Premium Templates', 'Advanced Customization', 'Unlimited Photos', 'Custom Domain', 'Remove Branding', 'Priority Support'], false, 3],
        ])->each(fn (array $plan) => PricingPlan::updateOrCreate(
            ['name' => $plan[0]],
            ['price' => $plan[1], 'features' => $plan[2], 'is_popular' => $plan[3], 'sort_order' => $plan[4], 'is_active' => true],
        ));

        collect([
            ['Neha & Rohit', 'InviteCraft made our wedding invitations so beautiful and easy to share. Everyone loved the design!', 1],
            ['Anjali Sharma', 'The templates are stunning and the platform is super easy to use. Worth every penny!', 2],
            ['Vikram Patel', 'We created our Griha Pravesh invitation in just 10 minutes. Amazing experience!', 3],
        ])->each(fn (array $testimonial) => Testimonial::updateOrCreate(
            ['customer_name' => $testimonial[0]],
            ['message' => $testimonial[1], 'rating' => 5, 'sort_order' => $testimonial[2], 'is_active' => true],
        ));

        collect([
            ['How long does it take to create an invitation?', 'Most customers finish their invitation website in less than 10 minutes.', 1],
            ['Can I share my invitation on WhatsApp?', 'Yes, every plan includes a shareable link and QR code.', 2],
            ['Do you provide custom domain?', 'Custom domains are available with Pro and Business plans.', 3],
        ])->each(fn (array $faq) => Faq::updateOrCreate(
            ['question' => $faq[0]],
            ['answer' => $faq[1], 'sort_order' => $faq[2] ?? 2, 'is_active' => true],
        ));
    }
}
