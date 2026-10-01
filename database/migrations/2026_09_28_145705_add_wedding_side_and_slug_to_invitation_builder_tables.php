<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasColumn('invitations', 'wedding_side')) {
            Schema::table('invitations', function (Blueprint $table): void {
                $table->string('wedding_side')->nullable();
            });
        }

        if (! Schema::hasColumn('invitation_ceremonies', 'slug')) {
            Schema::table('invitation_ceremonies', function (Blueprint $table): void {
                $table->string('slug')->nullable();
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasColumn('invitation_ceremonies', 'slug')) {
            Schema::table('invitation_ceremonies', function (Blueprint $table): void {
                $table->dropColumn('slug');
            });
        }

        if (Schema::hasColumn('invitations', 'wedding_side')) {
            Schema::table('invitations', function (Blueprint $table): void {
                $table->dropColumn('wedding_side');
            });
        }
    }
};
