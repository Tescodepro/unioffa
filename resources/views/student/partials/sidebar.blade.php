<div class="sidebar" id="sidebar">
    <div class="sidebar-inner slimscroll">
        <div id="sidebar-menu" class="sidebar-menu">
            <ul>
                <li>
                    <a href="{{ route('students.dashboard') }}" 
                       class="d-flex align-items-center border bg-white rounded p-2 mb-4">
                        <img src="{{ asset('assets/img/logo/logo_white.svg') }}" 
                             class="avatar avatar-md img-fluid rounded" alt="Profile">
                        <span class="text-dark ms-2 fw-normal">University of Offa</span>
                    </a>
                </li>
            </ul>

            <ul>
                <!-- MAIN -->
                <li>
                    <h6 class="submenu-hdr"><span>Main</span></h6>
                    <ul>
                        <li>
                            <a href="{{ route('students.dashboard') }}" 
                               class="{{ request()->routeIs('students.dashboard') ? 'active' : '' }}">
                                <i class="ti ti-layout-dashboard"></i>
                                <span>Dashboard</span>
                            </a>
                        </li>
                    </ul>
                </li>

                <!-- ACADEMICS -->
                <li class="{{ request()->is('students/course*') || request()->is('students/results*') ? 'open' : '' }}">
                    <h6 class="submenu-hdr"><span>Academics</span></h6>
                    <ul>
                        <li>
                            <a href="{{ route('students.course.registration') }}" 
                               class="{{ request()->routeIs('students.course.registration') ? 'active' : '' }}">
                                <i class="ti ti-book"></i>
                                <span>Course Registration</span>
                            </a>
                        </li>
                        <li>
                            <a href="#" class="{{ request()->is('students/results*') ? 'active' : '' }}">
                                <i class="ti ti-file-text"></i>
                                <span>Check Results</span>
                            </a>
                        </li>
                    </ul>
                </li>

                <!-- FINANCIAL SERVICES -->
                <li class="{{ request()->is('students/load_payment*') || request()->is('students/payment/history*') ? 'open' : '' }}">
                    <h6 class="submenu-hdr"><span>Financial Services</span></h6>
                    <ul>
                        <li>
                            <a href="{{ route('students.load_payment') }}" 
                               class="{{ request()->routeIs('students.load_payment') ? 'active' : '' }}">
                                <i class="ti ti-wallet"></i>
                                <span>School Fees</span>
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('students.payment.history') }}" 
                               class="{{ request()->routeIs('students.payment.history') ? 'active' : '' }}">
                                <i class="ti ti-history"></i>
                                <span>Payment History</span>
                            </a>
                        </li>
                    </ul>
                </li>

                <!-- HOSTEL SERVICES -->
                <li class="{{ request()->is('students/hostel*') ? 'open' : '' }}">
                    <h6 class="submenu-hdr"><span>Hostel Services</span></h6>
                    <ul>
                        <li><a href="{{ route('students.hostel.index')  }}" class="{{ request()->is('students/hostel/apply') ? 'active' : '' }}"><i class="ti ti-building"></i><span>Apply for Hostel</span></a></li>
                        {{-- <li><a href="#" class="{{ request()->is('students/hostel/status') ? 'active' : '' }}"><i class="ti ti-info-circle"></i><span>Hostel Status</span></a></li> --}}
                    </ul>
                </li>

                <!-- OTHER -->
                <li class="{{ request()->routeIs('students.admission.letter') ? 'open' : '' }}">
                    <h6 class="submenu-hdr"><span>Other</span></h6>
                    <ul>
                        <li>
                            <a href="{{ route('students.admission.letter') }}" 
                               class="{{ request()->routeIs('students.admission.letter') ? 'active' : '' }}">
                                <i class="ti ti-user"></i>
                                <span>Admission Letter</span>
                            </a>
                        </li>
                    </ul>
                </li>

                <!-- PROFILE -->
                <li class="{{ request()->routeIs('students.profile') ? 'open' : '' }}">
                    <h6 class="submenu-hdr"><span>Profile</span></h6>
                    <ul>
                        <li>
                            <a href="{{ route('students.profile') }}" 
                               class="{{ request()->routeIs('students.profile') ? 'active' : '' }}">
                                <i class="ti ti-user"></i>
                                <span>My Profile</span>
                            </a>
                        </li>
                    </ul>
                </li>

                <!-- ACCOUNT -->
                <li>
                    <h6 class="submenu-hdr"><span>Account</span></h6>
                    <ul>
                        <li>
                            <a href="{{ route('students.logout') }}">
                                <i class="ti ti-logout"></i>
                                <span>Logout</span>
                            </a>
                        </li>
                    </ul>
                </li>
            </ul>
        </div>
    </div>
</div>
