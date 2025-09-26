<div class="sidebar" id="sidebar">
    <div class="sidebar-inner slimscroll">
        <div id="sidebar-menu" class="sidebar-menu">
            <ul>
                <li>
                    <a href="{{ route('students.dashboard') }}" class="d-flex align-items-center border bg-white rounded p-2 mb-4">
                        <img src="{{ asset('assets/img/logo/logo_white.svg') }}" class="avatar avatar-md img-fluid rounded" alt="Profile">
                        <span class="text-dark ms-2 fw-normal">Unioffa</span>
                    </a>
                </li>
            </ul>
            <ul>
                <li>
                    <h6 class="submenu-hdr"><span>Main</span></h6>
                    <ul>
                        <li class="submenu">
                            <a href="{{ route('students.dashboard') }}" class="active"><i class="ti ti-layout-dashboard"></i><span>Dashboard</span></a>
                        </li>
                    </ul>
                </li>
                <li>
                    <h6 class="submenu-hdr"><span>Academics</span></h6>
                    <ul>
                        {{-- <li><a href="{{ route('students.course.registration') }}"><i class="ti ti-book"></i><span>Course Registration</span></a></li> --}}
                        <li><a href=""><i class="ti ti-book"></i><span>Course Registration</span></a></li>
                        <li><a href=""><i class="ti ti-file-text"></i><span>Check Results</span></a></li>
                    </ul>
                </li>
                <li>
                    <h6 class="submenu-hdr"><span>Financial Services</span></h6>
                    <ul>
                        <li><a href="{{ route('students.load_payment') }}"><i class="ti ti-wallet"></i><span>School Fees</span></a></li>
                        <li><a href=""><i class="ti ti-wallet"></i><span>School Fees</span></a></li>
                        {{-- <li><a href="{{ route('students.payment.history') }}"><i class="ti ti-history"></i><span>Payment History</span></a></li> --}}
                        <li><a href="{{ route('students.payment.history') }}"><i class="ti ti-history"></i><span>Payment History</span></a></li>
                    </ul>
                </li>
                <li>
                    <h6 class="submenu-hdr"><span>Hostel Services</span></h6>
                    <ul>
                        <li><a href=""><i class="ti ti-building"></i><span>Apply for Hostel</span></a></li>
                        <li><a href=""><i class="ti ti-info-circle"></i><span>Hostel Status</span></a></li>
                    </ul>
                </li>
                <li>
                    <h6 class="submenu-hdr"><span>Other</span></h6>
                    <ul>
                        <li><a href="{{ route('students.admission.letter')  }}"><i class="ti ti-user"></i><span>Admission Letter</span></a></li>
                    </ul>
                </li>
                <li>
                    <h6 class="submenu-hdr"><span>Profile</span></h6>
                    <ul>
                        <li><a href="{{ route('students.profile') }}"><i class="ti ti-user"></i><span>My Profile</span></a></li>
                    </ul>
                </li>
                <li>
                    <h6 class="submenu-hdr"><span>Account</span></h6>
                    <ul>
                        <li><a href=""><i class="ti ti-logout"></i><span>Logout</span></a></li>
                    </ul>
                </li>
            </ul>
        </div>
    </div>
</div>