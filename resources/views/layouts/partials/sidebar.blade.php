<nav class="pc-sidebar">
    <div class="navbar-wrapper">
        <div class="navbar-content">
            <div class="card pc-user-card">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-shrink-0">
                            <img src="{{ asset('assets/images/user/avatar-1.jpg') }}" alt="user-image"
                                class="user-avtar wid-45 rounded-circle">
                        </div>
                        <div class="flex-grow-1 ms-3 me-2">
                            <h6 class="mb-0">{{ auth()->user()->nama_lengkap }}</h6>
                            <small>{{ auth()->user()->position->name }}</small>
                        </div>
                        <a class="btn btn-icon btn-link-secondary avtar" data-bs-toggle="collapse"
                            href="#pc_sidebar_userlink">
                            <svg class="pc-icon">
                                <use xlink:href="#custom-sort-outline"></use>
                            </svg>
                        </a>
                    </div>
                    <div class="collapse pc-user-links" id="pc_sidebar_userlink">
                        <div class="pt-3">
                            <a href="#!"><i class="ti ti-power"></i> <span>Logout</span></a>
                        </div>
                    </div>
                </div>
            </div>
            <ul class="pc-navbar">
                @if (auth()->user()->role_id == 1)
                    <li class="pc-item">
                        <a href="/user" class="pc-link">
                            <span class="pc-micon">
                                <svg class="pc-icon">
                                    <use xlink:href="#custom-user"></use>
                                </svg>
                            </span>
                            <span class="pc-mtext">User</span>
                        </a>
                    </li>
                    {{-- <li class="nav-item">
                        <a href="/user" class="nav-link {{ $active === 'user' ? 'active' : '' }}"
                            style="{{ $active === 'user' ? 'background-color: #917FB3; color: white;' : '' }}">
                            <i class="nav-icon fas fa-users"></i>
                            <p>User</p>
                        </a>
                    </li> --}}
                @endif
                <li class="pc-item">
                    <a href="/employee_reviews" class="pc-link">
                        <span class="pc-micon">
                            <svg class="pc-icon">
                                <use xlink:href="#custom-note-1"></use>
                            </svg>
                        </span>
                        <span class="pc-mtext">Employee Reviews</span>
                    </a>
                </li>
            </ul>
        </div>
    </div>
</nav>
