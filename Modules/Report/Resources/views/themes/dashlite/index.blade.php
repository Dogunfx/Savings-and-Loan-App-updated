@extends('core::layouts.master')
@section('title')
    {{ trans_choice('report::general.report',2) }}
@endsection
@section('content')
    <div class="row">
        <div class="col-md-6">
            <div class="list-group">
                @foreach($data as $keys)
                    @foreach($keys as $key=>$value)
                        <a href="{{url($key)}}" class="list-group-item">{{$value}}</a>
                    @endforeach
                @endforeach
            </div>
        </div>
    </div>
@endsection
@section('scripts')

@endsection