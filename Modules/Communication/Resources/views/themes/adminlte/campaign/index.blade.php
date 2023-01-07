@extends('core::layouts.master')
@section('title')
    {{ trans_choice('communication::general.campaign',2) }}
@endsection
@section('styles')
@stop
@section('content')
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1>{{ trans_choice('communication::general.campaign',2) }}</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a
                                    href="{{url('dashboard')}}">{{ trans_choice('dashboard::general.dashboard',1) }}</a>
                        </li>
                        <li class="breadcrumb-item active">{{ trans_choice('communication::general.campaign',2) }}</li>
                    </ol>
                </div>
            </div>
        </div><!-- /.container-fluid -->
    </section>

    <!-- Main content -->
    <section class="content">
        <div class="card">
            <div class="card-header">
                @can('communication.campaigns.create')
                    <a href="{{ url('communication/campaign/create') }}"
                       class="btn btn-info btn-sm">
                        <i class="fas fa-plus"></i> {{ trans_choice('core::general.add',1) }} {{ trans_choice('communication::general.campaign',1) }}
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
                    <form class="form-inline ml-0 ml-md-3" action="{{url('communication/campaign')}}">
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
                            <a href="{{table_order_link('campaign_type')}}">
                                <span>{{ trans_choice('core::general.type',1) }}</span>
                            </a>
                        </th>
                        <th>
                            <span>{{ trans_choice('core::general.created_by',1) }}</span>
                        </th>
                        <th>
                            <a href="{{table_order_link('trigger_type')}}">
                                <span>{{ trans_choice('communication::general.campaign',1) }} {{ trans_choice('core::general.type',1) }}</span>
                            </a>
                        </th>
                        <th>
                            <a href="{{table_order_link('status')}}">
                                <span>{{ trans_choice('core::general.status',1) }}</span>
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
                                @if($key->campaign_type == 'sms')
                                    <span>{{trans_choice('communication::general.sms', 1)}}</span>
                                @endif
                                @if($key->campaign_type == 'email')
                                    <span>{{trans_choice('communication::general.email', 1)}}</span>
                                @endif
                            </td>
                            <td>
                                <a href="{{url('user/'.$key->created_by_id.'/show')}}">
                                    <span>{{$key->created_by}}</span>
                                </a>
                            </td>
                            <td>
                                @if($key->trigger_type == 'direct')
                                    <span>{{trans_choice('communication::general.direct', 1)}}</span>
                                @endif
                                @if($key->trigger_type == 'schedule')
                                    <span>{{trans_choice('communication::general.schedule', 1)}}</span>
                                @endif
                                @if($key->trigger_type == 'triggered')
                                    <span>{{trans_choice('communication::general.triggered', 1)}}</span>
                                @endif
                            </td>
                            <td>
                                @if($key->status == 'pending')
                                    <span class="badge badge-warning">{{trans_choice('communication::general.pending', 1)}}</span>
                                @endif
                                @if($key->status == 'active')
                                    <span class="badge badge-info">{{trans_choice('communication::general.active', 1)}}</span>
                                @endif
                                @if($key->status == 'inactive')
                                    <span class="badge badge-warning">{{trans_choice('communication::general.inactive', 1)}}</span>
                                @endif
                                @if($key->status == 'closed')
                                    <span class="badge badge-success">{{trans_choice('communication::general.closed', 1)}}</span>
                                @endif
                                @if($key->status == 'done')
                                    <span class="badge badge-success">{{trans_choice('communication::general.done', 1)}}</span>
                                @endif
                            </td>
                            <td>
                                <div class="btn-group">
                                    <button href="#" class="btn btn-default dropdown-toggle"
                                            data-toggle="dropdown">
                                        <i class="fas fa-ellipsis-h"></i>
                                    </button>
                                    <div class="dropdown-menu dropdown-menu-right">
                                        <a href="{{url('communication/campaign/' . $key->id . '/show')}}"
                                           class="dropdown-item">
                                            <i class="fas fa-eye"></i>
                                            <span>{{trans_choice('core::general.detail',2)}}</span>
                                        </a>
                                        @can('communication.campaigns.edit')
                                            <a href="{{url('communication/campaign/' . $key->id . '/edit')}}"
                                               class="dropdown-item">
                                                <i class="far fa-edit"></i>
                                                <span>{{trans_choice('core::general.edit',1)}}</span>
                                            </a>
                                        @endcan
                                        <div class="divider"></div>
                                        @can('communication.campaigns.destroy')
                                            <a href="{{url('communication/campaign/' . $key->id . '/destroy')}}"
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
