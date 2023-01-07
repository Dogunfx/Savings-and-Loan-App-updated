@extends('core::layouts.master')
@section('title')
    {{ trans_choice('core::general.upload',1) }} {{ trans_choice('core::general.module',1) }}
@endsection
@section('content')
    <div class="box box-primary">
        <div class="box-header with-border">
            <h3 class="box-title">{{ trans_choice('core::general.upload',1) }} {{ trans_choice('core::general.module',1) }}</h3>

            <div class="box-tools">
                <a href="#" onclick="window.history.back()"
                   class="btn btn-info btn-sm">{{ trans_choice('core::general.back',1) }}</a>
            </div>
        </div>
        <form method="post" action="{{ url('module/upload') }}" enctype="multipart/form-data">
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
                    <label for="file" class="control-label">{{trans_choice('core::general.file',1)}}</label>
                    <input type="file" name="file" id="file"
                           class="form-control" required>
                </div>
            </div>
            <!-- /.box-body -->
            <div class="box-footer">

                <button type="submit" class="btn btn-primary pull-right">{{trans_choice('core::general.upload',1)}}</button>

            </div>
        </form>
    </div>
@endsection
@section('scripts')

@endsection