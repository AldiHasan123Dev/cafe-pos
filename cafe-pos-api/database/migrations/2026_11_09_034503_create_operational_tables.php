<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('expense_categories', function (Blueprint $table) {
            $table->id();
            $table->string('name', 100)->unique();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        Schema::create('expenses', function (Blueprint $table) {
            $table->id();

            $table->foreignId('expense_category_id')
                ->constrained()
                ->restrictOnDelete();

            $table->foreignId('user_id')
                ->constrained()
                ->restrictOnDelete();

            $table->string('expense_number', 50)->unique();
            $table->string('description');
            $table->decimal('amount', 15, 2);
            $table->date('expense_date');

            $table->timestamps();

            $table->index('expense_date');
        });

        Schema::create('reservations', function (Blueprint $table) {
            $table->id();

            $table->foreignId('dining_table_id')
                ->constrained()
                ->restrictOnDelete();

            $table->foreignId('user_id')
                ->nullable()
                ->constrained()
                ->nullOnDelete();

            $table->string('customer_name', 100);
            $table->string('customer_phone', 20)->nullable();

            $table->unsignedSmallInteger('guest_count');
            $table->dateTime('reservation_at');

            $table->string('status', 30)->default('pending');
            $table->text('notes')->nullable();

            $table->timestamps();

            $table->index(['dining_table_id', 'reservation_at']);
        });

        Schema::create('settings', function (Blueprint $table) {
            $table->id();
            $table->string('key')->unique();
            $table->text('value')->nullable();
            $table->string('type', 30)->default('string');
            $table->timestamps();
        });

        Schema::create('audit_logs', function (Blueprint $table) {
            $table->id();

            $table->foreignId('user_id')
                ->nullable()
                ->constrained()
                ->nullOnDelete();

            $table->string('action', 100);
            $table->string('module', 100);
            $table->unsignedBigInteger('reference_id')->nullable();

            $table->json('old_values')->nullable();
            $table->json('new_values')->nullable();

            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();

            $table->timestamps();

            $table->index(['module', 'reference_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('audit_logs');
        Schema::dropIfExists('settings');
        Schema::dropIfExists('reservations');
        Schema::dropIfExists('expenses');
        Schema::dropIfExists('expense_categories');
    }
};