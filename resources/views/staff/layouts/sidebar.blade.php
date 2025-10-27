<div class="sidebar" id="sidebar">
    <div class="sidebar-inner slimscroll">
        <div id="sidebar-menu" class="sidebar-menu">
            <!-- Logo / Home -->
            <ul>
                <li>
                    @if (in_array(auth()->user()->userType->name, ['administrator', 'vice-chancellor', 'registrar']))
                        <a href="{{ route('students.dashboard') }}"
                            class="d-flex align-items-center border bg-white rounded p-2 mb-4">
                            <img src="{{ asset('assets/img/logo/logo_white.svg') }}"
                                class="avatar avatar-md img-fluid rounded" alt="Profile">
                            <span class="text-dark ms-2 fw-normal">University of Offa</span>
                        </a>
                    @elseif(auth()->user()->userType->name === 'bursary')
                        <a href="{{ route('burser.dashboard') }}"
                            class="d-flex align-items-center border bg-white rounded p-2 mb-4">
                            <img src="{{ asset('assets/img/logo/logo_white.svg') }}"
                                class="avatar avatar-md img-fluid rounded" alt="Profile">
                            <span class="text-dark ms-2 fw-normal">University of Offa</span>
                        </a>
                    @elseif(auth()->user()->userType->name === 'ict')
                        <a href="{{ route('ict.dashboard') }}"
                            class="d-flex align-items-center border bg-white rounded p-2 mb-4">
                            <img src="{{ asset('assets/img/logo/logo_white.svg') }}"
                                class="avatar avatar-md img-fluid rounded" alt="Profile">
                            <span class="text-dark ms-2 fw-normal">University of Offa</span>
                        </a>
                    @endif
                </li>
            </ul>

            <ul>
                <!-- MAIN -->
                <li>
                    <h6 class="submenu-hdr"><span>Main</span></h6>
                    <ul>
                        <li>
                            @if (in_array(auth()->user()->userType->name, ['administrator', 'vice-chancellor', 'registrar']))
                                <a href="{{ route('admin.dashboard') }}"
                                    class="{{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
                                    <i class="ti ti-layout-dashboard"></i>
                                    <span>Dashboard</span>
                                </a>
                            @elseif(auth()->user()->userType->name === 'bursary')
                                <a href="{{ route('burser.dashboard') }}"
                                    class="{{ request()->routeIs('burser.dashboard') ? 'active' : '' }}">
                                    <i class="ti ti-layout-dashboard"></i>
                                    <span>Dashboard</span>
                                </a>
                            @elseif(auth()->user()->userType->name === 'ict')
                                <a href="{{ route('ict.dashboard') }}"
                                    class="{{ request()->routeIs('ict.dashboard') ? 'active' : '' }}">
                                    <i class="ti ti-layout-dashboard"></i>
                                    <span>Dashboard</span>
                                </a>
                            @endif
                        </li>
                    </ul>
                </li>

                @if (in_array(auth()->user()->userType->name, ['ict', 'administrator']))
                    <!-- STUDENT MANAGEMENT -->
                    <li class="{{ request()->is('staff/ict/students*') ? 'open' : '' }}">
                        <h6 class="submenu-hdr"><span>Student Management</span></h6>
                        <ul>
                            <li>
                                <a href="{{ route('ict.students.index') }}"
                                    class="{{ request()->routeIs('ict.students.index') ? 'active' : '' }}">
                                    <i class="ti ti-users"></i>
                                    <span>All Students</span>
                                </a>
                            </li>
                            <li>
                                <a href="{{ route('ict.students.create') }}"
                                    class="{{ request()->routeIs('ict.students.create') ? 'active' : '' }}">
                                    <i class="ti ti-user-plus"></i>
                                    <span>Add Student</span>
                                </a>
                            </li>
                            <li>
                                <a href="{{ route('ict.students.bulk') }}"
                                    class="{{ request()->routeIs('ict.students.bulk') ? 'active' : '' }}">
                                    <i class="ti ti-upload"></i>
                                    <span>Bulk Upload</span>
                                </a>
                            </li>
                        </ul>
                    </li>
                @endif

                @if (in_array(auth()->user()->userType->name, ['bursary', 'vice-chancellor', 'ict']))
                    <!-- TRANSACTIONS bursary.transactions.create-->
                    <li
                        class="{{ request()->is('staff/bursary/transactions*') || request()->is('staff/bursary/verify*') ? 'open' : '' }}">
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
                            @if (in_array(auth()->user()->userType->name, ['bursary']))
                                <li>
                                    <a href="{{ route('bursary.transactions.create') }}"
                                        class="{{ request()->routeIs('bursary.transactions.create') ? 'active' : '' }}">
                                        <i class="ti ti-check"></i>
                                        <span>Upload Manual Payment</span>
                                    </a>
                                </li>
                            @endif
                        </ul>
                    </li>
                @endif

                @if (in_array(auth()->user()->userType->name, ['bursary', 'vice-chancellor', 'ict']))
                    <!-- PAYMENTS -->
                    <li class="{{ request()->is('staff/bursary/payment-settings*') ? 'open' : '' }}">
                        <h6 class="submenu-hdr"><span>Payments</span></h6>
                        <ul>
                            <li>
                                <a href="{{ route('bursary.payment-settings.index') }}"
                                    class="{{ request()->routeIs('bursary.payment-settings.index') ? 'active' : '' }}">
                                    <i class="ti ti-list-details"></i>
                                    <span>All Payment Settings</span>
                                </a>
                            </li>
                            <li>
                                <a href="{{ route('bursary.payment-settings.create') }}"
                                    class="{{ request()->routeIs('bursary.payment-settings.create') ? 'active' : '' }}">
                                    <i class="ti ti-plus"></i>
                                    <span>Add Payment Setting</span>
                                </a>
                            </li>
                        </ul>
                    </li>
                @endif

                @if (in_array(auth()->user()->userType->name, [ 'vice-chancellor']))
                    <!-- PAYMENTS -->
                    <li class="{{ request()->is('staff/bursary/payment-settings*') ? 'open' : '' }}">
                        <h6 class="submenu-hdr"><span>Agents</span></h6>
                        <ul>
                            <li>
                                <a href="{{ route('admin.agent.applicants') }}"
                                    class="{{ request()->routeIs('admin.agent.applicants') ? 'active' : '' }}">
                                    <i class="ti ti-list-details"></i>
                                    <span>All Agent Applications</span>
                                </a>
                            </li>
                        </ul>
                    </li>
                @endif

                {{-- @if (in_array(auth()->user()->userType->name, ['bursary', 'vice-chancellor']))
                    <!-- REPORTS -->
                    <li class="{{ request()->is('staff/bursary/reports*') ? 'open' : '' }}">
                        <h6 class="submenu-hdr"><span>Reports</span></h6>
                        <ul>
                            <li>
                                <a href="{{ route('bursary.reports.faculty') }}"
                                    class="{{ request()->routeIs('bursary.reports.faculty') ? 'active' : '' }}">
                                    <i class="ti ti-file-analytics"></i>
                                    <span>By Faculty</span>
                                </a>
                            </li>
                            <li>
                                <a href="{{ route('bursary.reports.department') }}"
                                    class="{{ request()->routeIs('bursary.reports.department') ? 'active' : '' }}">
                                    <i class="ti ti-file-text"></i>
                                    <span>By Department</span>
                                </a>
                            </li>
                            <li>
                                <a href="{{ route('bursary.reports.level') }}"
                                    class="{{ request()->routeIs('bursary.reports.level') ? 'active' : '' }}">
                                    <i class="ti ti-bar-chart"></i>
                                    <span>By Level</span>
                                </a>
                            </li>
                            <li>
                                <a href="{{ route('bursary.reports.student') }}"
                                    class="{{ request()->routeIs('bursary.reports.student') ? 'active' : '' }}">
                                    <i class="ti ti-user"></i>
                                    <span>By Student</span>
                                </a>
                            </li>
                        </ul>
                    </li>
                @endif --}}

                <!-- ACCOUNT -->
                <li>
                    <h6 class="submenu-hdr"><span>Account</span></h6>
                    <ul>
                        <li>
                            <a href="{{ route('staff.logout') }}">
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
