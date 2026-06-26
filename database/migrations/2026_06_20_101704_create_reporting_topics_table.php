<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('xlr8_utils_empreporting_topics', function (Blueprint $table) {
            $table->id();
            $table->string('code', 50)->unique();
            $table->string('name');
            $table->text('description')->nullable();
            $table->json('required_attributes')->nullable();
            $table->boolean('is_active')->default(true);

            // Audit Columns
            $table->unsignedBigInteger('created_by')->nullable();
            $table->unsignedBigInteger('updated_by')->nullable();
            $table->unsignedBigInteger('deleted_by')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->index(['code', 'is_active'], 'idx_empreporting_topics_code_active');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('xlr8_utils_empreporting_topics');
    }
};