<?php

namespace Modules\Loan\Console;

use Illuminate\Console\Command;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Modules\Loan\Entities\Loan;
use Modules\Loan\Entities\LoanLinkedCharge;
use Modules\Loan\Entities\LoanTransaction;
use Modules\Loan\Events\TransactionUpdated;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;

class ProcessPenalties extends Command
{
    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'loan_penalties:process';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Apply penalties to all due loans';

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
        $due_date = Carbon::today()->format("Y-m-d");
        //overdue installment fee
        $data = DB::table("loan_product_linked_charges")
            ->join("loan_charges", "loan_charges.id", "loan_product_linked_charges.loan_charge_id")
            ->join("loan_products", "loan_products.id", "loan_product_linked_charges.loan_product_id")
            ->join("loans", "loans.loan_product_id", "loan_products.id")
            ->join("loan_repayment_schedules", "loan_repayment_schedules.loan_id", "loans.id")
            ->where("loan_charges.loan_charge_type_id", 4)
            ->where("loan_charges.is_penalty", 1)
            ->where("loan_repayment_schedules.due_date", $due_date)
            ->where("loan_repayment_schedules.total_due", '>', 0)
            ->selectRaw("loan_charges.id loan_charge_id,loan_charges.loan_charge_type_id,loan_charges.loan_charge_option_id,loan_charges.name charge_name,loan_charges.amount,loan_repayment_schedules.id loan_repayment_schedule_id,loan_repayment_schedules.due_date,loans.id loan_id,loans.branch_id,loans.principal,(loan_repayment_schedules.principal-loan_repayment_schedules.principal_repaid_derived-loan_repayment_schedules.principal_written_off_derived) principal_due,(loan_repayment_schedules.interest-loan_repayment_schedules.interest_repaid_derived-loan_repayment_schedules.interest_waived_derived-loan_repayment_schedules.interest_written_off_derived) interest_due,loans.decimals")
            ->get();
        foreach ($data as $key) {
            $loan = Loan::with('repayment_schedules')->find($key->loan_id);
            $loan_linked_charge = new LoanLinkedCharge();
            $loan_linked_charge->loan_id = $loan->id;
            $loan_linked_charge->name = $key->charge_name;
            $loan_linked_charge->loan_charge_id = $key->loan_charge_id;
            $loan_linked_charge->amount = $key->amount;
            $loan_linked_charge->loan_charge_type_id = $key->loan_charge_type_id;
            $loan_linked_charge->loan_charge_option_id = $key->loan_charge_option_id;
            $loan_linked_charge->is_penalty = 1;
            $loan_linked_charge->save();
            //find schedule to apply this charge
            $repayment_schedule = $loan->repayment_schedules->where('due_date', '=', $key->due_date)->first();
            //calculate the amount
            if ($loan_linked_charge->loan_charge_option_id == 1) {
                $amount = $loan_linked_charge->amount;
            }
            if ($loan_linked_charge->loan_charge_option_id == 2) {
                $amount = round(($loan_linked_charge->amount * ($repayment_schedule->principal-$repayment_schedule->principal_repaid_derived-$repayment_schedule->principal_written_off_derived) / 100), $loan->decimals);
            }
            if ($loan_linked_charge->loan_charge_option_id == 3) {
                $amount = round(($loan_linked_charge->amount * (($repayment_schedule->interest-$repayment_schedule->interest_repaid_derived-$repayment_schedule->interest_waived_derived-$repayment_schedule->interest_written_off_derived) + ($repayment_schedule->principal-$repayment_schedule->principal_repaid_derived-$repayment_schedule->principal_written_off_derived)) / 100), $loan->decimals);
            }
            if ($loan_linked_charge->loan_charge_option_id == 4) {
                $amount = round(($loan_linked_charge->amount * ($repayment_schedule->interest-$repayment_schedule->interest_repaid_derived-$repayment_schedule->interest_waived_derived-$repayment_schedule->interest_written_off_derived) / 100), $loan->decimals);
            }
            if ($loan_linked_charge->loan_charge_option_id == 5) {
                $amount = round(($loan_linked_charge->amount * ($loan->repayment_schedules->sum('principal')-$loan->repayment_schedules->sum('principal_repaid_derived')-$loan->repayment_schedules->sum('principal_written_off_derived')) / 100), $loan->decimals);
            }
            if ($loan_linked_charge->loan_charge_option_id == 6) {
                $amount = round(($loan_linked_charge->amount * $loan->principal / 100), $loan->decimals);
            }
            if ($loan_linked_charge->loan_charge_option_id == 7) {
                $amount = round(($loan_linked_charge->amount * $loan->principal / 100), $loan->decimals);
            }
            $repayment_schedule->penalties = $repayment_schedule->penalties + $amount;
            $repayment_schedule->save();
            $loan_linked_charge->calculated_amount = $amount;
            $loan_linked_charge->due_date = $repayment_schedule->due_date;
            //create transaction
            $loan_transaction = new LoanTransaction();
            $loan_transaction->loan_id = $loan->id;
           // $loan_transaction->created_by_id=Auth::id();
            $loan_transaction->name = trans_choice('loan::general.penalty', 1) . ' ' . $loan_transaction->name = trans_choice('loan::general.applied', 1);
            $loan_transaction->loan_transaction_type_id = 10;
            $loan_transaction->submitted_on = $repayment_schedule->due_date;
            $loan_transaction->created_on = date("Y-m-d");
            $loan_transaction->amount = $loan_linked_charge->calculated_amount;
            $loan_transaction->due_date = $repayment_schedule->due_date;
            $loan_transaction->debit = $loan_linked_charge->calculated_amount;
            $loan_transaction->reversible = 1;
            $loan_transaction->save();
            $loan_linked_charge->loan_transaction_id = $loan_transaction->id;
            $loan_linked_charge->save();
            //add linked loan charge
            //fire transaction updated event
            event(new TransactionUpdated($loan));
        }
        $this->info("Penalties processed successfully");
    }


}
