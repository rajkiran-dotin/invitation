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
        Schema::create('template_categories', function (Blueprint $table): void {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->text('description')->nullable();
            $table->boolean('is_active')->default(true);
            $table->unsignedInteger('sort_order')->default(0);
            $table->timestamps();
        });

        $defaultCategories = [
            ['Wedding', 1],
            ['Engagement', 2],
            ['Birthday', 3],
            ['Mundan', 4],
            ['Griha Pravesh', 5],
            ['Anniversary', 6],
            ['Baby Shower', 7],
            ['Naming Ceremony', 8],
            ['Reception', 9],
        ];

        foreach ($defaultCategories as [$name, $sortOrder]) {
            DB::table('template_categories')->updateOrInsert(
                ['slug' => Str::slug($name)],
                [
                    'name' => $name,
                    'description' => null,
                    'is_active' => true,
                    'sort_order' => $sortOrder,
                    'created_at' => now(),
                    'updated_at' => now(),
                ],
            );
        }

        if (Schema::hasTable('invitation_templates')) {
            $blockedCategories = ['haldi', 'mehendi', 'sangeet'];

            DB::table('invitation_templates')
                ->select('category')
                ->whereNotNull('category')
                ->where('category', '!=', '')
                ->distinct()
                ->orderBy('category')
                ->pluck('category')
                ->each(function (string $name) use ($blockedCategories): void {
                    $slug = Str::slug($name);

                    if ($slug === '') {
                        return;
                    }

                    $isBlocked = in_array(Str::lower($name), $blockedCategories, true);
                    $existingCategory = DB::table('template_categories')->where('slug', $slug)->first();

                    if ($existingCategory !== null) {
                        if ($isBlocked) {
                            DB::table('template_categories')->where('id', $existingCategory->id)->update([
                                'is_active' => false,
                                'sort_order' => 999,
                                'updated_at' => now(),
                            ]);
                        }

                        return;
                    }

                    DB::table('template_categories')->insert([
                        'name' => $name,
                        'slug' => $slug,
                        'description' => null,
                        'is_active' => ! $isBlocked,
                        'sort_order' => $isBlocked ? 999 : 50,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);
                });

            Schema::table('invitation_templates', function (Blueprint $table): void {
                $table->foreignId('category_id')->nullable()->after('category')->constrained('template_categories')->nullOnDelete();
            });

            DB::table('invitation_templates')
                ->whereNotNull('category')
                ->where('category', '!=', '')
                ->orderBy('id')
                ->get(['id', 'category'])
                ->each(function (object $template): void {
                    $categoryId = DB::table('template_categories')->where('slug', Str::slug($template->category))->value('id');

                    if ($categoryId !== null) {
                        DB::table('invitation_templates')->where('id', $template->id)->update(['category_id' => $categoryId]);
                    }
                });
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('invitation_templates') && Schema::hasColumn('invitation_templates', 'category_id')) {
            Schema::table('invitation_templates', function (Blueprint $table): void {
                $table->dropConstrainedForeignId('category_id');
            });
        }

        Schema::dropIfExists('template_categories');
    }
};
