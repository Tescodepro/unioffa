<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=0">
	<meta name="description" content="University of Offa School Management System">
	<meta name="keywords" content="University of Offa, school management, dashboard, admin">
	<meta name="author" content="University of Offa">
	<meta name="robots" content="noindex, nofollow">
	<title>@yield('title') | {{ config('app.name') }}</title>

	<!-- Favicon -->
	{{--
	<link rel="shortcut icon" type="image/x-icon" href="{{ asset('portal_assets/img/favicon.png') }}"> --}}

	<!-- Bootstrap CSS -->
	<link rel="stylesheet" href="{{ asset('portal_assets/css/bootstrap.min.css')}}">

	<!-- animation CSS -->
	<link rel="stylesheet" href="{{ asset('portal_assets/css/animate.css')}}">

	<!-- Datatable CSS -->
	<link rel="stylesheet" href="{{ asset('portal_assets/css/dataTables.bootstrap5.min.css')}}">

	<!-- Tabler Icon CSS -->
	<link rel="stylesheet" href="{{ asset('portal_assets/plugins/tabler-icons/tabler-icons.css')}}">

	<!-- Daterangepikcer CSS -->
	<link rel="stylesheet" href="{{ asset('portal_assets/plugins/daterangepicker/daterangepicker.css')}}">

	<!-- Fontawesome CSS -->
	<link rel="stylesheet" href="{{ asset('portal_assets/plugins/fontawesome/css/fontawesome.min.css')}}">
	<link rel="stylesheet" href="{{ asset('portal_assets/plugins/fontawesome/css/all.min.css')}}">

	<!-- Main CSS -->
	<link rel="stylesheet" href="{{ asset('portal_assets/css/style.css')}}">

	@stack('styles') {{-- For page-specific CSS --}}
</head>

<body>

	@yield('content')
	<!-- jQuery -->
	<script src="{{asset('portal_assets/js/jquery-3.7.1.min.js')}}"></script>
	<!-- Feather Icon JS -->
	<script src="{{asset('portal_assets/js/feather.min.js')}}"></script>
	<!-- Slimscroll JS -->
	<script src="{{asset('portal_assets/js/jquery.slimscroll.min.js')}}"></script>
	<!-- Bootstrap Core JS -->
	<script src="{{asset('portal_assets/js/bootstrap.bundle.min.js')}}"></script>
	<!-- Daterangepikcer JS -->
	<script src="{{asset('portal_assets/js/moment.js')}}"></script>
	<script src="{{asset('portal_assets/plugins/daterangepicker/daterangepicker.js')}}"></script>
	<!-- Chart JS -->
	<script src="{{asset('portal_assets/plugins/chartjs/chart.min.js')}}"></script>
	{{--
	<script src="{{asset('portal_assets/plugins/chartjs/chart-data.js')}}"></script> --}}
	<!-- Custom JS -->
	<script src="{{asset('portal_assets/js/script.js')}}"></script>

	@stack('scripts') {{-- For page-specific JS --}}
</body>

</html>