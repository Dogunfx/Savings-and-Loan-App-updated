<div class="grid-stack-item loan_statistics"
     gs-x="{{$config["x"]}}" gs-y="{{$config["y"]}}"
     gs-w="{{$config["width"]}}" gs-h="{{$config["height"]}}" gs-id="LoanStatistics">
    <div class="grid-stack-item-content">
        <div class="row">
            <div class="col-md-12 col-lg-3">
                <div class="nk-wg-card is-s3 card card-bordered">
                    <div class="card-inner">
                        <div class="nk-iv-wg2">
                            <div class="nk-iv-wg2-title">
                                <h6 class="title">{{ trans_choice('loan::general.loan',2) }} {{ trans_choice('loan::general.disbursed',2) }} <em class="icon ni ni-info"></em></h6>
                            </div>
                            <div class="nk-iv-wg2-text">
                                <div class="nk-iv-wg2-amount"> {{number_format(\Modules\Loan\Entities\Loan::where('status','active')->sum('principal'))}}
                                </div>
                            </div>
                        </div>
                    </div>
                </div><!-- .card -->
            </div>
            <div class="col-md-12 col-lg-3">
                <div class="nk-wg-card is-s3 card card-bordered">
                    <div class="card-inner">
                        <div class="nk-iv-wg2">
                            <div class="nk-iv-wg2-title">
                                <h6 class="title">{{ trans_choice('loan::general.total',1) }} {{ trans_choice('loan::general.repayment',2) }} <em class="icon ni ni-info"></em></h6>
                            </div>
                            <div class="nk-iv-wg2-text">
                                <div class="nk-iv-wg2-amount"> {{number_format(\Modules\Loan\Entities\LoanTransaction::where('reversed',0)->whereIn('loan_transaction_type_id',[2,5,8])->sum('amount'))}}
                                </div>
                            </div>
                        </div>
                    </div>
                </div><!-- .card -->
            </div>
            <?php
            $total_principal = 0;
            $total_principal_waived = 0;
            $total_principal_paid = 0;
            $total_principal_written_off = 0;
            $total_principal_outstanding = 0;
            $total_principal_overdue = 0;
            $total_interest = 0;
            $total_interest_waived = 0;
            $total_interest_paid = 0;
            $total_interest_written_off = 0;
            $total_interest_outstanding = 0;
            $total_interest_overdue = 0;
            $total_fees = 0;
            $total_fees_waived = 0;
            $total_fees_paid = 0;
            $total_fees_written_off = 0;
            $total_fees_outstanding = 0;
            $total_fees_overdue = 0;
            $total_penalties = 0;
            $total_penalties_waived = 0;
            $total_penalties_paid = 0;
            $total_penalties_written_off = 0;
            $total_penalties_outstanding = 0;
            $total_penalties_overdue = 0;
            $total_arrears_amount = 0;
            foreach ($loans as $loan) {
                $total_principal = $total_principal + $loan->repayment_schedules->sum('principal');
                $total_principal_paid = $total_principal_paid + $loan->repayment_schedules->sum('principal_repaid_derived');
                $total_principal_written_off = $total_principal_written_off + $loan->repayment_schedules->sum('principal_written_off_derived');
                $total_interest = $total_interest + $loan->repayment_schedules->sum('interest');
                $total_interest_waived = $total_interest_waived + $loan->repayment_schedules->sum('interest_waived_derived');
                $total_interest_paid = $total_interest_paid + $loan->repayment_schedules->sum('interest_repaid_derived');
                $total_interest_written_off = $total_interest_written_off + $loan->repayment_schedules->sum('interest_written_off_derived');
                $total_fees = $total_fees + $loan->repayment_schedules->sum('fees') + $loan->disbursement_charges;
                $total_fees_waived = $total_fees_waived + $loan->repayment_schedules->sum('fees_waived_derived');
                $total_fees_paid = $total_fees_paid + $loan->repayment_schedules->sum('fees_repaid_derived') + $loan->disbursement_charges;
                $total_fees_written_off = $total_fees_written_off + $loan->repayment_schedules->sum('fees_written_off_derived');

                $total_penalties = $total_penalties + $loan->repayment_schedules->sum('penalties');
                $total_penalties_waived = $total_penalties_waived + $loan->repayment_schedules->sum('penalties_waived_derived');
                $total_penalties_paid = $total_penalties_paid + $loan->repayment_schedules->sum('penalties_repaid_derived');
                $total_penalties_written_off = $total_penalties_written_off + $loan->repayment_schedules->sum('penalties_written_off_derived');
                //arrears
                $arrears_last_schedule = $loan->repayment_schedules->sortByDesc('due_date')->where('due_date', '<', date("Y-m-d"))->where('total_due', '>', 0)->first();
                if (!empty($arrears_last_schedule)) {
                    $overdue_schedules = $loan->repayment_schedules->where('due_date', '<=', $arrears_last_schedule->due_date);
                    $total_principal_overdue = $total_principal_overdue + $overdue_schedules->sum('principal') - $overdue_schedules->sum('principal_written_off_derived') - $overdue_schedules->sum('principal_repaid_derived');
                    $total_interest_overdue = $total_interest_overdue + $overdue_schedules->sum('interest') - $overdue_schedules->sum('interest_written_off_derived') - $overdue_schedules->sum('interest_repaid_derived') - $overdue_schedules->sum('interest_waived_derived');
                    $total_fees_overdue = $total_fees_overdue + $overdue_schedules->sum('fees') - $overdue_schedules->sum('fees_written_off_derived') - $overdue_schedules->sum('fees_repaid_derived') - $overdue_schedules->sum('fees_waived_derived');
                    $total_penalties_overdue = $total_penalties_overdue + $overdue_schedules->sum('penalties') - $overdue_schedules->sum('penalties_written_off_derived') - $overdue_schedules->sum('penalties_repaid_derived') - $overdue_schedules->sum('penalties_waived_derived');
                }
            }
            $total_principal_outstanding = $total_principal - $total_principal_waived - $total_principal_paid - $total_principal_written_off;
            $total_interest_outstanding = $total_interest - $total_interest_waived - $total_interest_paid - $total_interest_written_off;
            $total_fees_outstanding = $total_fees - $total_fees_waived - $total_fees_paid - $total_fees_written_off;
            $total_penalties_outstanding = $total_penalties - $total_penalties_waived - $total_penalties_paid - $total_penalties_written_off;
            $total_balance = $total_principal_outstanding + $total_interest_outstanding + $total_fees_outstanding + $total_penalties_outstanding;
            $total_arrears_amount = $total_principal_overdue + $total_interest_overdue + $total_fees_overdue + $total_penalties_overdue;
            ?>
            <div class="col-md-12 col-lg-3">
                <div class="nk-wg-card is-s3 card card-bordered">
                    <div class="card-inner">
                        <div class="nk-iv-wg2">
                            <div class="nk-iv-wg2-title">
                                <h6 class="title">{{ trans_choice('loan::general.total',1) }} {{ trans_choice('loan::general.outstanding',2) }} <em class="icon ni ni-info"></em></h6>
                            </div>
                            <div class="nk-iv-wg2-text">
                                <div class="nk-iv-wg2-amount"> {{number_format($total_balance)}}
                                </div>
                            </div>
                        </div>
                    </div>
                </div><!-- .card -->
            </div>
            <div class="col-md-12 col-lg-3">
                <div class="nk-wg-card is-s3 card card-bordered">
                    <div class="card-inner">
                        <div class="nk-iv-wg2">
                            <div class="nk-iv-wg2-title">
                                <h6 class="title">{{ trans_choice('loan::general.total',1) }} {{ trans_choice('loan::general.arrears',2) }} <em class="icon ni ni-info"></em></h6>
                            </div>
                            <div class="nk-iv-wg2-text">
                                <div class="nk-iv-wg2-amount"> {{number_format($total_arrears_amount)}}
                                </div>
                            </div>
                        </div>
                    </div>
                </div><!-- .card -->
            </div>
        </div>

    </div>
</div>
