<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('xlr8_utils_empreporting_histories', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('employee_topic_reporter_id')->nullable();
            $table->string('employee_code', 50);
            $table->string('topic_code', 50);
            $table->string('field', 100);
            $table->json('old_value')->nullable();
            $table->json('new_value')->nullable();
            $table->unsignedBigInteger('changed_by')->nullable();
            $table->text('reason')->nullable();
            $table->timestamps();

            $table->index(['employee_code', 'topic_code'], 'idx_emp_reporter_history');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('xlr8_utils_empreporting_histories');
    }
};