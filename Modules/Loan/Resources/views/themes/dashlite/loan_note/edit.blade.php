@extends('core::layouts.master')
@section('title')
    {{ trans_choice('core::general.edit',1) }} {{ trans_choice('core::general.note',1) }}
@endsection
@section('content')
    <div class="box box-primary">
        <div class="box-header with-border">
            <h6 class="box-title">{{ trans_choice('core::general.edit',1) }} {{ trans_choice('core::general.note',1) }}</h6>

            <div class="heading-elements">

            </div>
        </div>
        <form method="post" action="{{url('loan/note/'.$loan_note->id.'/update')}}" class="form"
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
                    <label for="description"
                           class="control-label">{{trans_choice('core::general.description',1)}}</label>
                    <textarea type="text" name="description" id="description" class="form-control"
                              rows="3">{{ $loan_note->description }}</textarea>
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