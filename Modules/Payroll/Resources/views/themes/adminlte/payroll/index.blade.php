@extends('core::layouts.master')
@section('title')
    {{ trans_choice('payroll::general.payroll',2) }}
@endsection
@section('styles')
@stop
@section('content')
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1>{{ trans_choice('payroll::general.payroll',2) }}</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a
                                    href="{{url('dashboard')}}">{{ trans_choice('dashboard::general.dashboard',1) }}</a>
                        </li>
                        <li class="breadcrumb-item active">{{ trans_choice('payroll::general.payroll',2) }}</li>
                    </ol>
                </div>
            </div>
        </div><!-- /.container-fluid -->
    </section>

    <!-- Main content -->
    <section class="content">
        <div class="card">
            <div class="card-header">
                @can('payroll.payroll.items.create')
                    <a href="{{ url('payroll/create') }}" class="btn btn-info btn-sm">
                        <i class="fas fa-plus"></i> {{ trans_choice('core::general.add',1) }} {{ trans_choice('payroll::general.payroll',1) }}
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
                    <form class="form-inline ml-0 ml-md-3" action="{{url('payroll')}}">
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
                            <a href="{{table_order_link('id')}}">
                                {{ trans_choice('core::general.id',1) }}
                            </a>
                        </th>
                        <th>
                            <a href="{{table_order_link('user')}}">
                                {{ trans_choice('core::general.user',1) }}
                            </a>
                        </th>
                        <th>
                            <a href="{{table_order_link('date')}}">
                                {{ trans_choice('core::general.date',1) }}
                            </a>
                        </th>
                        <th>
                            <a href="{{table_order_link('gross_amount')}}">
                                {{ trans_choice('payroll::general.amount',1) }}
                            </a>
                        </th>
                        <th>{{ trans_choice('payroll::general.payment',1) }} {{ trans_choice('payroll::general.status',1) }}</th>
                        <th>{{ trans_choice('core::general.action',1) }}</th>
                    </tr>
                    </thead>
                    <tbody>
                    @foreach($data as $key)
                        <tr>
                            <td>
                                <a href="{{url('payroll/' . $key->id . '/show')}}">
                                    <span>{{$key->id}}</span>
                                </a>
                            </td>
                            <td>
                                <a href="{{url('user/' . $key->user_id . '/show')}}">
                                    <span>{{$key->user}}</span>
                                </a>
                            </td>
                            <td>
                                <span>{{$key->date}}</span>
                            </td>
                            <td>
                                <span>{{number_format($key->gross_amount,2)}}</span>
                            </td>
                            <td>
                                @if($key->payments >= $key->gross_amount)
                                    <span class="badge badge-success">{{trans_choice('payroll::general.paid', 1)}}</span>
                                @elseif($key->payments < $key->gross_amount && $key->payments > 0)
                                    <span class="badge badge-warning">{{trans_choice('payroll::general.partially_paid', 1)}}</span>
                                @else
                                    <span class="badge badge-danger">{{trans_choice('payroll::general.unpaid', 1)}}</span>
                                @endif
                            </td>
                            <td>
                                <div class="btn-group">
                                    <button href="#" class="btn btn-default dropdown-toggle"
                                            data-toggle="dropdown">
                                        <i class="fas fa-ellipsis-h"></i>
                                    </button>
                                    <div class="dropdown-menu dropdown-menu-right">
                                        <a href="{{url('payroll/' . $key->id . '/show')}}" class="dropdown-item">
                                            <i class="far fa-eye"></i>
                                            <span>{{trans_choice('core::general.detail',2)}}</span>
                                        </a>
                                        @can('payroll.payroll.edit')
                                            <a href="{{url('payroll/' . $key->id . '/edit')}}" class="dropdown-item">
                                                <i class="far fa-edit"></i>
                                                <span>{{trans_choice('core::general.edit',1)}}</span>
                                            </a>
                                        @endcan
                                        <div class="divider"></div>
                                        @can('payroll.payroll.destroy')
                                            <a href="{{url('payroll/' . $key->id . '/destroy')}}"
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
