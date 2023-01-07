@extends('core::layouts.master')
@section('title')
    {{ trans_choice('savings::general.savings',1) }} {{ trans_choice('core::general.detail',2) }}
@endsection
@section('styles')
@stop
@section('content')
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1>
                        {{ trans_choice('savings::general.savings',1) }} {{ trans_choice('core::general.detail',2) }}
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
                        <li class="breadcrumb-item"><a
                                    href="{{url('savings')}}">{{ trans_choice('savings::general.savings',2) }}</a>
                        </li>
                        <li class="breadcrumb-item active">{{ trans_choice('savings::general.savings',1) }} {{ trans_choice('core::general.detail',2) }}</li>
                    </ol>
                </div>
            </div>
        </div><!-- /.container-fluid -->
    </section>
    <section class="content" id="app">
        <div class="row">
            <div class="col-md-12">
                <div class="card ">
                    <div class="card-body">
                        <h5 class="card-title">{{$savings->savings_product->name}}(#{{$savings->id}})</h5>
                        <div class="row">
                            <div class="col-md-12">
                                <div class="float-right btn-group">
                                    @if($savings->status=='submitted' ||$savings->status=='pending')
                                        @can('savings.savings.approve_savings')
                                            <a href="#" data-toggle="modal" data-target="#approve_savings_modal"
                                               class="btn btn-primary"><i
                                                        class="fas fa-check"></i>
                                                {{ trans_choice('savings::general.approve',1) }}
                                            </a>
                                            <a href="#" data-toggle="modal" data-target="#reject_savings_modal"
                                               class="btn btn-primary"><i class="fas fa-times"></i>
                                                {{ trans_choice('savings::general.reject',1) }}
                                            </a>
                                            <a href="#" data-toggle="modal" data-target="#withdraw_savings_modal"
                                               class="btn btn-primary"><i class="fas fa-times"></i>
                                                {{ trans_choice('savings::general.withdraw',1) }}
                                            </a>
                                        @endcan
                                        @can('savings.savings.edit')
                                            <a href="{{url('savings/'.$savings->id.'/edit')}}"
                                               class="btn btn-primary">
                                                <i class="fas fa-edit"></i>
                                                {{ trans_choice('core::general.edit',1) }}
                                            </a>
                                        @endcan
                                        @can('savings.savings.approve_savings')
                                            <div class="modal fade" id="approve_savings_modal">
                                                <div class="modal-dialog">
                                                    <div class="modal-content">
                                                        <div class="modal-header">
                                                            <h4 class="modal-title">{{ trans_choice('savings::general.approve',1) }} {{ trans_choice('savings::general.savings',1) }}</h4>
                                                            <button type="button" class="close" data-dismiss="modal"
                                                                    aria-label="Close">
                                                                <span aria-hidden="true">×</span></button>

                                                        </div>
                                                        <form method="post"
                                                              action="{{ url('savings/'.$savings->id.'/approve_savings') }}">
                                                            {{csrf_field()}}
                                                            <div class="modal-body">
                                                                <div class="form-group">
                                                                    <label for="approved_on_date"
                                                                           class="control-label">{{ trans_choice('core::general.date',1) }}</label>
                                                                    <flat-pickr
                                                                            class="form-control  @error('approved_on_date') is-invalid @enderror"
                                                                            name="approved_on_date"
                                                                            value="{{date("Y-m-d")}}"
                                                                            id="approved_on_date" required>
                                                                    </flat-pickr>
                                                                </div>
                                                                <div class="form-group">
                                                                    <label for="approved_notes"
                                                                           class="control-label">{{ trans_choice('core::general.note',2) }}</label>
                                                                    <textarea name="approved_notes"
                                                                              class="form-control"
                                                                              id="approved_notes"
                                                                              rows="3"></textarea>
                                                                </div>
                                                            </div>
                                                            <div class="modal-footer">
                                                                <button type="button"
                                                                        class="btn btn-default pull-left"
                                                                        data-dismiss="modal">
                                                                    {{ trans_choice('core::general.close',1) }}
                                                                </button>
                                                                <button type="submit"
                                                                        class="btn btn-primary">{{ trans_choice('core::general.save',1) }}</button>
                                                            </div>
                                                        </form>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="modal fade" id="reject_savings_modal">
                                                <div class="modal-dialog">
                                                    <div class="modal-content">
                                                        <div class="modal-header">
                                                            <h4 class="modal-title">{{ trans_choice('savings::general.reject',1) }} {{ trans_choice('savings::general.savings',1) }}</h4>
                                                            <button type="button" class="close" data-dismiss="modal"
                                                                    aria-label="Close">
                                                                <span aria-hidden="true">×</span></button>

                                                        </div>
                                                        <form method="post"
                                                              action="{{ url('savings/'.$savings->id.'/reject_savings') }}">
                                                            {{csrf_field()}}
                                                            <div class="modal-body">
                                                                <div class="form-group">
                                                                    <label for="rejected_notes"
                                                                           class="control-label">{{ trans_choice('core::general.note',2) }}</label>
                                                                    <textarea name="rejected_notes"
                                                                              class="form-control"
                                                                              id="rejected_notes"
                                                                              rows="3" required=""></textarea>
                                                                </div>
                                                            </div>
                                                            <div class="modal-footer">
                                                                <button type="button"
                                                                        class="btn btn-default pull-left"
                                                                        data-dismiss="modal">
                                                                    {{ trans_choice('core::general.close',1) }}
                                                                </button>
                                                                <button type="submit"
                                                                        class="btn btn-primary">{{ trans_choice('core::general.save',1) }}</button>
                                                            </div>
                                                        </form>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="modal fade" id="withdraw_savings_modal">
                                                <div class="modal-dialog">
                                                    <div class="modal-content">
                                                        <div class="modal-header">
                                                            <h4 class="modal-title">{{ trans_choice('savings::general.withdraw',1) }} {{ trans_choice('savings::general.savings',1) }}</h4>
                                                            <button type="button" class="close" data-dismiss="modal"
                                                                    aria-label="Close">
                                                                <span aria-hidden="true">×</span></button>
                                                        </div>
                                                        <form method="post"
                                                              action="{{ url('savings/'.$savings->id.'/withdraw_savings') }}">
                                                            {{csrf_field()}}
                                                            <div class="modal-body">
                                                                <div class="form-group">
                                                                    <label for="withdrawn_notes"
                                                                           class="control-label">{{ trans_choice('core::general.note',2) }}</label>
                                                                    <textarea name="withdrawn_notes"
                                                                              class="form-control"
                                                                              id="withdrawn_notes" rows="3"
                                                                              required=""></textarea>
                                                                </div>
                                                            </div>
                                                            <div class="modal-footer">
                                                                <button type="button"
                                                                        class="btn btn-default pull-left"
                                                                        data-dismiss="modal">
                                                                    {{ trans_choice('core::general.close',1) }}
                                                                </button>
                                                                <button type="submit"
                                                                        class="btn btn-primary">{{ trans_choice('core::general.save',1) }}</button>
                                                            </div>
                                                        </form>
                                                    </div>
                                                </div>
                                            </div>
                                        @endcan
                                    @endif
                                    @if($savings->status=='active')
                                        @can('savings.savings.transactions.create')
                                            <a href="{{url('savings/'.$savings->id.'/deposit/create')}}"
                                               class="btn btn-success"><i class="fas fa-dollar-sign"></i>
                                                {{ trans_choice('savings::general.make',1) }} {{ trans_choice('savings::general.deposit',1) }}
                                            </a>
                                            <a href="{{url('savings/'.$savings->id.'/withdrawal/create')}}"
                                               class="btn btn-warning"><i class="fas fa-money-bill"></i>
                                                {{ trans_choice('savings::general.make',1) }} {{ trans_choice('savings::general.withdrawal',1) }}
                                            </a>
                                        @endcan
                                        @can('savings.savings.edit')
                                            <a href="#" data-toggle="modal"
                                               data-target="#change_savings_officer_modal"
                                               class="btn btn-primary">
                                                {{ trans_choice('savings::general.change',1) }} {{ trans_choice('savings::general.savings',1) }} {{ trans_choice('savings::general.officer',1) }}
                                            </a>
                                        @endcan
                                        @can('savings.savings.charges.create')
                                            <a href="{{url('savings/'.$savings->id.'/charge/create')}}"
                                               class="btn btn-primary"><i
                                                        class="fa fa-plus"></i>
                                                {{ trans_choice('core::general.add',1) }} {{ trans_choice('savings::general.charge',1) }}
                                            </a>
                                        @endcan
                                        @can('savings.savings.close_savings')
                                            <a href="#" data-toggle="modal" data-target="#close_savings_modal"
                                               class="btn btn-primary">
                                                {{ trans_choice('core::general.close',1) }} {{ trans_choice('savings::general.savings',1) }}
                                            </a>
                                        @endcan
                                        @can('savings.savings.activate_savings')
                                            <a href="{{url('savings/'.$savings->id.'/undo_activation')}}"
                                               class="btn btn-danger confirm"><i class="fa fa-undo"></i>
                                                {{ trans_choice('savings::general.undo',1) }} {{ trans_choice('savings::general.activation',1) }}
                                            </a>
                                        @endcan
                                        @can('savings.savings.edit')
                                            <div class="modal fade" id="change_savings_officer_modal">
                                                <div class="modal-dialog">
                                                    <div class="modal-content">
                                                        <div class="modal-header">
                                                            <h4 class="modal-title">{{ trans_choice('savings::general.change',1) }} {{ trans_choice('savings::general.savings',1) }} {{ trans_choice('savings::general.officer',1) }}</h4>
                                                            <button type="button" class="close" data-dismiss="modal"
                                                                    aria-label="Close">
                                                                <span aria-hidden="true">×</span></button>
                                                        </div>
                                                        <form method="post"
                                                              action="{{ url('savings/'.$savings->id.'/change_savings_officer') }}">
                                                            {{csrf_field()}}
                                                            <div class="modal-body">
                                                                <div class="form-group">
                                                                    <label for="savings_officer_id"
                                                                           class="control-label">{{trans_choice('savings::general.savings',1)}} {{trans_choice('savings::general.officer',1)}}</label>
                                                                    <select class="form-control select2"
                                                                            name="savings_officer_id"
                                                                            id="savings_officer_id"
                                                                            v-model="savings_officer_id"
                                                                            required>
                                                                        <option value=""></option>
                                                                        @foreach($users as $key)
                                                                            <option value="{{$key->id}}"
                                                                                    @if($key->id==$savings->savings_officer_id) selected @endif>{{$key->first_name}} {{$key->last_name}}</option>
                                                                        @endforeach
                                                                    </select>
                                                                </div>
                                                            </div>
                                                            <div class="modal-footer">
                                                                <button type="button"
                                                                        class="btn btn-default pull-left"
                                                                        data-dismiss="modal">
                                                                    {{ trans_choice('core::general.close',1) }}
                                                                </button>
                                                                <button type="submit"
                                                                        class="btn btn-primary">{{ trans_choice('core::general.save',1) }}</button>
                                                            </div>
                                                        </form>
                                                    </div>
                                                </div>
                                            </div>
                                        @endcan
                                        @can('savings.savings.close_savings')
                                            <div class="modal fade" id="close_savings_modal">
                                                <div class="modal-dialog">
                                                    <div class="modal-content">
                                                        <div class="modal-header">
                                                            <h4 class="modal-title">{{ trans_choice('core::general.close',1) }} {{ trans_choice('savings::general.savings',1) }}</h4>
                                                            <button type="button" class="close" data-dismiss="modal"
                                                                    aria-label="Close">
                                                                <span aria-hidden="true">×</span></button>
                                                        </div>
                                                        <form method="post"
                                                              action="{{ url('savings/'.$savings->id.'/close_savings') }}">
                                                            {{csrf_field()}}
                                                            <div class="modal-body">
                                                                <div class="form-group">
                                                                    <label for="closed_notes"
                                                                           class="control-label">{{ trans_choice('core::general.note',2) }}</label>
                                                                    <textarea name="closed_notes"
                                                                              class="form-control"
                                                                              id="closed_notes"
                                                                              rows="3" required></textarea>
                                                                </div>
                                                            </div>
                                                            <div class="modal-footer">
                                                                <button type="button"
                                                                        class="btn btn-default pull-left"
                                                                        data-dismiss="modal">
                                                                    {{ trans_choice('core::general.close',1) }}
                                                                </button>
                                                                <button type="submit"
                                                                        class="btn btn-primary">{{ trans_choice('core::general.save',1) }}</button>
                                                            </div>
                                                        </form>
                                                    </div>
                                                </div>
                                            </div>
                                        @endcan
                                    @endif
                                    @if($savings->status=='closed')
                                        @can('savings.savings.close_savings')
                                            <a href="{{url('savings/'.$savings->id.'/undo_closed')}}"
                                               class="btn btn-primary confirm"><i class="fas fa-undo"></i>
                                                {{ trans_choice('savings::general.activate',1) }} {{ trans_choice('savings::general.savings',1) }}
                                            </a>
                                        @endcan
                                    @endif
                                    @if($savings->status=='inactive')
                                        @can('savings.savings.inactive_savings')
                                            <a href="{{url('savings/'.$savings->id.'/undo_inactive')}}"
                                               class="btn btn-primary confirm"><i class="fa fa-undo"></i>
                                                {{ trans_choice('savings::general.activate',1) }} {{ trans_choice('savings::general.savings',1) }}
                                            </a>
                                        @endcan
                                    @endif
                                    @if($savings->status=='dormant')
                                        @can('savings.savings.dormant_savings')
                                            <a href="{{url('savings/'.$savings->id.'/undo_dormant')}}"
                                               class="btn btn-primary confirm"><i class="fa fa-undo"></i>
                                                {{ trans_choice('savings::general.activate',1) }} {{ trans_choice('savings::general.savings',1) }}
                                            </a>
                                        @endcan
                                    @endif
                                    @if($savings->status=='approved')
                                        @can('savings.savings.activate_savings')
                                            <a href="#" data-toggle="modal" data-target="#activate_savings_modal"
                                               class="btn btn-primary"><i class="fa fa-flag"></i>
                                                {{ trans_choice('savings::general.activate',1) }}
                                            </a>
                                        @endcan
                                        @can('savings.savings.edit')
                                            <a href="#" data-toggle="modal"
                                               data-target="#change_savings_officer_modal"
                                               class="btn btn-primary">
                                                {{ trans_choice('savings::general.change',1) }} {{ trans_choice('savings::general.savings',1) }} {{ trans_choice('savings::general.officer',1) }}
                                            </a>
                                        @endcan
                                        @can('savings.savings.approve_savings')
                                            <a href="{{url('savings/'.$savings->id.'/undo_approval')}}"
                                               class="btn btn-primary confirm"><i class="fas fa-undo"></i>
                                                {{ trans_choice('savings::general.undo',1) }} {{ trans_choice('savings::general.approval',1) }}
                                            </a>
                                        @endcan
                                        @can('savings.savings.edit')
                                            <div class="modal fade" id="change_savings_officer_modal">
                                                <div class="modal-dialog">
                                                    <div class="modal-content">
                                                        <div class="modal-header">
                                                            <h4 class="modal-title">{{ trans_choice('savings::general.change',1) }} {{ trans_choice('savings::general.savings',1) }} {{ trans_choice('savings::general.officer',1) }}</h4>
                                                            <button type="button" class="close" data-dismiss="modal"
                                                                    aria-label="Close">
                                                                <span aria-hidden="true">×</span></button>
                                                        </div>
                                                        <form method="post"
                                                              action="{{ url('savings/'.$savings->id.'/change_savings_officer') }}">
                                                            {{csrf_field()}}
                                                            <div class="modal-body">
                                                                <div class="form-group">
                                                                    <label for="savings_officer_id"
                                                                           class="control-label">{{trans_choice('savings::general.savings',1)}} {{trans_choice('savings::general.officer',1)}}</label>
                                                                    <select class="form-control select2"
                                                                            name="savings_officer_id"
                                                                            id="savings_officer_id"
                                                                            v-model="savings_officer_id"
                                                                            required>
                                                                        <option value=""></option>
                                                                        @foreach($users as $key)
                                                                            <option value="{{$key->id}}"
                                                                                    @if($key->id==$savings->savings_officer_id) selected @endif>{{$key->first_name}} {{$key->last_name}}</option>
                                                                        @endforeach
                                                                    </select>
                                                                </div>
                                                            </div>
                                                            <div class="modal-footer">
                                                                <button type="button"
                                                                        class="btn btn-default pull-left"
                                                                        data-dismiss="modal">
                                                                    {{ trans_choice('core::general.close',1) }}
                                                                </button>
                                                                <button type="submit"
                                                                        class="btn btn-primary">{{ trans_choice('core::general.save',1) }}</button>
                                                            </div>
                                                        </form>
                                                    </div>
                                                </div>
                                            </div>
                                        @endcan
                                        @can('savings.savings.activate_savings')
                                            <div class="modal fade in" id="activate_savings_modal">
                                                <div class="modal-dialog">
                                                    <div class="modal-content">
                                                        <div class="modal-header">
                                                            <h4 class="modal-title">{{ trans_choice('savings::general.activate',1) }} {{ trans_choice('savings::general.savings',1) }}</h4>
                                                            <button type="button" class="close" data-dismiss="modal">
                                                                <span>×</span></button>
                                                        </div>
                                                        <form method="post"
                                                              action="{{ url('savings/'.$savings->id.'/activate_savings') }}"
                                                              class="form-horizontal">
                                                            {{csrf_field()}}
                                                            <div class="modal-body">
                                                                <div class="form-group">
                                                                    <label for="activated_on_date"
                                                                           class="control-label">{{ trans_choice('savings::general.activation',1) }} {{ trans_choice('core::general.date',1) }}</label>

                                                                    <flat-pickr
                                                                            class="form-control  @error('activated_on_date') is-invalid @enderror"
                                                                            name="activated_on_date"
                                                                            value="{{date("Y-m-d")}}"
                                                                            id="activated_on_date" required>
                                                                    </flat-pickr>

                                                                </div>
                                                                <div class="form-group">
                                                                    <label for="activated_notes"
                                                                           class="control-label">{{ trans_choice('core::general.note',2) }}</label>

                                                                    <textarea name="activated_notes"
                                                                              class="form-control"
                                                                              id="activated_notes"
                                                                              rows="3"></textarea>
                                                                </div>
                                                            </div>
                                                            <div class="modal-footer">
                                                                <button type="button"
                                                                        class="btn btn-default pull-left"
                                                                        data-dismiss="modal">
                                                                    {{ trans_choice('core::general.close',1) }}
                                                                </button>
                                                                <button type="submit"
                                                                        class="btn btn-primary">{{ trans_choice('core::general.save',1) }}</button>
                                                            </div>
                                                        </form>
                                                    </div>
                                                </div>
                                            </div>
                                        @endcan
                                    @endif
                                    @if($savings->status=='rejected')
                                        @can('savings.savings.approve_savings')
                                            <a href="{{url('savings/'.$savings->id.'/undo_rejection')}}"
                                               class="btn btn-primary confirm"><i class="fas fa-undo"></i>
                                                {{ trans_choice('savings::general.undo',1) }} {{ trans_choice('savings::general.rejection',1) }}
                                            </a>
                                        @endcan
                                    @endif
                                    @if($savings->status=='withdrawn')
                                        @can('savings.savings.approve_savings')
                                            <a href="{{url('savings/'.$savings->id.'/undo_withdrawn')}}"
                                               class="btn btn-primary confirm"><i class="fas fa-undo"></i>
                                                {{ trans_choice('savings::general.undo',1) }} {{ trans_choice('savings::general.withdrawn',1) }}
                                            </a>
                                        @endcan
                                    @endif
                                </div>
                            </div>
                        </div>
                        <div class="row" style="margin-top: 20px">
                            <div class="col-sm-8 col-md-8 p-10">
                                @if($savings->status=='submitted' ||$savings->status=='pending'||$savings->status=='withdrawn'||$savings->status=='approved'||$savings->status=='rejected')
                                    @if($savings->status=='submitted')
                                        <span class="badge badge-warning badge-lg m-2 status-label">{{ trans_choice('savings::general.pending_approval',1) }}</span>
                                    @endif
                                    @if($savings->status=='approved')
                                        <span class="badge badge-warning badge-lg m-2 status-label">{{ trans_choice('savings::general.awaiting_activation',1) }}</span>
                                    @endif
                                    @if($savings->status=='withdrawn')
                                        <span class="badge badge-danger badge-lg m-2 status-label">{{ trans_choice('savings::general.withdrawn',1) }}</span>

                                    @endif
                                    @if($savings->status=='rejected')
                                        <span class="badge badge-danger badge-lg m-2 status-label">{{ trans_choice('savings::general.rejected',1) }}</span>
                                    @endif
                                @endif
                                @if($savings->status=='active' ||$savings->status=='closed'||$savings->status=='dormant'||$savings->status=='inactive')
                                    <table class="table table-striped table-bordered">
                                        <tbody>
                                        <tr>
                                            <th class="table-bold-savings">{{ trans_choice('savings::general.current',1) }} {{ trans_choice('savings::general.balance',1) }}</th>
                                            <td>
                                                {{number_format($savings->transactions->where('reversed',0)->sum('credit')-$savings->transactions->where('reversed',0)->sum('debit'),$savings->decimals)}}
                                            </td>
                                        </tr>
                                        <tr>
                                            <th class="table-bold-savings">{{ trans_choice('savings::general.interest',1) }} {{ trans_choice('savings::general.earned',1) }}</th>
                                            <td>
                                                {{number_format($savings->transactions->where('reversed',0)->where('savings_transaction_type_id',11)->sum('amount')+$savings->calculated_interest,$savings->decimals)}}
                                            </td>
                                        </tr>
                                        <tr>
                                            <th class="table-bold-savings">{{ trans_choice('savings::general.interest',1) }} {{ trans_choice('savings::general.posted',1) }}</th>
                                            <td>
                                                {{number_format($savings->transactions->where('reversed',0)->where('savings_transaction_type_id',11)->sum('amount'),$savings->decimals)}}
                                            </td>
                                        </tr>
                                        <tr>
                                            <th class="table-bold-savings">{{ trans_choice('core::general.total',1) }} {{ trans_choice('savings::general.deposit',2) }}</th>
                                            <td>
                                                {{number_format($savings->transactions->where('reversed',0)->where('savings_transaction_type_id',1)->sum('amount'),$savings->decimals)}}
                                            </td>
                                        </tr>
                                        <tr>
                                            <th class="table-bold-savings">{{ trans_choice('core::general.total',1) }} {{ trans_choice('savings::general.withdrawal',2) }}</th>
                                            <td>
                                                {{number_format($savings->transactions->where('reversed',0)->where('savings_transaction_type_id',2)->sum('amount'),$savings->decimals)}}
                                            </td>
                                        </tr>
                                        </tbody>
                                    </table>
                                @endif
                            </div>
                            <div class="col-sm-4 col-md-4">
                                <table class="table table-striped table-bordered">
                                    <tbody>
                                    <tr>
                                        <th class="table-bold-savings">{{ trans_choice('savings::general.status',1) }}</th>
                                        <td>
                                            @if($savings->status=='submitted')
                                                <span class="label label-warning">{{ trans_choice('savings::general.pending_approval',1) }}</span>
                                            @endif
                                            @if($savings->status=='approved')
                                                <span class="label label-warning">{{ trans_choice('savings::general.awaiting_activation',1) }}</span>
                                            @endif
                                            @if($savings->status=='active')
                                                <span class="label label-success">{{ trans_choice('savings::general.active',1) }}</span>
                                            @endif
                                            @if($savings->status=='withdrawn')
                                                <span class="label label-danger">{{ trans_choice('savings::general.withdrawn',1) }}</span>
                                            @endif
                                            @if($savings->status=='rejected')
                                                <span class="label label-danger">{{ trans_choice('savings::general.rejected',1) }}</span>
                                            @endif
                                            @if($savings->status=='closed')
                                                <span class="label label-info">{{ trans_choice('savings::general.closed',1) }}</span>
                                            @endif
                                            @if($savings->status=='dormant')
                                                <span class="label label-warning">{{ trans_choice('savings::general.dormant',1) }}</span>
                                            @endif
                                            @if($savings->status=='inactive')
                                                <span class="label label-warning">{{ trans_choice('savings::general.inactive',1) }}</span>
                                            @endif
                                        </td>
                                    </tr>
                                    <tr>
                                        <th class="table-bold-savings">{{ trans_choice('client::general.client',1) }}</th>
                                        <td>
                                            @if(!empty($savings->client))
                                                <a href="{{url('client/'.$savings->client_id.'/show')}}">{{$savings->client->first_name}} {{$savings->client->middle_name}} {{$savings->client->last_name}}</a>
                                            @endif
                                        </td>
                                    </tr>
                                    <tr>
                                        <th class="table-bold-savings">{{ trans_choice('savings::general.account_number',1) }}</th>
                                        <td>
                                            {{$savings->account_number}}
                                        </td>
                                    </tr>
                                    <tr>
                                        <th class="table-bold-savings">{{ trans_choice('savings::general.external_id',1) }}</th>
                                        <td>
                                            {{$savings->external_id}}
                                        </td>
                                    </tr>
                                    <tr>
                                        <th class="table-bold-savings">{{ trans_choice('core::general.currency',1) }}</th>
                                        <td>
                                            @if(!empty($savings->currency))
                                                {{$savings->currency->name}}
                                            @endif
                                        </td>
                                    </tr>
                                    <tr>
                                        <th class="table-bold-savings">{{ trans_choice('savings::general.savings',1) }} {{ trans_choice('savings::general.officer',1) }}</th>
                                        <td>
                                            @if(!empty($savings->savings_officer))
                                                {{$savings->savings_officer->first_name}} {{$savings->savings_officer->last_name}}
                                            @endif
                                        </td>
                                    </tr>

                                    <tr>
                                        <th class="table-bold-savings">{{ trans_choice('savings::general.interest_rate',1) }}</th>
                                        <td>
                                            {{number_format($savings->interest_rate,2)}}%
                                        </td>
                                    </tr>
                                    @if($savings->status=='active' ||$savings->status=='closed'||$savings->status=='dormant'||$savings->status=='inactive')
                                        <tr>
                                            <th class="table-bold-savings">{{ trans_choice('savings::general.activated_on',1) }}</th>
                                            <td>
                                                {{$savings->activated_on_date}}
                                            </td>
                                        </tr>

                                    @endif
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="row my-4">
            <div class="col-md-12">
                <div class="card card-primary card-outline card-outline-tabs">
                    <div class="card-header p-0 border-bottom-0">
                        <ul class="nav nav-tabs">
                            <li class="nav-item">
                                <a href="#account_details" class="nav-link active"
                                   data-toggle="tab">
                                    {{ trans_choice('savings::general.account',1) }} {{ trans_choice('core::general.detail',2) }}
                                </a>
                            </li>
                            @if($savings->status=='active' ||$savings->status=='closed'||$savings->status=='dormant'||$savings->status=='overpaid'||$savings->status=='rescheduled')
                                @can('savings.savings.transactions.index')
                                    <li class="nav-item">
                                        <a href="#savings_transactions" class="nav-link"
                                           data-toggle="tab">
                                            {{ trans_choice('savings::general.transaction',2) }}
                                        </a>
                                    </li>
                                @endcan
                            @endif
                            @can('savings.savings.charges.index')
                                <li class="nav-item">
                                    <a href="#savings_charges" class="nav-link"
                                       data-toggle="tab">
                                        {{ trans_choice('savings::general.charge',2) }}
                                    </a>
                                </li>
                            @endcan

                        </ul>
                    </div>
                    <div class="card-body">
                        <div class="tab-content">
                            <div class="tab-pane active" id="account_details">
                                <table class="table table-striped table-hover">
                                    <tbody>
                                    <tr>
                                        <td>{{trans_choice('savings::general.compounding_period',1)}}</td>
                                        <td>
                                            @if($savings->compounding_period=='daily')
                                                {{trans_choice('savings::general.daily',2)}}
                                            @endif
                                            @if($savings->compounding_period=='weekly')
                                                {{trans_choice('savings::general.weekly',2)}}
                                            @endif
                                            @if($savings->compounding_period=='monthly')
                                                {{trans_choice('savings::general.monthly',2)}}
                                            @endif
                                            @if($savings->compounding_period=='biannual')
                                                {{trans_choice('savings::general.biannual',2)}}
                                            @endif
                                            @if($savings->compounding_period=='annually')
                                                {{trans_choice('savings::general.annually',2)}}
                                            @endif
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>{{trans_choice('savings::general.interest_posting_period_type',1)}}</td>
                                        <td>
                                            @if($savings->interest_posting_period_type=='daily')
                                                {{trans_choice('savings::general.daily',2)}}
                                            @endif
                                            @if($savings->interest_posting_period_type=='weekly')
                                                {{trans_choice('savings::general.weekly',2)}}
                                            @endif
                                            @if($savings->interest_posting_period_type=='monthly')
                                                {{trans_choice('savings::general.monthly',2)}}
                                            @endif
                                            @if($savings->interest_posting_period_type=='biannual')
                                                {{trans_choice('savings::general.biannual',2)}}
                                            @endif
                                            @if($savings->interest_posting_period_type=='annually')
                                                {{trans_choice('savings::general.annually',2)}}
                                            @endif
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>{{trans_choice('savings::general.interest_calculation_type',1)}}</td>
                                        <td>
                                            @if($savings->interest_calculation_type=='daily_balance')
                                                {{trans_choice('savings::general.daily_balance',1)}}
                                            @endif
                                            @if($savings->interest_calculation_type=='average_daily_balance')
                                                {{trans_choice('savings::general.average_balance',1)}}
                                            @endif
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>{{trans_choice('savings::general.category',1)}}</td>
                                        <td>
                                            @if($savings->savings_product->savings_category=='voluntary')
                                                {{trans_choice('savings::general.voluntary',1)}}
                                            @endif
                                            @if($savings->savings_product->savings_category=='compulsory')
                                                {{trans_choice('savings::general.compulsory',1)}}
                                            @endif
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>{{trans_choice('core::general.submitted_on',1)}}</td>
                                        <td>
                                            {{$savings->submitted_on_date}}
                                            {{trans_choice('core::general.by',1)}}
                                            @if(!empty($savings->submitted_by))
                                                {{$savings->submitted_by->first_name}} {{$savings->submitted_by->last_name}}
                                            @endif
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>{{trans_choice('savings::general.approved',1)}} {{trans_choice('core::general.on',1)}}</td>
                                        <td>
                                            {{$savings->approved_on_date}}

                                            @if(!empty($savings->approved_by))
                                                {{trans_choice('core::general.by',1)}}
                                                {{$savings->approved_by->first_name}} {{$savings->approved_by->last_name}}
                                            @endif
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>{{trans_choice('savings::general.activated',1)}} {{trans_choice('core::general.on',1)}}</td>
                                        <td>
                                            {{$savings->activated_on_date}}

                                            @if(!empty($savings->activated_by))
                                                {{trans_choice('core::general.by',1)}}
                                                {{$savings->activated_by->first_name}} {{$savings->activated_by->last_name}}
                                            @endif
                                        </td>
                                    </tr>
                                    @foreach($custom_fields as $custom_field)
                                        <?php
                                        $field = custom_field_build_form_field($custom_field, $savings->id);
                                        ?>
                                        <tr>
                                            <td>{{$field['label']}}</td>
                                            <td>
                                                @if($custom_field->type=='checkbox')
                                                    @foreach(explode(',',$field['current'] ) as $key)
                                                        {{$key}}<br>
                                                    @endforeach
                                                @else
                                                    {{$field['current'] }}
                                                @endif
                                            </td>
                                        </tr>
                                    @endforeach
                                    </tbody>
                                </table>
                            </div>
                            @if($savings->status=='active' ||$savings->status=='closed'||$savings->status=='inactive'||$savings->status=='dormant'||$savings->status=='rescheduled')
                                @can('savings.savings.transactions.index')
                                    <div class="tab-pane" id="savings_transactions">
                                        <table class="table table-striped table-hover"
                                               id="savings_transactions_table">
                                            <thead>
                                            <tr>
                                                <th>{{trans_choice('core::general.date',1)}}</th>
                                                <th>{{trans_choice('core::general.submitted_on',1)}}</th>
                                                <th>{{trans_choice('savings::general.transaction',1)}} {{trans_choice('core::general.type',1)}}</th>
                                                <th>{{trans_choice('savings::general.transaction',1)}} {{trans_choice('core::general.id',1)}}</th>
                                                <th>{{trans_choice('accounting::general.debit',1)}}</th>
                                                <th>{{trans_choice('accounting::general.credit',1)}}</th>
                                                <th>{{trans_choice('savings::general.balance',1)}}</th>
                                                <th>{{trans_choice('core::general.action',1)}}</th>
                                            </tr>
                                            </thead>
                                            <tbody>
                                            <?php
                                            $balance = 0;
                                            ?>
                                            @foreach($savings->transactions as $key)
                                                <?php
                                                $balance = $balance + $key->credit - $key->debit;
                                                ?>
                                                <tr>
                                                    <td>{{$key->created_on}}</td>
                                                    <td>{{$key->submitted_on}}</td>
                                                    <td>
                                                        {{$key->name}}
                                                    </td>
                                                    <td>{{$key->id}}</td>
                                                    <td>{{number_format($key->debit,$savings->decimals)}}</td>
                                                    <td>{{number_format($key->credit,$savings->decimals)}}</td>
                                                    <td>{{number_format($balance,$savings->decimals)}}</td>
                                                    <td>
                                                        <div class="btn-group">
                                                            <button href="#" class="btn btn-default dropdown-toggle"
                                                                    data-toggle="dropdown">
                                                                <i class="fas fa-ellipsis-h"></i>
                                                            </button>
                                                            <div class="dropdown-menu dropdown-menu-right">
                                                                <a href="{{url('savings/transaction/' . $key->id . '/show') }}"
                                                                   class="dropdown-item"><i
                                                                            class="fas fa-eye"></i> {{ trans_choice('core::general.view', 2) }}
                                                                </a>

                                                                <a href="{{url('savings/transaction/' . $key->id . '/pdf') }}"
                                                                   target="_blank" class="dropdown-item"><i
                                                                            class="fas fa-file-pdf"></i> {{ trans_choice('core::general.receipt', 1) }}
                                                                </a>

                                                                <a href="{{url('savings/transaction/' . $key->id . '/print') }}"
                                                                   target="_blank" class="dropdown-item"><i
                                                                            class="fa fa-print"></i> {{ trans_choice('core::general.print', 1) }}
                                                                </a>

                                                                @if($key->reversible == 1 && $key->reversed==0)

                                                                    <a href="{{url('savings/transaction/' . $key->id . '/edit') }}"
                                                                       class="dropdown-item"><i
                                                                                class="fas fa-edit"></i> {{ trans_choice('core::general.edit', 1) }}
                                                                    </a>
                                                                    <a href="{{url('savings/transaction/' . $key->id . '/reverse') }}"
                                                                       class="dropdown-item confirm"><i
                                                                                class="fas fa-undo"></i> {{ trans_choice('savings::general.reverse', 1) }}
                                                                    </a>

                                                                @endif
                                                            </div>
                                                        </div>
                                                    </td>
                                                </tr>
                                            @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                @endcan
                            @endif
                            @can('savings.savings.charges.index')
                                <div class="tab-pane" id="savings_charges">
                                    @can('savings.savings.charges.create')
                                        <a href="{{url('savings/'.$savings->id.'/charge/create')}}"
                                           class="btn btn-info float-right m-2">{{trans_choice('core::general.add',1)}} {{trans_choice('savings::general.charge',1)}}</a>
                                    @endcan
                                    <table class="table table-striped table-bordered table-hover">
                                        <thead>
                                        <tr>
                                            <th>{{ trans_choice('core::general.name',1) }}</th>
                                            <th>{{ trans_choice('savings::general.charge',1) }} {{ trans_choice('core::general.type',1) }}</th>
                                            <th>{{ trans_choice('savings::general.collected_on',1) }}</th>
                                            <th>{{ trans_choice('core::general.action',1) }}</th>
                                        </tr>
                                        </thead>
                                        <tbody>
                                        @foreach($savings->charges as $key)
                                            <tr>
                                                <td>{{$key->name}}</td>
                                                <td>
                                                    @if($key->savings_charge_option_id==1)
                                                        {{number_format($key->amount,2)}} {{ trans_choice('savings::general.flat',1) }}
                                                    @endif
                                                    @if($key->savings_charge_option_id==2)
                                                        {{number_format($key->amount,2)}}
                                                        % {{ trans_choice('savings::general.percentage_of_amount',1) }}
                                                    @endif
                                                    @if($key->savings_charge_option_id==3)
                                                        {{number_format($key->amount,2)}}
                                                        %  {{ trans_choice('savings::general.percentage_of_savings_balance',1) }}
                                                    @endif
                                                </td>
                                                <td>
                                                    @if($key->savings_charge_type_id==1)
                                                        {{ trans_choice('savings::general.savings_activation',1) }}
                                                    @endif
                                                    @if($key->savings_charge_type_id==2)
                                                        {{ trans_choice('savings::general.specified_due_date',1) }}
                                                    @endif
                                                    @if($key->savings_charge_type_id==3)
                                                        {{ trans_choice('savings::general.withdrawal_fee',1) }}
                                                    @endif
                                                    @if($key->savings_charge_type_id==4)
                                                        {{ trans_choice('savings::general.annual_fee',1) }}
                                                    @endif
                                                    @if($key->savings_charge_type_id==5)
                                                        {{ trans_choice('savings::general.monthly_fee',1) }}
                                                    @endif
                                                    @if($key->savings_charge_type_id==6)
                                                        {{ trans_choice('savings::general.inactivity_fee',1) }}
                                                    @endif
                                                    @if($key->savings_charge_type_id==7)
                                                        {{ trans_choice('savings::general.quarterly_fee',1) }}
                                                    @endif
                                                </td>
                                                <td>
                                                    @if($key->is_paid==1)
                                                        {{ trans_choice('savings::general.charge',1) }} {{ trans_choice('savings::general.paid',1) }}
                                                    @else
                                                        @if($key->waived==1)
                                                            {{ trans_choice('savings::general.charge',1) }} {{ trans_choice('savings::general.waived',1) }}
                                                        @else
                                                            {{ trans_choice('savings::general.outstanding',1) }}
                                                            @can('savings.savings.transactions.create')
                                                                <a href="{{url('savings/charge/'.$key->id.'/pay')}}"
                                                                   class="btn btn-info btn-xs">{{ trans_choice('savings::general.pay',1) }}</a>
                                                            @endcan
                                                        @endif
                                                    @endif
                                                </td>
                                            </tr>
                                        @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            @endcan
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection
@section('scripts')
    <script>
        var app = new Vue({
            el: '#app',
            data: {
                savings_officer_id: '{{old('savings_officer_id',$savings->savings_officer_id)}}',
            }
        })
    </script>
@endsection
