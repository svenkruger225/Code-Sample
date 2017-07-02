<!DOCTYPE html>
<html lang="en">
	<head>
		<meta charset="utf-8">
		<meta charset="utf-8">
		<title>
			@section('title')
			The Coffee School
			@show
		</title>
		<meta http-equiv="cache-control" content="max-age=600, private" >
		<meta name="viewport" content="width=device-width, initial-scale=1.0">
		<meta name="description" content="{{$page->description}}">
		<meta name="keywords" content="{{$page->keywords}}">
		<meta name="author" content="The Coffee School">
                
                <!-- Le styles -->
                <link href='//fonts.googleapis.com/css?family=Economica:400,700,400italic,700italic' rel='stylesheet' type='text/css'>
		<link rel="stylesheet" type="text/css" href="/_scripts/src/bower_modules/frontend/bootstrap.css">
		<link rel="stylesheet" type="text/css" href="/_scripts/src/bower_modules/frontend/bootstrap-responsive.css">
                <!--<link rel="stylesheet" type="text/css" href="/_scripts/src/bower_modules/components-bootstrap/css/bootstrap.min.css">-->
                
		<link rel="stylesheet" type="text/css" href="/_scripts/src/bower_modules/frontend/font-awesome.css">
		<link rel="stylesheet" type="text/css" href="/_scripts/src/bower_modules/jquery-ui/themes/eggplant/jquery-ui.min.css">
		<link rel="stylesheet" type="text/css" href="/_scripts/src/bower_modules/toastr/toastr.min.css">
		<link rel="stylesheet" type="text/css" href="/_scripts/src/bower_modules/select2/select2.css">

		<script type="text/javascript"  src="/_scripts/src/bower_modules/jquery/jquery.min.js"></script>
		<script type="text/javascript"  src="/_scripts/src/bower_modules/knockout/dist/knockout.js"></script>
		<script type="text/javascript"  src="/_scripts/src/bower_modules/frontend/bootstrap.js"></script>
		<script type="text/javascript"  src="/_scripts/src/bower_modules/others/bootstrap-hover-dropdown.min.js"></script>
		<script type="text/javascript"  src='/_scripts/src/bower_modules/frontend/coffeeschool.js'></script>
        
                <style type="text/css">

			@media (min-width: 980px) {
				body {
					padding-top: 135px;
					padding-bottom: 40px;
				}
			}
			.sidebar-nav {
				padding:10px 10px;

				float: none;
			}

			@media (min-width: 980px) and (max-width: 1260px) {
				  body {
					padding-top: 180px !important;
				  }
			}
			
		</style>
		<!-- HTML5 shim, for IE6-8 support of HTML5 elements -->
		<!--[if lt IE 9]>
		<script src="//html5shim.googlecode.com/svn/trunk/html5.js"></script>
		<![endif]-->
		
		<!-- Gogle Webmaster tools varification -->
		<meta name="google-site-verification" content="NoKbfvLAUSQ-x_mouv5b0qz5UhkhU_cUaZqs2uSrOco" />
		
		<!-- Fav and touch icons -->
		<link rel="apple-touch-icon-precomposed" sizes="144x144" href="/assets/ico/apple-touch-icon-144-precomposed.png?v=2">
		<link rel="apple-touch-icon-precomposed" sizes="114x114" href="/assets/ico/apple-touch-icon-114-precomposed.png?v=2">
		<link rel="apple-touch-icon-precomposed" sizes="72x72" href="/assets/ico/apple-touch-icon-72-precomposed.png?v=2">
		<link rel="apple-touch-icon-precomposed" href="/assets/ico/apple-touch-icon-57-precomposed.png?v=2">
		<link rel="shortcut icon" href="/assets/ico/favicon.png?v=2">
	</head>


	<body>
            <!-- Content -->

		<!-- Header -->
		@include('frontend/common/header')
		<!-- Environment: {{App::environment()}} -->
		

		<div class="container-fluid">
			
			@include('frontend.notifications')

			<!-- Content -->
			@yield('content')

			<hr>

			<!-- footer -->	  
			@include('frontend/common/footer')
		  

		</div><!--/.fluid-container-->
		
		@include('frontend.common.send-page-to-friend')
		
	</body>
	<script>
		var last_used = {{\Session::getMetadataBag()->getLastUsed()}};
		var lifetime = {{\Config::get('session.lifetime') * 60}};
	</script>
	<script type="text/javascript" src="/_scripts/src/app/require.config.js"></script>
        @if(isset($data))
        <script data-main="/_scripts/src/app/bootstrapers/home_with_login.js" src="/_scripts/src/bower_modules/requirejs/require.js"></script>
        @else
        <script data-main="/_scripts/src/app/bootstrapers/home_with_login.js" src="/_scripts/src/bower_modules/requirejs/require.js"></script>
        @endif;
</html>
