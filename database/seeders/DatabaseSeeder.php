<?php

namespace Database\Seeders;

use App\Models\Faq;
use App\Models\InvitationTemplate;
use App\Models\PricingPlan;
use App\Models\Testimonial;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

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

        collect([
            ['Royal Wedding', 'Wedding', 'royal', 1],
            ['Floral Bliss', 'Wedding', 'floral', 2],
            ['Classic Elegance', 'Wedding', 'classic', 3],
            ['Minimal Love', 'Engagement', 'minimal', 4],
            ['Modern Chic', 'Birthday', 'modern', 5],
            ['Pastel Dream', 'Griha Pravesh', 'pastel', 6],
        ])->each(fn (array $template) => InvitationTemplate::updateOrCreate(
            ['name' => $template[0]],
            ['category' => $template[1], 'theme_class' => $template[2], 'price' => 499, 'sort_order' => $template[3], 'is_active' => true],
        ));

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
            ['answer' => $faq[1], 'sort_order' => $faq[2], 'is_active' => true],
        ));
    }
}
