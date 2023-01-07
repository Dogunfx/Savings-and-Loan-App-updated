<div class="card card-primary card-outline">
    <div class="card-body box-profile">
        <div class="text-center">
            @if(!empty($user->photo))
                <a href="{{asset('storage/uploads/'.$user->photo)}}"
                   class="fancybox">
                    <img
                            class="profile-user-img img-fluid img-circle"
                            src="{{asset('storage/uploads/'.$user->photo)}}"
                            alt="User profile picture">
                </a>
            @else
                <img class="profile-user-img img-fluid img-circle"
                     src="{{asset('themes/adminlte/img/user.png')}}"
                     alt="User profile picture">
            @endif
        </div>
        <h3 class="profile-username text-center">
            {{ $user->first_name }} {{ $user->last_name }}
        </h3>
        <p class="text-muted text-center">
            @if($user->gender='male')
                {{trans_choice("user::general.male",1)}}
            @elseif($user->gender='female')
                {{trans_choice("user::general.female",1)}}
            @else
                {{__('general.Unspecified')}}
                {{trans_choice("user::general.unspecified",1)}}
            @endif
        </p>
        <p class="text-muted text-center">
            @foreach($user->roles as $key)
                <span class="badge badge-light">{{$key->name}}</span>
            @endforeach
        </p>
        <p class="text-muted text-center">
            <a href="{{url('user/'.$user->id.'/edit')}}"
               class="btn btn-info confirm  btn-sm mr-1">{{trans_choice('core::general.edit',1)}}</a>
            <a href="{{url('user/'.$user->id.'/destroy')}}"
               class="btn btn-danger confirm btn-sm mr-1">{{trans_choice('core::general.delete',1)}}</a>
        </p>
        <div class="list-group list-group-unbordered mb-3">
            <a class="list-group-item @if(Request::segment(3)=='') active @endif" href="{{url('user/profile')}}">
                <i class="fas fa-user-edit"></i>
                <span>{{ __('user::general.Account Information') }}</span>
            </a>
            <a href="{{url('user/profile/change_password')}}"
               class="list-group-item @if(Request::segment(3)=='change_password') active @endif">
                <em class="fas fa-user-shield"></em>
                <span> {{ __('user::general.Change Password') }}</span>
            </a>
            <a href="{{url('user/profile/notification')}}"
               class="list-group-item @if(Request::segment(3)=='notification') active @endif">
                <i class="fas fa-bell"></i>
                <span>{{ __('user::general.Notifications') }}</span>
            </a>
            <a href="{{url('user/profile/activity_log')}}"
               class="list-group-item @if(Request::segment(3)=='activity_log') active @endif">
                <i class="fas fa-database"></i>
                <span>{{ __('user::general.Activity Logs') }}</span>
            </a>
            <a href="{{url('user/profile/two_factor')}}"
               class="list-group-item @if(Request::segment(3)=='two_factor') active @endif">
                <i class="fas fa-lock"></i>
                <span>{{ __('user::general.Two Factor Authentication') }}</span>
            </a>
            <a href="{{url('user/profile/note')}}"
               class="list-group-item @if(Request::segment(3)=='note') active @endif">
                <i class="fas fa-bookmark"></i>
                <span>{{ __('user::general.Notes') }}</span>
            </a>
            <a href="{{url('user/profile/api')}}" class="list-group-item @if(Request::segment(3)=='api') active @endif">
                <i class="fas fa-code"></i>
                <span>{{ __('user::general.API Keys') }}</span>
            </a>
        </div>
    </div>
    <!-- /.card-body -->
</div>
