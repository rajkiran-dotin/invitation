<?php

namespace App\Services;

use App\Models\Invitation;
use Illuminate\Support\Str;

class InvitationPublishingService
{
    public function publish(Invitation $invitation): Invitation
    {
        if (! $invitation->slug) {
            $invitation->slug = $this->uniqueSlug($invitation);
        }

        $invitation->forceFill([
            'status' => Invitation::Published,
            'published_at' => $invitation->published_at ?? now(),
        ])->save();

        return $invitation->refresh();
    }

    public function unpublish(Invitation $invitation): Invitation
    {
        $invitation->forceFill([
            'status' => Invitation::Unpublished,
        ])->save();

        return $invitation->refresh();
    }

    private function uniqueSlug(Invitation $invitation): string
    {
        $base = Str::slug(trim(($invitation->bride_name ?: 'bride').' '.($invitation->groom_name ?: 'groom'))) ?: 'invitation';
        $slug = $base;
        $suffix = 2;

        while (Invitation::query()
            ->where('slug', $slug)
            ->whereKeyNot($invitation->id)
            ->exists()) {
            $slug = $base.'-'.$suffix;
            $suffix++;
        }

        return $slug;
    }
}
