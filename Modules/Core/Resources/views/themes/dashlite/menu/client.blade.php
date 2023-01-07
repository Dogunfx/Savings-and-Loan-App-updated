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
            <li><a href="{{url('portal/dashboard')}}"><i class="fa fa-dashboard"></i>
                    <span>{{trans_choice('dashboard::general.dashboard', 1)}}</span></a></li>
            <li class="treeview">
                <a href="#">
                    <i class="fa fa-money"></i> <span>{{trans_choice('loan::general.loan', 2)}}</span>
                    <span class="pull-right-container">
              <i class="fa fa-angle-left pull-right"></i>
            </span>
                </a>
                <ul class="treeview-menu">
                    <li><a href="{{url('portal/loan')}}"><i
                                    class="fa fa-circle-o"></i> {{trans_choice('core::general.view', 1) . ' ' . trans_choice('loan::general.loan', 2)}}
                        </a></li>
                    <li><a href="{{url('portal/loan/application')}}"><i
                                    class="fa fa-circle-o"></i> {{trans_choice('core::general.view', 1) . ' ' . trans_choice('loan::general.application', 2)}}
                        </a></li>
                    <li><a href="{{url('portal/loan/application/create')}}"><i
                                    class="fa fa-circle-o"></i> {{trans_choice('core::general.create', 1) . ' ' . trans_choice('loan::general.application', 1)}}
                        </a></li>
                </ul>
            </li>
            <li class="treeview">
                <a href="#">
                    <i class="fa fa-bank"></i> <span>{{trans_choice('savings::general.savings', 2)}}</span>
                    <span class="pull-right-container">
              <i class="fa fa-angle-left pull-right"></i>
            </span>
                </a>
                <ul class="treeview-menu">
                    <li><a href="{{url('portal/savings')}}"><i
                                    class="fa fa-circle-o"></i> {{trans_choice('core::general.view', 1) . ' ' . trans_choice('savings::general.savings', 2)}}
                        </a></li>
                </ul>
            </li>
            <li><a href="{{url('portal/client')}}"><i class="fa fa-user"></i>
                    <span>{{trans_choice('client::general.client', 1) . ' ' . trans_choice('core::general.profile', 1)}}</span></a>
            </li>
        </ul>

    </section>
    <!-- /.sidebar -->
</aside>
