<div class="sidebar" id="sidebar">
    <div class="sidebar-inner slimscroll">
        <div id="sidebar-menu" class="sidebar-menu">
            <!-- Logo / Home -->
            <ul>
                <li>
                    <a href="{{ route('burser.dashboard') }}"
                        class="d-flex align-items-center border bg-white rounded p-2 mb-4">
                        <img src="{{ asset('assets/img/logo/logo_white.svg') }}"
                            class="avatar avatar-md img-fluid rounded" alt="Profile">
                        <span class="text-dark ms-2 fw-normal">UNIOFFA</span>
                    </a>
                </li>
            </ul>

            <ul>
                <!-- MAIN -->
                <li>
                    <h6 class="submenu-hdr"><span>Main</span></h6>
                    <ul>
                        <li>
                            @if (in_array(auth()->user()->userType->name, ['administrator', 'vice-chancellor', 'registrar']))
                                <a href="{{ route('students.dashboard') }}"
                                    class="{{ request()->routeIs('students.dashboard') ? 'active' : '' }}">
                                    <i class="ti ti-layout-dashboard"></i>
                                    <span>Dashboard</span>
                                </a>
                            @elseif(in_array(auth()->user()->userType->name, ['bursary']))
                                <a href="{{ route('burser.dashboard') }}"
                                    class="{{ request()->routeIs('burser.dashboard') ? 'active' : '' }}">
                                    <i class="ti ti-layout-dashboard"></i>
                                    <span>Dashboard</span>
                                </a>
                            @endif
                        </li>
                    </ul>
                </li>

                @if (in_array(auth()->user()->userType->name, ['bursary', 'vice-chancellor']))
                    <!-- TRANSACTIONS -->
                    <li>
                        <h6 class="submenu-hdr"><span>Transactions</span></h6>
                        <ul>
                            <li>
                                <a href="{{ route('bursary.transactions') }}"
                                    class="{{ request()->routeIs('bursary.transactions') ? 'active' : '' }}">
                                    <i class="ti ti-credit-card"></i>
                                    <span>All Transactions</span>
                                </a>
                            </li>
                            <li>
                                <a href="{{ route('bursary.verify.form') }}"
                                    class="{{ request()->routeIs('bursary.verify.form') ? 'active' : '' }}">
                                    <i class="ti ti-check"></i>
                                    <span>Verify Payment</span>
                                </a>
                            </li>
                        </ul>
                    </li>
                @endif

                @if (in_array(auth()->user()->userType->name, ['bursary']))
                    <!-- REPORTS -->
                    {{-- <li>
                        <h6 class="submenu-hdr"><span>Reports</span></h6>
                        <ul>
                            <li>
                                <a href="{{ route('bursary.reports.faculty') }}">
                                    <i class="ti ti-file-analytics"></i>
                                    <span>By Faculty</span>
                                </a>
                            </li>
                            <li>
                                <a href="{{ route('bursary.reports.department') }}">
                                    <i class="ti ti-file-text"></i>
                                    <span>By Department</span>
                                </a>
                            </li>
                            <li>
                                <a href="{{ route('bursary.reports.level') }}">
                                    <i class="ti ti-bar-chart"></i>
                                    <span>By Level</span>
                                </a>
                            </li>
                            <li>
                                <a href="{{ route('bursary.reports.student') }}">
                                    <i class="ti ti-user"></i>
                                    <span>By Student</span>
                                </a>
                            </li>
                        </ul>
                    </li> --}}


                    <!-- PAYMENTS -->
                    <li>
                        <h6 class="submenu-hdr"><span>Payments</span></h6>
                        <ul>
                            <li>
                                <a href="{{ route('bursary.payment-settings.index') }}">
                                    <i class="ti ti-list-details"></i>
                                    <span>All Payment Settings</span>
                                </a>
                            </li>

                            <!-- Add New Payment Setting -->
                            <li>
                                <a href="{{ route('bursary.payment-settings.create') }}">
                                    <i class="ti ti-plus"></i>
                                    <span>Add Payment Setting</span>
                                </a>
                            </li>
                        </ul>
                    </li>
                @endif

                <!-- LOGOUT -->
                <li>
                    <h6 class="submenu-hdr"><span>Account</span></h6>
                    <ul>
                        <li>
                            {{-- <a href="{{ route('staff.logout') }}">
                                <i class="ti ti-logout"></i>
                                <span>Logout</span>
                            </a> --}}
                        </li>
                    </ul>
                </li>
            </ul>
        </div>
    </div>
</div>
