@extends('core::layouts.master')
@section('title')
    {{ trans_choice('share::general.share',1) }}  {{ trans_choice('share::general.charge',2) }}
@endsection
@section('styles')
@stop
@section('content')
    <div class="nk-block-head-content">
        <div class="nk-block-head nk-block-head-sm">
            <div class="nk-block-between">
                <div class="nk-block-head-content">
                    <h3 class="nk-block-title page-title">{{ trans_choice('share::general.share',1) }}  {{ trans_choice('share::general.charge',2) }}</h3>
                    <div class="nk-block-des text-soft">
                        <p>{{number_format($data->total())}} {{ trans_choice('share::general.charge',2) }} {{ trans_choice('core::general.found',1) }}</p>
                    </div>
                </div><!-- .nk-block-head-content -->
                <div class="nk-block-head-content">
                    <div class="toggle-wrap nk-block-tools-toggle">
                        <a href="#" class="btn btn-icon btn-trigger toggle-expand mr-n1" data-target="pageMenu"><em
                                    class="icon ni ni-menu-alt-r"></em></a>
                        <div class="toggle-expand-content" data-content="pageMenu">
                            <ul class="nk-block-tools g-3">
                                <li><a href="#" class="btn btn-white btn-outline-light"><em
                                                class="icon ni ni-download-cloud"></em><span>Export</span></a></li>
                                <li class="nk-block-tools-opt">
                                    @can('share.shares.charges.create')
                                        <a href="{{ url('share/charge/create') }}" class="btn btn-info btn-sm">
                                            {{ trans_choice('core::general.add',1) }} {{ trans_choice('share::general.charge',1) }}
                                        </a>
                                    @endcan
                                </li>
                            </ul>
                        </div>
                    </div><!-- .toggle-wrap -->
                </div><!-- .nk-block-head-content -->
            </div><!-- .nk-block-between -->
        </div>
    </div>
    <div class="nk-block" id="app">
        <div class="card card-bordered card-stretch">
            <div class="card-inner-group">
                <div class="card-inner position-relative card-tools-toggle">
                    <div class="card-title-group">
                        <div class="card-tools">
                            <div class="form-inline flex-nowrap gx-3">
                                <div class="form-wrap w-150px">
                                    <select class="form-select form-select-sm" data-search="off"
                                            data-placeholder="Bulk Action">
                                        <option value="">Bulk Action</option>
                                        <option value="email">Send Email</option>
                                        <option value="group">Change Group</option>
                                        <option value="suspend">Suspend User</option>
                                        <option value="delete">Delete User</option>
                                    </select>
                                </div>
                                <div class="btn-wrap">
                                    <span class="d-none d-md-block"><button
                                                class="btn btn-dim btn-outline-light disabled">Apply</button></span>
                                    <span class="d-md-none"><button
                                                class="btn btn-dim btn-outline-light btn-icon disabled"><em
                                                    class="icon ni ni-arrow-right"></em></button></span>
                                </div>
                            </div><!-- .form-inline -->
                        </div><!-- .card-tools -->
                        <div class="card-tools mr-n1">
                            <ul class="btn-toolbar gx-1">
                                <li>
                                    <a href="#" class="btn btn-icon search-toggle toggle-search"
                                       data-target="search"><em class="icon ni ni-search"></em></a>
                                </li><!-- li -->
                                <li class="btn-toolbar-sep"></li><!-- li -->
                                <li>
                                    <div class="toggle-wrap">
                                        <a href="#" class="btn btn-icon btn-trigger toggle" data-target="cardTools"><em
                                                    class="icon ni ni-menu-right"></em></a>
                                        <div class="toggle-content" data-content="cardTools">
                                            <ul class="btn-toolbar gx-1">
                                                <li class="toggle-close">
                                                    <a href="#" class="btn btn-icon btn-trigger toggle"
                                                       data-target="cardTools"><em
                                                                class="icon ni ni-arrow-left"></em></a>
                                                </li><!-- li -->
                                                <li>
                                                    <div class="dropdown">
                                                        <a href="#" class="btn btn-trigger btn-icon dropdown-toggle"
                                                           data-toggle="dropdown">
                                                            <div class="dot dot-primary"></div>
                                                            <em class="icon ni ni-filter-alt"></em>
                                                        </a>
                                                        <div class="filter-wg dropdown-menu dropdown-menu-xl dropdown-menu-right">
                                                            <div class="dropdown-head">
                                                                <span class="sub-title dropdown-title">Filter Users</span>
                                                                <div class="dropdown">
                                                                    <a href="#" class="btn btn-sm btn-icon">
                                                                        <em class="icon ni ni-more-h"></em>
                                                                    </a>
                                                                </div>
                                                            </div>
                                                            <div class="dropdown-body dropdown-body-rg">
                                                                <div class="row gx-6 gy-3">
                                                                    <div class="col-6">
                                                                        <div class="form-group">
                                                                            <select class="form-control form-select-sm"
                                                                                    id="role">
                                                                                <option value="any">Any Role</option>
                                                                                <option value="investor">Investor
                                                                                </option>
                                                                                <option value="seller">Seller</option>
                                                                                <option value="buyer">Buyer</option>
                                                                            </select>
                                                                        </div>
                                                                    </div>
                                                                    <div class="col-6">
                                                                        <div class="custom-control custom-control-sm custom-checkbox">
                                                                            <input type="checkbox"
                                                                                   class="custom-control-input"
                                                                                   id="hasKYC">
                                                                            <label class="custom-control-label"
                                                                                   for="hasKYC"> KYC Verified</label>
                                                                        </div>
                                                                    </div>
                                                                    <div class="col-12">
                                                                        <div class="form-group">
                                                                            <button type="button"
                                                                                    class="btn btn-secondary">Filter
                                                                            </button>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            <div class="dropdown-foot between">
                                                                <a class="clickable" href="#">Reset Filter</a>
                                                                <a href="#">Save Filter</a>
                                                            </div>
                                                        </div><!-- .filter-wg -->
                                                    </div><!-- .dropdown -->
                                                </li><!-- li -->
                                                <li>
                                                    <div class="dropdown">
                                                        <a href="#" class="btn btn-trigger btn-icon dropdown-toggle"
                                                           data-toggle="dropdown">
                                                            <em class="icon ni ni-setting"></em>
                                                        </a>
                                                        <div class="dropdown-menu dropdown-menu-xs dropdown-menu-right">
                                                            <ul class="link-check">
                                                                <li><span>Show</span></li>
                                                                <li class="{{request('per_page')==10?'active':''}}">
                                                                    <a href="{{request()->fullUrlWithQuery(['per_page'=>10])}}">10</a>
                                                                </li>
                                                                <li class="{{(request('per_page')==20||!request('per_page'))?'active':''}}">
                                                                    <a href="{{request()->fullUrlWithQuery(['per_page'=>20])}}">20</a>
                                                                </li>
                                                                <li class="{{request('per_page')==50?'active':''}}">
                                                                    <a href="{{request()->fullUrlWithQuery(['per_page'=>50])}}">50</a>
                                                                </li>
                                                            </ul>
                                                            <ul class="link-check">
                                                                <li><span>Order</span></li>
                                                                <li class="{{(request('order_by_dir')=='asc'||!request('order_by_dir'))?'active':''}}">
                                                                    <a href="{{request()->fullUrlWithQuery(['order_by_dir'=>'asc'])}}">ASC</a>
                                                                </li>
                                                                <li class="{{request('order_by_dir')=='desc'?'active':''}}">
                                                                    <a href="{{request()->fullUrlWithQuery(['order_by_dir'=>'desc'])}}">DESC</a>
                                                                </li>
                                                            </ul>
                                                        </div>
                                                    </div><!-- .dropdown -->
                                                </li><!-- li -->
                                            </ul><!-- .btn-toolbar -->
                                        </div><!-- .toggle-content -->
                                    </div><!-- .toggle-wrap -->
                                </li><!-- li -->
                            </ul><!-- .btn-toolbar -->
                        </div><!-- .card-tools -->
                    </div><!-- .card-title-group -->
                    <div class="card-search search-wrap" data-search="search">
                        <div class="card-body">
                            <form method="get"
                                  action="{{url('share/charge')}}">
                                <div class="search-content">
                                    <a href="#" class="search-back btn btn-icon toggle-search" data-target="search"><em
                                                class="icon ni ni-arrow-left"></em></a>
                                    <input type="text" name="s" class="form-control border-transparent form-focus-none"
                                           placeholder="Search by name or email" value="{{request('s')}}">
                                    <button class="search-submit btn btn-icon"><em class="icon ni ni-search"></em>
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div><!-- .card-search -->
                </div><!-- .card-inner -->
                <div class="card-inner p-0">
                    <div class="nk-tb-list nk-tb-ulist is-compact">
                        <div class="nk-tb-item nk-tb-head">
                            <div class="nk-tb-col nk-tb-col-check">
                                <div class="custom-control custom-control-sm custom-checkbox notext">
                                    <input type="checkbox" class="custom-control-input" v-on:change="selectAllRecords"
                                           v-model="selectAll"
                                           id="selectAll">
                                    <label class="custom-control-label" for="selectAll"></label>
                                </div>
                            </div>
                            <div class="nk-tb-col tb-col-mb">
                                <a href="{{table_order_link('name')}}">
                                    <span class="sub-text">{{ trans_choice('core::general.name',1) }}</span>
                                </a>
                            </div>
                            <div class="nk-tb-col tb-col-mb">
                                <a href="{{table_order_link('share_charge_type_id')}}">
                                    <span class="sub-text">{{ trans_choice('share::general.charge',1) }} {{ trans_choice('core::general.type',1) }}</span>
                                </a>
                            </div>
                            <div class="nk-tb-col tb-col-mb">
                                <a href="{{table_order_link('amount')}}">
                                    <span class="sub-text">{{ trans_choice('core::general.amount',1) }}</span>
                                </a>
                            </div>
                            <div class="nk-tb-col tb-col-mb">
                                <a href="{{table_order_link('active')}}">
                                    <span class="sub-text">{{ trans('core::general.active') }}</span>
                                </a>
                            </div>
                            <div class="nk-tb-col nk-tb-col-tools text-right">
                                <span
                                        class="sub-text">{{ trans_choice('core::general.action',1) }}</span>
                            </div>
                        </div><!-- .nk-tb-item -->
                        @foreach($data as $key)
                            <div class="nk-tb-item">
                                <div class="nk-tb-col nk-tb-col-check">
                                    <div class="custom-control custom-control-sm custom-checkbox notext">
                                        <input type="checkbox" class="custom-control-input" value="{{$key->id}}"
                                               v-model="selectedRecords" v-on:click="selectAll=false" id="{{$key->id}}">
                                        <label class="custom-control-label" for="{{$key->id}}"></label>
                                    </div>
                                </div>
                                <div class="nk-tb-col  tb-col-mb">
                                    <a href="#">
                                        <span>{{$key->name}}</span>
                                    </a>
                                </div>
                                <div class="nk-tb-col  tb-col-mb">
                                    @if($key->share_charge_type_id==1)
                                        <span>{{trans_choice('share::general.share_account_activation', 1)}}</span>
                                    @endif
                                    @if($key->share_charge_type_id==2)
                                        <span>{{trans_choice('share::general.share_purchase', 1)}}</span>
                                    @endif
                                    @if($key->share_charge_type_id==3)
                                        <span>{{trans_choice('share::general.share_redeem', 1)}}</span>
                                    @endif
                                </div>
                                <div class="nk-tb-col  tb-col-mb">
                                    @if($key->share_charge_option_id==1)
                                        <span>{{number_format($key->amount, 2)}} {{trans_choice('share::general.flat', 1)}}</span>
                                    @endif
                                    @if($key->share_charge_option_id==2)
                                        <span>{{number_format($key->amount, 2)}} % {{trans_choice('share::general.percentage_of_amount', 1)}}</span>
                                    @endif
                                </div>
                                <div class="nk-tb-col  tb-col-mb">
                                    @if($key->active==1)
                                        <span class="badge badge-success">{{trans_choice('core::general.yes', 1)}}</span>
                                    @else
                                        <span class="badge badge-danger">{{trans_choice('core::general.no', 1)}}</span>
                                    @endif
                                </div>
                                <div class="nk-tb-col nk-tb-col-tools">
                                    <ul class="nk-tb-actions gx-1">
                                        <li>
                                            <div class="drodown">
                                                <a href="#" class="dropdown-toggle btn btn-icon btn-trigger"
                                                   data-toggle="dropdown"><em class="icon ni ni-more-h"></em></a>
                                                <div class="dropdown-menu dropdown-menu-right">
                                                    <ul class="link-list-opt no-bdr">
                                                        @can('share.shares.charges.edit')
                                                            <li>
                                                                <a href="{{url('share/charge/' . $key->id . '/edit')}}">
                                                                    <em class="icon ni ni-edit"></em>
                                                                    <span>{{trans_choice('core::general.edit',1)}}</span>
                                                                </a>
                                                            </li>
                                                        @endcan
                                                        <li class="divider"></li>
                                                        @can('share.shares.charges.destroy')
                                                            <li>
                                                                <a href="{{url('share/charge/' . $key->id . '/destroy')}}"
                                                                   class="confirm">
                                                                    <em class="icon ni ni-trash"></em>
                                                                    <span>{{trans_choice('core::general.delete',1)}}</span>
                                                                </a>
                                                            </li>
                                                        @endcan
                                                    </ul>
                                                </div>
                                            </div>
                                        </li>
                                    </ul>
                                </div>
                            </div>
                        @endforeach
                    </div><!-- .nk-tb-list -->
                </div><!-- .card-inner -->
                <div class="card-inner">
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
                </div><!-- .card-inner -->

            </div><!-- .card-inner-group -->
        </div><!-- .card -->
    </div>
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
