<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasColumn('invitation_templates', 'view_name')) {
            Schema::table('invitation_templates', function (Blueprint $table): void {
                $table->string('view_name')->default('invitations.public.default')->after('theme_class');
            });
        }

        Schema::create('invitations', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('template_id')->constrained('invitation_templates')->cascadeOnDelete();
            $table->string('bride_name')->nullable();
            $table->string('groom_name')->nullable();
            $table->string('bride_photo_path')->nullable();
            $table->string('groom_photo_path')->nullable();
            $table->string('hero_photo_path')->nullable();
            $table->string('bride_father_name')->nullable();
            $table->string('bride_mother_name')->nullable();
            $table->string('groom_father_name')->nullable();
            $table->string('groom_mother_name')->nullable();
            $table->date('wedding_date')->nullable();
            $table->time('wedding_time')->nullable();
            $table->string('title')->nullable();
            $table->text('message')->nullable();
            $table->string('venue_name')->nullable();
            $table->string('formatted_address')->nullable();
            $table->string('google_place_id')->nullable();
            $table->decimal('latitude', 10, 7)->nullable();
            $table->decimal('longitude', 10, 7)->nullable();
            $table->string('google_maps_url')->nullable();
            $table->json('settings')->nullable();
            $table->string('slug')->nullable()->unique();
            $table->unsignedBigInteger('views')->default(0);
            $table->string('status')->default('draft');
            $table->timestamp('published_at')->nullable();
            $table->timestamps();

            $table->index(['user_id', 'status']);
            $table->index('template_id');
        });

        Schema::create('invitation_ceremonies', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('invitation_id')->constrained()->cascadeOnDelete();
            $table->string('name');
            $table->date('date');
            $table->time('start_time')->nullable();
            $table->time('end_time')->nullable();
            $table->text('description')->nullable();
            $table->string('dress_code')->nullable();
            $table->string('image_path')->nullable();
            $table->string('venue_name')->nullable();
            $table->string('formatted_address')->nullable();
            $table->string('google_place_id')->nullable();
            $table->decimal('latitude', 10, 7)->nullable();
            $table->decimal('longitude', 10, 7)->nullable();
            $table->string('google_maps_url')->nullable();
            $table->unsignedInteger('sort_order')->default(0);
            $table->timestamps();

            $table->index(['invitation_id', 'sort_order']);
        });

        Schema::create('invitation_gallery_images', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('invitation_id')->constrained()->cascadeOnDelete();
            $table->string('image_path');
            $table->string('caption')->nullable();
            $table->string('category')->nullable();
            $table->unsignedInteger('sort_order')->default(0);
            $table->timestamps();

            $table->index(['invitation_id', 'sort_order']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('invitation_gallery_images');
        Schema::dropIfExists('invitation_ceremonies');
        Schema::dropIfExists('invitations');

        if (Schema::hasColumn('invitation_templates', 'view_name')) {
            Schema::table('invitation_templates', function (Blueprint $table): void {
                $table->dropColumn('view_name');
            });
        }
    }
};
