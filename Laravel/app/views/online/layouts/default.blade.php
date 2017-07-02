<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
	<title>
		@section('title')
		The Coffee School
		@show
	</title>
	<link rel="stylesheet" type="text/css" href="/_scripts/src/bower_modules/components-bootstrap/css/bootstrap.min.css">
	<link rel="stylesheet" type="text/css" href="/_scripts/src/bower_modules/jquery-ui/themes/eggplant/jquery-ui.min.css">
	<link rel="stylesheet" type="text/css" href="/_scripts/src/bower_modules/toastr/toastr.min.css">
	<link rel="stylesheet" type="text/css" href="/_scripts/src/bower_modules/select2/select2.css">
	<link rel="stylesheet" type="text/css" href="/_scripts/src/css/styles.css">
	<script type="text/javascript"  src="/_scripts/src/bower_modules/jquery/jquery.min.js"></script>
	<script type="text/javascript"  src="/_scripts/src/bower_modules/knockout/dist/knockout.js"></script>
	<script type="text/javascript"  src="/_scripts/src/bower_modules/components-bootstrap/js/bootstrap.min.js"></script>
	<script type="text/javascript"  src="/_scripts/src/bower_modules/others/bootstrap-hover-dropdown.min.js"></script>

  </head>
    <body>
		<!-- Content -->

		<!-- Header -->
		@include('online/common/header')
        <div class="wrapper">
            <div class="container-fluid">
			
			<!-- Content -->
			@yield('content')
			
			</div>
        </div>
		<!-- Header -->
		@include('online/common/footer')

    </body>
	<script>
		var last_used = {{\Session::getMetadataBag()->getLastUsed()}};
		var lifetime = {{\Config::get('session.lifetime') * 60}};
	</script>
    <script src="/_scripts/src/app/require.config.js"></script>
    <script data-main="/_scripts/src/app/bootstrapers/online.js" src="/_scripts/src/bower_modules/requirejs/require.js"></script>
	
	

</html>
