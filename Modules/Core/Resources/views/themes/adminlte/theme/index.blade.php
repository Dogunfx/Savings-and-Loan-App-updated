@extends('core::layouts.master')
@section('title')
    {{ trans_choice('core::general.manage',1) }} {{ trans_choice('core::general.theme',2) }}
@endsection
@section('styles')
@stop
@section('content')
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1>
                        {{ trans_choice('core::general.manage',1) }} {{ trans_choice('core::general.theme',2) }}
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
                        <li class="breadcrumb-item active">{{ trans_choice('core::general.manage',1) }} {{ trans_choice('core::general.theme',2) }}</li>
                    </ol>
                </div>
            </div>
        </div><!-- /.container-fluid -->
    </section>
    <section class="content" id="app">
        <div class="card">
            <div class="card-header">
                <a href="{{ url('theme/upload') }}" class="btn btn-info btn-sm">
                    {{ trans_choice('core::general.upload',1) }} {{ trans_choice('core::general.theme',1) }}
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
                <div class="card-tools">

                </div>
            </div>
            <div class="card-body p-0">
                <table id="data-table" class="table table-striped table-condensed table-hover">
                    <thead>
                    <tr>
                        <th>
                            {{ trans_choice('core::general.name',1) }}
                        </th>
                        <th>
                            {{ trans_choice('core::general.description',1) }}
                        </th>
                        <th>
                            {{ trans_choice('core::general.version',1) }}
                        </th>
                        <th>
                            {{ trans_choice('core::general.active',1) }}
                        </th>
                    </tr>
                    </thead>
                    <tbody>
                    @foreach($data as $key)
                        <tr>
                            <td>
                                <span>{{$key->name}}</span>
                            </td>
                            <td>
                                <span>{{$key->description}}</span>
                            </td>
                            <td>
                                <span>{{$key->version}}</span>
                            </td>
                            <td>
                                @if($key->name!=config('active_theme'))
                                    <a href="{{url('theme/?action=activate&name='.strtolower($key->name))}}"
                                       class="btn btn-danger confirm">{{trans_choice('core::general.activate',1)}}</a>
                                @else
                                    <span class="badge badge-success">{{trans_choice('core::general.active',1)}}</span>
                                @endif
                            </td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>

            </div>


        </div>
    </section>
@endsection
@section('scripts')
    <script>
        var app = new Vue({
            el: "#app",
            data: {
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
