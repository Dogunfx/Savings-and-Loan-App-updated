@extends('core::layouts.master')
@section('title')
    {{ trans_choice('asset::general.asset',2) }}
@endsection
@section('styles')
@stop
@section('content')
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1>{{ trans_choice('asset::general.asset',2) }}</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a
                                    href="{{url('dashboard')}}">{{ trans_choice('dashboard::general.dashboard',1) }}</a>
                        </li>
                        <li class="breadcrumb-item active">{{ trans_choice('asset::general.asset',2) }}</li>
                    </ol>
                </div>
            </div>
        </div><!-- /.container-fluid -->
    </section>
    <section class="content">
        <div class="card">
            <div class="card-header">
                @can('asset.assets.create')
                    <a href="{{ url('asset/create') }}" class="btn btn-info btn-sm">
                        <i class="fas fa-plus"></i> {{ trans_choice('core::general.add',1) }} {{ trans_choice('asset::general.asset',1) }}
                    </a>
                @endif
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
                    <form class="form-inline ml-0 ml-md-3" action="{{url('asset')}}">
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
                <table class="table table-hover table-striped" id="data-table">
                    <thead>
                    <tr>
                        <th>
                            <a href="{{table_order_link('name')}}">
                                {{ trans_choice('core::general.name',1) }}
                            </a>
                        </th>
                        <th>
                            <a href="{{table_order_link('branch')}}">
                                {{ trans_choice('core::general.branch',1) }}
                            </a>
                        </th>
                        <th>
                            <a href="{{table_order_link('asset_type')}}">
                                {{ trans_choice('core::general.type',1) }}
                            </a>
                        </th>
                        <th>
                            <a href="{{table_order_link('purchase_date')}}">
                                {{ trans_choice('asset::general.purchase',1) }} {{ trans('core::general.date') }}
                            </a>
                        </th>
                        <th>
                            <a href="{{table_order_link('value')}}">
                                {{ trans_choice('asset::general.cost',1) }}
                            </a>
                        </th>
                        <th>
                            <a href="{{table_order_link('life_span')}}">
                                {{ trans_choice('asset::general.life_span',1) }}
                            </a>
                        </th>
                        <th>
                            <a href="{{table_order_link('salvage_value')}}">
                                {{ trans_choice('asset::general.salvage_value',1) }}
                            </a>
                        </th>
                        <th>{{ trans_choice('asset::general.current',1) }} {{ trans_choice('asset::general.value',1) }}</th>
                        <th>{{ trans_choice('core::general.action',1) }}</th>
                    </tr>
                    </thead>
                    <tbody>
                    @foreach($data as $key)
                        <tr>
                            <td>
                                <a href="{{url('asset/' . $key->id . '/show')}}">
                                    <span>{{$key->name}}</span>
                                </a>
                            </td>
                            <td>
                                <span>{{$key->branch}}</span>
                            </td>
                            <td>
                                <span>{{$key->asset_type}}</span>
                            </td>
                            <td>
                                <span>{{$key->purchase_date}}</span>
                            </td>
                            <td>
                                <span>{{number_format($key->value,2)}}</span>
                            </td>
                            <td>
                                <span>{{number_format($key->life_span)}}</span>
                            </td>
                            <td>
                                <span>{{number_format($key->salvage_value,2)}}</span>
                            </td>
                            <td>
                                @if(!empty($key->depreciation->first()))
                                    <span>{{number_format($key->depreciation->first()->ending_value,2)}}</span>
                                @endif
                            </td>
                            <td>
                                <div class="btn-group">
                                    <button href="#" class="btn btn-default dropdown-toggle"
                                            data-toggle="dropdown">
                                        <i class="fas fa-ellipsis-h"></i>
                                    </button>
                                    <div class="dropdown-menu dropdown-menu-right">
                                        <a href="{{url('asset/' . $key->id . '/show')}}" class="dropdown-item">
                                            <i class="fas fa-eye"></i>
                                            <span>{{trans_choice('core::general.detail',2)}}</span>
                                        </a>
                                        @can('asset.assets.edit')
                                            <a href="{{url('asset/' . $key->id . '/edit')}}" class="dropdown-item">
                                                <i class="fas fa-edit"></i>
                                                <span>{{trans_choice('core::general.edit',1)}}</span>
                                            </a>
                                        @endcan
                                        <div class="divider"></div>
                                        @can('asset.assets.destroy')
                                            <a href="{{url('asset/' . $key->id . '/destroy')}}"
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
