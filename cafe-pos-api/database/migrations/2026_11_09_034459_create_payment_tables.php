<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('payments', function (Blueprint $table) {
            $table->id();

            $table->foreignId('order_id')
                ->constrained()
                ->restrictOnDelete();

            $table->string('payment_number', 100)->unique();

            $table->string('payment_method', 30);
            $table->string('payment_provider', 50)->nullable();

            $table->string('provider_transaction_id', 150)
                ->nullable()->index();

            $table->string('provider_order_id', 150)
                ->nullable()->index();

            $table->decimal('amount', 15, 2);

            $table->string('status', 30)->default('pending');
            $table->string('payment_type', 30)->nullable();

            $table->text('qr_string')->nullable();
            $table->text('qr_url')->nullable();

            $table->dateTime('paid_at')->nullable();
            $table->dateTime('expired_at')->nullable();

            $table->text('failure_reason')->nullable();
            $table->json('provider_response')->nullable();

            $table->timestamps();

            $table->index(['order_id', 'status']);
        });

        Schema::create('payment_logs', function (Blueprint $table) {
            $table->id();

            $table->foreignId('payment_id')
                ->constrained()
                ->restrictOnDelete();

            $table->string('provider_event_id', 150)->nullable();
            $table->string('event_type', 100)->nullable();

            $table->json('payload');

            $table->boolean('is_processed')->default(false);
            $table->dateTime('processed_at')->nullable();

            $table->timestamps();

            $table->unique(
                ['payment_id', 'provider_event_id'],
                'payment_event_unique'
            );
        });

        Schema::create('refunds', function (Blueprint $table) {
            $table->id();

            $table->foreignId('payment_id')
                ->constrained()
                ->restrictOnDelete();

            $table->string('refund_number', 100)->unique();
            $table->string('provider_refund_id', 150)->nullable();

            $table->decimal('amount', 15, 2);
            $table->string('status', 30)->default('pending');
            $table->text('reason')->nullable();

            $table->dateTime('refunded_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('refunds');
        Schema::dropIfExists('payment_logs');
        Schema::dropIfExists('payments');
    }
};