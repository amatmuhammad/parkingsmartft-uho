 <!-- Topbar header - style you can find in pages.scss -->
        <!-- ============================================================== -->
        <header class="topbar" data-navbarbg="skin6">
            <nav class="navbar top-navbar navbar-expand-md">
                <div class="navbar-header" data-logobg="skin6">
                    <!-- This is for the sidebar toggle which is visible on mobile only -->
                    <a class="nav-toggler waves-effect waves-light d-block d-md-none" href="javascript:void(0)"><i
                            class="ti-menu ti-close"></i></a>
                    <!-- ============================================================== -->
                    <!-- Logo -->
                    <!-- ============================================================== -->
                    <div class="navbar-brand">
                        <!-- Logo icon -->
                        <a href="#">
                            <b class="logo-icon">
                                <!-- Dark Logo icon -->
                                <img src="{{ asset('/public/assets/assets/images/SMART1.png') }}" alt="homepage" class="dark-logo pt-2" style="width: 150px; heigth:70px;" />
                                <!-- Light Logo icon -->
                                <img src="{{ asset('/public/assets/assets/images/SMART1.png') }}" alt="homepage" class="light-logo" style="width: 150px; heigth:70px;"/>
                            </b>
                                <!--End Logo icon -->
                                <!-- Logo text -->
                                {{-- <span class="logo-text">
                                    <!-- dark Logo text -->
                                    <img src="{{ asset('assets/assets/images/logo-text.png') }}" alt="homepage" class="dark-logo" />
                                    <!-- Light Logo text -->
                                    <img src="{{ asset('assets/assets/images/logo-light-text.png') }}" class="light-logo" alt="homepage" />
                                </span> --}}
                        </a>
                    </div>
                    <!-- ============================================================== -->
                    <!-- End Logo -->
                    <!-- ============================================================== -->
                    <!-- ============================================================== -->
                    <!-- Toggle which is visible on mobile only -->
                    <!-- ============================================================== -->
                    <a class="topbartoggler d-block d-md-none waves-effect waves-light" href="javascript:void(0)"
                        data-toggle="collapse" data-target="#navbarSupportedContent"
                        aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
                        <i class="ti-more"></i>
                    </a>
                </div>
                
                <!-- End Logo -->
                
                <div class="navbar-collapse collapse" id="navbarSupportedContent">
                    <!-- toggle and nav items -->
                    <!-- Right side toggle and nav items -->
                    <ul class="navbar-nav float-right ml-auto ml-3 pl-1">
                        
                        <!-- User profile and search -->
                     
                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle" href="javascript:void(0)" data-toggle="dropdown"
                                aria-haspopup="true" aria-expanded="false">
                                <img src="{{ asset('assets/assets/images/users/profile-pic.jpg') }}" alt="user" class="rounded-circle"
                                    width="40">
                                <span class="ml-2 d-none d-lg-inline-block"><span>Hallo,</span> <span
                                        class="text-dark">{{ Auth::user()->name ?? 'Guest' }}</span> <i data-feather="chevron-down"
                                        class="svg-icon"></i></span>
                            </a>
                            <div class="dropdown-menu dropdown-menu-right user-dd animated flipInY">
                               
                                <a class="dropdown-item" href="#" onclick="event.preventDefault(); document.getElementById('logout-form').submit();" 
                            aria-expanded="false"><i data-feather="power"
                                        class="svg-icon mr-2 ml-1"></i>
                                    Logout</a>
                            @can('isUser')
                                    <div class="dropdown-divider"></div>
                                    <div class="pl-4 p-3"><a href="{{ route('user.profile') }}" class="btn btn-sm btn-info">View
                                        Profile</a>
                                    </div>
                                @endcan
                            </div>
                        </li>
                        <!-- ============================================================== -->
                        <!-- User profile and search -->
                        <!-- ============================================================== -->
                    </ul>
                </div>
            </nav>
        </header>
        <!-- End Topbar header -->

        <!-- Left Sidebar - style you can find in sidebar.scss  -->
        <aside class="left-sidebar" data-sidebarbg="skin6">
            <!-- Sidebar scroll-->
            <div class="scroll-sidebar" data-sidebarbg="skin6">
                <!-- Sidebar navigation-->
                <nav class="sidebar-nav">
                    <ul id="sidebarnav">
                        @can('isAdmin')
                            
                            <li class="sidebar-item"> 
                                <a class="sidebar-link sidebar-link {{ Request::is('dashboard') ? 'active' : '' }}" href="{{ route('index') }}" aria-expanded="false"><i data-feather="home" class="feather-icon">
                                    </i><span class="hide-menu">Dashboard</span>
                                </a>
                            </li>
                        @endcan
                        <li class="list-divider"></li>
                        <li class="nav-small-cap"><span class="hide-menu">Feature</span></li>

                        @can('isAdmin')
                            
                        <li class="sidebar-item"> 
                            <a class="sidebar-link sidebar-link {{ Request::is('maps-slot') ? 'active' : '' }}" href="{{ route('maps-slot') }}" aria-expanded="false">
                                <i data-feather="calendar" class="feather-icon"></i>
                                <span class="hide-menu">Maps & Slot</span>
                            </a>
                        </li>
                         <li class="sidebar-item"> 
                            <a class="sidebar-link has-arrow {{ request()->is('history/*') ? 'active' : '' }}" href="javascript:void(0)" aria-expanded="false">
                                <i data-feather="file-text" class="feather-icon"></i>
                                <span class="hide-menu">History</span>
                            </a>
                            <ul aria-expanded="false" class="collapse first-level base-level-line">
                                <li class="sidebar-item">
                                    <a href="{{ route('booking') }}" 
                                    class="sidebar-link {{ request()->is('booking') ? 'active' : '' }}">
                                    <span class="hide-menu">Reserved</span>
                                </a>
                            </li>
                            <li class="sidebar-item">
                                <a href="{{ route('parked') }}" 
                                class="sidebar-link {{ request()->is('parked') ? 'active' : '' }}">
                                    <span class="hide-menu">Parking</span>
                                </a>
                            </li>
                                <li class="sidebar-item">
                                    <a href="{{ route('out') }}" 
                                    class="sidebar-link {{ request()->is('out*') ? 'active' : '' }}">
                                        <span class="hide-menu">Exit</span>
                                    </a>
                                </li>
                            </ul>
                        </li>

                        <li class="sidebar-item"> <a class="sidebar-link has-arrow {{ request()->is('manage/*') ? 'active' : '' }}" href="javascript:void(0)"
                                aria-expanded="false"><i data-feather="grid" class="feather-icon"></i><span
                                    class="hide-menu">Manage</span></a>
                            <ul aria-expanded="false" class="collapse  first-level base-level-line">
                                <li class="sidebar-item">
                                    <a href="{{ route('users') }}" class="sidebar-link {{ request()->is('users') ? 'active' : '' }}">
                                        <span class="hide-menu"> Users </span>
                                    </a>
                                </li>
                                {{-- <li class="sidebar-item">
                                    <a href="{{ route('price') }}" class="sidebar-link {{ request()->is('price') ? 'active' : '' }}">
                                        <span class="hide-menu"> Price </span>
                                    </a>
                                </li> --}}
                               
                            </ul>
                        </li>
                        @endcan


                        @can('isUser')
                            <li class="sidebar-item"> 
                                <a class="sidebar-link sidebar-link {{ Request::is('dashboard-user') ? 'active' : '' }}" href="{{ route('index.user') }}" aria-expanded="false">
                                    <i data-feather="clipboard" class="feather-icon"></i>
                                    <span class="hide-menu">Reservasi</span>
                                </a>
                            </li>
                                {{-- <li class="sidebar-item"> 
                                    <a class="sidebar-link sidebar-link {{ Request::is('dashboard-user') ? 'active' : '' }}" href="{{ route('index.user') }}" aria-expanded="false">
                                        <i data-feather="truck" class="feather-icon"></i>
                                        <span class="hide-menu">Parkir</span>
                                    </a>
                                </li> --}}
                        @endcan

                        <li class="list-divider"></li>

                        <li class="nav-small-cap">
                            <span class="hide-menu">Authetication</span>
                        </li>
                        <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display:none;">
                            @csrf
                        </form>

                        <li class="sidebar-item"> 
                            <a class="sidebar-link sidebar-link" href="#" 
                            onclick="event.preventDefault(); document.getElementById('logout-form').submit();" 
                            aria-expanded="false">
                                <i data-feather="log-out" class="feather-icon"></i>
                                <span class="hide-menu">Logout</span>
                            </a>
                        </li>

                    </ul>
                </nav>
                <!-- End Sidebar navigation -->
            </div>
            <!-- End Sidebar scroll-->
        </aside>
        <!-- End Left Sidebar - style you can find in sidebar.scss  -->