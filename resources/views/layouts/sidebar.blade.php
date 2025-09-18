<div class="sidebar" id="sidebar">
			<div class="sidebar-inner slimscroll">
				<div id="sidebar-menu" class="sidebar-menu">
					<ul>
						<li>
							<a href="{{ route('students.dashboard') }};" class="d-flex align-items-center border bg-white rounded p-2 mb-4">
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
									<a href="{{ route('students.dashboard') }}" class=" active"><i class="ti ti-layout-dashboard"></i><span>Dashboard</span></a>
								</li>
							</ul>
						</li>
						<li>
							<h6 class="submenu-hdr"><span>Result</span></h6>
							<ul>
								<li><a href="{{ route('students.course.registration') }}"><i class="ti ti-layout-sidebar"></i><span>Course Registration </span></a></li>
								<li><a href="{{ route('students.load_payment') }}"><i class="ti ti-layout-sidebar"></i><span>School Fees </span></a></li>
							</ul>
						</li>
					</ul>
				</div>
			</div>
		</div>