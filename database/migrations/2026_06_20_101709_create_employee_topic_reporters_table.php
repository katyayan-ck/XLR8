<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('xlr8_utils_empreporting_reporters', function (Blueprint $table) {
            $table->id();
            $table->string('employee_code', 50);
            $table->string('topic_code', 50);
            $table->string('reporting_to_code', 50);
            $table->json('scopes')->nullable();
            $table->json('attributes')->nullable();
            $table->unsignedTinyInteger('priority')->default(0);
            $table->boolean('is_active')->default(true);

            // Audit Columns
            $table->unsignedBigInteger('created_by')->nullable();
            $table->unsignedBigInteger('updated_by')->nullable();
            $table->unsignedBigInteger('deleted_by')->nullable();
            $table->timestamps();
            $table->softDeletes();

            // Short index name (to avoid 64 char limit)
            $table->index(['employee_code', 'topic_code', 'is_active'], 'idx_emp_reporter_lookup');

            // Unique constraint with short name
            $table->unique(
                ['employee_code', 'topic_code', 'reporting_to_code'], 
                'uq_emp_topic_reporter'
            );
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('xlr8_utils_empreporting_reporters');
    }
};