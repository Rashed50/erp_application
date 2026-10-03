<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Groups permissions for the role add/edit screens.
        Schema::create('permission_categories', function (Blueprint $table) {
            $table->id();
            $table->string('name', 100)->unique();
            $table->timestamps();
        });

        // A permission belongs to at most one category.
        Schema::create('permission_category_relations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('per_cate_id')->constrained('permission_categories')->cascadeOnDelete();
            $table->foreignId('permission_id')->unique()->constrained('permissions')->cascadeOnDelete();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('permission_category_relations');
        Schema::dropIfExists('permission_categories');
    }
};
