@extends('core::layouts.master')
@section('title')
    {{ trans_choice('payroll::general.item',2) }}
@endsection
@section('styles')
@stop
@section('content')
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1>{{ trans_choice('payroll::general.item',2) }}</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a
                                    href="{{url('dashboard')}}">{{ trans_choice('dashboard::general.dashboard',1) }}</a>
                        </li>
                        <li class="breadcrumb-item active">{{ trans_choice('payroll::general.item',2) }}</li>
                    </ol>
                </div>
            </div>
        </div><!-- /.container-fluid -->
    </section>

    <!-- Main content -->
    <section class="content">
        <div class="card">
            <div class="card-header with-border">
                @can('payroll.payroll.items.create')
                    <a href="{{ url('payroll/item/create') }}" class="btn btn-info btn-sm">
                        <i class="fas fa-plus"></i> {{ trans_choice('core::general.add',1) }} {{ trans_choice('payroll::general.item',1) }}
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
                    <form class="form-inline ml-0 ml-md-3" action="{{url('payroll/item')}}">
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
            <div class="card-body table-responsive p-0">
                <table class="table  table-striped table-hover table-condensed" id="data-table">
                    <thead>
                    <tr>
                        <th>
                            <a href="{{table_order_link('name')}}">
                                {{ trans_choice('core::general.name',1) }}
                            </a>
                        </th>
                        <th>
                            <a href="{{table_order_link('amount_type')}}">
                                {{ trans_choice('payroll::general.type',1) }}
                            </a>
                        </th>
                        <th>
                            <a href="{{table_order_link('amount')}}">
                                {{ trans_choice('payroll::general.amount',1) }}
                            </a>
                        </th>
                        <th>{{ trans_choice('core::general.action',1) }}</th>
                    </tr>
                    </thead>
                    <tbody>
                    @foreach($data as $key)
                        <tr>
                            <td>{{$key->name}}</td>
                            <td>
                                @if($key->type=='allowance')
                                    {{trans_choice('payroll::general.allowance',1)}}
                                @else
                                    {{trans_choice('payroll::general.deduction',1)}}
                                @endif
                            </td>
                            <td>
                                {{$key->amount}}
                                @if($key->amount_type=='fixed')
                                    {{trans_choice('payroll::general.fixed',1)}}
                                @else
                                    %
                                @endif
                            </td>
                            <td>
                                <div class="btn-group">
                                    <button href="#" class="btn btn-default dropdown-toggle"
                                            data-toggle="dropdown">
                                        <i class="fas fa-ellipsis-h"></i>
                                    </button>
                                    <div class="dropdown-menu dropdown-menu-right">
                                        @can('payroll.payroll.items.edit')
                                            <a href="{{url('payroll/item/' . $key->id . '/edit') }}"
                                               class="dropdown-item"> <i
                                                        class="far fa-edit"></i> {{ trans_choice('core::general.edit', 2) }}
                                            </a>
                                        @endcan
                                        @can('payroll.payroll.items.destroy')
                                            <a href="{{url('payroll/item/' . $key->id . '/destroy') }}"
                                               class="dropdown-item confirm"><i
                                                        class="fas fa-trash"></i> {{ trans_choice('core::general.delete', 2) }}
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
                <div class="nk-block-between-md g-3">
                    <div class="g">
                        {{$data->links()}}
                    </div>
                    <div class="g">
                        <div class="pagination-goto d-flex justify-content-center justify-content-md-start gx-3">
                            <div>{{ trans_choice('core::general.page',1) }} {{$data->currentPage()}} {{ trans_choice('core::general.of',1) }} {{$data->lastPage()}}</div>
                        </div>
                    </div><!-- .pagination-goto -->
                </div><!-- .nk-block-between -->
            </div>
        </div>
    </section>
@endsection
@section('scripts')

@endsection
