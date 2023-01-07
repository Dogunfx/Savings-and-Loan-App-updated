@extends('core::layouts.master')
@section('title')
    {{ trans_choice('communication::general.log',2) }}
@endsection
@section('styles')
@stop
@section('content')
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1>{{ trans_choice('communication::general.log',2) }}</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a
                                    href="{{url('dashboard')}}">{{ trans_choice('dashboard::general.dashboard',1) }}</a>
                        </li>
                        <li class="breadcrumb-item active">{{ trans_choice('communication::general.log',2) }}</li>
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
                    <form class="form-inline ml-0 ml-md-3" action="{{url('communication/log')}}">
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
                            <a href="{{table_order_link('campaign_type')}}">
                                <span>{{ trans_choice('core::general.type',1) }}</span>
                            </a>
                        </th>
                        <th>
                            <span>{{ trans_choice('communication::general.send_to',1) }}</span>
                        </th>
                        <th>
                            <span>{{ trans_choice('core::general.description',1) }}</span>
                        </th>
                        <th class="nk-tb-col tb-col-md">
                            <a href="{{table_order_link('status')}}">
                                <span>{{ trans_choice('core::general.status',1) }}</span>
                            </a>
                        </th>

                        <th class="nk-tb-col tb-col-md">

                            <span>{{ trans_choice('communication::general.campaign',1) }} {{ trans_choice('core::general.name',1) }}</span>

                        </th>
                        <th class="nk-tb-col tb-col-md">
                            <a href="{{table_order_link('created_at')}}">
                                <span>{{ trans_choice('core::general.date',1) }}</span>
                            </a>
                        </th>
                    </tr>
                    </thead>
                    <tbody>
                    @foreach($data as $key)
                        <tr>
                            <td>
                                @if($key->campaign_type == 'sms')
                                    <span>{{trans_choice('communication::general.sms', 1)}}</span>
                                @endif
                                @if($key->campaign_type == 'email')
                                    <span>{{trans_choice('communication::general.email', 1)}}</span>
                                @endif
                            </td>
                            <td>
                                <span>{{$key->send_to}}</span>
                            </td>
                            <td>
                                <span>{{$key->description}}</span>
                            </td>
                            <td>
                                @if($key->status == 'pending')
                                    <span class="badge badge-warning">{{trans_choice('communication::general.pending', 1)}}</span>
                                @endif
                                @if($key->status == 'failed')
                                    <span class="badge badge-danger">{{trans_choice('communication::general.failed', 1)}}</span>
                                @endif
                                @if($key->status == 'sent')
                                    <span class="badge badge-info">{{trans_choice('communication::general.sent', 1)}}</span>
                                @endif
                                @if($key->status == 'delivered')
                                    <span class="badge badge-success">{{trans_choice('communication::general.delivered', 1)}}</span>
                                @endif
                                @if($key->status == 'done')
                                    <span class="badge badge-success">{{trans_choice('communication::general.done', 1)}}</span>
                                @endif
                            </td>
                            <td>
                                    <span>{{$key->campaign}}</span>

                            </td>
                            <td>
                                <span>{{$key->created_at->format('Y-m-d')}}</span>
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
