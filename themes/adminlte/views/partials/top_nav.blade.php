<nav class="main-header navbar navbar-expand navbar-white navbar-light">
    <!-- Left navbar links -->
    <ul class="navbar-nav">
        <li class="nav-item">
            <a class="nav-link" data-widget="pushmenu" href="#"><i class="fas fa-bars"></i></a>
        </li>
    </ul>

    <!-- SEARCH FORM -->
    <form class="form-inline ml-3">
        <div class="input-group input-group-sm">
            <input class="form-control form-control-navbar" type="search" placeholder="Search" aria-label="Search">
            <div class="input-group-append">
                <button class="btn btn-navbar" type="submit">
                    <i class="fas fa-search"></i>
                </button>
            </div>
        </div>
    </form>

    <!-- Right navbar links -->
    <ul class="navbar-nav ml-auto">
        <!-- Notifications Dropdown Menu -->
        <li class="nav-item dropdown">
            <a class="nav-link" data-toggle="dropdown" href="#">
                <i class="far fa-bell"></i>
                <span class="badge badge-warning navbar-badge">{{Auth::user()->unreadNotifications()->count()}}</span>
            </a>
            <div class="dropdown-menu dropdown-menu-lg dropdown-menu-right">
                <span class="dropdown-item dropdown-header">{{Auth::user()->unreadNotifications()->count()}} {{trans_choice('core::general.notification',2)}}</span>
                @foreach(Auth::user()->unreadNotifications as $notification)
                    <div class="dropdown-divider"></div>
                    <a href="@if(!empty($notification->data['link'])) {{url($notification->data['link'])}} @else {{url('user/profile/notification/'.$notification->id.'/show')}} @endif"
                       class="dropdown-item">
                        {{$notification->data['message']}}
                        <span class="float-right text-muted text-sm">{{$notification->created_at->diffForHumans()}}</span>
                    </a>
                @endforeach
                <div class="dropdown-divider"></div>
                <a href="{{url('user/profile/notification')}}"
                   class="dropdown-item dropdown-footer">{{__('core::general.View All')}}</a>
            </div>
        </li>
        <li class="nav-item dropdown user-menu">
            <a href="#" class="nav-link dropdown-toggle" data-toggle="dropdown" aria-expanded="true">
                @if(!empty(Auth::user()->photo))
                    <img class="user-image img-circle elevation-2"
                         src="{{asset('storage/uploads/'.Auth::user()->photo)}}"
                         alt="User Image">
                @else
                    <img class="user-image img-circle elevation-2"
                         src="{{asset('themes/adminlte/img/user.png')}}"
                         alt="User profile picture">
                @endif
                <span class="d-none d-md-inline">{{Auth::user()->full_name}}</span>
            </a>
            <ul class="dropdown-menu dropdown-menu-lg dropdown-menu-right">
                <!-- User image -->
                <li class="user-header bg-primary">
                    @if(!empty(Auth::user()->photo))
                        <img class="user-image img-circle elevation-2"
                             src="{{asset('storage/uploads/'.Auth::user()->photo)}}"
                             alt="User Image">
                    @else
                        <img class="user-image img-circle elevation-2"
                             src="{{asset('themes/adminlte/img/user.png')}}"
                             alt="User profile picture">
                    @endif
                    <p>
                        {{Auth::user()->full_name}}
                        <small></small>
                    </p>
                </li>
                <!-- Menu Footer-->
                <li class="user-footer">
                    <a href="{{url('user/profile')}}"
                       class="btn btn-default btn-flat">
                        {{trans_choice('core::general.profile',1)}}
                    </a>
                    <a href="#" onclick="logout()" class="btn btn-default btn-flat float-right">
                        {{trans_choice('core::general.logout',1)}}</a>
                    <form action="{{url('logout')}}" method="post" id="logout_form">
                        {{csrf_field()}}
                    </form>
                    <script>function logout() {
                            $("#logout_form").submit();
                        }</script>
                </li>
            </ul>
        </li>
    </ul>
</nav>
