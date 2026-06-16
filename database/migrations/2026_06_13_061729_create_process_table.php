<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Creates missing xlr8_iam_process table (code-based relations, full audit + soft deletes to match BaseModel).
     * Updates related IAM tables for missing audit keys (created_by/updated_by/deleted_by/deleted_at) and switches to code-based relation keys (no DB FKs, relations in ORM only).
     */
    public function up(): void
    {
        // 1. Create Process table (code-based, no FK constraints)
        if (!Schema::hasTable('xlr8_iam_process')) {
            Schema::create('xlr8_iam_process', function (Blueprint $table) {
                $table->id();
                $table->string('module_code')->nullable()->index();
                $table->string('code')->unique();
                $table->string('name');
                $table->text('description')->nullable();
                $table->boolean('is_active')->default(true)->index();

                // Full audit fields matching BaseModel expectations
                $table->unsignedBigInteger('created_by')->nullable()->index();
                $table->unsignedBigInteger('updated_by')->nullable()->index();
                $table->unsignedBigInteger('deleted_by')->nullable()->index();

                $table->timestamps();
                $table->softDeletes();

                $table->index(['code', 'is_active']);
            });
        }

        // 2. Update xlr8_iam_permissions: switch to code keys + add missing audit
        if (Schema::hasTable('xlr8_iam_permissions')) {
            Schema::table('xlr8_iam_permissions', function (Blueprint $table) {
                // Add code-based relation columns (nullable for existing data compatibility)
                if (!Schema::hasColumn('xlr8_iam_permissions', 'module_code')) {
                    $table->string('module_code')->nullable()->after('guard_name');
                }
                if (!Schema::hasColumn('xlr8_iam_permissions', 'process_code')) {
                    $table->string('process_code')->nullable()->after('module_code');
                }

                // Add missing audit columns if not present (BaseModel style)
                if (!Schema::hasColumn('xlr8_iam_permissions', 'created_by')) {
                    $table->unsignedBigInteger('created_by')->nullable()->after('updated_at');
                }
                if (!Schema::hasColumn('xlr8_iam_permissions', 'updated_by')) {
                    $table->unsignedBigInteger('updated_by')->nullable()->after('created_by');
                }
                if (!Schema::hasColumn('xlr8_iam_permissions', 'deleted_by')) {
                    $table->unsignedBigInteger('deleted_by')->nullable()->after('updated_by');
                }
                if (!Schema::hasColumn('xlr8_iam_permissions', 'deleted_at')) {
                    $table->timestamp('deleted_at')->nullable()->after('deleted_by');
                }

                // Drop legacy id-based columns (data was NULL anyway; safe)
                if (Schema::hasColumn('xlr8_iam_permissions', 'module_id')) {
                    $table->dropColumn('module_id');
                }
                if (Schema::hasColumn('xlr8_iam_permissions', 'process_id')) {
                    $table->dropColumn('process_id');
                }
            });
        }

        // 3. Ensure xlr8_iam_module has full audit (if missing columns)
        if (Schema::hasTable('xlr8_iam_module')) {
            Schema::table('xlr8_iam_module', function (Blueprint $table) {
                if (!Schema::hasColumn('xlr8_iam_module', 'created_by')) {
                    $table->unsignedBigInteger('created_by')->nullable()->after('is_active');
                }
                if (!Schema::hasColumn('xlr8_iam_module', 'updated_by')) {
                    $table->unsignedBigInteger('updated_by')->nullable()->after('created_by');
                }
                if (!Schema::hasColumn('xlr8_iam_module', 'deleted_by')) {
                    $table->unsignedBigInteger('deleted_by')->nullable()->after('updated_by');
                }
                if (!Schema::hasColumn('xlr8_iam_module', 'deleted_at')) {
                    $table->timestamp('deleted_at')->nullable()->after('deleted_by');
                }
            });
        }

        // Note: xlr8_admin_designation (used as Roles) already has full audit + code-based parent_desig_code. No changes needed.
        // Spatie pivot tables (role_has_permissions etc.) left as-is (id-based internal to Spatie).
    }

    public function down(): void
    {
        // Reverse order
        if (Schema::hasTable('xlr8_iam_permissions')) {
            Schema::table('xlr8_iam_permissions', function (Blueprint $table) {
                if (Schema::hasColumn('xlr8_iam_permissions', 'deleted_at')) $table->dropColumn('deleted_at');
                if (Schema::hasColumn('xlr8_iam_permissions', 'deleted_by')) $table->dropColumn('deleted_by');
                if (Schema::hasColumn('xlr8_iam_permissions', 'updated_by')) $table->dropColumn('updated_by');
                if (Schema::hasColumn('xlr8_iam_permissions', 'created_by')) $table->dropColumn('created_by');
                if (Schema::hasColumn('xlr8_iam_permissions', 'process_code')) $table->dropColumn('process_code');
                if (Schema::hasColumn('xlr8_iam_permissions', 'module_code')) $table->dropColumn('module_code');
                // Note: cannot easily restore dropped module_id/process_id without data backup
            });
        }

        if (Schema::hasTable('xlr8_iam_module')) {
            Schema::table('xlr8_iam_module', function (Blueprint $table) {
                if (Schema::hasColumn('xlr8_iam_module', 'deleted_at')) $table->dropColumn('deleted_at');
                if (Schema::hasColumn('xlr8_iam_module', 'deleted_by')) $table->dropColumn('deleted_by');
                if (Schema::hasColumn('xlr8_iam_module', 'updated_by')) $table->dropColumn('updated_by');
                if (Schema::hasColumn('xlr8_iam_module', 'created_by')) $table->dropColumn('created_by');
            });
        }

        Schema::dropIfExists('xlr8_iam_process');
    }
};