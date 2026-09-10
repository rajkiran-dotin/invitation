<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('invitation_templates', function (Blueprint $table): void {
            $table->string('slug')->nullable()->unique()->after('name');
            $table->text('description')->nullable()->after('category_id');
            $table->string('preview_image')->nullable()->after('description');
            $table->string('demo_url')->nullable()->after('preview_image');
            $table->json('features')->nullable()->after('demo_url');
            $table->boolean('is_premium')->default(true)->after('features');
        });

        DB::table('invitation_templates')
            ->orderBy('id')
            ->get(['id', 'name', 'slug'])
            ->each(function (object $template): void {
                if ($template->slug !== null && $template->slug !== '') {
                    return;
                }

                $baseSlug = Str::slug($template->name) ?: 'template';
                $slug = $baseSlug;
                $suffix = 2;

                while (DB::table('invitation_templates')->where('slug', $slug)->where('id', '!=', $template->id)->exists()) {
                    $slug = $baseSlug.'-'.$suffix;
                    $suffix++;
                }

                DB::table('invitation_templates')->where('id', $template->id)->update([
                    'slug' => $slug,
                    'description' => 'Beautiful digital invitation template for your special celebration.',
                    'preview_image' => '/images/templates/'.$slug.'.svg',
                    'features' => json_encode(['Responsive invitation website', 'Photo gallery ready', 'WhatsApp share link']),
                    'is_premium' => true,
                    'updated_at' => now(),
                ]);
            });
    }

    public function down(): void
    {
        Schema::table('invitation_templates', function (Blueprint $table): void {
            $table->dropColumn(['slug', 'description', 'preview_image', 'demo_url', 'features', 'is_premium']);
        });
    }
};
