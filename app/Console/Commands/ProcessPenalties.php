<?php

namespace App\Console\Commands;

use App\Helpers\GeneralHelper;
use App\Models\JournalEntry;
use App\Models\Loan;
use App\Models\LoanCharge;
use App\Models\LoanSchedule;
use App\Models\LoanTransaction;
use App\Models\SavingTransaction;
use Illuminate\Console\Command;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class ProcessPenalties extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'penalties:process_old';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Check and apply penalties';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {

        //missed payment penalty
        $due_date = Carbon::today()->format("Y-m-d");
        foreach (DB::table("loan_schedules")->join("loans", "loans.id", "loan_schedules.loan_id")->join("loan_products", "loan_products.id", "loans.loan_product_id")->selectRaw(DB::raw("loans.branch_id,loans.principal original_principal,loan_products.accounting_rule,loan_products.late_repayment_penalty_grace_period,loan_products.after_maturity_date_penalty_grace_period,loan_schedules.*,(SELECT SUM(principal) FROM loan_schedules WHERE loan_schedules.due_date<$due_date AND loan_id=loans.id) total_principal,(SELECT SUM(interest)  FROM loan_schedules WHERE loan_schedules.due_date<$due_date AND loan_id=loans.id) total_interest,(SELECT SUM(fees)  FROM loan_schedules WHERE loan_schedules.due_date<$due_date AND loan_id=loans.id) total_fees,(SELECT SUM(penalty)  FROM loan_schedules WHERE loan_schedules.due_date<$due_date AND loan_id=loans.id) total_penalty,(SELECT SUM(principal_waived)  FROM loan_schedules WHERE loan_schedules.due_date<$due_date AND loan_id=loans.id) total_principal_waived,(SELECT SUM(interest_waived)  FROM loan_schedules WHERE loan_schedules.due_date<$due_date AND loan_id=loans.id) total_interest_waived,(SELECT SUM(fees_waived) total_fees_waived FROM loan_schedules WHERE loan_schedules.due_date<$due_date AND loan_id=loans.id) total_fees_waived,(SELECT SUM(penalty_waived)  FROM loan_schedules WHERE loan_schedules.due_date<$due_date AND loan_id=loans.id) total_penalty_waived,(SELECT SUM(credit) FROM loan_transactions WHERE transaction_type='repayment' AND reversed=0 AND loan_transactions.loan_id=loan_schedules.loan_id) payments"))->where('loan_schedules.due_date', '<', $due_date)->where('loan_schedules.missed_penalty_applied', 0)->where('loans.status', 'disbursed')->get() as $schedule) {
            $schedule_balance = ($schedule->principal - $schedule->principal_waived + $schedule->interest - $schedule->interest_waived + $schedule->fees - $schedule->fees_waived + $schedule->penalty - $schedule->penalty_waived) - ($schedule->principal_paid + $schedule->interest_paid + $schedule->penalty_paid + $schedule->fees_paid);
            if ($schedule_balance > 0 && Carbon::today()->equalTo(Carbon::parse($schedule->due_date)->addDays($schedule->late_repayment_penalty_grace_period))) {
                $principal_due = 0;
                $interest_due = 0;
                $total_due = 0;
                $original_principal = $schedule->original_principal;
                foreach (DB::table("loan_schedules")->join("loans", "loans.id", "loan_schedules.loan_id")->join("loan_products", "loan_products.id", "loans.loan_product_id")->selectRaw(DB::raw("loans.principal original_principal,loan_products.late_repayment_penalty_grace_period,loan_products.after_maturity_date_penalty_grace_period,loan_schedules.*,(SELECT SUM(principal) FROM loan_schedules WHERE loan_schedules.due_date<$due_date AND loan_id=loans.id) total_principal,(SELECT SUM(principal_paid) FROM loan_schedules ) total_principal_paid,(SELECT SUM(interest)  FROM loan_schedules WHERE loan_schedules.due_date<$due_date AND loan_id=loans.id) total_interest,(SELECT SUM(interest_paid)  FROM loan_schedules) total_interest_paid,(SELECT SUM(fees)  FROM loan_schedules) total_fees,(SELECT SUM(fees_paid)  FROM loan_schedules) total_fees_paid,(SELECT SUM(penalty)  FROM loan_schedules) total_penalty,(SELECT SUM(penalty_paid)  FROM loan_schedules) total_penalty_paid,(SELECT SUM(principal_waived)  FROM loan_schedules) total_principal_waived,(SELECT SUM(interest_waived)  FROM loan_schedules) total_interest_waived,(SELECT SUM(fees_waived) total_fees_waived FROM loan_schedules) total_fees_waived,(SELECT SUM(penalty_waived)  FROM loan_schedules) total_penalty_waived"))->where('loan_schedules.loan_id', $schedule->loan_id)->where('loan_schedules.due_date', '<', $due_date)->where('loans.status', 'disbursed')->get() as $key) {
                    $principal_due = $principal_due + ($key->principal - $key->principal_waived - $key->principal_paid);
                    $interest_due = $interest_due + ($key->interest - $key->interest_waived - $key->interest_paid);
                    $total_due = $total_due + $principal_due + $interest_due + ($key->fees - $key->fees_waived - $key->fees_paid) + ($key->penalty - $key->penalty_waived - $key->penalty_paid);
                }
                foreach (LoanCharge::join('charges', 'charges.id', 'loan_charges.charge_id')->selectRaw("loan_charges.amount charge_amount,loan_charges.grace_period,charges.charge_option,charges.id")->where('charges.charge_type', 'overdue_installment_fee')->where('loan_charges.loan_id', $schedule->loan_id)->get() as $key) {
                    $amount = 0;
                    if ($key->charge_option == 'fixed') {
                        $amount = $key->charge_amount;
                    }
                    if ($key->charge_option == 'principal_due') {
                        $amount = $key->charge_amount * $principal_due / 100;
                    }
                    if ($key->charge_option == 'interest_due') {
                        $amount = $key->charge_amount * $interest_due / 100;
                    }
                    if ($key->charge_option == 'total_due') {
                        $amount = $key->charge_amount * $total_due / 100;
                    }
                    if ($key->charge_option == 'original_due') {
                        $amount = $key->charge_amount * $original_principal / 100;
                    }
                    //adjust schedule
                    $adjusted_schedule = LoanSchedule::find($schedule->id);
                    $adjusted_schedule->penalty = $adjusted_schedule->penalty + $amount;
                    $adjusted_schedule->save();
                    //penalty transaction
                    $loan_transaction = new LoanTransaction();
                    $loan_transaction->branch_id = $schedule->branch_id;
                    $loan_transaction->loan_id = $schedule->loan_id;
                    $loan_transaction->charge_id = $key->id;
                    $loan_transaction->reversible = 1;
                    $loan_transaction->loan_schedule_id = $schedule->id;
                    $loan_transaction->transaction_type = "overdue_installment_fee";
                    $loan_transaction->date = $due_date;
                    $date = explode('-', $due_date);
                    $loan_transaction->year = $date[0];
                    $loan_transaction->month = $date[1];
                    $loan_transaction->debit = $amount;
                    $loan_transaction->save();
                }
            }
        }
        //check after maturity date penalty
        foreach (DB::table("loans")->leftJoin("loan_schedules", "loan_schedules.loan_id", "loans.id")->join("loan_products", "loan_products.id", "loans.loan_product_id")->selectRaw(DB::raw("loans.maturity_date,loans.branch_id,loans.principal original_principal,loan_products.accounting_rule,loan_products.late_repayment_penalty_grace_period,loan_products.after_maturity_date_penalty_grace_period,loan_schedules.*,(SELECT SUM(principal) FROM loan_schedules WHERE  loan_id=loans.id) total_principal,(SELECT SUM(principal_paid) FROM loan_schedules WHERE  loan_id=loans.id ) total_principal_paid,(SELECT SUM(interest)  FROM loan_schedules WHERE  loan_id=loans.id) total_interest,(SELECT SUM(interest_paid)  FROM loan_schedules WHERE  loan_id=loans.id) total_interest_paid,(SELECT SUM(fees)  FROM loan_schedules WHERE  loan_id=loans.id) total_fees,(SELECT SUM(fees_paid)  FROM loan_schedules WHERE  loan_id=loans.id) total_fees_paid,(SELECT SUM(penalty)  FROM loan_schedules WHERE  loan_id=loans.id) total_penalty,(SELECT SUM(penalty_paid)  FROM loan_schedules WHERE  loan_id=loans.id) total_penalty_paid,(SELECT SUM(principal_waived)  FROM loan_schedules WHERE  loan_id=loans.id) total_principal_waived,(SELECT SUM(interest_waived)  FROM loan_schedules WHERE  loan_id=loans.id) total_interest_waived,(SELECT SUM(fees_waived) total_fees_waived FROM loan_schedules WHERE  loan_id=loans.id) total_fees_waived,(SELECT SUM(penalty_waived)  FROM loan_schedules WHERE  loan_id=loans.id) total_penalty_waived,(SELECT SUM(credit) FROM loan_transactions WHERE transaction_type='repayment' AND reversed=0 AND loan_transactions.loan_id=loan_schedules.loan_id) payments"))->where('loans.status', 'disbursed')->where('loans.maturity_date', '<', $due_date)->groupBy('loans.id')->get() as $loan) {
            $balance = ($loan->principal - $loan->principal_waived + $loan->interest - $loan->interest_waived + $loan->fees - $loan->fees_waived + $loan->penalty - $loan->penalty_waived) - ($loan->principal_paid + $loan->interest_paid + $loan->penalty_paid + $loan->fees_paid);
            if ($balance > 0 && Carbon::today()->equalTo(Carbon::parse($loan->maturity_date)->addDays($loan->after_maturity_date_penalty_grace_period))) {
                $principal_due = ($loan->total_principal - $loan->total_principal_waived - $loan->total_principal_paid);
                $interest_due = ($loan->total_interest - $loan->total_interest_waived - $loan->total_interest_paid);
                $total_due = $principal_due + $interest_due + ($loan->total_fees - $loan->total_fees_waived - $loan->total_fees_paid) + ($loan->total_penalty - $loan->total_penalty_waived - $loan->total_penalty_paid);
                $original_principal = $loan->original_principal;
                foreach (LoanCharge::join('charges', 'charges.id', 'loan_charges.charge_id')->selectRaw("loan_charges.amount charge_amount,loan_charges.grace_period,charges.charge_option,charges.id")->where('charges.charge_type', 'overdue_maturity')->where('loan_charges.loan_id', $loan->loan_id)->get() as $key) {
                    $amount = 0;
                    if ($key->charge_option == 'fixed') {
                        $amount = $key->charge_amount;
                    }
                    if ($key->charge_option == 'principal_due') {
                        $amount = $key->charge_amount * $principal_due / 100;
                    }
                    if ($key->charge_option == 'interest_due') {
                        $amount = $key->charge_amount * $interest_due / 100;
                    }
                    if ($key->charge_option == 'total_due') {
                        $amount = $key->charge_amount * $total_due / 100;
                    }
                    if ($key->charge_option == 'original_due') {
                        $amount = $key->charge_amount * $original_principal / 100;
                    }
                    //adjust schedule
                    $adjusted_schedule = LoanSchedule::where('loan_id', $loan->loan_id)->get()->last();
                    $adjusted_schedule->penalty = $adjusted_schedule->penalty + $amount;
                    $adjusted_schedule->save();
                    //penalty transaction
                    $loan_transaction = new LoanTransaction();
                    $loan_transaction->branch_id = $loan->branch_id;
                    $loan_transaction->loan_id = $loan->loan_id;
                    $loan_transaction->charge_id = $key->id;
                    $loan_transaction->reversible = 1;
                    $loan_transaction->loan_schedule_id = $adjusted_schedule->id;
                    $loan_transaction->transaction_type = "overdue_maturity";
                    $loan_transaction->date = $due_date;
                    $date = explode('-', $due_date);
                    $loan_transaction->year = $date[0];
                    $loan_transaction->month = $date[1];
                    $loan_transaction->debit = $amount;
                    $loan_transaction->save();
                }
            }
        }
        //savings charges
        foreach (DB::table("savings_charges")->join('charges', 'charges.id', 'savings_charges.charge_id')->join("savings", 'savings.id', 'savings_charges.savings_id')->leftJoin("savings_products", 'savings_products.id', 'savings.savings_product_id')->selectRaw("savings_charges.amount,savings.*,charges.charge_option,charges.charge_type,savings_products.chart_reference_id,savings_products.accounting_rule,savings_products.chart_expense_interest_id")->whereIn("charges.charge_type", ['annual_fee', 'monthly_fee'])->get() as $savings) {
//monthly fee
            if ($savings->charge_type == "monthly_fee" && date("d") == "01") {
                $savings_transaction = new SavingTransaction();
                $savings_transaction->borrower_id = $savings->borrower_id;
                $savings_transaction->branch_id = $savings->branch_id;
                $savings_transaction->savings_id = $savings->id;
                $savings_transaction->type = "bank_fees";
                $savings_transaction->reversible = 1;
                $savings_transaction->date = date("Y-m-d");
                $savings_transaction->time = date("H:i");
                $date = explode('-', date("Y-m-d"));
                $savings_transaction->year = $date[0];
                $savings_transaction->month = $date[1];
                $savings_transaction->debit = $savings->amount;
                $savings_transaction->save();
                if ($savings->accounting_rule == 'cash_based') {
                    $journal = new JournalEntry();
                    $journal->account_id = $savings->chart_reference_id;
                    $journal->branch_id = $savings_transaction->branch_id;
                    $journal->date = date("Y-m-d");
                    $journal->year = $date[0];
                    $journal->month = $date[1];
                    $journal->borrower_id = $savings_transaction->borrower_id;
                    $journal->transaction_type = 'pay_charge';
                    $journal->name = "Charge";
                    $journal->savings_id = $savings->id;
                    $journal->credit = $savings->amount;
                    $journal->reference = $savings_transaction->id;
                    $journal->save();

                    $journal = new JournalEntry();
                    $journal->account_id = $savings->chart_expense_interest_id;
                    $journal->branch_id = $savings_transaction->branch_id;
                    $journal->date = date("Y-m-d");
                    $journal->year = $date[0];
                    $journal->month = $date[1];
                    $journal->borrower_id = $savings_transaction->borrower_id;
                    $journal->transaction_type = 'pay_charge';
                    $journal->name = "Charge";
                    $journal->savings_id = $savings->id;
                    $journal->debit = $savings->amount;
                    $journal->reference = $savings_transaction->id;
                    $journal->save();
                }
            }
            if ($savings->charge_type == "annual_fee" && date("d-m") == "01-01") {
                $savings_transaction = new SavingTransaction();
                $savings_transaction->borrower_id = $savings->borrower_id;
                $savings_transaction->branch_id = $savings->branch_id;
                $savings_transaction->savings_id = $savings->id;
                $savings_transaction->type = "bank_fees";
                $savings_transaction->reversible = 1;
                $savings_transaction->date = date("Y-m-d");
                $savings_transaction->time = date("H:i");
                $date = explode('-', date("Y-m-d"));
                $savings_transaction->year = $date[0];
                $savings_transaction->month = $date[1];
                $savings_transaction->debit = $savings->amount;
                $savings_transaction->save();
                if ($savings->accounting_rule == 'cash_based') {
                    $journal = new JournalEntry();
                    $journal->account_id = $key->chart_reference_id;
                    $journal->branch_id = $savings_transaction->branch_id;
                    $journal->date = date("Y-m-d");
                    $journal->year = $date[0];
                    $journal->month = $date[1];
                    $journal->borrower_id = $savings_transaction->borrower_id;
                    $journal->transaction_type = 'pay_charge';
                    $journal->name = "Charge";
                    $journal->savings_id = $savings->id;
                    $journal->credit = $savings->amount;
                    $journal->reference = $savings_transaction->id;
                    $journal->save();

                    $journal = new JournalEntry();
                    $journal->account_id = $key->chart_expense_interest_id;
                    $journal->branch_id = $savings_transaction->branch_id;
                    $journal->date = date("Y-m-d");
                    $journal->year = $date[0];
                    $journal->month = $date[1];
                    $journal->borrower_id = $savings_transaction->borrower_id;
                    $journal->transaction_type = 'pay_charge';
                    $journal->name = "Charge";
                    $journal->savings_id = $savings->id;
                    $journal->debit = $savings->amount;
                    $journal->reference = $savings_transaction->id;
                    $journal->save();
                }
            }
        }
        //check for frequency of already applied charges
    }
}
