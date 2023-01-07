@extends('core::layouts.master')
@section('title')
    {{ trans_choice('core::general.edit',1) }} {{ trans_choice('loan::general.charge',1) }}
@endsection
@section('content')
    <div class="box box-primary">
        <div class="box-header with-border">
            <h6 class="box-title">{{ trans_choice('core::general.edit',1) }} {{ trans_choice('loan::general.charge',1) }}</h6>

            <div class="heading-elements">

            </div>
        </div>
        <form method="post" action="{{url('loan/charge/'.$loan_charge->id.'/update')}}" class="form"
              enctype="multipart/form-data">
            {{csrf_field()}}
            <div class="box-body">
                @if (count($errors) > 0)
                    <div class="form-group has-feedback">
                        <div class="alert alert-danger">
                            <ul>
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    </div>
                @endif
                <div class="form-group">
                    <label for="name" class="control-label">{{trans_choice('core::general.name',1)}}</label>
                    <input type="text" name="name" value="{{ $loan_charge->name }}" id="name"
                           class="form-control">
                </div>
                <div class="form-group">
                    <label for="loan_charge_type_id"
                           class="control-label">{{trans_choice('loan::general.charge',1)}} {{trans_choice('core::general.type',1)}}</label>
                    <select class="form-control select2" name="loan_charge_type_id" id="loan_charge_type_id" required>
                        <option value=""></option>
                        @foreach($charge_types as $key)
                            <option value="{{$key->id}}" @if($loan_charge->loan_charge_type_id==$key->id) selected @endif>{{$key->name}}</option>
                        @endforeach

                    </select>
                </div>
                <div class="form-group">
                    <label for="amount" class="control-label">{{trans('core::general.amount')}}</label>
                    <input type="text" name="amount" value="{{ $loan_charge->amount }}" id="amount"
                           class="form-control numeric" required>
                </div>
                <div class="form-group">
                    <label for="loan_charge_option_id"
                           class="control-label">{{trans_choice('loan::general.charge',1)}} {{trans_choice('core::general.option',1)}}</label>
                    <select class="form-control select2" name="loan_charge_option_id" id="loan_charge_option_id"
                            required>
                        <option value=""></option>
                        @foreach($charge_options as $key)
                            <option value="{{$key->id}}" @if($loan_charge->loan_charge_option_id==$key->id) selected @endif>{{$key->name}}</option>
                        @endforeach

                    </select>
                </div>
                <div class="form-group">
                    <label for="currency_id"
                           class="control-label">{{trans_choice('core::general.currency',1)}}</label>
                    <select class="form-control select2" name="currency_id" id="currency_id"
                            required>
                        <option value=""></option>
                        @foreach($currencies as $key)
                            <option value="{{$key->id}}" @if($loan_charge->currency_id==$key->id) selected @endif>{{$key->name}}</option>
                        @endforeach

                    </select>
                </div>
                <div class="form-group">
                    <label for="is_penalty" class="control-label">{{trans_choice('loan::general.penalty',1)}}</label>
                    <select class="form-control" name="is_penalty" id="is_penalty" required>
                        <option value="0"
                                @if($loan_charge->is_penalty=="0") selected @endif>{{trans_choice("core::general.no",1)}}</option>
                        <option value="1"
                                @if($loan_charge->is_penalty=="1") selected @endif>{{trans_choice("core::general.yes",1)}}</option>
                    </select>
                </div>
                <div class="form-group">
                    <label for="allow_override" class="control-label">{{trans('loan::general.override')}}</label>
                    <select class="form-control" name="allow_override" id="allow_override" required>
                        <option value="0"
                                @if($loan_charge->allow_override=="0") selected @endif>{{trans_choice("core::general.no",1)}}</option>
                        <option value="1"
                                @if($loan_charge->allow_override=="1") selected @endif>{{trans_choice("core::general.yes",1)}}</option>
                    </select>
                </div>
                <div class="form-group">
                    <label for="active" class="control-label">{{trans('core::general.active')}}</label>
                    <select class="form-control" name="active" id="active" required>
                        <option value="1"
                                @if($loan_charge->active=="1") selected @endif>{{trans_choice("core::general.yes",1)}}</option>
                        <option value="0"
                                @if($loan_charge->active=="0") selected @endif>{{trans_choice("core::general.no",1)}}</option>
                    </select>
                </div>
            </div>
            <!-- /.box-body -->
            <div class="box-footer">
                <button type="submit" class="btn btn-primary pull-right">{{trans_choice('general.save',1)}}</button>
            </div>
        </form>
    </div>
@endsection
@section('scripts')
    <script>
        var rating_type = $("#rating_type");
        if (rating_type.val() === 'boolean') {
            $("#score_div").hide();
            $("#pass_max_amount").removeAttr('required');
            $("#pass_min_amount").removeAttr('required');
            $("#warn_max_amount").removeAttr('required');
            $("#warn_min_amount").removeAttr('required');
            $("#fail_max_amount").removeAttr('required');
            $("#fail_min_amount").removeAttr('required');
        } else {
            $("#score_div").show();
            $("#pass_max_amount").attr('required', 'required');
            $("#pass_min_amount").attr('required', 'required');
            $("#warn_max_amount").attr('required', 'required');
            $("#warn_min_amount").attr('required', 'required');
            $("#fail_max_amount").attr('required', 'required');
            $("#fail_min_amount").attr('required', 'required');
        }
        rating_type.change(function () {
            if (rating_type.val() === 'boolean') {
                $("#score_div").hide();
                $("#pass_max_amount").removeAttr('required');
                $("#pass_min_amount").removeAttr('required');
                $("#warn_max_amount").removeAttr('required');
                $("#warn_min_amount").removeAttr('required');
                $("#fail_max_amount").removeAttr('required');
                $("#fail_min_amount").removeAttr('required');
            } else {
                $("#score_div").show();
                $("#pass_max_amount").attr('required', 'required');
                $("#pass_min_amount").attr('required', 'required');
                $("#warn_max_amount").attr('required', 'required');
                $("#warn_min_amount").attr('required', 'required');
                $("#fail_max_amount").attr('required', 'required');
                $("#fail_min_amount").attr('required', 'required');
            }
        })
    </script>
@endsection