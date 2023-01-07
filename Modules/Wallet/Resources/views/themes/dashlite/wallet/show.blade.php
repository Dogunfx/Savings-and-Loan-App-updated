@extends('core::layouts.master')
@section('title')
    {{ trans_choice('wallet::general.wallet',1) }} {{ trans_choice('core::general.detail',2) }}
@endsection
@section('styles')
@stop
@section('content')
    <div class="row">
        <div class="col-md-12">
            <div class="card ">
                <div class="card-inner">
                    <h5 class="card-title">{{ trans_choice('wallet::general.wallet',1) }} #{{$wallet->id}}</h5>

                    <div class="panel-body">
                        <div class="row">
                            <div class="col-md-12">
                                <div class="float-right btn-group">
                                    @if($wallet->status=='submitted' ||$wallet->status=='pending')
                                        @can('wallet.wallets.approve_wallet')
                                            <a href="#" data-toggle="modal" data-target="#approve_wallet_modal"
                                               class="btn btn-primary"><i
                                                        class="fa fa-check"></i>
                                                {{ trans_choice('wallet::general.approve',1) }}
                                            </a>
                                            <a href="#" data-toggle="modal" data-target="#reject_wallet_modal"
                                               class="btn btn-primary"><i class="fa fa-times"></i>
                                                {{ trans_choice('wallet::general.reject',1) }}
                                            </a>
                                        @endcan
                                        @can('wallet.wallets.edit')
                                            <a href="{{url('wallet/'.$wallet->id.'/edit')}}" class="btn btn-primary">
                                                <i class="fa fa-edit"></i>
                                                {{ trans_choice('core::general.edit',1) }}
                                            </a>
                                        @endcan
                                        @can('wallet.wallets.approve_wallet')
                                            <div class="modal fade" id="approve_wallet_modal">
                                                <div class="modal-dialog">
                                                    <div class="modal-content">
                                                        <div class="modal-header">
                                                            <button type="button" class="close" data-dismiss="modal"
                                                                    aria-label="Close">
                                                                <span aria-hidden="true">×</span></button>
                                                            <h4 class="modal-title">{{ trans_choice('wallet::general.approve',1) }} {{ trans_choice('wallet::general.wallet',1) }}</h4>
                                                        </div>
                                                        <form method="post"
                                                              action="{{ url('wallet/'.$wallet->id.'/approve_wallet') }}">
                                                            {{csrf_field()}}
                                                            <div class="modal-body">
                                                                <div class="form-group">
                                                                    <label for="approved_on_date"
                                                                           class="control-label">{{ trans_choice('core::general.date',1) }}</label>
                                                                    <input type="text" name="approved_on_date"
                                                                           class="form-control date-picker"
                                                                           value="{{date("Y-m-d")}}" required=""
                                                                           id="approved_on_date">
                                                                </div>
                                                                <div class="form-group">
                                                                    <label for="approved_notes"
                                                                           class="control-label">{{ trans_choice('core::general.note',2) }}</label>
                                                                    <textarea name="approved_notes" class="form-control"
                                                                              id="approved_notes"
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
                                            <div class="modal fade" id="reject_wallet_modal">
                                                <div class="modal-dialog">
                                                    <div class="modal-content">
                                                        <div class="modal-header">
                                                            <button type="button" class="close" data-dismiss="modal"
                                                                    aria-label="Close">
                                                                <span aria-hidden="true">×</span></button>
                                                            <h4 class="modal-title">{{ trans_choice('wallet::general.reject',1) }} {{ trans_choice('wallet::general.wallet',1) }}</h4>
                                                        </div>
                                                        <form method="post"
                                                              action="{{ url('wallet/'.$wallet->id.'/reject_wallet') }}">
                                                            {{csrf_field()}}
                                                            <div class="modal-body">
                                                                <div class="form-group">
                                                                    <label for="rejected_notes"
                                                                           class="control-label">{{ trans_choice('core::general.note',2) }}</label>
                                                                    <textarea name="rejected_notes" class="form-control"
                                                                              id="rejected_notes"
                                                                              rows="3" required=""></textarea>
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
                                        @endcan
                                    @endif
                                    @if($wallet->status=='active')
                                        @can('wallet.wallets.transactions.create')
                                            <a href="{{url('wallet/'.$wallet->id.'/deposit/create')}}"
                                               class="btn btn-success"><i class="fa fa-dollar"></i>
                                                {{ trans_choice('wallet::general.make',1) }} {{ trans_choice('wallet::general.deposit',1) }}
                                            </a>
                                            <a href="{{url('wallet/'.$wallet->id.'/transfer/loan/create')}}"
                                               class="btn btn-info"><i class="fa fa-money"></i>
                                                {{ trans_choice('wallet::general.pay',1) }} {{ trans_choice('loan::general.loan',1) }}
                                            </a>
                                            <a href="{{url('wallet/'.$wallet->id.'/transfer/savings/create')}}"
                                               class="btn btn-info"><i class="fa fa-money"></i>
                                                {{ trans_choice('wallet::general.pay',1) }} {{ trans_choice('savings::general.savings',1) }}
                                            </a>
                                        @endcan
                                        @can('wallet.wallet.edit')
                                            <a href="#" data-toggle="modal" data-target="#change_wallet_officer_modal"
                                               class="btn btn-primary">
                                                {{ trans_choice('wallet::general.change',1) }} {{ trans_choice('wallet::general.wallet',1) }} {{ trans_choice('wallet::general.officer',1) }}
                                            </a>
                                        @endcan
                                        @can('wallet.wallet.charges.create')
                                            <a href="{{url('wallet/'.$wallet->id.'/charge/create')}}"
                                               class="btn btn-primary"><i
                                                        class="fa fa-plus"></i>
                                                {{ trans_choice('core::general.add',1) }} {{ trans_choice('wallet::general.charge',1) }}
                                            </a>
                                        @endcan
                                        @can('wallet.wallet.close_wallet')
                                            <a href="#" data-toggle="modal" data-target="#close_wallet_modal"
                                               class="btn btn-primary">
                                                {{ trans_choice('core::general.close',1) }} {{ trans_choice('wallet::general.wallet',1) }}
                                            </a>
                                        @endcan
                                        @can('wallet.wallet.activate_wallet')
                                            <a href="{{url('wallet/'.$wallet->id.'/undo_activation')}}"
                                               class="btn btn-danger confirm"><i class="fa fa-undo"></i>
                                                {{ trans_choice('wallet::general.undo',1) }} {{ trans_choice('wallet::general.activation',1) }}
                                            </a>
                                        @endcan
                                        @can('wallet.wallet.edit')

                                        @endcan
                                        @can('wallet.wallet.close_wallet')
                                            <div class="modal fade" id="close_wallet_modal">
                                                <div class="modal-dialog">
                                                    <div class="modal-content">
                                                        <button type="button" class="close" data-dismiss="modal"
                                                                aria-label="Close">
                                                            <span aria-hidden="true">×</span></button>
                                                        <div class="modal-header">
                                                            <h4 class="modal-title">{{ trans_choice('core::general.close',1) }} {{ trans_choice('wallet::general.wallet',1) }}</h4>
                                                        </div>
                                                        <form method="post"
                                                              action="{{ url('wallet/'.$wallet->id.'/close_wallet') }}">
                                                            {{csrf_field()}}
                                                            <div class="modal-body">
                                                                <div class="form-group">
                                                                    <label for="closed_notes"
                                                                           class="control-label">{{ trans_choice('core::general.note',2) }}</label>
                                                                    <textarea name="closed_notes" class="form-control"
                                                                              id="closed_notes"
                                                                              rows="3" required></textarea>
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
                                        @endcan
                                    @endif
                                    @if($wallet->status=='closed')
                                        @can('wallet.wallets.close_wallet')
                                            <a href="{{url('wallet/'.$wallet->id.'/undo_closed')}}"
                                               class="btn btn-primary confirm"><i class="fa fa-undo"></i>
                                                {{ trans_choice('wallet::general.activate',1) }} {{ trans_choice('wallet::general.wallet',1) }}
                                            </a>
                                        @endcan
                                    @endif
                                    @if($wallet->status=='inactive')
                                        @can('wallet.wallets.inactive_wallet')
                                            <a href="{{url('wallet/'.$wallet->id.'/undo_inactive')}}"
                                               class="btn btn-primary confirm"><i class="fa fa-undo"></i>
                                                {{ trans_choice('wallet::general.activate',1) }} {{ trans_choice('wallet::general.wallet',1) }}
                                            </a>
                                        @endcan
                                    @endif
                                    @if($wallet->status=='dormant')
                                        @can('wallet.wallets.dormant_wallet')
                                            <a href="{{url('wallet/'.$wallet->id.'/undo_dormant')}}"
                                               class="btn btn-primary confirm"><i class="fa fa-undo"></i>
                                                {{ trans_choice('wallet::general.activate',1) }} {{ trans_choice('wallet::general.wallet',1) }}
                                            </a>
                                        @endcan
                                    @endif
                                    @if($wallet->status=='approved')
                                        @can('wallet.wallets.activate_wallet')
                                            <a href="#" data-toggle="modal" data-target="#activate_wallet_modal"
                                               class="btn btn-primary"><i class="fa fa-flag"></i>
                                                {{ trans_choice('wallet::general.activate',1) }}
                                            </a>
                                        @endcan
                                        @can('wallet.wallets.approve_wallet')
                                            <a href="{{url('wallet/'.$wallet->id.'/undo_approval')}}"
                                               class="btn btn-primary confirm"><i class="fa fa-undo"></i>
                                                {{ trans_choice('wallet::general.undo',1) }} {{ trans_choice('wallet::general.approval',1) }}
                                            </a>
                                        @endcan
                                        @can('wallet.wallets.activate_wallet')
                                            <div class="modal fade in" id="activate_wallet_modal">
                                                <div class="modal-dialog">
                                                    <div class="modal-content">
                                                        <button type="button" class="close" data-dismiss="modal">
                                                            <span>×</span></button>
                                                        <div class="modal-header">
                                                            <h4 class="modal-title">{{ trans_choice('wallet::general.activate',1) }} {{ trans_choice('wallet::general.wallet',1) }}</h4>
                                                        </div>
                                                        <form method="post"
                                                              action="{{ url('wallet/'.$wallet->id.'/activate_wallet') }}"
                                                              class="form-horizontal">
                                                            {{csrf_field()}}
                                                            <div class="modal-body">
                                                                <div class="form-group">
                                                                    <label for="activated_on_date"
                                                                           class="control-label col-md-4">{{ trans_choice('wallet::general.activation',1) }} {{ trans_choice('core::general.date',1) }}</label>
                                                                    <div class="col-md-8">
                                                                        <input type="text" name="activated_on_date"
                                                                               class="form-control date-picker"
                                                                               value="{{date("Y-m-d")}}"
                                                                               required=""
                                                                               id="activated_on_date">
                                                                    </div>
                                                                </div>
                                                                <div class="form-group">
                                                                    <label for="activated_notes"
                                                                           class="control-label col-md-4">{{ trans_choice('core::general.note',2) }}</label>
                                                                    <div class="col-md-8">
                                                                <textarea name="activated_notes" class="form-control"
                                                                          id="activated_notes" rows="3"></textarea>
                                                                    </div>
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
                                        @endcan
                                    @endif
                                    @if($wallet->status=='rejected')
                                        @can('wallet.wallets.approve_wallet')
                                            <a href="{{url('wallet/'.$wallet->id.'/undo_rejection')}}"
                                               class="btn btn-primary confirm"><i class="fa fa-undo"></i>
                                                {{ trans_choice('wallet::general.undo',1) }} {{ trans_choice('wallet::general.rejection',1) }}
                                            </a>
                                        @endcan
                                    @endif
                                    @if($wallet->status=='withdrawn')
                                        @can('wallet.wallets.approve_wallet')
                                            <a href="{{url('wallet/'.$wallet->id.'/undo_withdrawn')}}"
                                               class="btn btn-primary confirm"><i class="fa fa-undo"></i>
                                                {{ trans_choice('wallet::general.undo',1) }} {{ trans_choice('wallet::general.withdrawn',1) }}
                                            </a>
                                        @endcan
                                    @endif
                                </div>
                            </div>
                        </div>
                        <div class="row" style="margin-top: 20px">
                            <div class="col-sm-8 col-md-8">
                                <table class="table table-striped table-bordered">
                                    <tbody>
                                    <tr>
                                        <th class="table-bold-wallet">{{ trans_choice('wallet::general.current',1) }} {{ trans_choice('wallet::general.balance',1) }}</th>
                                        <td>
                                            {{number_format($wallet->transactions->where('reversed',0)->sum('credit')-$wallet->transactions->where('reversed',0)->sum('debit'),$wallet->decimals)}}
                                        </td>
                                    </tr>
                                    <tr>
                                        <th class="table-bold-wallet">{{ trans_choice('core::general.total',1) }} {{ trans_choice('wallet::general.deposit',2) }}</th>
                                        <td>
                                            {{number_format($wallet->transactions->where('reversed',0)->where('transaction_type','deposit')->sum('amount'),$wallet->decimals)}}
                                        </td>
                                    </tr>
                                    <tr>
                                        <th class="table-bold-wallet">{{ trans_choice('core::general.total',1) }} {{ trans_choice('wallet::general.withdrawal',2) }}</th>
                                        <td>
                                            {{number_format($wallet->transactions->where('reversed',0)->where('transaction_type','!=','deposit')->sum('amount'),$wallet->decimals)}}
                                        </td>
                                    </tr>
                                    </tbody>
                                </table>
                            </div>
                            <div class="col-sm-4 col-md-4">
                                <table class="table table-striped table-bordered">
                                    <tbody>
                                    <tr>
                                        <th class="table-bold-wallet">{{ trans_choice('core::general.status',1) }}</th>
                                        <td>
                                            @if($wallet->status=='submitted')
                                                <span class="label label-warning">{{ trans_choice('wallet::general.pending_approval',1) }}</span>
                                            @endif
                                            @if($wallet->status=='approved')
                                                <span class="label label-warning">{{ trans_choice('wallet::general.awaiting_activation',1) }}</span>
                                            @endif
                                            @if($wallet->status=='active')
                                                <span class="label label-success">{{ trans_choice('wallet::general.active',1) }}</span>
                                            @endif
                                            @if($wallet->status=='withdrawn')
                                                <span class="label label-danger">{{ trans_choice('wallet::general.withdrawn',1) }}</span>
                                            @endif
                                            @if($wallet->status=='rejected')
                                                <span class="label label-danger">{{ trans_choice('wallet::general.rejected',1) }}</span>
                                            @endif
                                            @if($wallet->status=='closed')
                                                <span class="label label-info">{{ trans_choice('wallet::general.closed',1) }}</span>
                                            @endif
                                            @if($wallet->status=='dormant')
                                                <span class="label label-warning">{{ trans_choice('wallet::general.dormant',1) }}</span>
                                            @endif
                                            @if($wallet->status=='inactive')
                                                <span class="label label-warning">{{ trans_choice('wallet::general.inactive',1) }}</span>
                                            @endif
                                        </td>
                                    </tr>
                                    <tr>
                                        <th class="table-bold-wallet">{{ trans_choice('client::general.client',1) }}</th>
                                        <td>
                                            @if(!empty($wallet->client))
                                                <a href="{{url('client/'.$wallet->client_id.'/show')}}">{{$wallet->client->first_name}} {{$wallet->client->middle_name}} {{$wallet->client->last_name}}</a>
                                            @endif
                                        </td>
                                    </tr>
                                    <tr>
                                        <th class="table-bold-wallet">{{ trans_choice('core::general.currency',1) }}</th>
                                        <td>
                                            @if(!empty($wallet->currency))
                                                {{$wallet->currency->name}}
                                            @endif
                                        </td>
                                    </tr>
                                    @if($wallet->status=='active' ||$wallet->status=='closed'||$wallet->status=='dormant'||$wallet->status=='inactive')
                                        <tr>
                                            <th class="table-bold-wallet">{{ trans_choice('wallet::general.activated_on',1) }}</th>
                                            <td>
                                                {{$wallet->activated_on_date}}
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
    </div>
    <div class="row my-4">
        <div class="col-md-12">
            <div class="card card-bordered card-preview">
                <div class="card-inner">
                    <ul class="nav nav-tabs">
                        <li class="nav-item">
                            <a href="#account_details" class="nav-link active"
                               data-toggle="tab">
                                {{ trans_choice('wallet::general.account',1) }} {{ trans_choice('core::general.detail',2) }}
                            </a>
                        </li>
                        @if($wallet->status=='active' ||$wallet->status=='closed'||$wallet->status=='dormant'||$wallet->status=='overpaid'||$wallet->status=='rescheduled')
                            @can('wallet.wallets.transactions.index')
                                <li class="nav-item">
                                    <a href="#wallet_transactions" class="nav-link"
                                       data-toggle="tab">
                                        {{ trans_choice('wallet::general.transaction',2) }}
                                    </a>
                                </li>
                            @endcan
                        @endif
                    </ul>
                    <div class="tab-content">
                        <div class="tab-pane active" id="account_details">
                            <table class="table table-striped table-hover">
                                <tbody>
                                <tr>
                                    <td>{{trans_choice('core::general.submitted_on',1)}}</td>
                                    <td>
                                        {{$wallet->submitted_on_date}}
                                        {{trans_choice('core::general.by',1)}}
                                        @if(!empty($wallet->submitted_by))
                                            {{$wallet->submitted_by->first_name}} {{$wallet->submitted_by->last_name}}
                                        @endif
                                    </td>
                                </tr>
                                <tr>
                                    <td>{{trans_choice('wallet::general.approved',1)}} {{trans_choice('core::general.on',1)}}</td>
                                    <td>
                                        {{$wallet->approved_on_date}}

                                        @if(!empty($wallet->approved_by))
                                            {{trans_choice('core::general.by',1)}}
                                            {{$wallet->approved_by->first_name}} {{$wallet->approved_by->last_name}}
                                        @endif
                                    </td>
                                </tr>
                                <tr>
                                    <td>{{trans_choice('wallet::general.activated',1)}} {{trans_choice('core::general.on',1)}}</td>
                                    <td>
                                        {{$wallet->activated_on_date}}

                                        @if(!empty($wallet->activated_by))
                                            {{trans_choice('core::general.by',1)}}
                                            {{$wallet->activated_by->first_name}} {{$wallet->activated_by->last_name}}
                                        @endif
                                    </td>
                                </tr>
                                @foreach($custom_fields as $custom_field)
                                    <?php
                                    $field = custom_field_build_form_field($custom_field, $wallet->id);
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
                        @if($wallet->status=='active' ||$wallet->status=='closed'||$wallet->status=='inactive'||$wallet->status=='dormant'||$wallet->status=='rescheduled')
                            @can('wallet.wallets.transactions.index')
                                <div class="tab-pane" id="wallet_transactions">
                                    <table class="table table-striped table-hover" id="wallet_transactions_table">
                                        <thead>
                                        <tr>
                                            <th>{{trans_choice('core::general.date',1)}}</th>
                                            <th>{{trans_choice('core::general.submitted_on',1)}}</th>
                                            <th>{{trans_choice('wallet::general.transaction',1)}} {{trans_choice('core::general.type',1)}}</th>
                                            <th>{{trans_choice('wallet::general.transaction',1)}} {{trans_choice('core::general.id',1)}}</th>
                                            <th>{{trans_choice('accounting::general.debit',1)}}</th>
                                            <th>{{trans_choice('accounting::general.credit',1)}}</th>
                                            <th>{{trans_choice('wallet::general.balance',1)}}</th>
                                            <th>{{trans_choice('core::general.action',1)}}</th>
                                        </tr>
                                        </thead>
                                        <tbody>
                                        <?php
                                        $balance = 0;
                                        ?>
                                        @foreach($wallet->transactions as $key)
                                            <?php
                                            $balance = $balance + $key->credit - $key->debit;
                                            ?>
                                            <tr>
                                                <td>{{$key->created_on}}</td>
                                                <td>{{$key->submitted_on}}</td>
                                                <td>
                                                    @if($key->transaction_type=='deposit')
                                                        {{ trans_choice('wallet::general.deposit', 1) }}
                                                    @endif
                                                    @if($key->transaction_type=='withdrawal')
                                                        {{ trans_choice('wallet::general.withdrawal', 1) }}
                                                    @endif
                                                    @if($key->transaction_type=='savings_transfer')
                                                        {{ trans_choice('savings::general.savings', 1) }} {{ trans_choice('wallet::general.transfer', 1) }}
                                                    @endif
                                                    @if($key->transaction_type=='loan_transfer')
                                                        {{ trans_choice('loan::general.loan', 1) }} {{ trans_choice('wallet::general.transfer', 1) }}
                                                    @endif

                                                </td>
                                                <td>{{$key->id}}</td>
                                                <td>{{number_format($key->debit,$wallet->decimals)}}</td>
                                                <td>{{number_format($key->credit,$wallet->decimals)}}</td>
                                                <td>{{number_format($balance,$wallet->decimals)}}</td>
                                                <td>
                                                    <div class="dropdown">
                                                        <button type="button"
                                                                class="btn btn-info  btn-action dropdown-toggle"
                                                                data-toggle="dropdown">
                                                            <span>{{trans_choice('core::general.action',1)}}</span>
                                                            <em class="icon ni ni-chevron-down"></em>
                                                        </button>
                                                        <div class="dropdown-menu mt-1">
                                                            <ul class="link-list-plain">
                                                                <li>
                                                                    <a href="{{url('wallet/transaction/' . $key->id . '/show') }}"
                                                                       class=""><i
                                                                                class="fa fa-search"></i> {{ trans_choice('core::general.view', 2) }}
                                                                    </a></li>

                                                                <li>
                                                                    <a href="{{url('wallet/transaction/' . $key->id . '/pdf') }}"
                                                                       target="_blank"><i
                                                                                class="fa fa-file-pdf-o"></i> {{ trans_choice('core::general.receipt', 1) }}
                                                                    </a>
                                                                </li>
                                                                <li>
                                                                    <a href="{{url('wallet/transaction/' . $key->id . '/print') }}"
                                                                       target="_blank"><i
                                                                                class="fa fa-print"></i> {{ trans_choice('core::general.print', 1) }}
                                                                    </a>
                                                                </li>
                                                                @if($key->reversible == 1 && $key->reversed==0)
                                                                    <li>
                                                                        <a href="{{url('wallet/transaction/' . $key->id . '/edit') }}"><i
                                                                                    class="fa fa-edit"></i> {{ trans_choice('core::general.edit', 1) }}
                                                                        </a>
                                                                    </li>
                                                                    <li>
                                                                        <a href="{{url('wallet/transaction/' . $key->id . '/reverse') }}"
                                                                           class="confirm"><i
                                                                                    class="fa fa-undo"></i> {{ trans_choice('wallet::general.reverse', 1) }}
                                                                        </a>
                                                                    </li>
                                                                @endif
                                                            </ul>
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
                    </div>
                    <!-- /.tab-content -->
                </div>
            </div>
        </div>
    </div>
@endsection
@section('scripts')

    @if($wallet->status=='active')

    @endif
@endsection
