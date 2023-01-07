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
                    <h1>{{ trans_choice('loan::general.loan',1) }}  {{ trans_choice('loan::general.application',2) }}</h1>
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

    <!-- Main content -->
    <section class="content">
        <div class="card">
            <div class="card-header">
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
                    <form class="form-inline ml-0 ml-md-3" action="{{url('loan/application')}}">
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
                            <a href="{{table_order_link('id')}}">
                                <span>{{ trans_choice('core::general.id',1) }}</span>
                            </a>
                        </th>
                        <th>
                            <a href="{{table_order_link('branch')}}">
                                <span>{{ trans_choice('core::general.branch',1) }}</span>
                            </a>
                        </th>
                        <th>
                            <a href="{{table_order_link('client')}}">
                                <span>{{ trans_choice('client::general.client',1) }}</span>
                            </a>
                        </th>
                        <th>
                            <a href="{{table_order_link('loan_product')}}">
                                <span>{{ trans_choice('loan::general.product',1) }}</span>
                            </a>
                        </th>
                        <th>
                            <a href="{{table_order_link('amount')}}">
                                <span>{{ trans_choice('core::general.amount',1) }}</span>
                            </a>
                        </th>
                        <th>
                            <a href="{{table_order_link('status')}}">
                                <span>{{ trans_choice('loan::general.status',1) }}</span>
                            </a>
                        </th>
                        <th>
                            <a href="{{table_order_link('created_at')}}">
                                <span>{{ trans('core::general.date') }}</span>
                            </a>
                        </th>
                        <th>{{ trans_choice('core::general.action',1) }}</th>
                    </tr>
                    </thead>
                    <tbody>
                    @foreach($data as $key)
                        <tr>
                            <td>
                                <a href="{{url('loan/'.$key->id.'/show')}}">
                                    <span>{{$key->id}}</span>
                                </a>
                            </td>
                            <td>
                                <a href="{{url('branch/'.$key->id.'/show')}}">
                                    <span>{{$key->branch}}</span>
                                </a>
                            </td>
                            <td>
                                <a href="{{url('client/'.$key->id.'/show')}}">
                                    <span>{{$key->client}}</span>
                                </a>
                            </td>
                            <td>
                                <span>{{$key->loan_product}}</span>
                            </td>
                            <td>
                                <span>{{number_format($key->amount,2)}}</span>
                            </td>
                            <td>
                                @if($key->status == 'pending')
                                    <span class="badge badge-warning">{{trans_choice('loan::general.pending_approval', 1)}}</span>
                                @endif
                                @if($key->status == 'submitted')
                                    <span class="badge badge-warning">{{trans_choice('loan::general.pending_approval', 1)}}</span>
                                @endif
                                @if($key->status == 'overpaid')
                                    <span class="badge badge-warning">{{trans_choice('loan::general.overpaid', 1)}}</span>
                                @endif
                                @if($key->status == 'approved')
                                    <span class="badge badge-warning">{{trans_choice('loan::general.awaiting_disbursement', 1)}}</span>
                                @endif
                                @if($key->status == 'active')
                                    <span class="badge badge-info">{{trans_choice('loan::general.active', 1)}}</span>
                                @endif
                                @if($key->status == 'rejected')
                                    <span class="badge badge-danger">{{trans_choice('loan::general.rejected', 1)}}</span>
                                @endif
                                @if($key->status == 'withdrawn')
                                    <span class="badge badge-danger">{{trans_choice('loan::general.withdrawn', 1)}}</span>
                                @endif
                                @if($key->status == 'written_off')
                                    <span class="badge badge-danger">{{trans_choice('loan::general.written_off', 1)}}</span>
                                @endif
                                @if($key->status == 'closed')
                                    <span class="badge badge-success">{{trans_choice('loan::general.closed', 1)}}</span>
                                @endif
                                @if($key->status == 'pending_reschedule')
                                    <span class="badge badge-warning">{{trans_choice('loan::general.pending_reschedule', 1)}}</span>
                                @endif
                                @if($key->status == 'rescheduled')
                                    <span class="badge badge-info">{{trans_choice('loan::general.rescheduled', 1)}}</span>
                                @endif
                            </td>
                            <td>
                                <span>{{$key->created_at}}</span>
                            </td>
                            <td>
                                <div class="btn-group">
                                    <button href="#" class="btn btn-default dropdown-toggle"
                                            data-toggle="dropdown">
                                        <i class="fas fa-ellipsis-h"></i>
                                    </button>
                                    <div class="dropdown-menu dropdown-menu-right">
                                        <a href="{{url('loan/application/' . $key->id . '/show')}}" class="dropdown-item">
                                            <i class="far fa-eye"></i>
                                            <span>{{trans_choice('core::general.detail',2)}}</span>
                                        </a>

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
