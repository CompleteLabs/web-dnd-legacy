 <!-- Main Sidebar Container -->
 <aside class="main-sidebar main-sidebar-custom sidebar-dark-purple elevation-4" style="background-color: #2A2F4F;">
     <!-- Brand Logo -->
     <a href="#" class="brand-link" style="background-color: #2A2F4F;">
         <img src="{{ asset('dnd-purple.png') }}" class="brand-image" style="opacity: .8">
         <span class="brand-text font-weight-light" style="color: white;"><strong>Do and Done</strong></span>
     </a>

     <!-- Sidebar -->
     <div class="sidebar">
         <!-- Sidebar user (optional) -->
         <div class="user-panel mt-3 pb-3 mb-3 d-flex">
             <div class="image">
                 <img src="{{ asset('memoji.jpg') }}" class="img-circle elevation-2" alt="">
             </div>
             <div class="info">
                 <a href="/dashboard" class="d-block"><strong>{{ auth()->user()->nama_lengkap }}</strong></a>
             </div>
         </div>
         <!-- Sidebar Menu -->
         <nav class="mt-2">
             <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu"
                 data-accordion="false">
                 <!-- Add icons to the links using the .nav-icon class
               with font-awesome or any other icon font library -->
                 @if (auth()->user()->role_id == 1)
                     <li class="nav-item">
                         <a href="/user" class="nav-link {{ $active === 'user' ? 'active' : '' }}"
                             style="{{ $active === 'user' ? 'background-color: #917FB3; color: white;' : '' }}">
                             <i class="nav-icon fas fa-users"></i>
                             <p>User</p>
                         </a>
                     </li>
                 @endif
                 <li class="nav-item">
                     <a href='/dash-monthly' class="nav-link {{ $active === 'kpi-dashboard' ? 'active' : '' }}"
                         style="{{ $active === 'kpi-dashboard' ? 'background-color: #917FB3; color: white;' : '' }}">
                         <i class="nav-icon fas fa-tachometer-alt"></i>
                         <p>KPI Dashboard</p>
                     </a>
                 </li>
                 @if (auth()->user()->role_id != 2 && auth()->user()->role_id != 3)
                     <li class="nav-item">
                         <a href='/kpi' class="nav-link {{ $active === 'kpi' ? 'active' : '' }}"
                             style="{{ $active === 'kpi' ? 'background-color: #917FB3; color: white;' : '' }}">
                             <i class="nav-icon fa fa-list"></i>
                             <p>KPI</p>
                         </a>
                     </li>
                 @endif
                 @if (auth()->user()->role_id != 2 && auth()->user()->role_id != 3)
                     <li class="nav-item">
                         <a href='/teams' class="nav-link {{ $active === 'my_team' ? 'active' : '' }}"
                             style="{{ $active === 'my_team' ? 'background-color: #917FB3; color: white;' : '' }}">
                             <i class="nav-icon fa fa-briefcase"></i>
                             <p>My Team</p>
                         </a>
                     </li>
                      <li class="nav-item">
                    <a href="/attendance" class="nav-link {{ $active === 'attendance' ? 'active' : '' }}"
                        style="{{ $active === 'attendance' ? 'background-color: #917FB3; color: white;' : '' }}">
                        <i class="nav-icon fas fa-clock"></i>
                        <p>Attendance</p>
                    </a>
                </li>
                 @endif

                @if (auth()->user()->role_id == 1)
                 <li class="nav-item">
                     <a href="#" class="nav-link">
                         <i class="nav-icon fas fa-calendar-check"></i>
                         <p>
                             Data Management
                             <i class="right fas fa-angle-left"></i>
                         </p>
                     </a>
                     <ul class="nav nav-treeview">
                         <!-- Attendance Menu -->
                         <li class="nav-item">
                             <a href="/attendance" class="nav-link {{ $active === 'attendance' ? 'active' : '' }}"
                                 style="{{ $active === 'attendance' ? 'background-color: #917FB3; color: white;' : '' }}">
                                 <i class="nav-icon fas fa-clock"></i>
                                 <p>Attendance</p>
                             </a>
                         </li>
                         <!-- Employee Reviews Menu -->
                         <li class="nav-item">
                             <a href="/employee_reviews"
                                 class="nav-link {{ $active === 'employee_reviews' ? 'active' : '' }}"
                                 style="{{ $active === 'employee_reviews' ? 'background-color: #917FB3; color: white;' : '' }}">
                                 <i class="nav-icon fas fa-star"></i>
                                 <p>Employee Reviews</p>
                             </a>
                         </li>
                     </ul>
                 </li>
                 @endauth

                 {{-- <li class="nav-item">
                     <a href="#" class="nav-link">
                         <i class="nav-icon fas fa-calendar-check"></i>
                         <p>
                             {{ auth()->user()->role_id == 1 ? 'Todo List' : 'My Todo' }}
                             <i class="right fas fa-angle-left"></i>
                         </p>
                     </a>
                     <ul class="nav nav-treeview">
                         <li class="nav-item">
                             <a href={{ auth()->user()->role_id == 1 ? '/admin/daily' : '/daily' }}
                                 class="nav-link {{ $active === 'daily' ? 'active' : '' }}"
                                 style="{{ $active === 'daily' ? 'background-color: #917FB3; color: white;' : '' }}">
                                 <img src="{{ asset('assets') }}/daily-white.png" width='25' height='25'
                                     class='mr-1'>
                                 <p>Daily</p>
                             </a>
                         </li>
                     </ul>
                     <ul class="nav nav-treeview">
                         <li class="nav-item">
                             <a href={{ auth()->user()->role_id == 1 ? '/admin/weekly' : '/weekly' }}
                                 class="nav-link {{ $active === 'weekly' ? 'active' : '' }}"
                                 style="{{ $active === 'weekly' ? 'background-color: #917FB3; color: white;' : '' }}">
                                 <img src="{{ asset('assets') }}/week-white.png" width='25' height='25'
                                     class='mr-1'>
                                 <p>Weekly</p>
                             </a>
                         </li>
                     </ul>
                     @if (auth()->user()->role_id == 1 || (auth()->user()->mn || auth()->user()->mr))
                         <ul class="nav nav-treeview">
                             <li class="nav-item">
                                 <a href={{ auth()->user()->role_id == 1 ? '/admin/monthly' : '/monthly' }}
                                     class="nav-link {{ $active === 'monthly' ? 'active' : '' }}"
                                     style="{{ $active === 'monthly' ? 'background-color: #917FB3; color: white;' : '' }}">
                                     <img src="{{ asset('assets') }}/monthly-white.png" width='25' height='25'
                                         class='mr-1'>
                                     <p>Monthly</p>
                                 </a>
                             </li>
                         </ul>
                     @endif
                     @if (auth()->user()->role_id == 1)
                         <ul class="nav nav-treeview">
                             <li class="nav-item">
                                 <a href='/admin/report' class="nav-link {{ $active === 'report' ? 'active' : '' }}"
                                     style="{{ $active === 'report' ? 'background-color: #917FB3; color: white;' : '' }}">
                                     <i class="nav-icon fas fa-file-invoice"></i>
                                     <p>Report</p>
                                 </a>
                             </li>
                         </ul>
                         <ul class="nav nav-treeview">
                             <li class="nav-item">
                                 <a href='/admin/overopen'
                                     class="nav-link {{ $active === 'overopen' ? 'active' : '' }}"
                                     style="{{ $active === 'overopen' ? 'background-color: #917FB3; color: white;' : '' }}">
                                     <i class="nav-icon fas fa-file-invoice"></i>
                                     <p>Cut Point</p>
                                 </a>
                             </li>
                         </ul>
                         <ul class="nav nav-treeview">
                             <li class="nav-item">
                                 <a href='/req/' class="nav-link {{ $active === 'req' ? 'active' : '' }}"
                                     style="{{ $active === 'req' ? 'background-color: #917FB3; color: white;' : '' }}">
                                     <i class="nav-icon fas fa-check"></i>
                                     <p>Approval</p>
                                 </a>
                             </li>
                         </ul>
                     @endif
                     @if (auth()->user()->role_id != 1)
                         <ul class="nav nav-treeview">
                             <li class="nav-item">
                                 <a href='/result/' class="nav-link {{ $active === 'result' ? 'active' : '' }}"
                                     style="{{ $active === 'result' ? 'background-color: #917FB3; color: white;' : '' }}">
                                     <i class="nav-icon fas fa-poll"></i>
                                     <p>Result</p>
                                 </a>
                             </li>
                         </ul>
                         <ul class="nav nav-treeview">
                             <li class="nav-item">
                                 <a href='/request/' class="nav-link {{ $active === 'request' ? 'active' : '' }}"
                                     style="{{ $active === 'request' ? 'background-color: #917FB3; color: white;' : '' }}">
                                     <i class="nav-icon fas fa-undo"></i>
                                     <p>Change Task</p>
                                 </a>
                             </li>
                         </ul>
                     @endif
                 </li> --}}
                 @if (auth()->user()->role_id == 1)
                     <li class="nav-item">
                         <a href="#" class="nav-link">
                             <i class="nav-icon fas fa-edit"></i>
                             <p>
                                 KPI Settings
                                 <i class="right fas fa-angle-left"></i>
                             </p>
                         </a>
                         <ul class="nav nav-treeview">
                             <li class="nav-item">
                                 <a href='/kpicategory'
                                     class="nav-link {{ $active === 'kpi-category' ? 'active' : '' }}"
                                     style="{{ $active === 'kpi-category' ? 'background-color: #917FB3; color: white;' : '' }}">
                                     <i class="nav-icon fas fa-table"></i>
                                     <p>KPI Category</p>
                                 </a>
                             </li>
                             <li class="nav-item">
                                 <a href='/kpidescription'
                                     class="nav-link {{ $active === 'kpi-description' ? 'active' : '' }}"
                                     style="{{ $active === 'kpi-description' ? 'background-color: #917FB3; color: white;' : '' }}">
                                     <i class="nav-icon fas fa-book"></i>
                                     <p>KPI Description</p>
                                 </a>
                             </li>
                             <li class="nav-item">
                                 <a href='/kpitype' class="nav-link {{ $active === 'kpi-type' ? 'active' : '' }}"
                                     style="{{ $active === 'kpi-type' ? 'background-color: #917FB3; color: white;' : '' }}">
                                     <i class="nav-icon fas fa-calendar-alt"></i>
                                     <p>KPI Type</p>
                                 </a>
                             </li>
                             <li class="nav-item">
                                 <a href='/position' class="nav-link {{ $active === 'position' ? 'active' : '' }}"
                                     style="{{ $active === 'position' ? 'background-color: #917FB3; color: white;' : '' }}">
                                     <i class="nav-icon fa fa-briefcase"></i>
                                     <p>Job Position</p>
                                 </a>
                             </li>
                         </ul>
                 @endif
                 @if (auth()->user()->role_id == 1)
                     <li class="nav-item">
                         <a href="#" class="nav-link">
                             <i class="nav-icon fas fa-cogs"></i>
                             <p>
                                 Settings
                                 <i class="right fas fa-angle-left"></i>
                             </p>
                         </a>
                         <ul class="nav nav-treeview">
                             <li class="nav-item">
                                 <a href="/setting/role" class="nav-link"
                                     style="{{ $active === 'setting-role' ? 'background-color: #917FB3; color: white;' : '' }}">
                                     <i class="fas fa-user-cog nav-icon"></i>
                                     <p>Role</p>
                                 </a>
                             </li>
                             <li class="nav-item">
                                 <a href="/setting/area" class="nav-link"
                                     style="{{ $active === 'setting-area' ? 'background-color: #917FB3; color: white;' : '' }}">
                                     <i class="fas fa-map nav-icon"></i>
                                     <p>Area</p>
                                 </a>
                             </li>
                             <li class="nav-item">
                                 <a href="/setting/divisi" class="nav-link"
                                     style="{{ $active === 'setting-divisi' ? 'background-color: #917FB3; color: white;' : '' }}">
                                     <i class="fas fa-briefcase nav-icon"></i>
                                     <p>Divisi</p>
                                 </a>
                             </li>
                             <li class="nav-item">
                                 <a href="/setting/taskcategory" class="nav-link"
                                     style="{{ $active === 'task-category' ? 'background-color: #917FB3; color: white;' : '' }}">
                                     <i class="fas fa-list nav-icon"></i>
                                     <p>Task Category</p>
                                 </a>
                             </li>
                             <li class="nav-item">
                                 <a href="/setting/taskstatus" class="nav-link"
                                     style="{{ $active === 'task-status' ? 'background-color: #917FB3; color: white;' : '' }}">
                                     <i class="fas fa-ellipsis-h nav-icon"></i>
                                     <p>Task Status</p>
                                 </a>
                             </li>
                         </ul>
                     </li>
                 @else
                     {{-- <li class="nav-item {{ $active === 'teams-daily' ? 'menu-open' : 'menu' }}">
                         <a href="#" class="nav-link">
                             <i class="nav-icon fas fa-users"></i>
                             <p>
                                 My Team's Todo
                                 <i class="right fas fa-angle-left"></i>
                             </p>
                         </a>
                         <ul class="nav nav-treeview">
                             <li class="nav-item">
                                 <a href="/teams/daily"
                                     class="nav-link {{ $active === 'teams-daily' ? 'active' : '' }}"
                                     style="{{ $active === 'teams-daily' ? 'background-color: #917FB3; color: white;' : '' }}">
                                     <img src="{{ asset('assets') }}/daily-white.png" width='25' height='25'
                                         class='mr-1'>
                                     <p>Team's Daily</p>
                                 </a>
                             </li>
                         </ul>
                         <ul class="nav nav-treeview">
                             <li class="nav-item">
                                 <a href="/teams/weekly"
                                     class="nav-link {{ $active === 'teams-weekly' ? 'active' : '' }}"
                                     style="{{ $active === 'teams-weekly' ? 'background-color: #917FB3; color: white;' : '' }}">
                                     <img src="{{ asset('assets') }}/week-white.png" width='25' height='25'
                                         class='mr-1'>
                                     <p>Team's Weekly</p>
                                 </a>
                             </li>
                         </ul>
                     </li> --}}
                 @endif
             </ul>
         </nav>
         <!-- /.sidebar-menu -->
     </div>
     <!-- /.sidebar -->

     <div class="sidebar-custom">
         <form action="/logout" method="POST">
             @csrf
             <button type="submit" class="btn btn-link" style="color: #917FB3;"><i class="fas fa-sign-out-alt"></i>
                 Log Out</button>
         </form>
     </div>
     <!-- /.sidebar-custom -->
 </aside>
