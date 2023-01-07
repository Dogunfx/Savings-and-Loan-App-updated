@extends('core::layouts.master')
@section('title')
    {{ trans_choice('core::general.edit',1) }} {{ trans_choice('core::general.type',1) }}
@endsection
@section('content')
    <div class="box box-primary">
        <div class="box-header with-border">
            <h6 class="box-title">{{ trans_choice('core::general.edit',1) }} {{ trans_choice('core::general.type',1) }}</h6>

            <div class="heading-elements">

            </div>
        </div>
        <form method="post" action="{{url('payment_type/'.$payment_type->id.'/update')}}" class="form">
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
                    <input type="text" name="name" value="{{ $payment_type->name }}" id="name"
                           class="form-control" required>
                </div>
                <div class="form-group">
                    <label for="description"
                           class="control-label">{{trans_choice('core::general.description',1)}}</label>
                    <textarea name="description" id="description"
                              class="form-control">{{ $payment_type->description  }}</textarea>
                </div>
                <div class="form-group">
                    <label for="position" class="control-label">{{trans_choice('core::general.position',1)}}</label>
                    <input type="text" name="position" value="{{ $payment_type->position  }}" id="position"
                           class="form-control">
                </div>
                <div class="form-group">
                    <label for="is_cash" class="control-label">{{trans_choice('core::general.is_cash',1)}}</label>
                    <select class="form-control" name="is_cash" id="is_cash">
                        <option value="1"
                                @if($payment_type->is_cash==1) selected @endif>{{trans_choice('core::general.yes',1)}}</option>
                        <option value="0"
                                @if($payment_type->is_cash==0) selected @endif>{{trans_choice('core::general.no',1)}}</option>
                    </select>
                </div>
                <div class="form-group">
                    <label for="active" class="control-label">{{trans_choice('core::general.active',1)}}</label>
                    <select class="form-control" name="active" id="active">
                        <option value="1"
                                @if($payment_type->active==1) selected @endif>{{trans_choice('core::general.yes',1)}}</option>
                        <option value="0"
                                @if($payment_type->active==0) selected @endif>{{trans_choice('core::general.no',1)}}</option>
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