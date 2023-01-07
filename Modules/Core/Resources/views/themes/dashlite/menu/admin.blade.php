<aside class="main-sidebar">
    <!-- sidebar: style can be found in sidebar.less -->
    <section class="sidebar">
        <!-- Sidebar user panel -->
        <div class="user-panel">
            <div class="pull-left image">
                <i class="fa fa-user"></i>
            </div>
            <div class="pull-left info">
                <p>{{Auth::user()->first_name}} {{Auth::user()->last_name}}</p>
                <a href="#"><i class="fa fa-circle text-success"></i> Online</a>
            </div>
        </div>
        <!-- search form -->
        <form action="#" method="get" class="sidebar-form">
            <div class="input-group">
                <input type="text" name="q" class="form-control" placeholder="Search...">
                <span class="input-group-btn">
                <button type="submit" name="search" id="search-btn" class="btn btn-flat"><i class="fa fa-search"></i>
                </button>
              </span>
            </div>
        </form>
        <!-- /.search form -->
        <!-- sidebar menu: : style can be found in sidebar.less -->
        <ul class="sidebar-menu" data-widget="tree">
            @foreach(\Modules\Core\Entities\Menu::with('children')->where('is_parent',1)->orderBy('menu_order','asc')->get() as $parent)
                @if($parent->children->count()==0)
                    @if(!empty($parent->permissions))
                        @can($parent->permissions)
                            <li class=" @if(Request::is($parent->url)) active @endif"><a href="{{url($parent->url)}}"><i class="{{$parent->icon}}"></i>
                                    <span>{{$parent->name}}</span></a>
                            </li>
                        @endcan
                    @else
                        <li class=" @if(Request::is($parent->url)) active @endif"><a href="{{url($parent->url)}}"><i class="{{$parent->icon}}"></i>
                                <span>{{$parent->name}}</span></a>
                        </li>
                    @endif
                @else
                    @if(!empty($parent->permissions))
                        @can($parent->permissions)
                            <li class="treeview @if(Request::is($parent->url.'*')) active @endif">
                                <a href="{{url($parent->url)}}">
                                    <i class="{{$parent->icon}}"></i> <span>{{$parent->name}}</span>
                                    <span class="pull-right-container"><i
                                                class="fa fa-angle-left pull-right"></i></span>
                                </a>
                                <ul class="treeview-menu">
                                    @foreach($parent->children as $child)
                                        @if(!empty($child->permissions))
                                            @can($child->permissions)
                                                <li class=" @if(Request::is($child->url)) active @endif"> <a href="{{url($child->url)}}"><i
                                                                class="{{$child->icon}}"></i> {{$child->name}}</a>
                                                </li>
                                            @endcan
                                        @else
                                            <li class=" @if(Request::is($child->url)) active @endif"><a href="{{url($child->url)}}"><i
                                                            class="{{$child->icon}}"></i> {{$child->name}}
                                                </a>
                                            </li>
                                        @endif
                                    @endforeach
                                </ul>
                            </li>
                        @endcan
                    @else
                        <li class="treeview @if(Request::is($parent->url.'*')) active @endif">
                            <a href="{{url($parent->url)}}">
                                <i class="{{$parent->icon}}"></i> <span>{{$parent->name}}</span>
                                <span class="pull-right-container"><i
                                            class="fa fa-angle-left pull-right"></i></span>
                            </a>
                            <ul class="treeview-menu">
                                @foreach($parent->children as $child)
                                    @if(!empty($child->permissions))
                                        @can($child->permissions)
                                            <li class=" @if(Request::is($child->url)) active @endif"><a href="{{url($child->url)}}"><i
                                                            class="{{$child->icon}}"></i> {{$child->name}}</a>
                                            </li>
                                        @endcan
                                    @else
                                        <li class=" @if(Request::is($child->url)) active @endif"><a href="{{url($child->url)}}"><i
                                                        class="{{$child->icon}}"></i> {{$child->name}}
                                            </a>
                                        </li>
                                    @endif
                                @endforeach
                            </ul>
                        </li>
                    @endif
                @endif
            @endforeach
        </ul>
    </section>
    <!-- /.sidebar -->
</aside>
