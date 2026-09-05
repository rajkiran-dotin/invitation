<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('purchases', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('invitation_template_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('pricing_plan_id')->nullable()->constrained()->nullOnDelete();
            $table->string('product_name')->nullable();
            $table->decimal('amount', 10, 2)->default(0);
            $table->string('currency', 3)->default('INR');
            $table->string('payment_status')->default('pending');
            $table->string('gateway')->nullable();
            $table->string('transaction_id')->nullable();
            $table->timestamp('purchased_at')->nullable();
            $table->timestamps();

            $table->index('user_id');
            $table->index('invitation_template_id');
            $table->index('pricing_plan_id');
            $table->index('payment_status');
            $table->index('purchased_at');
            $table->index('created_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('purchases');
    }
};
