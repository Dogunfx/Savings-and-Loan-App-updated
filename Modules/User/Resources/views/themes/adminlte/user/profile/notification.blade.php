@extends('core::layouts.master')
@section('title')
    {{__('user::general.Notifications')}}
@endsection
@section('styles')
@stop
@section('content')
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1>
                        {{__('user::general.Notifications')}} / <strong
                                class="text-primary small">{{ $user->first_name }} {{ $user->last_name }}</strong>
                        <a href="#" onclick="window.history.back()"
                           class="btn btn-outline-light bg-white d-none d-sm-inline-flex">
                            <em class="icon ni ni-arrow-left"></em><span>{{ trans_choice('core::general.back',1) }}</span>
                        </a>
                    </h1>

                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a
                                    href="{{url('dashboard')}}">{{ trans_choice('dashboard::general.dashboard',1) }}</a>
                        </li>
                        <li class="breadcrumb-item active">{{__('user::general.Notifications')}} / <strong
                                    class="text-primary small">{{ $user->first_name }} {{ $user->last_name }}</strong>
                        </li>
                    </ol>
                </div>
            </div>
        </div><!-- /.container-fluid -->
    </section>
    <section class="content" id="app">
        <div class="row">
            <div class="col-md-3">
                @include('user::themes.adminlte.user.profile.user_profile_menu')
            </div>
            <!-- /.col -->
            <div class="col-md-9">
                <div class="card card-bordered card-preview">
                    <div class="card-header">
                        <h4 class="card-title">{{__('user::general.Notifications')}}</h4>
                        <div class="card-tools">
                            <a href="{{url('admin/user/profile/notification/mark_all_as_read')}}"
                               class="btn  btn-success confirm">
                                <small>{{__('user::general.Mark All As Read')}}</small>
                            </a>
                        </div>
                    </div>
                    <div class="card-body">
                        @foreach($data as $notification)

                            <div class="post  @if(!$notification->read_at) bg-light @endif">
                               <p>{{$notification->data['message']}}</p>
                                <p>
                                    <small>{{$notification->created_at->diffForHumans()}}</small>
                                    <a class="link-black text-sm mr-2"
                                       href="{{url('user/profile/notification/'.$notification->id.'/show')}}">
                                        <small><i
                                                class="fas fa-eye"></i>
                                        {{trans_choice('core::general.detail',2)}}
                                        </small>
                                    </a>
                                    @if(!$notification->read_at)
                                        <a href="{{url('user/profile/notification/'.$notification->id.'/mark_as_read')}}"
                                           class="link-black text-sm mr-2">
                                            <small>
                                            <i class="icon ni ni-emails"></i>
                                            {{__('user::general.Mark As Read')}}
                                            </small>
                                        </a>

                                    @endif
                                    <a class="link-black text-sm mr-2 confirm"
                                       href="{{url('user/profile/notification/'.$notification->id.'/destroy')}}">
                                        <small><i
                                                class="fas fa-trash"></i>
                                            {{trans_choice('core::general.delete',1)}}
                                        </small>
                                    </a>
                                </p>
                            </div>
                        @endforeach
                        <div class="d-flex">
                            <div class="mx-auto">
                                {{$data->links()}}
                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </section>

@endsection
@section('scripts')

@endsection
