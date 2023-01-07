@extends('core::layouts.master')
@section('title')
    {{ __('user::general.Activity Logs') }}
@endsection
@section('styles')
@stop

@section('content')
    <div class="nk-block-head nk-block-head-sm">
        <div class="nk-block-between g-3">
            <div class="nk-block-head-content">
                <h3 class="nk-block-title page-title">{{ __('user::general.Activity Logs') }} / <strong
                            class="text-primary small">{{ $user->first_name }} {{ $user->last_name }}</strong></h3>

            </div>
            <div class="nk-block-head-content">

                <a href="#" onclick="window.history.back()"
                   class="btn btn-outline-light bg-white d-none d-sm-inline-flex"><em
                            class="icon ni ni-arrow-left"></em><span>{{ trans_choice('core::general.back',1) }}</span></a>

            </div>
        </div>
    </div>
    <div class="nk-block nk-block-lg" id="app">
        <div class="row">
            <div class="col-md-3">
                @include('user::themes.dashlite.user.profile.user_profile_menu')
            </div>
            <!-- /.col -->
            <div class="col-md-9">


                <div class="card card-bordered card-preview">

                    <div class="card-inner">
                        <div class="nk-block-head nk-block-head-lg">
                            <div class="nk-block-between">
                                <div class="nk-block-head-content">
                                    <h4 class="nk-block-title">{{ __('user::general.Activity Logs') }}</h4>

                                </div>
                                <div class="nk-block-head-content">

                                </div>
                            </div>
                        </div>
                        <table class="table  table-striped table-hover">
                            <thead>
                            <tr>
                                <th>{{ trans_choice('general.description',1) }}</th>
                                <th>{{ trans_choice('general.created_at',1) }}</th>
                                <th>{{ trans_choice('general.action',1) }}</th>
                            </tr>
                            </thead>
                            <tbody>
                            @foreach($data as $key)
                                <tr>
                                    <td>{{$key->description}}</td>
                                    <td>{{$key->created_at->format('Y-m-d H:i:s')}}</td>
                                    <td>
                                        <a href="{{url('user/profile/activity_log/' . $key->id . '/show') }}"
                                           class="bg-white btn btn-sm btn-outline-light btn-icon btn-tooltip" title=""
                                           data-original-title="Details">
                                            <em class="icon ni ni-eye"></em>
                                        </a>
                                    </td>
                                </tr>
                            @endforeach
                            </tbody>
                        </table>

                    </div>
                    <div class="card-inner">
                        {{$data->links()}}
                    </div>
                </div>

            </div>
        </div>
    </div>


@endsection
@section('scripts')
    <script>

    </script>
@endsection
