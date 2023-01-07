<?php

namespace Modules\Loan\Entities;

use Illuminate\Database\Eloquent\Model;
use Modules\Branch\Entities\Branch;
use Modules\Client\Entities\Client;
use Modules\Core\Entities\Currency;
use Modules\User\Entities\User;

class Loan extends Model
{
    protected $fillable = [];
    public $table = "loans";

    public function charges()
    {
        return $this->hasMany(LoanLinkedCharge::class, 'loan_id', 'id');
    }

    public function client()
    {
        return $this->hasOne(Client::class, 'id', 'client_id');
    }
    public function branch()
    {
        return $this->hasOne(Branch::class, 'id', 'branch_id');
    }

    public function loan_product()
    {
        return $this->hasOne(LoanProduct::class, 'id', 'loan_product_id');
    }

    public function currency()
    {
        return $this->hasOne(Currency::class, 'id', 'currency_id');
    }

    public function loan_officer()
    {
        return $this->hasOne(User::class, 'id', 'loan_officer_id');
    }

    public function loan_purpose()
    {
        return $this->hasOne(LoanPurpose::class, 'id', 'loan_purpose_id');
    }

    public function fund()
    {
        return $this->hasOne(Fund::class, 'id', 'fund_id');
    }

    public function loan_transaction_processing_strategy()
    {
        return $this->hasOne(LoanTransactionProcessingStrategy::class, 'id', 'loan_transaction_processing_strategy_id');
    }

    public function submitted_by()
    {
        return $this->hasOne(User::class, 'id', 'submitted_by_user_id');
    }

    public function approved_by()
    {
        return $this->hasOne(User::class, 'id', 'approved_by_user_id');
    }

    public function disbursed_by()
    {
        return $this->hasOne(User::class, 'id', 'disbursed_by_user_id');
    }

    public function rejected_by()
    {
        return $this->hasOne(User::class, 'id', 'rejected_by_user_id');
    }

    public function written_off_by()
    {
        return $this->hasOne(User::class, 'id', 'written_off_by_user_id');
    }

    public function closed_by()
    {
        return $this->hasOne(User::class, 'id', 'closed_by_user_id');
    }

    public function withdrawn_by()
    {
        return $this->hasOne(User::class, 'id', 'withdrawn_by_user_id');
    }

    public function rescheduled_by()
    {
        return $this->hasOne(User::class, 'id', 'rescheduled_by_user_id');
    }

    public function files()
    {
        return $this->hasMany(LoanFile::class, 'loan_id', 'id');
    }

    public function collateral()
    {
        return $this->hasMany(LoanCollateral::class, 'loan_id', 'id');
    }

    public function notes()
    {
        return $this->hasMany(LoanNote::class, 'loan_id', 'id')->orderBy('created_at', 'desc');
    }

    public function guarantors()
    {
        return $this->hasMany(LoanGuarantor::class, 'loan_id', 'id');
    }

    public function repayment_schedules()
    {
        return $this->hasMany(LoanRepaymentSchedule::class, 'loan_id', 'id')->orderBy('due_date', 'asc');
    }

    public function transactions()
    {
        return $this->hasMany(LoanTransaction::class, 'loan_id', 'id')->orderBy('submitted_on', 'asc')->orderBy('id', 'asc');
    }
}
