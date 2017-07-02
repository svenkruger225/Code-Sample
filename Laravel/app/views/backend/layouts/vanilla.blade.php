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
		<!-- CSS
		================================================== -->
		<link rel="stylesheet" type="text/css" href="/_scripts/src/bower_modules/backend/bootstrap.css">
		<link rel="stylesheet" type="text/css" href="/_scripts/src/bower_modules/backend/bootstrap-responsive.css">
		<link rel="stylesheet" type="text/css" href="/_scripts/src/bower_modules/backend/main.css">
		<link rel="stylesheet" type="text/css" href="/_scripts/src/bower_modules/backend/backend.css">
		<link rel="stylesheet" type="text/css" href="/_scripts/src/bower_modules/jquery-ui/themes/eggplant/jquery-ui.min.css">
		<link rel="stylesheet" type="text/css" href="/_scripts/src/bower_modules/toastr/toastr.min.css">
		<link rel="stylesheet" type="text/css" href="/_scripts/src/bower_modules/select2/select2.css">

		<script type="text/javascript"  src="/_scripts/src/bower_modules/jquery/jquery.min.js"></script>
		<script type="text/javascript"  src="/_scripts/src/bower_modules/knockout/dist/knockout.js"></script>
		<script type="text/javascript"  src="/_scripts/src/bower_modules/frontend/bootstrap.js"></script>
		<script type="text/javascript"  src="/_scripts/src/bower_modules/others/bootstrap-hover-dropdown.min.js"></script>

	</head>

	<body>
		<div id="busyindicator"></div>
		<!-- Container -->
		<div class="container-fluid">
			<!-- Content -->
			@yield('content')
		</div>
	

	</body>
</html>
