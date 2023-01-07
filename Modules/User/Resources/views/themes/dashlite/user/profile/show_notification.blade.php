@extends('core::layouts.master')
@section('title')
    {{__('user::general.Notifications')}}
@endsection
@section('styles')
@stop

@section('content')
    <div class="nk-block-head nk-block-head-sm">
        <div class="nk-block-between g-3">
            <div class="nk-block-head-content">
                <h3 class="nk-block-title page-title">{{__('user::general.Notifications')}} / <strong
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
                                    <h4 class="nk-block-title">{{__('user::general.Notifications')}}</h4>

                                </div>
                                <div class="nk-block-head-content">
                                    <a href="{{url('admin/user/profile/notification/mark_all_as_read')}}"
                                       class="btn btn-outline-light btn-success d-none d-sm-inline-flex confirm">
                                        <small>{{__('user::general.Mark All As Read')}}</small>
                                    </a>
                                </div>
                            </div>
                        </div>

                            <div class="nk-ibx-item  @if(!$notification->read_at) bg-lighter @endif">
                                <a href="{{url('user/profile/notification/'.$notification->id.'/show')}}"
                                   class="nk-ibx-link"></a>
                                <div class="nk-ibx-item-elem nk-ibx-item-fluid">
                                    <div class="nk-ibx-context-group">
                                        <div class="nk-ibx-context">
                                                        <span class="nk-ibx-context-text">
                                                            <span class="heading">{{$notification->data['message']}}</span>
                                                        </span>
                                            <div class="sub-text">{{$notification->created_at->diffForHumans()}}</div>
                                        </div>
                                    </div>
                                </div>
                                <div class="nk-ibx-item-elem nk-ibx-item-tools">
                                    <div class="ibx-actions">
                                        <ul class="ibx-actions-hidden gx-1">
                                            <li>
                                                <a href="{{url('user/profile/notification/'.$notification->id.'/destroy')}}"
                                                   class="btn btn-sm btn-icon btn-trigger confirm"
                                                   data-toggle="tooltip" data-placement="top" title=""
                                                   data-original-title="{{trans_choice('core::general.delete',1)}}"><em
                                                            class="icon ni ni-trash"></em>
                                                </a>
                                            </li>
                                        </ul>
                                        <ul class="ibx-actions-visible gx-2">
                                            <li>
                                                <div class="dropdown">
                                                    <a href="#"
                                                       class="dropdown-toggle btn btn-sm btn-icon btn-trigger"
                                                       data-toggle="dropdown"><em
                                                                class="icon ni ni-more-h"></em></a>
                                                    <div class="dropdown-menu dropdown-menu-right">
                                                        <ul class="link-list-opt no-bdr">

                                                            <li>
                                                                <a class="dropdown-item"
                                                                   href="{{url('user/profile/notification/'.$notification->id.'/show')}}"><em
                                                                            class="icon ni ni-eye"></em>
                                                                    <span>{{trans_choice('core::general.detail',2)}}</span>
                                                                </a>
                                                            </li>
                                                            @if(!$notification->read_at)
                                                                <li>
                                                                    <a href="{{url('user/profile/notification/'.$notification->id.'/mark_as_read')}}"
                                                                       class="dropdown-item">
                                                                        <em class="icon ni ni-emails"></em>
                                                                        {{__('user::general.Mark As Read')}}
                                                                    </a>
                                                                </li>
                                                            @endif
                                                            <li>
                                                                <a class="dropdown-item confirm"
                                                                   href="{{url('user/profile/notification/'.$notification->id.'/destroy')}}"><em
                                                                            class="icon ni ni-trash"></em><span>{{trans_choice('core::general.delete',1)}}</span>
                                                                </a>
                                                            </li>
                                                        </ul>
                                                    </div>
                                                </div>
                                            </li>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                    </div>
                </div>

            </div>
        </div>
    </div>

@endsection
@section('scripts')

@endsection
