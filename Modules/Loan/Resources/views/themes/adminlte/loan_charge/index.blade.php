@extends('core::layouts.master')
@section('title')
    {{ trans_choice('loan::general.loan',1) }}  {{ trans_choice('loan::general.charge',2) }}
@endsection
@section('styles')
@stop
@section('content')
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1>{{ trans_choice('loan::general.loan',1) }}  {{ trans_choice('loan::general.charge',2) }}</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a
                                    href="{{url('dashboard')}}">{{ trans_choice('dashboard::general.dashboard',1) }}</a>
                        </li>
                        <li class="breadcrumb-item active">{{ trans_choice('loan::general.loan',1) }}  {{ trans_choice('loan::general.charge',2) }}</li>
                    </ol>
                </div>
            </div>
        </div><!-- /.container-fluid -->
    </section>

    <!-- Main content -->
    <section class="content">
        <div class="card">
            <div class="card-header">
                @can('loan.loans.charges.create')
                    <a href="{{ url('loan/charge/create') }}" class="btn btn-info btn-sm">
                        <i class="fas fa-plus"></i> {{ trans_choice('core::general.add',1) }} {{ trans_choice('loan::general.charge',1) }}
                    </a>
                @endcan
                <div class="btn-group">
                    <div class="dropdown">
                        <a href="#" class="btn btn-trigger btn-icon dropdown-toggle"
                           data-toggle="dropdown">
                            <i class="fas fa-wrench"></i>
                        </a>
                        <div class="dropdown-menu dropdown-menu-xs">
                            <a class="dropdown-item"><span>Show</span></a>
                            <a href="{{request()->fullUrlWithQuery(['per_page'=>10])}}"
                               class="dropdown-item {{request('per_page')==10?'active':''}}">
                                10
                            </a>
                            <a href="{{request()->fullUrlWithQuery(['per_page'=>20])}}"
                               class="dropdown-item {{(request('per_page')==20||!request('per_page'))?'active':''}}">
                                20
                            </a>
                            <a href="{{request()->fullUrlWithQuery(['per_page'=>50])}}"
                               class="dropdown-item {{request('per_page')==50?'active':''}}">50</a>
                            <a class="dropdown-item">Order</a>
                            <a href="{{request()->fullUrlWithQuery(['order_by_dir'=>'asc'])}}"
                               class="dropdown-item {{(request('order_by_dir')=='asc'||!request('order_by_dir'))?'active':''}}">
                                ASC
                            </a>
                            <a href="{{request()->fullUrlWithQuery(['order_by_dir'=>'desc'])}}"
                               class="dropdown-item {{request('order_by_dir')=='desc'?'active':''}}">
                                DESC
                            </a>
                        </div>
                    </div>
                </div>
                <div class="card-tools">
                    <form class="form-inline ml-0 ml-md-3" action="{{url('loan/charge')}}">
                        <div class="input-group input-group-sm">
                            <input type="text" name="s" class="form-control" value="{{request('s')}}"
                                   placeholder="Search">
                            <div class="input-group-append">
                                <button class="btn btn-primary" type="submit">
                                    <i class="fas fa-search"></i>
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
            <div class="card-body p-0">
                <table id="data-table" class="table table-striped table-condensed table-hover">
                    <thead>
                    <tr>
                        <th>
                            <a href="{{table_order_link('name')}}">
                                {{ trans_choice('core::general.name',1) }}
                            </a>
                        </th>
                        <th>
                            <a href="{{table_order_link('loan_charge_type_id')}}">
                                <span>{{ trans_choice('loan::general.charge',1) }} {{ trans_choice('core::general.type',1) }}</span>
                            </a>
                        </th>
                        <th>
                            <a href="{{table_order_link('amount')}}">
                                <span>{{ trans_choice('core::general.amount',1) }}</span>
                            </a>
                        </th>
                        <th>
                            <a href="{{table_order_link('active')}}">
                                {{ trans_choice('core::general.active',1) }}
                            </a>
                        </th>
                        <th>{{ trans_choice('core::general.action',1) }}</th>
                    </tr>
                    </thead>
                    <tbody>
                    @foreach($data as $key)
                        <tr>
                            <td>
                                <a href="#">
                                    <span>{{$key->name}}</span>
                                </a>
                            </td>
                            <td>
                                @if($key->loan_charge_type_id==1)
                                    <span>{{trans_choice('loan::general.disbursement', 1)}}</span>
                                @endif
                                @if($key->loan_charge_type_id==2)
                                    <span>{{trans_choice('loan::general.specified_due_date', 1)}}</span>
                                @endif
                                @if($key->loan_charge_type_id==3)
                                    <span>{{trans_choice('loan::general.installment', 1)}} {{trans_choice('loan::general.fee', 2)}}</span>
                                @endif
                                @if($key->loan_charge_type_id==4)
                                    <span>{{trans_choice('loan::general.overdue', 1)}} {{trans_choice('loan::general.installment', 1)}} {{trans_choice('loan::general.fee', 2)}}</span>
                                @endif
                                @if($key->loan_charge_type_id==5)
                                    <span>{{trans_choice('loan::general.disbursement_paid_with_repayment', 1)}}</span>
                                @endif
                                @if($key->loan_charge_type_id==6)
                                    <span>{{trans_choice('loan::general.loan_rescheduling_fee', 1)}}</span>
                                @endif
                                @if($key->loan_charge_type_id==7)
                                    <span>{{trans_choice('loan::general.overdue_on_loan_maturity', 1)}}</span>
                                @endif
                                @if($key->loan_charge_type_id==8)
                                    <span>{{trans_choice('loan::general.last_installment_fee', 1)}}</span>
                                @endif
                            </td>
                            <td>
                                @if($key->loan_charge_option_id==1)
                                    <span>{{number_format($key->amount, 2)}} {{trans_choice('loan::general.flat', 1)}}</span>
                                @endif
                                @if($key->loan_charge_option_id==2)
                                    <span>{{number_format($key->amount, 2)}} % {{trans_choice('loan::general.principal_due_on_installment', 1)}}</span>
                                @endif
                                @if($key->loan_charge_option_id==3)
                                    <span>{{number_format($key->amount, 2)}} % {{trans_choice('loan::general.principal_interest_due_on_installment', 1)}}</span>
                                @endif
                                @if($key->loan_charge_option_id==4)
                                    <span>{{number_format($key->amount, 2)}} %  {{trans_choice('loan::general.interest_due_on_installment', 1)}}</span>
                                @endif
                                @if($key->loan_charge_option_id==5)
                                    <span>{{number_format($key->amount, 2)}} %  {{trans_choice('loan::general.total_outstanding_loan_principal', 1)}}</span>
                                @endif
                                @if($key->loan_charge_option_id==6)
                                    <span>{{number_format($key->amount, 2)}} %  {{trans_choice('loan::general.percentage_of_original_loan_principal_per_installment', 1)}}</span>
                                @endif
                                @if($key->loan_charge_option_id==7)
                                    <span>{{number_format($key->amount, 2)}} %  {{trans_choice('loan::general.original_loan_principal', 1)}}</span>
                                @endif
                            </td>
                            <td>
                                @if($key->active==1)
                                    <span class="badge badge-success">{{trans_choice('core::general.yes', 1)}}</span>
                                @else
                                    <span class="badge badge-danger">{{trans_choice('core::general.no', 1)}}</span>
                                @endif
                            </td>
                            <td>
                                <div class="btn-group">
                                    <button href="#" class="btn btn-default dropdown-toggle"
                                            data-toggle="dropdown">
                                        <i class="fas fa-ellipsis-h"></i>
                                    </button>
                                    <div class="dropdown-menu dropdown-menu-right">
                                        @can('loan.loans.charges.edit')

                                            <a href="{{url('loan/charge/' . $key->id . '/edit')}}" class="dropdown-item">
                                                <i class="fas fa-edit"></i>
                                                <span>{{trans_choice('core::general.edit',1)}}</span>
                                            </a>

                                        @endcan
                                        <div class="divider"></div>
                                        @can('loan.loans.charges.destroy')

                                            <a href="{{url('loan/charge/' . $key->id . '/destroy')}}"
                                               class="dropdown-item confirm">
                                                <i class="fas fa-trash"></i>
                                                <span>{{trans_choice('core::general.delete',1)}}</span>
                                            </a>

                                        @endcan

                                    </div>
                                </div>
                            </td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>

            </div>
            <div class="card-footer">
                <div class="row">
                    <div class="col-md-4">
                        <div>{{ trans_choice('core::general.page',1) }} {{$data->currentPage()}} {{ trans_choice('core::general.of',1) }} {{$data->lastPage()}}</div>
                    </div>
                    <div class="col-md-4">
                        <div class="d-flex justify-content-center">
                            {{$data->links()}}
                        </div>
                    </div>
                    <div class="col-md-4">

                    </div>
                </div>

            </div>

        </div>

    </section>
@endsection
@section('scripts')
    <script>
        var app = new Vue({
            el: "#app",
            data: {
                records:{!!json_encode($data)!!},
                selectAll: false,
                selectedRecords: []
            },
            methods: {
                selectAllRecords() {
                    this.selectedRecords = [];
                    if (this.selectAll) {
                        this.records.data.forEach(item => {
                            this.selectedRecords.push(item.id);
                        });
                    }
                },
            },
        })
    </script>
@endsection
