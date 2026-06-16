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
        Schema::table('xlr8_admin_designation', function (Blueprint $table) {
            $table->string('parent_desig_code', 50)->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('xlr8_admin_designation', function (Blueprint $table) {
            $table->string('parent_desig_code', 10)->nullable()->change();
        });
    }
};
