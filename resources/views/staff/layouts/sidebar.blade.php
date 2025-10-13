<div class="sidebar" id="sidebar">
    <div class="sidebar-inner slimscroll">
        <div id="sidebar-menu" class="sidebar-menu">
            <ul>
                <li>
                    <a href="{{ route('students.dashboard') }};"
                        class="d-flex align-items-center border bg-white rounded p-2 mb-4">
                        <img src="{{ asset('assets/img/logo/logo_white.svg') }}"
                            class="avatar avatar-md img-fluid rounded" alt="Profile">
                        <span class="text-dark ms-2 fw-normal">Unioffa</span>
                    </a>
                </li>
            </ul>
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
                        <a href="{{ route('bursary.transactions.export', ['format' => 'xlsx']) }}">
                            <i class="ti ti-download"></i>
                            <span>Export Transactions</span>
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
        </div>
    </div>
</div>
