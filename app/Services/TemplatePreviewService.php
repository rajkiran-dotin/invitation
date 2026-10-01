<?php

namespace App\Services;

use App\Models\Invitation;
use App\Models\InvitationCeremony;
use App\Models\InvitationGalleryImage;
use App\Models\InvitationTemplate;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Str;

class TemplatePreviewService
{
    public function invitationFor(InvitationTemplate $template): Invitation
    {
        $invitation = new Invitation;

        $invitation->forceFill([
            'user_id' => 0,
            'template_id' => $template->id,
            'bride_name' => 'Priya',
            'groom_name' => 'Rahul',
            'bride_photo_path' => 'https://images.unsplash.com/photo-1583391733981-849840c5d628?auto=format&fit=crop&w=900&q=80',
            'groom_photo_path' => 'https://images.unsplash.com/photo-1610173827043-62b52d7c2300?auto=format&fit=crop&w=900&q=80',
            'hero_photo_path' => 'https://images.unsplash.com/photo-1587271636175-90d58cdad458?auto=format&fit=crop&w=1800&q=80',
            'bride_father_name' => 'Mr. Anil Sharma',
            'bride_mother_name' => 'Mrs. Kavita Sharma',
            'groom_father_name' => 'Mr. Suresh Mehta',
            'groom_mother_name' => 'Mrs. Neeta Mehta',
            'wedding_date' => '2036-12-25',
            'wedding_time' => '19:00',
            'title' => 'Priya & Rahul Wedding Invitation',
            'message' => 'Together with their families, Priya and Rahul invite you to celebrate an evening of blessings, rituals, music, and love.',
            'venue_name' => 'The Grand Palace',
            'formatted_address' => 'Jaipur, Rajasthan',
            'google_maps_url' => 'https://www.google.com/maps/search/?api=1&query=Jaipur%20Rajasthan',
            'settings' => [
                'music_enabled' => true,
                'rsvp_enabled' => true,
                'gallery_enabled' => true,
                'story_enabled' => true,
                'family_enabled' => true,
            ],
            'status' => Invitation::Published,
            'published_at' => now(),
        ]);

        $invitation->setRelation('template', $template);
        $invitation->setRelation('ceremonies', new Collection($this->ceremonies()));
        $invitation->setRelation('galleryImages', new Collection($this->galleryImages()));

        return $invitation;
    }

    public function publicViewFor(InvitationTemplate $template): string
    {
        $view = $template->view_name ?: 'invitations.public.default';

        return view()->exists($view) ? $view : 'invitations.public.default';
    }

    private function ceremonies(): array
    {
        return collect([
            ['Haldi', '2036-12-23', '10:00', 'Courtyard Lawn', 'A sunlit morning of turmeric, laughter, and family blessings.'],
            ['Mehendi', '2036-12-23', '17:00', 'Garden Pavilion', 'Intricate henna, folk songs, and an evening dressed in color.'],
            ['Sangeet', '2036-12-24', '19:30', 'Royal Ballroom', 'Music, dance, and performances from both families.'],
            ['Wedding', '2036-12-25', '19:00', 'Mandap Gardens', 'Sacred vows, floral decor, and blessings beneath the mandap.'],
            ['Reception', '2036-12-26', '20:00', 'Grand Banquet Hall', 'A formal dinner reception to celebrate the newlyweds.'],
        ])->map(function (array $ceremony, int $index): InvitationCeremony {
            $model = new InvitationCeremony;
            $model->forceFill([
                'name' => $ceremony[0],
                'slug' => Str::slug($ceremony[0]),
                'date' => $ceremony[1],
                'start_time' => $ceremony[2],
                'description' => $ceremony[4],
                'venue_name' => $ceremony[3],
                'formatted_address' => 'Jaipur, Rajasthan',
                'google_maps_url' => 'https://www.google.com/maps/search/?api=1&query='.rawurlencode($ceremony[3].', Jaipur, Rajasthan'),
                'sort_order' => $index + 1,
            ]);

            return $model;
        })->all();
    }

    private function galleryImages(): array
    {
        return collect([
            ['https://images.unsplash.com/photo-1519741497674-611481863552?auto=format&fit=crop&w=900&q=80', 'Couple celebration'],
            ['https://images.unsplash.com/photo-1523438885200-e635ba2c371e?auto=format&fit=crop&w=900&q=80', 'Wedding decor'],
            ['https://images.unsplash.com/photo-1511285560929-80b456fea0bc?auto=format&fit=crop&w=900&q=80', 'Wedding rings'],
            ['https://images.unsplash.com/photo-1465495976277-4387d4b0e4a6?auto=format&fit=crop&w=900&q=80', 'Wedding lights'],
        ])->map(function (array $image, int $index): InvitationGalleryImage {
            $model = new InvitationGalleryImage;
            $model->forceFill([
                'image_path' => $image[0],
                'caption' => $image[1],
                'category' => 'Gallery',
                'sort_order' => $index,
            ]);

            return $model;
        })->all();
    }
}
