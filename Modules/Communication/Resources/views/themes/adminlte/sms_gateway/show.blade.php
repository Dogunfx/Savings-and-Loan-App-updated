@extends('core::layouts.master')
@section('title')
    {{ $sms_gateway->name }}
@endsection
@section('content')
    <div class="box box-primary">
        <div class="box-header with-border">
            <h6 class="box-title">{{ $sms_gateway->name }}</h6>

            <div class="heading-elements">

            </div>
        </div>

        <div class="box-body">


        </div>

    </div>
@endsection
@section('scripts')

@endsection