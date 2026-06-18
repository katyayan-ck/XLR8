<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::dropIfExists('xlr8_admin_emp_branch_pivot');
        Schema::dropIfExists('xlr8_admin_emp_department_pivot');
        Schema::dropIfExists('xlr8_admin_emp_division_pivot');
        Schema::dropIfExists('xlr8_admin_emp_location_pivot');
        Schema::dropIfExists('xlr8_admin_emp_segment_pivot');
        Schema::dropIfExists('xlr8_admin_emp_sub_segment_pivot');
        Schema::dropIfExists('xlr8_admin_emp_vertical_pivot');
        Schema::dropIfExists('xlr8_iam_user_data_scopes'); // old scoping table
    }

    public function down(): void
    {
        // Recreate only if truly needed later (not recommended)
    }
};