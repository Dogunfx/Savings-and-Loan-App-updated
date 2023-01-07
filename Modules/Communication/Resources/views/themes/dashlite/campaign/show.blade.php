@extends('core::layouts.master')
@section('title')
    {{ $communication_campaign->name }}
@endsection
@section('content')
    <div class="box box-primary">
        <div class="box-header with-border">
            <h6 class="box-title">{{ $communication_campaign->name }}</h6>

            <div class="heading-elements">

            </div>
        </div>

        <div class="box-body">


        </div>

    </div>
@endsection
@section('scripts')

@endsection