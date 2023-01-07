@extends('core::layouts.master')
@section('title')
    {{ trans_choice('share::general.share',1) }} {{ trans_choice('core::general.detail',2) }}
@endsection
@section('styles')
@stop
@section('content')
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1>
                        {{ trans_choice('share::general.share',1) }} {{ trans_choice('core::general.detail',2) }}
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
                                    href="{{url('share')}}">{{ trans_choice('share::general.share',2) }}</a>
                        </li>
                        <li class="breadcrumb-item active">{{ trans_choice('share::general.share',1) }} {{ trans_choice('core::general.detail',2) }}</li>
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
                        <h5 class="card-title">{{$share->share_product->name}}(#{{$share->id}})</h5>
                        <div class="row">
                            <div class="col-md-12">
                                <div class="float-right btn-group">
                                    @if($share->status=='submitted' ||$share->status=='pending')
                                        @can('share.shares.approve_shares')
                                            <a href="#" data-toggle="modal" data-target="#approve_share_modal"
                                               class="btn btn-primary"><i
                                                        class="fas fa-check"></i>
                                                {{ trans_choice('share::general.approve',1) }}
                                            </a>
                                            <a href="#" data-toggle="modal" data-target="#reject_share_modal"
                                               class="btn btn-primary"><i class="fa fa-times"></i>
                                                {{ trans_choice('share::general.reject',1) }}
                                            </a>
                                            <a href="#" data-toggle="modal" data-target="#withdraw_share_modal"
                                               class="btn btn-primary"><i class="fas fa-times"></i>
                                                {{ trans_choice('share::general.withdraw',1) }}
                                            </a>
                                        @endcan
                                        @can('share.shares.edit')
                                            <a href="{{url('share/'.$share->id.'/edit')}}" class="btn btn-primary">
                                                <i class="fas fa-edit"></i>
                                                {{ trans_choice('core::general.edit',1) }}
                                            </a>
                                        @endcan
                                        @can('share.shares.approve_shares')
                                            <div class="modal fade" id="approve_share_modal">
                                                <div class="modal-dialog">
                                                    <div class="modal-content">
                                                        <div class="modal-header">
                                                            <h4 class="modal-title">{{ trans_choice('share::general.approve',1) }} {{ trans_choice('share::general.share',2) }}</h4>
                                                            <button type="button" class="close" data-dismiss="modal"
                                                                    aria-label="Close">
                                                                <span aria-hidden="true">×</span></button>
                                                        </div>
                                                        <form method="post"
                                                              action="{{ url('share/'.$share->id.'/approve_share') }}">
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
                                            <div class="modal fade" id="reject_share_modal">
                                                <div class="modal-dialog">
                                                    <div class="modal-content">
                                                        <div class="modal-header">
                                                            <h4 class="modal-title">{{ trans_choice('share::general.reject',1) }} {{ trans_choice('share::general.share',2) }}</h4>
                                                            <button type="button" class="close" data-dismiss="modal"
                                                                    aria-label="Close">
                                                                <span aria-hidden="true">×</span></button>
                                                        </div>
                                                        <form method="post"
                                                              action="{{ url('share/'.$share->id.'/reject_share') }}">
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
                                            <div class="modal fade" id="withdraw_share_modal">
                                                <div class="modal-dialog">
                                                    <div class="modal-content">
                                                        <div class="modal-header">
                                                            <h4 class="modal-title">{{ trans_choice('share::general.withdraw',1) }} {{ trans_choice('share::general.share',2) }}</h4>
                                                            <button type="button" class="close" data-dismiss="modal"
                                                                    aria-label="Close">
                                                                <span aria-hidden="true">×</span></button>
                                                        </div>
                                                        <form method="post"
                                                              action="{{ url('share/'.$share->id.'/withdraw_share') }}">
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
                                    @if($share->status=='active')
                                        @can('share.shares.transactions.create')
                                            <a href="#" data-toggle="modal" data-target="#redeem_share_modal"
                                               class="btn btn-success"><i class="fas fa-dollar-sign"></i>
                                                {{ trans_choice('share::general.redeem',1) }} {{ trans_choice('share::general.share',2) }}
                                            </a>
                                            <a href="#" data-toggle="modal" data-target="#purchase_share_modal"
                                               class="btn btn-warning"><i class="fas fa-money-bill"></i>
                                                {{ trans_choice('share::general.purchase',1) }} {{ trans_choice('share::general.share',2) }}
                                            </a>
                                        @endcan
                                        @can('share.share.edit')
                                            <a href="#" data-toggle="modal"
                                               data-target="#change_share_officer_modal"
                                               class="btn btn-primary">
                                                {{ trans_choice('share::general.change',1) }} {{ trans_choice('share::general.share',1) }} {{ trans_choice('share::general.officer',1) }}
                                            </a>
                                        @endcan
                                        @can('share.shares.charges.create')
                                            <a href="{{url('share/'.$share->id.'/charge/create')}}"
                                               class="btn btn-primary d-none"><i
                                                        class="fas fa-plus"></i>
                                                {{ trans_choice('core::general.add',1) }} {{ trans_choice('share::general.charge',1) }}
                                            </a>
                                        @endcan
                                        @can('share.shares.close_shares')
                                            <a href="#" data-toggle="modal" data-target="#close_share_modal"
                                               class="btn btn-primary">
                                                {{ trans_choice('core::general.close',1) }} {{ trans_choice('share::general.share',1) }}
                                            </a>
                                        @endcan
                                        @can('share.shares.activate_shares')
                                            <a href="{{url('share/'.$share->id.'/undo_activation')}}"
                                               class="btn btn-danger confirm"><i class="fa fa-undo"></i>
                                                {{ trans_choice('share::general.undo',1) }} {{ trans_choice('share::general.activation',1) }}
                                            </a>
                                        @endcan
                                        @can('share.shares.edit')
                                            <div class="modal fade" id="change_share_officer_modal">
                                                <div class="modal-dialog">
                                                    <div class="modal-content">
                                                        <div class="modal-header">
                                                            <h4 class="modal-title">{{ trans_choice('share::general.change',1) }} {{ trans_choice('share::general.share',1) }} {{ trans_choice('share::general.officer',1) }}</h4>
                                                            <button type="button" class="close" data-dismiss="modal"
                                                                    aria-label="Close">
                                                                <span aria-hidden="true">×</span></button>
                                                        </div>
                                                        <form method="post"
                                                              action="{{ url('share/'.$share->id.'/change_share_officer') }}">
                                                            {{csrf_field()}}
                                                            <div class="modal-body">
                                                                <div class="form-group">
                                                                    <label for="share_officer_id"
                                                                           class="control-label">{{trans_choice('share::general.share',1)}} {{trans_choice('share::general.officer',1)}}</label>
                                                                    <select class="form-control select2"
                                                                            name="share_officer_id"
                                                                            id="share_officer_id"
                                                                            v-model="share_officer_id"
                                                                            required>
                                                                        <option value=""></option>
                                                                        @foreach($users as $key)
                                                                            <option value="{{$key->id}}"
                                                                                    @if($key->id==$share->share_officer_id) selected @endif>{{$key->first_name}} {{$key->last_name}}</option>
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
                                        @can('share.shares.close_shares')
                                            <div class="modal fade" id="close_share_modal">
                                                <div class="modal-dialog">
                                                    <div class="modal-content">
                                                        <div class="modal-header">
                                                            <h4 class="modal-title">{{ trans_choice('core::general.close',1) }} {{ trans_choice('share::general.share',1) }}</h4>
                                                            <button type="button" class="close" data-dismiss="modal"
                                                                    aria-label="Close">
                                                                <span aria-hidden="true">×</span></button>
                                                        </div>
                                                        <form method="post"
                                                              action="{{ url('share/'.$share->id.'/close_share') }}">
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
                                        <div class="modal fade" id="redeem_share_modal">
                                            <div class="modal-dialog">
                                                <div class="modal-content">
                                                    <div class="modal-header">
                                                        <h4 class="modal-title">{{ trans_choice('share::general.redeem',1) }} {{ trans_choice('share::general.share',2) }}</h4>
                                                        <button type="button" class="close" data-dismiss="modal"
                                                                aria-label="Close">
                                                            <span aria-hidden="true">×</span></button>
                                                    </div>
                                                    <form method="post"
                                                          action="{{ url('share/'.$share->id.'/redeem_share') }}">
                                                        {{csrf_field()}}
                                                        <div class="modal-body">
                                                            <div class="form-group">
                                                                <label for="redeem_on_date"
                                                                       class="control-label">{{ trans_choice('core::general.date',1) }}</label>
                                                                <flat-pickr
                                                                        class="form-control  @error('redeem_on_date') is-invalid @enderror"
                                                                        name="redeem_on_date"
                                                                        value="{{date("Y-m-d")}}"
                                                                        id="redeem_on_date" required>
                                                                </flat-pickr>
                                                            </div>
                                                            <div class="form-group">
                                                                <label for="redeem_total_shares"
                                                                       class="control-label">{{ trans_choice('share::general.share',2) }}</label>
                                                                <input type="text" name="redeem_total_shares"
                                                                       class="form-control numeric"
                                                                       value="" required=""
                                                                       id="redeem_total_shares">
                                                            </div>
                                                            <div class="form-group">
                                                                <label for="redeem_notes"
                                                                       class="control-label">{{ trans_choice('core::general.note',2) }}</label>
                                                                <textarea name="redeem_notes" class="form-control"
                                                                          id="redeem_notes"
                                                                          rows="3"></textarea>
                                                            </div>
                                                        </div>
                                                        <div class="modal-footer">
                                                            <button type="button" class="btn btn-default pull-left"
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
                                        <div class="modal fade" id="purchase_share_modal">
                                            <div class="modal-dialog">
                                                <div class="modal-content">
                                                    <div class="modal-header">
                                                        <h4 class="modal-title">{{ trans_choice('share::general.purchase',1) }} {{ trans_choice('share::general.share',2) }}</h4>
                                                        <button type="button" class="close" data-dismiss="modal"
                                                                aria-label="Close">
                                                            <span aria-hidden="true">×</span></button>
                                                    </div>
                                                    <form method="post"
                                                          action="{{ url('share/'.$share->id.'/purchase_share') }}">
                                                        {{csrf_field()}}
                                                        <div class="modal-body">
                                                            <div class="form-group">
                                                                <label for="purchase_on_date"
                                                                       class="control-label">{{ trans_choice('core::general.date',1) }}</label>
                                                                <flat-pickr
                                                                        class="form-control  @error('purchase_on_date') is-invalid @enderror"
                                                                        name="purchase_on_date"
                                                                        value="{{date("Y-m-d")}}"
                                                                        id="purchase_on_date" required>
                                                                </flat-pickr>
                                                            </div>
                                                            <div class="form-group">
                                                                <label for="purchase_total_shares"
                                                                       class="control-label">{{ trans_choice('share::general.share',2) }}</label>
                                                                <input type="text" name="purchase_total_shares"
                                                                       class="form-control numeric"
                                                                       value="" required=""
                                                                       id="purchase_total_shares">
                                                            </div>
                                                            <div class="form-group">
                                                                <label for="purchase_notes"
                                                                       class="control-label">{{ trans_choice('core::general.note',2) }}</label>
                                                                <textarea name="purchase_notes" class="form-control"
                                                                          id="purchase_notes"
                                                                          rows="3"></textarea>
                                                            </div>
                                                        </div>
                                                        <div class="modal-footer">
                                                            <button type="button" class="btn btn-default pull-left"
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
                                    @endif
                                    @if($share->status=='closed')
                                        @can('share.share.close_share')
                                            <a href="{{url('share/'.$share->id.'/undo_closed')}}"
                                               class="btn btn-primary confirm"><i class="fas fa-undo"></i>
                                                {{ trans_choice('share::general.activate',1) }} {{ trans_choice('share::general.share',1) }}
                                            </a>
                                        @endcan
                                    @endif
                                    @if($share->status=='inactive')
                                        @can('share.share.inactive_share')
                                            <a href="{{url('share/'.$share->id.'/undo_inactive')}}"
                                               class="btn btn-primary confirm"><i class="fas fa-undo"></i>
                                                {{ trans_choice('share::general.activate',1) }} {{ trans_choice('share::general.share',1) }}
                                            </a>
                                        @endcan
                                    @endif
                                    @if($share->status=='dormant')
                                        @can('share.share.dormant_share')
                                            <a href="{{url('share/'.$share->id.'/undo_dormant')}}"
                                               class="btn btn-primary confirm"><i class="fas fa-undo"></i>
                                                {{ trans_choice('share::general.activate',1) }} {{ trans_choice('share::general.share',1) }}
                                            </a>
                                        @endcan
                                    @endif
                                    @if($share->status=='approved')
                                        @can('share.shares.activate_shares')
                                            <a href="#" data-toggle="modal" data-target="#activate_share_modal"
                                               class="btn btn-primary"><i class="fas fa-flag"></i>
                                                {{ trans_choice('share::general.activate',1) }}
                                            </a>
                                        @endcan
                                        @can('share.shares.edit')
                                            <a href="#" data-toggle="modal"
                                               data-target="#change_share_officer_modal"
                                               class="btn btn-primary d-none">
                                                {{ trans_choice('share::general.change',1) }} {{ trans_choice('share::general.share',1) }} {{ trans_choice('share::general.officer',1) }}
                                            </a>
                                        @endcan
                                        @can('share.shares.approve_shares')
                                            <a href="{{url('share/'.$share->id.'/undo_approval')}}"
                                               class="btn btn-primary confirm"><i class="fas fa-undo"></i>
                                                {{ trans_choice('share::general.undo',1) }} {{ trans_choice('share::general.approval',1) }}
                                            </a>
                                        @endcan
                                        @can('share.shares.edit')
                                            <div class="modal fade" id="change_share_officer_modal">
                                                <div class="modal-dialog">
                                                    <div class="modal-content">
                                                        <div class="modal-header">
                                                            <h4 class="modal-title">{{ trans_choice('share::general.change',1) }} {{ trans_choice('share::general.share',1) }} {{ trans_choice('share::general.officer',1) }}</h4>
                                                            <button type="button" class="close" data-dismiss="modal"
                                                                    aria-label="Close">
                                                                <span aria-hidden="true">×</span></button>
                                                        </div>
                                                        <form method="post"
                                                              action="{{ url('share/'.$share->id.'/change_share_officer') }}">
                                                            {{csrf_field()}}
                                                            <div class="modal-body">
                                                                <div class="form-group">
                                                                    <label for="share_officer_id"
                                                                           class="control-label">{{trans_choice('share::general.share',1)}} {{trans_choice('share::general.officer',1)}}</label>
                                                                    <select class="form-control select2"
                                                                            name="share_officer_id"
                                                                            id="share_officer_id"
                                                                            v-model="share_officer_id"
                                                                            required>
                                                                        <option value=""></option>
                                                                        @foreach($users as $key)
                                                                            <option value="{{$key->id}}"
                                                                                    @if($key->id==$share->share_officer_id) selected @endif>{{$key->first_name}} {{$key->last_name}}</option>
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
                                        @can('share.shares.activate_shares')
                                            <div class="modal fade in" id="activate_share_modal">
                                                <div class="modal-dialog">
                                                    <div class="modal-content">
                                                        <div class="modal-header">
                                                            <h4 class="modal-title">{{ trans_choice('share::general.activate',1) }} {{ trans_choice('share::general.share',2) }}</h4>
                                                            <button type="button" class="close"
                                                                    data-dismiss="modal">
                                                                <span>×</span></button>
                                                        </div>
                                                        <form method="post"
                                                              action="{{ url('share/'.$share->id.'/activate_share') }}"
                                                              class="form-horizontal">
                                                            {{csrf_field()}}
                                                            <div class="modal-body">
                                                                <div class="form-group">
                                                                    <label for="activated_on_date"
                                                                           class="control-label">{{ trans_choice('share::general.activation',1) }} {{ trans_choice('core::general.date',1) }}</label>
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
                                    @if($share->status=='rejected')
                                        @can('share.shares.approve_shares')
                                            <a href="{{url('share/'.$share->id.'/undo_rejection')}}"
                                               class="btn btn-primary confirm"><i class="fas fa-undo"></i>
                                                {{ trans_choice('share::general.undo',1) }} {{ trans_choice('share::general.rejection',1) }}
                                            </a>
                                        @endcan
                                    @endif
                                    @if($share->status=='withdrawn')
                                        @can('share.shares.approve_shares')
                                            <a href="{{url('share/'.$share->id.'/undo_withdrawn')}}"
                                               class="btn btn-primary confirm"><i class="fas fa-undo"></i>
                                                {{ trans_choice('share::general.undo',1) }} {{ trans_choice('share::general.withdrawn',1) }}
                                            </a>
                                        @endcan
                                    @endif
                                </div>
                            </div>
                        </div>
                        <div class="row" style="margin-top: 20px">
                            <div class="col-sm-8 col-md-8 p-10">
                                @if($share->status=='submitted' ||$share->status=='pending'||$share->status=='withdrawn'||$share->status=='approved'||$share->status=='rejected')
                                    @if($share->status=='submitted')
                                        <span class="badge badge-warning badge-lg m-2 status-label">{{ trans_choice('share::general.pending_approval',1) }}</span>
                                    @endif
                                    @if($share->status=='approved')
                                        <span class="badge badge-warning badge-lg m-2 status-label">{{ trans_choice('share::general.awaiting_activation',1) }}</span>
                                    @endif
                                    @if($share->status=='withdrawn')
                                        <span class="badge badge-danger badge-lg m-2 status-label">{{ trans_choice('share::general.withdrawn',1) }}</span>

                                    @endif
                                    @if($share->status=='rejected')
                                        <span class="badge badge-danger badge-lg m-2 status-label">{{ trans_choice('share::general.rejected',1) }}</span>
                                    @endif
                                @endif
                                @if($share->status=='active' ||$share->status=='closed'||$share->status=='dormant'||$share->status=='inactive')
                                    <table class="table table-striped table-bordered">
                                        <tbody>
                                        <tr>
                                            <th class="table-bold-share">{{ trans_choice('share::general.current',1) }} {{ trans_choice('share::general.value',1) }}</th>
                                            <td>
                                                {{number_format($share->total_shares*$share->share_product->nominal_price,$share->decimals)}}
                                            </td>
                                        </tr>
                                        <tr>
                                            <th class="table-bold-share">{{ trans_choice('core::general.total',1) }} {{ trans_choice('share::general.charge',2) }}</th>
                                            <td>
                                                {{number_format($share->transactions->where('reversed',0)->where('share_transaction_type_id',4)->sum('amount'),$share->decimals)}}
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
                                        <th class="table-bold-share">{{ trans_choice('share::general.status',1) }}</th>
                                        <td>
                                            @if($share->status=='submitted')
                                                <span class="badge badge-warning">{{ trans_choice('share::general.pending_approval',1) }}</span>
                                            @endif
                                            @if($share->status=='approved')
                                                <span class="badge badge-warning">{{ trans_choice('share::general.awaiting_activation',1) }}</span>
                                            @endif
                                            @if($share->status=='active')
                                                <span class="badge badge-success">{{ trans_choice('share::general.active',1) }}</span>
                                            @endif
                                            @if($share->status=='withdrawn')
                                                <span class="badge badge-danger">{{ trans_choice('share::general.withdrawn',1) }}</span>
                                            @endif
                                            @if($share->status=='rejected')
                                                <span class="badge badge-danger">{{ trans_choice('share::general.rejected',1) }}</span>
                                            @endif
                                            @if($share->status=='closed')
                                                <span class="badge badge-info">{{ trans_choice('share::general.closed',1) }}</span>
                                            @endif
                                            @if($share->status=='dormant')
                                                <span class="badge badge-warning">{{ trans_choice('share::general.dormant',1) }}</span>
                                            @endif
                                            @if($share->status=='inactive')
                                                <span class="badge badge-warning">{{ trans_choice('share::general.inactive',1) }}</span>
                                            @endif
                                        </td>
                                    </tr>
                                    <tr>
                                        <th class="table-bold-share">{{ trans_choice('client::general.client',1) }}</th>
                                        <td>
                                            @if(!empty($share->client))
                                                <a href="{{url('client/'.$share->client_id.'/show')}}">{{$share->client->first_name}} {{$share->client->middle_name}} {{$share->client->last_name}}</a>
                                            @endif
                                        </td>
                                    </tr>
                                    <tr>
                                        <th class="table-bold-share">{{ trans_choice('core::general.currency',1) }}</th>
                                        <td>
                                            @if(!empty($share->currency))
                                                {{$share->currency->name}}
                                            @endif
                                        </td>
                                    </tr>
                                    <tr>
                                        <th class="table-bold-share">{{ trans_choice('savings::general.savings',1) }}</th>
                                        <td>
                                            @if(!empty($share->savings))
                                                <a href="{{url('savings/'.$share->savings_id.'/show')}}">#{{$share->savings->id}}</a>
                                            @endif
                                        </td>
                                    </tr>

                                    <tr>
                                        <th class="table-bold-share">{{ trans_choice('share::general.total_shares',1) }}</th>
                                        <td>
                                            {{number_format($share->total_shares,2)}}
                                        </td>
                                    </tr>
                                    @if($share->status=='active' ||$share->status=='closed'||$share->status=='dormant'||$share->status=='inactive')
                                        <tr>
                                            <th class="table-bold-share">{{trans_choice('share::general.activated',1)}} {{trans_choice('core::general.on',1)}}</th>
                                            <td>
                                                {{$share->activated_on_date}}
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
                                    {{ trans_choice('share::general.account',1) }} {{ trans_choice('core::general.detail',2) }}
                                </a>
                            </li>
                            @if($share->status=='active' ||$share->status=='closed'||$share->status=='dormant'||$share->status=='overpaid'||$share->status=='rescheduled')
                                @can('share.shares.transactions.index')
                                    <li class="nav-item">
                                        <a href="#share_transactions" class="nav-link"
                                           data-toggle="tab">
                                            {{ trans_choice('share::general.transaction',2) }}
                                        </a>
                                    </li>
                                @endcan
                            @endif
                            @can('share.shares.charges.index')
                                <li class="nav-item">
                                    <a href="#share_charges" class="nav-link"
                                       data-toggle="tab">
                                        {{ trans_choice('share::general.charge',2) }}
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
                                        <td>{{trans_choice('core::general.submitted_on',1)}}</td>
                                        <td>
                                            {{$share->submitted_on_date}}
                                            {{trans_choice('core::general.by',1)}}
                                            @if(!empty($share->submitted_by))
                                                {{$share->submitted_by->first_name}} {{$share->submitted_by->last_name}}
                                            @endif
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>{{trans_choice('share::general.approved',1)}} {{trans_choice('core::general.on',1)}}</td>
                                        <td>
                                            {{$share->approved_on_date}}

                                            @if(!empty($share->approved_by))
                                                {{trans_choice('core::general.by',1)}}
                                                {{$share->approved_by->first_name}} {{$share->approved_by->last_name}}
                                            @endif
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>{{trans_choice('share::general.activated',1)}} {{trans_choice('core::general.on',1)}}</td>
                                        <td>
                                            {{$share->activated_on_date}}

                                            @if(!empty($share->activated_by))
                                                {{trans_choice('core::general.by',1)}}
                                                {{$share->activated_by->first_name}} {{$share->activated_by->last_name}}
                                            @endif
                                        </td>
                                    </tr>
                                    @foreach($custom_fields as $custom_field)
                                        <?php
                                        $field = custom_field_build_form_field($custom_field, $share->id);
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
                            @if($share->status=='active' ||$share->status=='closed'||$share->status=='inactive'||$share->status=='dormant'||$share->status=='rescheduled')
                                @can('share.shares.transactions.index')
                                    <div class="tab-pane" id="share_transactions">
                                        <table class="table table-striped table-hover" id="share_transactions_table">
                                            <thead>
                                            <tr>
                                                <th>{{trans_choice('core::general.date',1)}}</th>
                                                <th>{{trans_choice('core::general.submitted_on',1)}}</th>
                                                <th>{{trans_choice('share::general.transaction',1)}} {{trans_choice('core::general.type',1)}}</th>
                                                <th>{{trans_choice('share::general.transaction',1)}} {{trans_choice('core::general.id',1)}}</th>
                                                <th>{{trans_choice('share::general.total_shares',1)}}</th>
                                                <th>{{trans_choice('share::general.price',1)}}</th>
                                                <th>{{trans_choice('share::general.amount',1)}}</th>
                                                <th>{{trans_choice('core::general.action',1)}}</th>
                                            </tr>
                                            </thead>
                                            <tbody>
                                            <?php
                                            $balance = 0;
                                            ?>
                                            @foreach($share->transactions as $key)
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
                                                    <td>{{$key->total_shares?number_format($key->total_shares,$share->decimals):""}}</td>
                                                    <td>{{$key->total_shares?number_format($key->amount/$key->total_shares,$share->decimals):""}}</td>
                                                    <td>{{number_format($key->amount,$share->decimals)}}</td>
                                                    <td>
                                                        <div class="btn-group">
                                                            <button href="#" class="btn btn-default dropdown-toggle"
                                                                    data-toggle="dropdown">
                                                                <i class="fas fa-ellipsis-h"></i>
                                                            </button>
                                                            <div class="dropdown-menu dropdown-menu-left">
                                                                <a href="{{url('share/transaction/' . $key->id . '/show') }}"
                                                                   class="dropdown-item"><i
                                                                            class="fas fa-eye"></i> {{ trans_choice('core::general.view', 2) }}
                                                                </a>
                                                                <a href="{{url('share/transaction/' . $key->id . '/pdf') }}" class="dropdown-item"
                                                                   target="_blank"><i
                                                                            class="fas fa-file-pdf"></i> {{ trans_choice('core::general.receipt', 1) }}
                                                                </a>

                                                                <a href="{{url('share/transaction/' . $key->id . '/print') }}" class="dropdown-item"
                                                                   target="_blank"><i
                                                                            class="fas fa-print"></i> {{ trans_choice('core::general.print', 1) }}
                                                                </a>

                                                                @if($key->reversible == 1 && $key->reversed==0)
                                                                    <a href="{{url('share/transaction/' . $key->id . '/edit') }}" class="dropdown-item"><i
                                                                                class="fas fa-edit"></i> {{ trans_choice('core::general.edit', 1) }}
                                                                    </a>

                                                                    <a href="{{url('share/transaction/' . $key->id . '/reverse') }}"
                                                                       class="dropdown-item confirm"><i
                                                                                class="fas fa-undo"></i> {{ trans_choice('share::general.reverse', 1) }}
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
                            @can('share.shares.charges.index')
                                <div class="tab-pane" id="share_charges">
                                    @can('share.shares.charges.create')
                                        <a href="{{url('share/'.$share->id.'/charge/create')}}"
                                           class="btn btn-info d-none float-right m-2">{{trans_choice('core::general.add',1)}} {{trans_choice('share::general.charge',1)}}</a>
                                    @endcan
                                    <table class="table table-striped table-bordered table-hover">
                                        <thead>
                                        <tr>
                                            <th>{{ trans_choice('core::general.name',1) }}</th>
                                            <th>{{ trans_choice('share::general.charge',1) }} {{ trans_choice('core::general.type',1) }}</th>
                                            <th>{{ trans_choice('share::general.collected_on',1) }}</th>
                                            <th>{{ trans_choice('core::general.action',1) }}</th>
                                        </tr>
                                        </thead>
                                        <tbody>
                                        @foreach($share->charges as $key)
                                            <tr>
                                                <td>{{$key->name}}</td>
                                                <td>
                                                    @if($key->share_charge_option_id==1)
                                                        {{number_format($key->amount,2)}} {{ trans_choice('share::general.flat',1) }}
                                                    @endif
                                                    @if($key->share_charge_option_id==2)
                                                        {{number_format($key->amount,2)}}
                                                        % {{ trans_choice('share::general.percentage_of_amount',1) }}
                                                    @endif
                                                </td>
                                                <td>
                                                    @if($key->share_charge_type_id==1)
                                                        {{ trans_choice('share::general.share_account_activation',1) }}
                                                    @endif
                                                    @if($key->share_charge_type_id==2)
                                                        {{ trans_choice('share::general.share_purchase',1) }}
                                                    @endif
                                                    @if($key->share_charge_type_id==3)
                                                        {{ trans_choice('share::general.share_redeem',1) }}
                                                    @endif
                                                </td>
                                                <td>
                                                    @if($key->is_paid==1)
                                                        {{ trans_choice('share::general.charge',1) }} {{ trans_choice('share::general.paid',1) }}
                                                    @else
                                                        @if($key->waived==1)
                                                            {{ trans_choice('share::general.charge',1) }} {{ trans_choice('share::general.waived',1) }}
                                                        @else
                                                            {{ trans_choice('share::general.outstanding',1) }}
                                                            @can('share.shares.transactions.create')
                                                                <a href="{{url('share/charge/'.$key->id.'/pay')}}"
                                                                   class="btn btn-info btn-xs d-none">{{ trans_choice('share::general.pay',1) }}</a>
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
                        <!-- /.tab-content -->
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
            data: {}
        })
    </script>
@endsection
