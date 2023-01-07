@extends('core::layouts.master')
@section('title')
    {{ trans_choice('dashboard::general.dashboard',1) }}
@endsection
@section('content')
    <div class="row">
        <div class="col-md-3">
            <div class="info-box">
                <span class="info-box-icon bg-aqua"><i class="fa fa-money"></i></span>
                <div class="info-box-content">
                    <span class="info-box-text">{{ trans_choice('loan::general.total',1) }} {{ trans_choice('loan::general.loan',2) }}</span>
                    <span class="info-box-number">{{number_format(\Modules\Loan\Entities\Loan::where('client_id',session('client_id'))->count())}}</span>
                </div>
                <!-- /.info-box-content -->
            </div>
        </div>
        <div class="col-md-3">
            <div class="info-box">
                <span class="info-box-icon bg-green"><i class="fa fa-money"></i></span>
                <div class="info-box-content">
                    <span class="info-box-text">{{ trans_choice('loan::general.loan',2) }} {{ trans_choice('loan::general.disbursed',2) }}</span>
                    <span class="info-box-number">{{number_format(\Modules\Loan\Entities\Loan::where('status','active')->where('client_id',session('client_id'))->sum('principal'))}}</span>
                </div>
                <!-- /.info-box-content -->
            </div>
        </div>
        <div class="col-md-3">
            <div class="info-box">
                <span class="info-box-icon bg-aqua"><i class="fa fa-bank"></i></span>
                <div class="info-box-content">
                    <span class="info-box-text">{{ trans_choice('loan::general.total',1) }} {{ trans_choice('savings::general.savings',2) }}</span>
                    <span class="info-box-number">{{number_format(\Modules\Savings\Entities\Savings::where('client_id',session('client_id'))->count())}}</span>
                </div>
                <!-- /.info-box-content -->
            </div>
        </div>
        <div class="col-md-3">
            <div class="info-box">
                <span class="info-box-icon bg-aqua"><i class="fa fa-bank"></i></span>
                <div class="info-box-content">
                    <span class="info-box-text">{{ trans_choice('savings::general.savings',1) }} {{ trans_choice('savings::general.balance',2) }}</span>
                    <span class="info-box-number">{{number_format(\Modules\Savings\Entities\Savings::where('client_id',session('client_id'))->sum('balance_derived'))}}</span>
                </div>
                <!-- /.info-box-content -->
            </div>
        </div>
    </div>
@stop
