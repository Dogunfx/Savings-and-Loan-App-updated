@extends('core::layouts.master')
@section('title')
    {{ trans_choice('dashboard::general.dashboard',1) }}
@endsection
@section('content')
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">


                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a
                                    href="{{url('dashboard')}}">{{ trans_choice('dashboard::general.dashboard',1) }}</a>
                        </li>
                        <li class="breadcrumb-item active">{{ trans_choice('dashboard::general.dashboard',1) }}</li>
                    </ol>
                </div>
            </div>
        </div><!-- /.container-fluid -->
    </section>
    <section class="content" id="app">
        <div class="row">
            <div class="col-md-3">
                <div class="info-box">
                    <span class="info-box-icon bg-info"><i class="fas fa-money-bill"></i></span>
                    <div class="info-box-content">
                        <span class="info-box-text">{{ trans_choice('loan::general.total',1) }} {{ trans_choice('loan::general.loan',2) }}</span>
                        <span class="info-box-number">{{number_format(\Modules\Loan\Entities\Loan::where('client_id',session('client_id'))->count())}}</span>
                    </div>
                    <!-- /.info-box-content -->
                </div>
            </div>
            <div class="col-md-3">
                <div class="info-box">
                    <span class="info-box-icon bg-green"><i class="fas fa-money-bill"></i></span>
                    <div class="info-box-content">
                        <span class="info-box-text">{{ trans_choice('loan::general.loan',2) }} {{ trans_choice('loan::general.disbursed',2) }}</span>
                        <span class="info-box-number">{{number_format(\Modules\Loan\Entities\Loan::where('status','active')->where('client_id',session('client_id'))->sum('principal'))}}</span>
                    </div>
                    <!-- /.info-box-content -->
                </div>
            </div>
            <div class="col-md-3">
                <div class="info-box">
                    <span class="info-box-icon bg-success"><i class="fas fa-university"></i></span>
                    <div class="info-box-content">
                        <span class="info-box-text">{{ trans_choice('loan::general.total',1) }} {{ trans_choice('savings::general.savings',2) }}</span>
                        <span class="info-box-number">{{number_format(\Modules\Savings\Entities\Savings::where('client_id',session('client_id'))->count())}}</span>
                    </div>
                    <!-- /.info-box-content -->
                </div>
            </div>
            <div class="col-md-3">
                <div class="info-box">
                    <span class="info-box-icon bg-info"><i class="fas fa-university"></i></span>
                    <div class="info-box-content">
                        <span class="info-box-text">{{ trans_choice('savings::general.savings',1) }} {{ trans_choice('savings::general.balance',2) }}</span>
                        <span class="info-box-number">{{number_format(\Modules\Savings\Entities\Savings::where('client_id',session('client_id'))->sum('balance_derived'))}}</span>
                    </div>
                    <!-- /.info-box-content -->
                </div>
            </div>
        </div>
    </section>
@stop
