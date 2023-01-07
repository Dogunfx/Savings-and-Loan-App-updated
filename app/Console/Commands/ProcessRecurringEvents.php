<?php

namespace App\Console\Commands;

use App\Models\CustomFieldMeta;
use App\Models\Expense;
use App\Models\Payroll;
use App\Models\PayrollMeta;
use Illuminate\Console\Command;

class ProcessRecurringEvents extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'events:recur';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Process Recurring events';

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
        //check for recurring expenses
        $expenses = Expense::where('recurring', 1)->get();
        foreach ($expenses as $expense) {
            if (empty($expense->recur_end_date)) {
                if ($expense->recur_next_date == date("Y-m-d")) {
                    $exp1 = new Expense();
                    $exp1->expense_type_id = $expense->expense_type_id;
                    $exp1->amount = $expense->amount;
                    $exp1->notes = $expense->notes;
                    $exp1->date = date("Y-m-d");
                    $date = explode('-', date("Y-m-d"));
                    $exp1->year = $date[0];
                    $exp1->month = $date[1];
                    $exp1->save();
                    $custom_fields = CustomFieldMeta::where('parent_id', $expense->id)->where('category',
                        'expenses')->get();
                    foreach ($custom_fields as $key) {
                        $custom_field = new CustomFieldMeta();
                        $custom_field->name = $key->name;
                        $custom_field->parent_id = $exp1->id;
                        $custom_field->custom_field_id = $key->custom_field_id;
                        $custom_field->category = "expenses";
                        $custom_field->save();
                    }
                    $exp2 = Expense::find($expense->id);
                    $exp2->recur_next_date = date_format(date_add(date_create(date("Y-m-d")),
                        date_interval_create_from_date_string($expense->recur_frequency . ' ' . $expense->recur_type . 's')),
                        'Y-m-d');
                    $exp2->save();
                }
            } else {
                if (date("Y-m-d") <= $expense->recur_end_date) {
                    if ($expense->recur_next_date == date("Y-m-d")) {
                        $exp1 = new Expense();
                        $exp1->expense_type_id = $expense->expense_type_id;
                        $exp1->amount = $expense->amount;
                        $exp1->notes = $expense->notes;
                        $exp1->date = date("Y-m-d");
                        $date = explode('-', date("Y-m-d"));
                        $exp1->year = $date[0];
                        $exp1->month = $date[1];
                        $exp1->save();
                        $custom_fields = CustomFieldMeta::where('parent_id', $expense->id)->where('category',
                            'expenses')->get();
                        foreach ($custom_fields as $key) {
                            $custom_field = new CustomFieldMeta();
                            $custom_field->name = $key->name;
                            $custom_field->parent_id = $exp1->id;
                            $custom_field->custom_field_id = $key->custom_field_id;
                            $custom_field->category = "expenses";
                            $custom_field->save();
                        }
                        $exp2 = Expense::find($expense->id);
                        $exp2->recur_next_date = date_format(date_add(date_create(date("Y-m-d")),
                            date_interval_create_from_date_string($expense->recur_frequency . ' ' . $expense->recur_type . 's')),
                            'Y-m-d');
                        $exp2->save();
                    }
                }
            }
        }
        //check for recurring payroll
        $payrolls = Payroll::where('recurring', 1)->get();
        foreach ($payrolls as $payroll) {
            if (empty($payroll->recur_end_date)) {
                if ($payroll->recur_next_date == date("Y-m-d")) {
                    $pay1 = new Payroll();
                    $pay1->payroll_template_id = $payroll->payroll_template_id;
                    $pay1->user_id = $payroll->user_id;
                    $pay1->employee_name = $payroll->employee_name;
                    $pay1->business_name = $payroll->business_name;
                    $pay1->payment_method = $payroll->payment_method;
                    $pay1->bank_name = $payroll->bank_name;
                    $pay1->account_number = $payroll->account_number;
                    $pay1->description = $payroll->description;
                    $pay1->comments = $payroll->comments;
                    $pay1->paid_amount = $payroll->paid_amount;
                    $date = explode('-', date("Y-m-d"));
                    $pay1->date = date("Y-m-d");
                    $pay1->year = $date[0];
                    $pay1->month = $date[1];
                    $pay1->save();
                    //save payroll meta
                    $metas = PayrollMeta::where('payroll_id',
                        $payroll->id)->get();;
                    foreach ($metas as $key) {
                        $meta = new PayrollMeta();
                        $meta->value = $key->value;
                        $meta->payroll_id = $pay1->id;
                        $meta->payroll_template_meta_id = $key->payroll_template_meta_id;
                        $meta->position = $key->position;
                        $meta->save();
                    }
                    $pay2 = Payroll::find($payroll->id);
                    $pay2->recur_next_date = date_format(date_add(date_create(date("Y-m-d")),
                        date_interval_create_from_date_string($payroll->recur_frequency . ' ' . $payroll->recur_type . 's')),
                        'Y-m-d');
                    $pay2->save();
                } else {
                    if (date("Y-m-d") <= $payroll->recur_end_date) {
                        if ($payroll->recur_next_date == date("Y-m-d")) {
                            $pay1 = new Payroll();
                            $pay1->payroll_template_id = $payroll->payroll_template_id;
                            $pay1->user_id = $payroll->user_id;
                            $pay1->employee_name = $payroll->employee_name;
                            $pay1->business_name = $payroll->business_name;
                            $pay1->payment_method = $payroll->payment_method;
                            $pay1->bank_name = $payroll->bank_name;
                            $pay1->account_number = $payroll->account_number;
                            $pay1->description = $payroll->description;
                            $pay1->comments = $payroll->comments;
                            $pay1->paid_amount = $payroll->paid_amount;
                            $date = explode('-', date("Y-m-d"));
                            $pay1->date = date("Y-m-d");
                            $pay1->year = $date[0];
                            $pay1->month = $date[1];
                            $pay1->save();
                            //save payroll meta
                            $metas = PayrollMeta::where('payroll_id',
                                $payroll->id)->get();;
                            foreach ($metas as $key) {
                                $meta = new PayrollMeta();
                                $meta->value = $key->value;
                                $meta->payroll_id = $pay1->id;
                                $meta->payroll_template_meta_id = $key->payroll_template_meta_id;
                                $meta->position = $key->position;
                                $meta->save();
                            }
                            $pay2 = Payroll::find($payroll->id);
                            $pay2->recur_next_date = date_format(date_add(date_create(date("Y-m-d")),
                                date_interval_create_from_date_string($payroll->recur_frequency . ' ' . $payroll->recur_type . 's')),
                                'Y-m-d');
                            $pay2->save();
                        }
                    }
                }
            }
        }
    }
}
