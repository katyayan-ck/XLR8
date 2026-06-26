<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // ========== LEAD SOURCES ==========
        Schema::create('xlr8_crm_lead_sources', function (Blueprint $table) {
            $table->id();
            $table->string('code', 50)->unique()->index();
            $table->string('name', 100);
            $table->text('description')->nullable();
            $table->boolean('is_active')->default(true)->index();
            $table->unsignedInteger('sort_order')->default(0)->index();

            $table->unsignedBigInteger('created_by')->nullable()->index();
            $table->unsignedBigInteger('updated_by')->nullable()->index();
            $table->unsignedBigInteger('deleted_by')->nullable()->index();

            $table->timestamps();
            $table->softDeletes();

            $table->index(['is_active', 'sort_order']);
            $table->index('created_at');
        });

        // ========== LEADS (Minimum Capture) ==========
        Schema::create('xlr8_crm_leads', function (Blueprint $table) {
            $table->id();
            $table->string('lead_no', 50)->unique()->index();
            $table->date('capture_date')->index();

            $table->unsignedBigInteger('source_id')->nullable()->index();
            $table->string('referral_details', 255)->nullable();

            $table->string('first_name', 100);
            $table->string('last_name', 100)->nullable();
            $table->string('mobile', 15)->index();
            $table->string('email', 150)->nullable();
            $table->string('occupation', 150)->nullable();

            $table->string('model_code', 50)->nullable()->index();
            $table->string('variant_code', 50)->nullable()->index();
            $table->string('color_code', 50)->nullable()->index();

            $table->date('expected_delivery_date')->nullable();
            $table->text('notes')->nullable();

            $table->string('status', 30)->default('new')->index(); // KeyValue LEAD_STATUS
            $table->string('priority', 20)->default('medium')->index();

            $table->unsignedBigInteger('verified_by')->nullable()->index();
            $table->dateTime('verified_at')->nullable();
            $table->text('conversion_notes')->nullable();

            $table->unsignedBigInteger('assigned_to')->nullable()->index(); // FSC user id
            $table->dateTime('assigned_at')->nullable();

            $table->unsignedBigInteger('created_by')->nullable()->index();
            $table->unsignedBigInteger('updated_by')->nullable()->index();
            $table->unsignedBigInteger('deleted_by')->nullable()->index();

            $table->timestamps();
            $table->softDeletes();

            $table->index(['status', 'assigned_to']);
            $table->index(['status', 'capture_date']);
            $table->index('mobile');
            $table->index(['model_code', 'variant_code']);
            $table->index('created_at');
        });

        // ========== ENQUIRIES ==========
        Schema::create('xlr8_crm_enquiries', function (Blueprint $table) {
            $table->id();
            $table->string('enquiry_no', 50)->unique()->index();
            $table->date('enquiry_date')->index();

            $table->unsignedBigInteger('lead_id')->nullable()->index();
            $table->string('person_code', 100)->nullable()->index(); // Immutable link to xlr8_admin_person

            $table->unsignedBigInteger('source_id')->nullable()->index();
            $table->string('referral_details', 255)->nullable();

            // Snapshot fields (Person is source of truth via person_code)
            $table->string('first_name', 100);
            $table->string('last_name', 100)->nullable();
            $table->string('mobile', 15)->index();
            $table->string('email', 150)->nullable();
            $table->string('occupation', 150)->nullable();

            $table->string('model_code', 50)->nullable()->index();
            $table->string('variant_code', 50)->nullable()->index();
            $table->string('color_code', 50)->nullable()->index();

            $table->string('place_of_registration', 100)->nullable();
            $table->string('registration_by', 20)->nullable();
            $table->string('insurance_by', 20)->nullable();
            $table->boolean('has_rsa')->default(false);
            $table->boolean('has_extended_warranty')->default(false);

            $table->date('expected_delivery_date')->nullable();
            $table->string('dms_enquiry_no', 50)->nullable()->index();

            $table->unsignedBigInteger('sales_consultant_id')->nullable()->index(); // Primary FSC

            $table->string('status', 30)->default('new')->index(); // KeyValue ENQUIRY_STATUS
            $table->string('lost_reason', 255)->nullable();
            $table->string('priority', 20)->default('medium')->index();

            $table->text('notes')->nullable();
            $table->text('conversion_notes')->nullable();

            $table->unsignedBigInteger('created_by')->nullable()->index();
            $table->unsignedBigInteger('updated_by')->nullable()->index();
            $table->unsignedBigInteger('deleted_by')->nullable()->index();

            $table->timestamps();
            $table->softDeletes();

            $table->index(['status', 'sales_consultant_id']);
            $table->index(['status', 'enquiry_date']);
            $table->index('mobile');
            $table->index(['model_code', 'variant_code']);
            $table->index('lead_id');
            $table->index('person_code');
            $table->index('created_at');
        });

        // ========== QUOTATIONS (Standard + Custom + Multilevel Approval) ==========
        Schema::create('xlr8_crm_quotations', function (Blueprint $table) {
            $table->id();
            $table->string('quotation_no', 50)->unique()->index();
            $table->unsignedBigInteger('enquiry_id')->nullable()->index();
            $table->string('person_code', 100)->nullable()->index();

            $table->string('model_code', 50)->nullable()->index();
            $table->string('variant_code', 50)->nullable()->index();
            $table->string('color_code', 50)->nullable()->index();

            $table->unsignedBigInteger('sales_consultant_id')->nullable()->index(); // FSC who created
            $table->unsignedBigInteger('assigned_to')->nullable()->index(); // Current approver

            $table->unsignedInteger('revision')->default(0);

            // Flexible data (matches old standard/requested/proposed + your pricing engine)
            $table->longText('standard_data')->nullable();   // JSON: ex-showroom, standard additions, standard discounts
            $table->longText('requested_data')->nullable();  // JSON: customer requested customizations (extra disc, insurance, RSA, accessories etc.)
            $table->longText('proposed_data')->nullable();   // JSON: final proposed after negotiation

            $table->decimal('onroad_price', 15, 2)->nullable();
            $table->decimal('invoice_price', 15, 2)->nullable();

            $table->string('status', 30)->default('raised')->index(); // KeyValue QUOTE_STATUS

            $table->text('fsc_last_remark')->nullable();
            $table->text('approver_last_remark')->nullable();

            $table->unsignedBigInteger('created_by')->nullable()->index();
            $table->unsignedBigInteger('updated_by')->nullable()->index();
            $table->unsignedBigInteger('deleted_by')->nullable()->index();

            $table->timestamps();
            $table->softDeletes();

            $table->index(['status', 'assigned_to']);
            $table->index(['status', 'enquiry_id']);
            $table->index('created_at');
        });

        // ========== QUOTE ACTIONS / HISTORY (Approval flow + Comments) ==========
        Schema::create('xlr8_crm_quote_actions', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('quotation_id')->index();
            $table->unsignedBigInteger('action_by')->index(); // User who performed action

            $table->string('action', 30)->index(); // KeyValue QUOTE_ACTION (REQUEST, PROPOSE, EDIT, COMMENT, INQUIRE, ANSWER, ESCALATE, APPROVE, REJECT, REOPEN, CANCEL etc.)

            $table->unsignedInteger('revision')->default(0);

            $table->longText('requested')->nullable(); // What was requested/proposed in this action
            $table->decimal('onroad', 15, 2)->nullable();

            $table->string('status', 30)->nullable()->index();

            $table->text('remarks')->nullable();

            $table->unsignedBigInteger('created_by')->nullable()->index();
            $table->unsignedBigInteger('updated_by')->nullable()->index();
            $table->unsignedBigInteger('deleted_by')->nullable()->index();

            $table->timestamps();
            $table->softDeletes();

            $table->index(['quotation_id', 'action']);
            $table->index('created_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('xlr8_crm_quote_actions');
        Schema::dropIfExists('xlr8_crm_quotations');
        Schema::dropIfExists('xlr8_crm_enquiries');
        Schema::dropIfExists('xlr8_crm_leads');
        Schema::dropIfExists('xlr8_crm_lead_sources');
    }
};