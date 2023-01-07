@extends('core::layouts.master')
@section('title')
    {{ trans_choice('loan::general.loan',1) }}  {{ trans_choice('loan::general.application',1) }} #{{$loan_application->id}}
@endsection
@section('styles')
@stop
@section('content')
    <div class="nk-block-head-content mb-4">
        <div class="nk-block-head nk-block-head-sm">
            <div class="nk-block-between">
                <div class="nk-block-head-content">
                    <h3 class="nk-block-title page-title">{{ trans_choice('loan::general.loan',1) }}  {{ trans_choice('loan::general.application',1) }}
                        #{{$loan_application->id}}</h3>
                    <div class="nk-block-des text-soft">

                    </div>
                </div><!-- .nk-block-head-content -->
                <div class="nk-block-head-content">
                    <a href="#" onclick="window.history.back()"
                       class="btn btn-outline-light bg-white d-none d-sm-inline-flex">
                        <em class="icon ni ni-arrow-left"></em><span>{{ trans_choice('core::general.back',1) }}</span>
                    </a>

                </div><!-- .nk-block-head-content -->
            </div><!-- .nk-block-between -->
        </div>
    </div>
    <div class="nk-block nk-block-lg" id="app">
        <div class="card card-bordered card-preview">
            <div class="card-inner">
                <table class="table  table-striped table-hover table-condensed" id="data-table">

                    <tr>
                        <td>{{ trans_choice('core::general.id',1) }} </td>
                        <td>{{$loan_application->id}}</td>
                    </tr>
                    <tr>
                        <td>{{ trans_choice('core::general.branch',1) }} </td>
                        <td>
                            @if(!empty($loan_application->branch))
                                {{$loan_application->branch->name}}
                            @endif
                        </td>

                    </tr>
                    <tr>
                        <td>{{ trans_choice('client::general.client',1) }} </td>
                        <td>
                            @if(!empty($loan_application->client))
                                <a href="{{url('client/'.$loan_application->client_id.'/show')}}">{{$loan_application->client->first_name}} {{$loan_application->client->last_name}}</a>
                            @endif
                        </td>
                    </tr>
                    <tr>
                        <td>{{ trans_choice('loan::general.product',1) }} </td>
                        <td>
                            @if(!empty($loan_application->loan_product))
                                {{$loan_application->loan_product->name}}
                            @endif
                        </td>
                    </tr>
                    <tr>
                        <td>{{ trans_choice('loan::general.amount',1) }} </td>
                        <td>{{number_format($loan_application->amount,2)}}</td>
                    </tr>
                    <tr>
                        <td>{{ trans_choice('loan::general.status',1) }} </td>
                        <td>
                            @if($loan_application->status=='pending')
                                {{ trans_choice('loan::general.pending_approval',1) }}
                                <a href="{{url('loan/application/'.$loan_application->id.'/approve')}}"
                                   class="btn btn-info">{{ trans_choice('loan::general.approve',1) }}</a>
                                <a href="{{url('loan/application/'.$loan_application->id.'/reject')}}"
                                   class="btn btn-danger confirm">{{ trans_choice('loan::general.reject',1) }}</a>
                            @endif
                            @if($loan_application->status=='approved')
                                {{ trans_choice('loan::general.approved',1) }}
                            @endif
                            @if($loan_application->status=='rejected')
                                {{ trans_choice('loan::general.rejected',1) }}
                                <a href="{{url('loan/application/'.$loan_application->id.'/undo_reject')}}"
                                   class="btn btn-danger confirm">{{ trans_choice('loan::general.undo',1) }} {{ trans_choice('loan::general.reject',1) }}</a>

                            @endif
                        </td>
                    </tr>
                    <tr>
                        <td>{{ trans_choice('core::general.date',1) }} </td>
                        <td>{{$loan_application->created_at}}</td>
                    </tr>
                </table>
            </div>
        </div>
    </div>

@endsection
@section('scripts')

@endsection
