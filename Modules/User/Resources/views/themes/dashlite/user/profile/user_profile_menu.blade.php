<div class="card card-primary card-outline">
    <div class="card-body box-profile">
        <div class="text-center">
            @if(!empty($user->photo))
                <img src="{{asset('storage/uploads/'.$user->photo)}}"
                     class="profile-user-img img-fluid img-circle"
                     alt="Profile"/>
            @else
                <em class="icon ni ni-user-alt"></em>
            @endif
        </div>

        <h3 class="profile-username text-center">{{ $user->first_name }} {{ $user->last_name }}</h3>

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
        <ul class="link-list-menu">
            <li>
                <a class="@if(Request::segment(3)=='') active @endif" href="{{url('user/profile')}}">
                    <em class="icon ni ni-user-fill-c"></em>
                    <span>{{ __('user::general.Account Information') }}</span>
                </a>
            </li>
            <li>
                <a href="{{url('user/profile/change_password')}}"
                   class="@if(Request::segment(3)=='change_password') active @endif">
                    <em class="icon ni ni-shield-star-fill"></em>
                    <span> {{ __('user::general.Change Password') }}</span>
                </a>
            </li>
            <li>
                <a href="{{url('user/profile/notification')}}"
                   class="@if(Request::segment(3)=='notification') active @endif">
                    <em class="icon ni ni-bell-fill"></em>
                    <span>{{ __('user::general.Notifications') }}</span>
                </a>
            </li>
            <li>
                <a href="{{url('user/profile/activity_log')}}"
                   class="@if(Request::segment(3)=='activity_log') active @endif">
                    <em class="icon ni ni-activity-round-fill"></em>
                    <span>{{ __('user::general.Activity Logs') }}</span>
                </a>
            </li>
            <li>
                <a href="{{url('user/profile/two_factor')}}" class="@if(Request::segment(3)=='two_factor') active @endif">
                    <em class="icon ni ni-lock-alt-fill"></em>
                    <span>{{ __('user::general.Two Factor Authentication') }}</span>
                </a>
            </li>
            <li>
                <a href="{{url('user/profile/note')}}" class="@if(Request::segment(3)=='note') active @endif">
                    <em class="icon ni ni-notes"></em>
                    <span>{{ __('user::general.Notes') }}</span>
                </a>
            </li>
            <li>
                <a href="{{url('user/profile/api')}}" class="@if(Request::segment(3)=='api') active @endif">
                    <em class="icon ni ni-code"></em>
                    <span>{{ __('user::general.API Keys') }}</span>
                </a>
            </li>
        </ul>
    </div>
    <!-- /.card-body -->
</div>
