@extends('core::layouts.master')
@section('title')
    {{ trans_choice('loan::general.loan',1) }}  {{ trans_choice('loan::general.application',2) }}
@endsection
@section('styles')
@stop
@section('content')
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1>
                        {{ trans_choice('loan::general.loan',1) }}  {{ trans_choice('loan::general.application',2) }}
                        <a href="#" onclick="window.history.back()"
                           class="btn btn-outline-light bg-white d-none d-sm-inline-flex">
                            <em class="icon ni ni-arrow-left"></em><span>{{ trans_choice('core::general.back',1) }}</span>
                        </a>
                    </h1>

                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a
                                    href="{{url('dashboard')}}">{{ trans_choice('dashboard::general.dashboard',1) }}</a>
                        </li>
                        <li class="breadcrumb-item active">{{ trans_choice('loan::general.loan',1) }}  {{ trans_choice('loan::general.application',2) }}</li>
                    </ol>
                </div>
            </div>
        </div><!-- /.container-fluid -->
    </section>
    <section class="content" id="app">
        <div class="card ">
            <div class="card-header with-border">
                <a href="{{ url('portal/loan/application/create') }}" class="btn btn-info btn-sm">
                    <i class="fas fa-plus"></i>  {{ trans_choice('core::general.create',1) }} {{ trans_choice('loan::general.application',1) }}
                </a>
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
            </div>
            <div class="card-body table-responsive">
                <table class="table  table-striped table-hover table-condensed" id="data-table">
                    <thead>
                    <tr>
                        <th>{{ trans_choice('core::general.id',1) }}</th>
                        <th>{{ trans_choice('loan::general.product',1) }}</th>
                        <th>{{ trans_choice('core::general.amount',1) }}</th>
                        <th>{{ trans_choice('loan::general.status',1) }}</th>
                        <th>{{ trans_choice('core::general.date',1) }}</th>
                        <th>{{ trans_choice('core::general.action',1) }}</th>
                    </tr>
                    </thead>
                    <tbody>
                    @foreach($data as $key)
                        <tr>
                            <td>{{$key->id}}</td>
                            <td>
                                {{$key->loan_product}}
                            </td>
                            <td>{{number_format($key->amount,2)}}</td>
                            <td>
                                @if($key->status=='pending')
                                    {{ trans_choice('loan::general.pending_approval',1) }}
                                @endif
                                @if($key->status=='approved')
                                    {{ trans_choice('loan::general.approved',1) }}
                                @endif
                                @if($key->status=='rejected')
                                    {{ trans_choice('loan::general.rejected',1) }}
                                @endif
                            </td>
                            <td>{{$key->created_at}}</td>
                            <td>
                                @if($key->status=='pending')
                                    <a href="{{url('portal/loan/application/'.$key->id.'/destroy')}}" class="confirm">
                                        <i class="fa fa-trash"></i>
                                    </a>
                                @endif
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

@endsection
