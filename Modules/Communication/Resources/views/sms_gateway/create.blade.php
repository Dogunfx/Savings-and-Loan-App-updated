@extends('core::layouts.master')
@section('title')
    {{ trans_choice('core::general.add',1) }} {{ trans_choice('communication::general.gateway',1) }}
@endsection
@section('content')
    <div class="box box-primary">
        <div class="box-header with-border">
            <h3 class="box-title">{{ trans_choice('core::general.add',1) }} {{ trans_choice('communication::general.gateway',1) }}</h3>

            <div class="box-tools">
                <a href="#" onclick="window.history.back()"
                   class="btn btn-info btn-sm">{{ trans_choice('core::general.back',1) }}</a>
            </div>
        </div>
        <form method="post" action="{{ url('communication/sms_gateway/store') }}">
            {{csrf_field()}}
            <div class="box-body">
                {{csrf_field()}}
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
                    <input type="text" name="name" value="{{ old('name') }}" id="name"
                           class="form-control" required>
                </div>
                <div class="form-group">
                    <label for="to_name" class="control-label">{{trans_choice('communication::general.to_name',1)}}</label>
                    <input type="text" name="to_name" value="{{ old('to_name') }}" id="to_name"
                           class="form-control" required>
                </div>
                <div class="form-group">
                    <label for="msg_name" class="control-label">{{trans_choice('communication::general.msg_name',1)}}</label>
                    <input type="text" name="msg_name" value="{{ old('msg_name') }}" id="msg_name"
                           class="form-control" required>
                </div>
                <div class="form-group">
                    <label for="url" class="control-label">{{trans_choice('communication::general.url',1)}}</label>
                    <input type="text" name="url" value="{{ old('url') }}" id="url"
                           class="form-control" required>
                </div>
                <div class="form-group">
                    <label class="control-label" for="active">{{trans_choice('core::general.active',1)}}</label>
                    <select class="form-control" name="active" id="active" required>
                        <option value="1">{{trans_choice('core::general.yes',1)}}</option>
                        <option value="0">{{trans_choice('core::general.no',1)}}</option>
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