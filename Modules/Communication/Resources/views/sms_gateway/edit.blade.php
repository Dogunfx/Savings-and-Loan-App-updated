@extends('core::layouts.master')
@section('title')
    {{ trans_choice('core::general.edit',1) }} {{ trans_choice('communication::general.gateway',1) }}
@endsection
@section('content')
    <div class="box box-primary">
        <div class="box-header with-border">
            <h6 class="box-title">{{ trans_choice('core::general.edit',1) }} {{ trans_choice('communication::general.gateway',1) }}</h6>

            <div class="heading-elements">

            </div>
        </div>
        <form method="post" action="{{url('communication/sms_gateway/'.$sms_gateway->id.'/update')}}" class="form">
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
                    <input type="text" name="name" value="{{ $sms_gateway->name }}" id="name"
                           class="form-control" required>
                </div>
                <div class="form-group">
                    <label for="to_name"
                           class="control-label">{{trans_choice('communication::general.to_name',1)}}</label>
                    <input type="text" name="to_name" value="{{ $sms_gateway->to_name }}" id="to_name"
                           class="form-control" required>
                </div>
                <div class="form-group">
                    <label for="msg_name"
                           class="control-label">{{trans_choice('communication::general.msg_name',1)}}</label>
                    <input type="text" name="msg_name" value="{{ $sms_gateway->msg_name }}" id="msg_name"
                           class="form-control" required>
                </div>
                <div class="form-group">
                    <label for="url" class="control-label">{{trans_choice('communication::general.url',1)}}</label>
                    <input type="text" name="url" value="{{ $sms_gateway->url }}" id="url"
                           class="form-control" required>
                </div>
                <div class="form-group">
                    <label class="control-label" for="active">{{trans_choice('core::general.active',1)}}</label>
                    <select class="form-control" name="active" id="active" required>
                        <option value="1"
                                @if($sms_gateway->active==1) selected @endif>{{trans_choice('core::general.yes',1)}}</option>
                        <option value="0"
                                @if($sms_gateway->active==0) selected @endif>{{trans_choice('core::general.no',1)}}</option>
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

@endsection