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
        Schema::create('employee_infos', function (Blueprint $table) {
            $table->id();
            $table->uuid('employee_id');
            $table->foreign('employee_id')->references('id')->on('employees')->cascadeOnDelete();

            // Identification
            $table->string('control_no', 100)->nullable();
            $table->string('id_card_no', 100)->nullable();

            // Payment / banking
            $table->string('active_status', 50)->nullable()->default('Active');
            $table->string('payment_method', 50)->nullable();
            $table->string('account_no', 100)->nullable();
            $table->string('bank', 100)->nullable();
            $table->string('taxcode', 100)->nullable();
            $table->string('tin_no', 50)->nullable();
            $table->string('sss_no', 50)->nullable();
            $table->string('hdmf_no', 50)->nullable();
            $table->string('philhealth_no', 50)->nullable();
            $table->string('hmo_no', 50)->nullable();
            $table->string('email_personal')->nullable();
            $table->string('email_company')->nullable();

            // Employment details
            $table->date('resigned_date')->nullable();
            $table->date('regular_date')->nullable();
            $table->string('resign_process', 100)->nullable();
            $table->boolean('resign_on_next_payroll')->default(false);
            $table->string('paycode', 100)->nullable();
            $table->string('period_type', 100)->nullable();
            $table->string('paylevel', 100)->nullable();
            $table->string('job_grade', 100)->nullable();
            $table->decimal('cola', 12, 2)->nullable();
            $table->decimal('basic_pay_2', 12, 2)->nullable();

            // Position / Department tab
            $table->string('branch', 100)->nullable();
            $table->string('shuttle_location', 100)->nullable();
            $table->string('cost_center', 100)->nullable();
            $table->string('sub_cost_center', 100)->nullable();
            $table->string('schedule', 100)->nullable();
            $table->string('group_schedule', 100)->nullable();
            $table->boolean('allow_flexible_time')->default(false);
            $table->decimal('max_sick', 8, 2)->nullable();
            $table->decimal('max_vacation', 8, 2)->nullable();
            $table->decimal('max_sl', 8, 2)->nullable();
            $table->decimal('max_spl', 8, 2)->nullable();
            $table->decimal('max_pl', 8, 2)->nullable();
            $table->decimal('max_vawc', 8, 2)->nullable();
            $table->decimal('max_ml', 8, 2)->nullable();
            $table->decimal('max_bl', 8, 2)->nullable();
            $table->decimal('max_el', 8, 2)->nullable();

            // MFG Groups tab
            $table->string('cluster', 100)->nullable();
            $table->string('section', 100)->nullable();
            $table->string('sub_section', 100)->nullable();
            $table->string('group_name', 100)->nullable();
            $table->string('line_name', 100)->nullable();
            $table->string('mfg_position', 100)->nullable();

            // Projects tab
            $table->string('contract_ref', 100)->nullable();
            $table->string('project', 100)->nullable();
            $table->string('category', 100)->nullable();

            // Allowances tab
            $table->decimal('allowance_1', 12, 2)->nullable();
            $table->decimal('allowance_2', 12, 2)->nullable();
            $table->decimal('allowance_3', 12, 2)->nullable();
            $table->decimal('allowance_4', 12, 2)->nullable();
            $table->decimal('allowance_5', 12, 2)->nullable();
            $table->decimal('allow_transpo', 12, 2)->nullable();
            $table->decimal('allow_housing', 12, 2)->nullable();
            $table->decimal('allow_communication', 12, 2)->nullable();
            $table->decimal('allow_4_nt', 12, 2)->nullable();
            $table->decimal('allow_5_nt', 12, 2)->nullable();

            // Others tab — Override Table
            $table->boolean('override_sss_exclude')->default(false);
            $table->decimal('override_sss_employee', 12, 2)->nullable();
            $table->decimal('override_sss_employer', 12, 2)->nullable();
            $table->boolean('override_philhealth_exclude')->default(false);
            $table->decimal('override_philhealth_employee', 12, 2)->nullable();
            $table->decimal('override_philhealth_employer', 12, 2)->nullable();
            $table->boolean('override_pagibig_exclude')->default(false);
            $table->decimal('override_pagibig_employee', 12, 2)->nullable();
            $table->decimal('override_pagibig_employer', 12, 2)->nullable();
            $table->boolean('override_tax_exclude')->default(false);
            $table->boolean('pagibig_voluntary')->default(false);
            $table->decimal('pagibig_vol_amount', 12, 2)->nullable();
            $table->string('tax_computation_type', 30)->nullable()->default('annualized');
            
            $table->timestamps();
        });
        
        // Remove columns that were added to `employees` before failure (if any)
        Schema::table('employees', function(Blueprint $table) {
            $cols = ['control_no', 'id_card_no', 'active_status'];
            foreach($cols as $c) {
                if(Schema::hasColumn('employees', $c)) {
                    $table->dropColumn($c);
                }
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('employee_infos');
    }
};
