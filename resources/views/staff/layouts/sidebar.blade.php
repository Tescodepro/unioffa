<div class="sidebar" id="sidebar">
    <div class="sidebar-inner slimscroll">
        <div id="sidebar-menu" class="sidebar-menu">
            <!-- Logo / Home -->
            <ul>
                <li>
                    @php
                        $dashboardRoute = match (auth()->user()->userType->name) {
                            'administrator', 'vice-chancellor', 'registrar' => 'students.dashboard',
                            'bursary' => 'burser.dashboard',
                            'dean' => 'lecturer.dean.dashboard',
                            'ict' => 'ict.dashboard',
                            default => 'lecturer.dashboard',
                        };
                    @endphp
                    <a href="{{ route($dashboardRoute) }}"
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
                            @php
                                $route = match (auth()->user()->userType->name) {
                                    'administrator', 'vice-chancellor', 'registrar' => 'admin.dashboard',
                                    'bursary' => 'burser.dashboard',
                                    'ict' => 'ict.dashboard',
                                    'dean' => 'lecturer.dean.dashboard',
                                    'lecturer', 'hod' => 'lecturer.dashboard',
                                    default => 'lecturer.dashboard',
                                };

                                $dashboardRoutes = match (auth()->user()->userType->name) {
                                    'lecturer', 'hod' => ['lecturer.dashboard'],
                                    'admin.dashboard' => ['admin.dashboard'],
                                    'burser.dashboard' => ['burser.dashboard'],
                                    'ict.dashboard' => ['ict.dashboard'],
                                    'lecturer.dean.dashboard' => ['lecturer.dean.dashboard'],
                                    default => ['lecturer.dashboard'],
                                };
                            @endphp
                            <a href="{{ route($route) }}" class="{{ activeClass($dashboardRoutes) }}">
                                <i class="ti ti-layout-dashboard"></i>
                                <span>Dashboard</span>
                            </a>
                        </li>
                    </ul>
                </li>

                <!-- ACADEMIC STAFF (DEAN, HOD, LECTURER, VICE-CHANCELLOR) MENUS -->
                @if (in_array(auth()->user()->userType->name, ['dean', 'hod', 'lecturer', 'vice-chancellor']))
                    {{-- RESULT MANAGEMENT --}}
                    <li class="{{ openMenuClass(['staff/dean/results*', 'staff/lecturer/results*']) }}">
                        <h6 class="submenu-hdr">
                            <span>Result Management</span>
                        </h6>
                        <ul>
                            @if (auth()->user()->userType->name !== 'vice-chancellor')
                                <li>
                                    <a href="{{ route('staff.results.upload') }}"
                                        class="{{ activeClass('staff.results.upload') }}">
                                        <i class="ti ti-cloud-upload"></i>
                                        <span>Upload Results</span>
                                    </a>
                                </li>

                                <li>
                                    <a href="{{ route('results.viewUploaded') }}"
                                        class="{{ activeClass('results.viewUploaded') }}">
                                        <i class="ti ti-file-check"></i>
                                        <span>View Uploaded Results</span>
                                    </a>
                                </li>
                            @endif

                            <li>
                                <a href="{{ route('results.manage.status') }}"
                                    class="{{ activeClass('results.manage.status') }}">
                                    <i class="ti ti-adjustments"></i>
                                    <span>Manage Result Status</span>
                                </a>
                            </li>

                            <li>
                                <a href="{{ route('results.summary') }}" class="{{ activeClass('results.summary') }}">
                                    <i class="ti ti-chart-bar"></i>
                                    <span>Result Summary</span>
                                </a>
                            </li>

                            @if (auth()->user()->userType->name !== 'vice-chancellor')
                                <li>
                                    <a href="{{ route('backlog.upload.page') }}"
                                        class="{{ activeClass('backlog.upload.page') }}">
                                        <i class="ti ti-history"></i>
                                        <span>Upload Backlog Results</span>
                                    </a>
                                </li>
                            @endif

                            <li>
                                <a href="{{ route('transcript.search.page') }}"
                                    class="{{ activeClass('transcript.search.page') }}">
                                    <i class="ti ti-document"></i>
                                    <span>View Transcript</span>
                                </a>
                            </li>
                        </ul>
                    </li>

                    {{-- COURSE MANAGEMENT --}}
                    <li
                        class="{{ openMenuClass(['staff/dean/*', 'staff/lecturer/*', '!staff/dean/staff*', '!staff/hod/staff*']) }}">
                        <h6 class="submenu-hdr"><span>Course Management</span></h6>
                        <ul>
                            <li>
                                <a href="{{ route('staff.courses.index') }}"
                                    class="{{ activeClass('staff.courses.index') }}">
                                    <i class="ti ti-book-2"></i>
                                    <span>All Courses</span>
                                </a>
                            </li>
                            <li>
                                <a href="{{ route('staff.course.assignments') }}"
                                    class="{{ activeClass('staff.course.assignments') }}">
                                    <i class="ti ti-clipboard-list"></i>
                                    <span>Course Assignments</span>
                                </a>
                            </li>
                        </ul>
                    </li>

                    {{-- STAFF MANAGEMENT (for Dean and HOD) --}}
                    @if (in_array(auth()->user()->userType->name, ['dean', 'hod']))
                        <li class="{{ openMenuClass(['staff/dean/staff*', 'staff/hod/staff*']) }}">
                            <h6 class="submenu-hdr"><span>Staff Management</span></h6>
                            <ul>
                                <li>
                                    <a href="{{ route('staff.index') }}" class="{{ activeClass('staff.index') }}">
                                        <i class="ti ti-users-group"></i>
                                        <span>All Staff</span>
                                    </a>
                                </li>
                            </ul>
                        </li>
                    @endif
                @endif

                <!-- ICT AND ADMINISTRATOR ONLY MENUS -->
                @if (in_array(auth()->user()->userType->name, ['ict']))
                    <!-- STUDENT MANAGEMENT -->
                    <li class="{{ openMenuClass('staff/ict/students*') }}">
                        <h6 class="submenu-hdr"><span>Student Management</span></h6>
                        <ul>
                            <li>
                                <a href="{{ route('ict.students.index') }}" class="{{ activeClass('ict.students.index') }}">
                                    <i class="ti ti-users"></i>
                                    <span>All Students</span>
                                </a>
                            </li>
                            <li>
                                <a href="{{ route('ict.students.create') }}"
                                    class="{{ activeClass('ict.students.create') }}">
                                    <i class="ti ti-user-plus"></i>
                                    <span>Add Student</span>
                                </a>
                            </li>
                            <li>
                                <a href="{{ route('ict.students.bulk') }}" class="{{ activeClass('ict.students.bulk') }}">
                                    <i class="ti ti-upload"></i>
                                    <span>Bulk Upload</span>
                                </a>
                            </li>
                            <li>
                                <a href="{{ route('admitted-students.index') }}"
                                    class="{{ activeClass('admitted-students.index') }}">
                                    <i class="ti ti-download"></i>
                                    <span>Download Admitted Students</span>
                                </a>
                            </li>
                        </ul>
                    </li>

                    <!-- USER MANAGEMENT -->
                    <li class="{{ openMenuClass('staff/ict/users*') }}">
                        <h6 class="submenu-hdr"><span>User Management</span></h6>
                        <ul>
                            <li>
                                <a href="{{ route('ict.staff.users.index') }}"
                                    class="{{ activeClass('ict.staff.users.index') }}">
                                    <i class="ti ti-users-group"></i>
                                    <span>All Users</span>
                                </a>
                            </li>
                        </ul>
                    </li>
                @endif

                <!-- WEBSITE MANAGEMENT (ICT ONLY) -->
                @if (in_array(auth()->user()->userType->name, ['ict']))
                    <li class="{{ openMenuClass('staff/ict/news*') }}">
                        <h6 class="submenu-hdr"><span>Website Management</span></h6>
                        <ul>
                            <li>
                                <a href="{{ route('ict.news.index') }}" class="{{ activeClass('ict.news.index') }}">
                                    <i class="ti ti-news"></i>
                                    <span>News</span>
                                </a>
                            </li>
                        </ul>
                    </li>
                @endif

                <!-- APPLICATION CONFIGURATION (ICT ONLY) -->
                @if (in_array(auth()->user()->userType->name, ['ict']))
                    <li class="{{ openMenuClass(['staff/ict/application-settings*', 'staff/ict/system-settings*']) }}">
                        <h6 class="submenu-hdr"><span>Configuration</span></h6>
                        <ul>
                            <li>
                                <a href="{{ route('ict.application_settings.index') }}"
                                    class="{{ activeClass('ict.application_settings.index') }}">
                                    <i class="ti ti-settings"></i>
                                    <span>App Settings</span>
                                </a>
                            </li>
                            <li>
                                <a href="{{ route('ict.system_settings.index') }}"
                                    class="{{ activeClass('ict.system_settings.index') }}">
                                    <i class="ti ti-adjustments"></i>
                                    <span>System Settings</span>
                                </a>
                            </li>
                        </ul>
                    </li>
                @endif

                <!-- FINANCIAL MANAGEMENT (BURSARY, VICE-CHANCELLOR, ICT) -->
                @if (in_array(auth()->user()->userType->name, ['bursary', 'vice-chancellor', 'ict']))
                    {{-- TRANSACTIONS --}}
                    <li class="{{ openMenuClass(['staff/bursary/transactions*', 'staff/bursary/verify*']) }}">
                        <h6 class="submenu-hdr"><span>Transactions</span></h6>
                        <ul>
                            @if (in_array(auth()->user()->userType->name, ['vice-chancellor']))
                                <li>
                                    <a href="{{ route('burser.dashboard') }}" class="{{ activeClass('burser.dashboard') }}">
                                        <i class="ti ti-chart-line"></i>
                                        <span>Payment Summary</span>
                                    </a>
                                </li>
                            @endif

                            <li>
                                <a href="{{ route('bursary.transactions') }}"
                                    class="{{ activeClass('bursary.transactions') }}">
                                    <i class="ti ti-receipt"></i>
                                    <span>All Transactions</span>
                                </a>
                            </li>

                            <li>
                                <a href="{{ route('bursary.verify.form') }}"
                                    class="{{ activeClass('bursary.verify.form') }}">
                                    <i class="ti ti-check"></i>
                                    <span>Verify Payment</span>
                                </a>
                            </li>

                            @if (in_array(auth()->user()->userType->name, ['bursary', 'vice-chancellor']))
                                <li>
                                    <a href="{{ route('bursary.transactions.create') }}"
                                        class="{{ activeClass('bursary.transactions.create') }}">
                                        <i class="ti ti-plus"></i>
                                        <span>Upload Manual Payment</span>
                                    </a>
                                </li>
                            @endif
                        </ul>
                    </li>

                    {{-- REPORTS --}}
                    @if (in_array(auth()->user()->userType->name, ['bursary', 'vice-chancellor']))
                        <li class="{{ openMenuClass('staff/burser/reports*') }}">
                            <h6 class="submenu-hdr"><span>Reports</span></h6>
                            <ul>
                                <li>
                                    <a href="{{ route('bursary.reports.faculty') }}"
                                        class="{{ activeClass('bursary.reports.faculty') }}">
                                        <i class="ti ti-building"></i>
                                        <span>By Faculty</span>
                                    </a>
                                </li>
                                <li>
                                    <a href="{{ route('bursary.reports.department') }}"
                                        class="{{ activeClass('bursary.reports.department') }}">
                                        <i class="ti ti-category"></i>
                                        <span>By Department</span>
                                    </a>
                                </li>
                                <li>
                                    <a href="{{ route('bursary.reports.level') }}"
                                        class="{{ activeClass('bursary.reports.level') }}">
                                        <i class="ti ti-stack"></i>
                                        <span>By Level</span>
                                    </a>
                                </li>
                                <li>
                                    <a href="{{ route('bursary.reports.student') }}"
                                        class="{{ activeClass('bursary.reports.student') }}">
                                        <i class="ti ti-user"></i>
                                        <span>By Student</span>
                                    </a>
                                </li>
                            </ul>
                        </li>
                    @endif

                    {{-- PAYMENT SETTINGS --}}
                    <li class="{{ openMenuClass('staff/bursary/payment-settings*') }}">
                        <h6 class="submenu-hdr"><span>Payment Settings</span></h6>
                        <ul>
                            <li>
                                <a href="{{ route('bursary.payment-settings.index') }}"
                                    class="{{ activeClass('bursary.payment-settings.index') }}">
                                    <i class="ti ti-settings"></i>
                                    <span>All Settings</span>
                                </a>
                            </li>
                            <li>
                                <a href="{{ route('bursary.payment-settings.create') }}"
                                    class="{{ activeClass('bursary.payment-settings.create') }}">
                                    <i class="ti ti-plus"></i>
                                    <span>Add New Setting</span>
                                </a>
                            </li>
                        </ul>
                    </li>
                @endif

                <!-- AGENT MANAGEMENT (VICE-CHANCELLOR, ICT) -->
                @if (in_array(auth()->user()->userType->name, ['vice-chancellor', 'ict']))
                    <li class="{{ openMenuClass('staff/admin/agents*') }}">
                        <h6 class="submenu-hdr"><span>Agents</span></h6>
                        <ul>
                            <li>
                                <a href="{{ route('admin.agent.applicants') }}"
                                    class="{{ activeClass('admin.agent.applicants') }}">
                                    <i class="ti ti-briefcase"></i>
                                    <span>Agent Applications</span>
                                </a>
                            </li>
                        </ul>
                    </li>
                @endif

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