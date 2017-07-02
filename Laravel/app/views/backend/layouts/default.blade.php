<!DOCTYPE html>
<html lang="en">
	<head>
		<!-- Basic Page Needs
		================================================== -->
		<meta charset="utf-8" />
		<title>
			@section('title')
			Administration
			@show
		</title>
		<meta name="keywords" content="your, awesome, keywords, here" />
		<meta name="author" content="Carlos Souza" />
		<meta name="description" content="Lorem ipsum dolor sit amet, nihil fabulas et sea, nam posse menandri scripserit no, mei." />

		<!-- Mobile Specific Metas
		================================================== -->
		<meta name="viewport" content="width=device-width, initial-scale=1.0">

		<!-- CSS
		================================================== -->
		<link rel="stylesheet" type="text/css" href="/_scripts/src/bower_modules/backend/bootstrap.css">
		<link rel="stylesheet" type="text/css" href="/_scripts/src/bower_modules/backend/bootstrap-responsive.css">
		<link rel="stylesheet" type="text/css" href="/_scripts/src/bower_modules/backend/main.css">
		<link rel="stylesheet" type="text/css" href="/_scripts/src/bower_modules/backend/backend.css">
		<link rel="stylesheet" type="text/css" href="/_scripts/src/bower_modules/frontend/font-awesome.css">
		<link rel="stylesheet" type="text/css" href="/_scripts/src/bower_modules/jquery-ui/themes/eggplant/jquery-ui.min.css">
		<link rel="stylesheet" type="text/css" href="/_scripts/src/bower_modules/toastr/toastr.min.css">
		<link rel="stylesheet" type="text/css" href="/_scripts/src/bower_modules/select2/select2.css">

		<script type="text/javascript"  src="/_scripts/src/bower_modules/jquery/jquery.min.js"></script>
		<script type="text/javascript"  src="/_scripts/src/bower_modules/knockout/dist/knockout.js"></script>
		<script type="text/javascript"  src="/_scripts/src/bower_modules/frontend/bootstrap.js"></script>
		<script type="text/javascript"  src="/_scripts/src/bower_modules/others/bootstrap-hover-dropdown.min.js"></script>

		<style>
		@section('styles')
		body {
			padding: 30px 0;
		}
		@show
		</style>

		<!-- HTML5 shim, for IE6-8 support of HTML5 elements -->
		<!--[if lt IE 9]>
		<script src="http://html5shim.googlecode.com/svn/trunk/html5.js"></script>
		<![endif]-->

		<!-- Favicons
		================================================== -->
		<link rel="apple-touch-icon-precomposed" sizes="144x144" href="/assets/ico/apple-touch-icon-144-precomposed.png?v=2">
		<link rel="apple-touch-icon-precomposed" sizes="114x114" href="/assets/ico/apple-touch-icon-114-precomposed.png?v=2">
		<link rel="apple-touch-icon-precomposed" sizes="72x72" href="/assets/ico/apple-touch-icon-72-precomposed.png?v=2">
		<link rel="apple-touch-icon-precomposed" href="/assets/ico/apple-touch-icon-57-precomposed.png?v=2">
		<link rel="shortcut icon" href="/assets/ico/favicon.png?v=2">
	</head>

	<body>
		<!-- Environment: {{App::environment()}} -->
		<div id="busyindicator"></div>
		<!-- Navbar -->
		@include('backend.common.navigation')

		<!-- Container -->
		<div class="container-fluid">

			<!-- Notifications -->
			@include('frontend.notifications')

			<!-- Content -->
			@yield('content')
		</div>
	
		@include('backend.common.confirm')
		@include('backend.common.loading')

	</body>
</html>
