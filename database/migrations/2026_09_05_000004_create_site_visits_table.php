<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('site_visits', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->string('session_id')->nullable();
            $table->string('ip_hash')->nullable();
            $table->string('path');
            $table->string('route_name')->nullable();
            $table->text('referrer')->nullable();
            $table->text('user_agent')->nullable();
            $table->timestamp('visited_at');
            $table->timestamps();

            $table->index('user_id');
            $table->index('session_id');
            $table->index('visited_at');
            $table->index('created_at');
            $table->index('path');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('site_visits');
    }
};
