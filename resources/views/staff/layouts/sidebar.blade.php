<div class="sidebar" id="sidebar">
    <div class="sidebar-inner slimscroll">
        <div id="sidebar-menu" class="sidebar-menu">
            <!-- Logo / Home -->
            <ul>
                <li>
                    @php
                        $dashboardRoute = match (auth()->user()->userType->name) {
                            'administrator' => 'admin.dashboard',
                            'vice-chancellor' => 'vc.dashboard',
                            'registrar' => 'registrar.dashboard',
                            'bursary' => 'burser.dashboard',
                            'dean' => 'lecturer.dean.dashboard',
                            'ict' => 'ict.dashboard',
                            'center-director' => 'center-director.dashboard',
                            'programme-director' => 'programme-director.dashboard',
                            default => 'lecturer.dashboard',
                        };
                    @endphp
                    <a href="{{ route($dashboardRoute) }}"
                        class="d-flex align-items-center border bg-white rounded p-2 mb-4">
                        <img src="{{ asset($schoolLogo) }}" class="avatar avatar-md img-fluid rounded"
                            alt="{{ $schoolName }}">
                        <span class="text-dark ms-2 fw-normal">{{ $schoolName }}</span>
                    </a>
                </li>
            </ul>

            @php
                $user = auth()->user();
                $userType = $user->userType->name;

                // Load all active menu items once and group by section
                $allMenuItems = \App\Models\MenuItem::where('is_active', true)
                    ->orderBy('sort_order')
                    ->get();

                // Filter to items this user can see:
                //   - no permission_identifier   → always show
                //   - user_type_scope set        → only show for that user type
                //   - permission required        → user must have it
                $visibleItems = $allMenuItems->filter(function ($item) use ($user, $userType) {
                    if ($item->user_type_scope && $item->user_type_scope !== $userType) {
                        return false;
                    }
                    if ($item->permission_identifier) {
                        return $user->hasPermission($item->permission_identifier);
                    }
                    return true;
                })->groupBy('section');
            @endphp

            <ul>
                {{-- MAIN: Dashboard (route varies by user type — legitimate) --}}
                @php
                    $dashRoute = match ($userType) {
                        'administrator' => 'admin.dashboard',
                        'vice-chancellor' => 'vc.dashboard',
                        'registrar' => 'registrar.dashboard',
                        'bursary' => 'burser.dashboard',
                        'ict' => 'ict.dashboard',
                        'dean' => 'lecturer.dean.dashboard',
                        'center-director' => 'center-director.dashboard',
                        default => 'lecturer.dashboard',
                    };
                @endphp
                <li>
                    <h6 class="submenu-hdr"><span>Main</span></h6>
                    <ul>
                        <li>
                            <a href="{{ route($dashRoute) }}" class="{{ activeClass($dashRoute) }}">
                                <i class="ti ti-layout-dashboard"></i>
                                <span>Dashboard</span>
                            </a>
                        </li>
                    </ul>
                </li>

                {{-- DYNAMIC SECTIONS from menu_items table --}}
                @foreach ($visibleItems as $section => $items)
                    <li>
                        <h6 class="submenu-hdr"><span>{{ $section }}</span></h6>
                        <ul>
                            @foreach ($items as $item)
                                <li>
                                    <a href="{{ route($item->route_name) }}"
                                        class="{{ activeClass($item->route_pattern ?? $item->route_name) }}">
                                        <i class="{{ $item->icon }}"></i>
                                        <span>{{ $item->label }}</span>
                                    </a>
                                </li>
                            @endforeach
                        </ul>
                    </li>
                @endforeach

                {{-- ACCOUNT: always visible --}}
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