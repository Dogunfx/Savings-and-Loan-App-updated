<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateLoanProductsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('loan_products', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->bigInteger('created_by_id')->unsigned()->nullable();
            $table->bigInteger('currency_id')->unsigned();
            $table->bigInteger('loan_disbursement_channel_id')->unsigned()->nullable();
            $table->bigInteger('loan_transaction_processing_strategy_id')->unsigned();
            $table->bigInteger('fund_id')->unsigned();
            $table->bigInteger('fund_source_chart_of_account_id')->unsigned()->nullable();
            $table->bigInteger('loan_portfolio_chart_of_account_id')->unsigned()->nullable();
            $table->bigInteger('interest_receivable_chart_of_account_id')->unsigned()->nullable();
            $table->bigInteger('penalties_receivable_chart_of_account_id')->unsigned()->nullable();
            $table->bigInteger('fees_receivable_chart_of_account_id')->unsigned()->nullable();
            $table->bigInteger('fees_chart_of_account_id')->unsigned()->nullable();
            $table->bigInteger('overpayments_chart_of_account_id')->unsigned()->nullable();
            $table->bigInteger('suspended_income_chart_of_account_id')->unsigned()->nullable();
            $table->bigInteger('income_from_interest_chart_of_account_id')->unsigned()->nullable();
            $table->bigInteger('income_from_penalties_chart_of_account_id')->unsigned()->nullable();
            $table->bigInteger('income_from_fees_chart_of_account_id')->unsigned()->nullable();
            $table->bigInteger('income_from_recovery_chart_of_account_id')->unsigned()->nullable();
            $table->bigInteger('losses_written_off_chart_of_account_id')->unsigned()->nullable();
            $table->bigInteger('interest_written_off_chart_of_account_id')->unsigned()->nullable();
            $table->text('name');
            $table->text('short_name')->nullable();
            $table->text('description')->nullable();
            $table->integer('decimals')->nullable();
            $table->integer('instalment_multiple_of')->default(1)->nullable();
            $table->decimal('minimum_principal', 65, 6);
            $table->decimal('default_principal', 65, 6);
            $table->decimal('maximum_principal', 65, 6);
            $table->integer('minimum_loan_term');
            $table->integer('default_loan_term');
            $table->integer('maximum_loan_term');
            $table->integer('repayment_frequency');
            $table->enum('repayment_frequency_type', array(
                'days',
                'weeks',
                'months',
                'years',
            ));
            $table->decimal('minimum_interest_rate', 65, 6);
            $table->decimal('default_interest_rate', 65, 6);
            $table->decimal('maximum_interest_rate', 65, 6);
            $table->enum('interest_rate_type', array(
                'day',
                'week',
                'month',
                'year',
            ));
            $table->tinyInteger('enable_balloon_payments')->default(0);
            $table->tinyInteger('allow_schedule_adjustments')->default(0);
            $table->integer('grace_on_principal_paid')->default(0);
            $table->integer('grace_on_interest_paid')->default(0);
            $table->integer('grace_on_interest_charged')->default(0);
            $table->tinyInteger('allow_custom_grace_period')->default(0);
            $table->tinyInteger('allow_topup')->default(0);
            $table->enum('interest_methodology', ['flat', 'declining_balance']);
            $table->tinyInteger('interest_recalculation')->default(0);
            $table->enum('amortization_method', ['equal_installments', 'equal_principal_payments'])->nullable();
            $table->enum('interest_calculation_period_type', ['daily', 'same'])->nullable();
            $table->enum('days_in_year', ['actual', '360', '365', '364'])->default('actual')->nullable();
            $table->enum('days_in_month', ['actual', '30', '31'])->default('actual')->nullable();
            $table->tinyInteger('include_in_loan_cycle')->default(0);
            $table->tinyInteger('lock_guarantee_funds')->default(0);
            $table->tinyInteger('auto_allocate_overpayments')->default(0);
            $table->tinyInteger('allow_additional_charges')->default(0);
            $table->tinyInteger('auto_disburse')->default(0);
            $table->tinyInteger('require_linked_savings_account')->default(0);
            $table->decimal('min_amount', 65, 6)->nullable();
            $table->decimal('max_amount', 65, 6)->nullable();
            $table->enum('accounting_rule', ['none', 'cash', 'accrual_periodic', 'accrual_upfront'])->default('none')->nullable();
            $table->integer('npa_overdue_days')->default(0);
            $table->tinyInteger('npa_suspend_accrued_income')->default(0);
            $table->tinyInteger('active')->default(0);
            $table->timestamps();
          });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('loan_products');
    }
}
