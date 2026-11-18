<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('categories', function (Blueprint $table) {
            $table->id();
            $table->string('name', 100);
            $table->text('description')->nullable();
            $table->string('image')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('products', function (Blueprint $table) {
            $table->id();

            $table->foreignId('category_id')
                ->constrained()
                ->restrictOnDelete();

            $table->string('sku', 50)->unique();
            $table->string('name', 150);
            $table->text('description')->nullable();
            $table->string('image')->nullable();

            $table->decimal('price', 15, 2);
            $table->decimal('cost_price', 15, 2)->default(0);

            $table->boolean('is_available')->default(true);
            $table->boolean('is_active')->default(true);

            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('product_variants', function (Blueprint $table) {
            $table->id();

            $table->foreignId('product_id')
                ->constrained()
                ->cascadeOnDelete();

            $table->string('name', 100);
            $table->decimal('additional_price', 15, 2)->default(0);
            $table->boolean('is_active')->default(true);

            $table->timestamps();
        });

        Schema::create('modifier_groups', function (Blueprint $table) {
            $table->id();
            $table->string('name', 100);

            $table->string('selection_type', 20)->default('single');

            $table->boolean('is_required')->default(false);
            $table->unsignedSmallInteger('min_selection')->default(0);
            $table->unsignedSmallInteger('max_selection')->default(1);

            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        Schema::create('modifiers', function (Blueprint $table) {
            $table->id();

            $table->foreignId('modifier_group_id')
                ->constrained()
                ->cascadeOnDelete();

            $table->string('name', 100);
            $table->decimal('additional_price', 15, 2)->default(0);
            $table->boolean('is_active')->default(true);

            $table->timestamps();
        });

        Schema::create('product_modifier_groups', function (Blueprint $table) {
            $table->id();

            $table->foreignId('product_id')
                ->constrained()
                ->cascadeOnDelete();

            $table->foreignId('modifier_group_id')
                ->constrained()
                ->cascadeOnDelete();

            $table->unique(['product_id', 'modifier_group_id']);
            $table->timestamps();
        });

        Schema::create('dining_tables', function (Blueprint $table) {
            $table->id();
            $table->string('table_number', 20)->unique();
            $table->unsignedSmallInteger('capacity')->default(2);
            $table->string('status', 20)->default('available');
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('dining_tables');
        Schema::dropIfExists('product_modifier_groups');
        Schema::dropIfExists('modifiers');
        Schema::dropIfExists('modifier_groups');
        Schema::dropIfExists('product_variants');
        Schema::dropIfExists('products');
        Schema::dropIfExists('categories');
    }
};