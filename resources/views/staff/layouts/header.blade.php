<div class="header">

			<!-- Logo -->
			<div class="header-left active">
				<a href="" class="logo logo-normal">
					<img src="{{asset('assets/img/logo/logo_white.svg')}}" style="height: 60px;" alt="Logo">
				</a>
				<a href="" class="logo-small">
					<img src="{{asset('assets/img/logo/logo_white.svg')}}" style="height: 60px;" alt="Logo">
				</a>
				<a href="" class="dark-logo">
					<img src="{{asset('assets/img/logo/logo_white.svg')}}" style="height: 60px;" alt="Logo">
				</a>
				{{-- <a id="toggle_btn" href="javascript:void(0);">
					<i class="ti ti-menu-deep"></i>
				</a> --}}
			</div>
			<!-- /Logo -->

			<a id="mobile_btn" class="mobile_btn" href="#sidebar">
				<span class="bar-icon">
					<span></span>
					<span></span>
					<span></span>
				</span>
			</a>

			<div class="header-user">
				<div class="nav user-menu">
					
					<!-- Search -->
					<div class="nav-item nav-search-inputs me-auto">
						{{-- <div class="top-nav-search">
							<a href="javascript:void(0);" class="responsive-search">
								<i class="fa fa-search"></i>
							</a>
							<form action="#" class="dropdown">
								<div class="searchinputs" id="dropdownMenuClickable">
									<input type="text" placeholder="Search">
									<div class="search-addon">
										<button type="submit"><i class="ti ti-command"></i></button>
									</div>
								</div>
							</form>
						</div> --}}
					</div>
					<!-- /Search -->

						<div class="dropdown ms-1">
							<a href="javascript:void(0);" class="dropdown-toggle d-flex align-items-center" data-bs-toggle="dropdown">
								<span class="avatar avatar-md rounded">
									<img src="{{ asset('portal_assets/img/profiles/avatar-27.jpg') }}" alt="img avatar">
								</span>
							</a>
							<div class="dropdown-menu">
								<div class="d-block">
									<div class="d-flex align-items-center p-2">
										<span class="avatar avatar-md me-2 online avatar-rounded">
											<img src="{{ asset('portal_assets/img/profiles/avatar-27.jpg') }}" alt="img avatar">
										</span>
										<div>
											<h6 class="">Kevin Larry</h6>
										</div>
									</div>
									<hr class="m-0">
									<a class="dropdown-item d-inline-flex align-items-center p-2" href="profile.html"> <i class="ti ti-user-circle me-2"></i>My Profile</a>
									<hr class="m-0">
									<a class="dropdown-item d-inline-flex align-items-center p-2" href="login.html"><i class="ti ti-login me-2"></i>Logout</a>
								</div>
							</div>
						</div>
					</div>

				</div>
			</div>

			<!-- Mobile Menu -->
			<div class="dropdown mobile-user-menu">
				<a href="javascript:void(0);" class="nav-link dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false"><i class="fa fa-ellipsis-v"></i></a>
				<div class="dropdown-menu dropdown-menu-end">
					<a class="dropdown-item" href="profile.html">My Profile</a>
					<a class="dropdown-item" href="profile-settings.html">Settings</a>
					<a class="dropdown-item" href="login.html">Logout</a>
				</div>
			</div>
			<!-- /Mobile Menu -->

		</div>