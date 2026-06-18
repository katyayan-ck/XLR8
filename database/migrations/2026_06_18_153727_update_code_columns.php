<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('xlr8_admin_employee', function (Blueprint $table) {
            // These are the columns causing "Data too long" errors
            $table->string('designation_code', 20)->nullable()->change();   // was varchar(10) → AST_ACC_MGR etc.
            $table->string('desig_code', 20)->nullable()->change();         // was varchar(10)
            $table->string('primary_branch_code', 10)->nullable()->change(); // was varchar(5) → LMM-WS etc.
            $table->string('primary_loc_code', 10)->nullable()->change();    // was varchar(5)

            // Future-proofing other code columns (recommended)
            $table->string('vertical_code', 10)->nullable()->change();
            $table->string('segment_code', 10)->nullable()->change();
            $table->string('sub_segment_code', 10)->nullable()->change();
        });

        // Also fix in xlr8_iam_roles (post_code) if it exists and is narrow
        if (Schema::hasTable('xlr8_iam_roles')) {
            Schema::table('xlr8_iam_roles', function (Blueprint $table) {
                $table->string('post_code', 30)->nullable()->change();
            });
        }
    }

    public function down(): void
    {
        Schema::table('xlr8_admin_employee', function (Blueprint $table) {
            $table->string('designation_code', 10)->nullable()->change();
            $table->string('desig_code', 10)->nullable()->change();
            $table->string('primary_branch_code', 5)->nullable()->change();
            $table->string('primary_loc_code', 5)->nullable()->change();
            $table->string('vertical_code', 10)->nullable()->change();
            $table->string('segment_code', 5)->nullable()->change();
            $table->string('sub_segment_code', 5)->nullable()->change();
        });

        if (Schema::hasTable('xlr8_iam_roles')) {
            Schema::table('xlr8_iam_roles', function (Blueprint $table) {
                $table->string('post_code', 20)->nullable()->change(); // revert if needed
            });
        }
    }
};