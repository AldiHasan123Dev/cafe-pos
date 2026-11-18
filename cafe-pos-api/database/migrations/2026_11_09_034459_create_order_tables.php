<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('cashier_shifts', function (Blueprint $table) {
            $table->id();

            $table->foreignId('user_id')
                ->constrained()
                ->restrictOnDelete();

            $table->decimal('opening_cash', 15, 2);
            $table->decimal('closing_cash', 15, 2)->nullable();
            $table->decimal('expected_cash', 15, 2)->nullable();
            $table->decimal('difference', 15, 2)->nullable();

            $table->dateTime('opened_at');
            $table->dateTime('closed_at')->nullable();

            $table->string('status', 20)->default('open');
            $table->text('notes')->nullable();

            $table->timestamps();

            $table->index(['user_id', 'status']);
        });

        Schema::create('discounts', function (Blueprint $table) {
            $table->id();
            $table->string('name', 100);
            $table->string('code', 50)->nullable()->unique();
            $table->string('discount_type', 20);
            $table->decimal('value', 15, 2);
            $table->decimal('max_discount', 15, 2)->nullable();
            $table->decimal('min_purchase', 15, 2)->default(0);
            $table->dateTime('start_at')->nullable();
            $table->dateTime('end_at')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        Schema::create('orders', function (Blueprint $table) {
            $table->id();

            $table->string('order_number', 50)->unique();

            $table->foreignId('user_id')
                ->constrained()
                ->restrictOnDelete();

            $table->foreignId('cashier_shift_id')
                ->nullable()
                ->constrained()
                ->restrictOnDelete();

            $table->foreignId('table_id')
                ->nullable()
                ->constrained('dining_tables')
                ->restrictOnDelete();

            $table->foreignId('discount_id')
                ->nullable()
                ->constrained()
                ->nullOnDelete();

            $table->string('order_type', 20);
            $table->string('customer_name', 100)->nullable();
            $table->string('customer_phone', 20)->nullable();

            $table->string('order_status', 30)->default('pending');
            $table->string('payment_status', 30)->default('unpaid');

            $table->decimal('subtotal', 15, 2)->default(0);
            $table->decimal('discount_amount', 15, 2)->default(0);
            $table->decimal('tax_amount', 15, 2)->default(0);
            $table->decimal('service_charge', 15, 2)->default(0);
            $table->decimal('grand_total', 15, 2)->default(0);

            $table->text('notes')->nullable();
            $table->dateTime('confirmed_at')->nullable();
            $table->dateTime('completed_at')->nullable();
            $table->dateTime('cancelled_at')->nullable();
            $table->text('cancel_reason')->nullable();

            $table->timestamps();

            $table->index(['created_at', 'order_status']);
            $table->index(['payment_status', 'created_at']);
        });

        Schema::create('order_items', function (Blueprint $table) {
            $table->id();

            $table->foreignId('order_id')
                ->constrained()
                ->cascadeOnDelete();

            $table->foreignId('product_id')
                ->nullable()
                ->constrained()
                ->nullOnDelete();

            $table->foreignId('product_variant_id')
                ->nullable()
                ->constrained()
                ->nullOnDelete();

            $table->string('product_name', 150);
            $table->string('variant_name', 100)->nullable();

            $table->unsignedInteger('quantity');

            $table->decimal('base_price', 15, 2);
            $table->decimal('variant_price', 15, 2)->default(0);
            $table->decimal('unit_price', 15, 2);
            $table->decimal('subtotal', 15, 2);

            $table->text('notes')->nullable();
            $table->string('item_status', 30)->default('waiting');

            $table->timestamps();
        });

        Schema::create('order_item_modifiers', function (Blueprint $table) {
            $table->id();

            $table->foreignId('order_item_id')
                ->constrained()
                ->cascadeOnDelete();

            $table->foreignId('modifier_id')
                ->nullable()
                ->constrained()
                ->nullOnDelete();

            $table->string('modifier_name', 100);
            $table->decimal('additional_price', 15, 2)->default(0);
            $table->unsignedInteger('quantity')->default(1);

            $table->timestamps();
        });

        Schema::create('order_status_histories', function (Blueprint $table) {
            $table->id();

            $table->foreignId('order_id')
                ->constrained()
                ->cascadeOnDelete();

            $table->foreignId('user_id')
                ->nullable()
                ->constrained()
                ->nullOnDelete();

            $table->string('old_status', 30)->nullable();
            $table->string('new_status', 30);
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('order_status_histories');
        Schema::dropIfExists('order_item_modifiers');
        Schema::dropIfExists('order_items');
        Schema::dropIfExists('orders');
        Schema::dropIfExists('discounts');
        Schema::dropIfExists('cashier_shifts');
    }
};