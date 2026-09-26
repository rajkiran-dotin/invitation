<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasColumn('invitation_ceremonies', 'venue_name')) {
            Schema::table('invitation_ceremonies', function (Blueprint $table): void {
                $table->string('venue_name')->nullable();
            });
        }

        if (! Schema::hasColumn('invitation_ceremonies', 'formatted_address')) {
            Schema::table('invitation_ceremonies', function (Blueprint $table): void {
                $table->string('formatted_address')->nullable();
            });
        }

        if (! Schema::hasColumn('invitation_ceremonies', 'google_place_id')) {
            Schema::table('invitation_ceremonies', function (Blueprint $table): void {
                $table->string('google_place_id')->nullable();
            });
        }

        if (! Schema::hasColumn('invitation_ceremonies', 'latitude')) {
            Schema::table('invitation_ceremonies', function (Blueprint $table): void {
                $table->decimal('latitude', 10, 7)->nullable();
            });
        }

        if (! Schema::hasColumn('invitation_ceremonies', 'longitude')) {
            Schema::table('invitation_ceremonies', function (Blueprint $table): void {
                $table->decimal('longitude', 10, 7)->nullable();
            });
        }

        if (! Schema::hasColumn('invitation_ceremonies', 'google_maps_url')) {
            Schema::table('invitation_ceremonies', function (Blueprint $table): void {
                $table->string('google_maps_url', 2048)->nullable();
            });
        }
    }

    public function down(): void
    {
        $columns = collect([
            'venue_name',
            'formatted_address',
            'google_place_id',
            'latitude',
            'longitude',
            'google_maps_url',
        ])->filter(fn (string $column): bool => Schema::hasColumn('invitation_ceremonies', $column))->all();

        if ($columns !== []) {
            Schema::table('invitation_ceremonies', function (Blueprint $table) use ($columns): void {
                $table->dropColumn($columns);
            });
        }
    }
};
