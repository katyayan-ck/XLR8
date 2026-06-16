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
        DB::statement("
            ALTER TABLE xlr8_admin_department
            CHANGE parent_department_id parent_department_code VARCHAR(10) NULL
        ");

        DB::statement("
            ALTER TABLE xlr8_admin_department
            CHANGE branch_id branch_code VARCHAR(10) NULL
        ");

        DB::statement("
            ALTER TABLE xlr8_admin_department
            CHANGE head_id head_code VARCHAR(10) NULL
        ");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::statement("
            ALTER TABLE xlr8_admin_department
            CHANGE parent_department_code parent_department_id BIGINT UNSIGNED NULL
        ");

        DB::statement("
            ALTER TABLE xlr8_admin_department
            CHANGE branch_code branch_id BIGINT UNSIGNED NULL
        ");

        DB::statement("
            ALTER TABLE xlr8_admin_department
            CHANGE head_code head_id BIGINT UNSIGNED NULL
        ");
    }
};
