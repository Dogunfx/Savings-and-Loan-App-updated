@extends('core::layouts.master')
@section('title')
    {{ trans_choice('core::general.edit',1) }} {{ trans_choice('loan::general.check',1) }}
@endsection
@section('content')
    <div class="box box-primary">
        <div class="box-header with-border">
            <h6 class="box-title">{{ trans_choice('core::general.edit',1) }} {{ trans_choice('loan::general.check',1) }}</h6>

            <div class="heading-elements">

            </div>
        </div>
        <form method="post" action="{{url('loan/credit_check/'.$loan_credit_check->id.'/update')}}" class="form"
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
                    <label for="translated_name" class="control-label">{{trans_choice('core::general.name',1)}}</label>
                    <input type="text" name="translated_name" value="{{$loan_credit_check->translated_name}}"
                           id="translated_name"
                           class="form-control">
                </div>
                <div class="form-group">
                    <label for="active" class="control-label">{{trans('core::general.active')}}</label>
                    <select class="form-control" name="active" id="active" required>
                        <option value="1"
                                @if($loan_credit_check->active=="1") selected @endif>{{trans_choice("core::general.yes",1)}}</option>
                        <option value="0"
                                @if($loan_credit_check->active=="0") selected @endif>{{trans_choice("core::general.no",1)}}</option>
                    </select>
                </div>
                <div class="form-group">
                    <label for="security_level" class="control-label">{{trans('loan::general.security_level')}}</label>
                    <select class="form-control" name="security_level" id="security_level" required>
                        <option value="block"
                                @if($loan_credit_check->security_level=="block") selected @endif> {{ trans_choice('loan::general.block',1) }} {{ trans_choice('loan::general.loan',1) }}</option>
                        <option value="pass"
                                @if($loan_credit_check->security_level=="pass") selected @endif> {{ trans_choice('loan::general.pass',1) }}</option>
                        <option value="warning"
                                @if($loan_credit_check->security_level=="warning") selected @endif> {{ trans_choice('loan::general.warning',1) }}</option>
                    </select>
                </div>
                <div class="form-group">
                    <label for="rating_type" class="control-label">{{trans('loan::general.rating_type')}}</label>
                    <select class="form-control" name="rating_type" id="rating_type" required>
                        <option value="boolean"
                                @if($loan_credit_check->rating_type=="boolean") selected @endif> {{ trans_choice('loan::general.boolean',1) }}</option>
                        <option value="score"
                                @if($loan_credit_check->rating_type=="score") selected @endif> {{ trans_choice('loan::general.score',1) }}</option>
                    </select>
                </div>
                <div id="score_div">
                    <div class="form-group">
                        <label for="pass_max_amount"
                               class="control-label">{{trans('loan::general.pass_max_amount')}}</label>
                        <input type="text" name="pass_max_amount" value="{{$loan_credit_check->pass_max_amount}}"
                               id="pass_max_amount"
                               class="form-control">
                    </div>
                    <div class="form-group">
                        <label for="pass_min_amount"
                               class="control-label">{{trans('loan::general.pass_min_amount')}}</label>
                        <input type="text" name="pass_min_amount" value="{{$loan_credit_check->pass_min_amount}}"
                               id="pass_min_amount"
                               class="form-control">
                    </div>
                    <div class="form-group">
                        <label for="warn_max_amount"
                               class="control-label">{{trans('loan::general.warn_max_amount')}}</label>
                        <input type="text" name="warn_max_amount" value="{{$loan_credit_check->warn_max_amount}}"
                               id="warn_max_amount"
                               class="form-control">
                    </div>
                    <div class="form-group">
                        <label for="warn_min_amount"
                               class="control-label">{{trans('loan::general.warn_min_amount')}}</label>
                        <input type="text" name="warn_min_amount" value="{{$loan_credit_check->warn_min_amount}}"
                               id="warn_min_amount"
                               class="form-control">
                    </div>
                    <div class="form-group">
                        <label for="fail_max_amount"
                               class="control-label">{{trans('loan::general.fail_max_amount')}}</label>
                        <input type="text" name="fail_max_amount" value="{{$loan_credit_check->fail_max_amount}}"
                               id="fail_max_amount"
                               class="form-control">
                    </div>
                    <div class="form-group">
                        <label for="fail_min_amount"
                               class="control-label">{{trans('loan::general.fail_min_amount')}}</label>
                        <input type="text" name="fail_min_amount" value="{{$loan_credit_check->fail_min_amount}}"
                               id="fail_min_amount"
                               class="form-control">
                    </div>

                </div>
                <div class="form-group">
                    <label for="general_error_msg"
                           class="control-label">{{trans('loan::general.general_error_msg')}}</label>
                    <textarea type="text" name="general_error_msg" id="general_error_msg" class="form-control"
                              rows="3" required>{{$loan_credit_check->general_error_msg}}</textarea>
                </div>
                <div class="form-group">
                    <label for="user_friendly_error_msg"
                           class="control-label">{{trans('loan::general.user_friendly_error_msg')}}</label>
                    <textarea type="text" name="user_friendly_error_msg" id="user_friendly_error_msg"
                              class="form-control"
                              rows="3" required>{{$loan_credit_check->user_friendly_error_msg}}</textarea>
                </div>
                <div class="form-group">
                    <label for="general_warning_msg"
                           class="control-label">{{trans('loan::general.general_warning_msg')}}</label>
                    <textarea type="text" name="general_warning_msg" id="general_warning_msg" class="form-control"
                              rows="3" required>{{$loan_credit_check->general_warning_msg}}</textarea>
                </div>
                <div class="form-group">
                    <label for="user_friendly_warning_msg"
                           class="control-label">{{trans('loan::general.user_friendly_warning_msg')}}</label>
                    <textarea type="text" name="user_friendly_warning_msg" id="user_friendly_warning_msg"
                              class="form-control"
                              rows="3" required>{{$loan_credit_check->user_friendly_warning_msg}}</textarea>
                </div>
                <div class="form-group">
                    <label for="general_success_msg"
                           class="control-label">{{trans('loan::general.general_success_msg')}}</label>
                    <textarea type="text" name="general_success_msg" id="general_success_msg" class="form-control"
                              rows="3" required>{{$loan_credit_check->general_success_msg}}</textarea>
                </div>
                <div class="form-group">
                    <label for="user_friendly_success_msg"
                           class="control-label">{{trans('loan::general.user_friendly_success_msg')}}</label>
                    <textarea type="text" name="user_friendly_success_msg" id="user_friendly_success_msg"
                              class="form-control"
                              rows="3" required>{{$loan_credit_check->user_friendly_success_msg}}</textarea>
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
        }else{
            $("#score_div").show();
            $("#pass_max_amount").attr('required','required');
            $("#pass_min_amount").attr('required','required');
            $("#warn_max_amount").attr('required','required');
            $("#warn_min_amount").attr('required','required');
            $("#fail_max_amount").attr('required','required');
            $("#fail_min_amount").attr('required','required');
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
            }else{
                $("#score_div").show();
                $("#pass_max_amount").attr('required','required');
                $("#pass_min_amount").attr('required','required');
                $("#warn_max_amount").attr('required','required');
                $("#warn_min_amount").attr('required','required');
                $("#fail_max_amount").attr('required','required');
                $("#fail_min_amount").attr('required','required');
            }
        })
    </script>
@endsection