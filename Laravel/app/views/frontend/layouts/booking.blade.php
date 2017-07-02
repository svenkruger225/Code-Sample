<!DOCTYPE html>
<html lang="en">
	<head>
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
		<link rel="stylesheet" type="text/css" href="/_scripts/src/bower_modules/backend/bootstrap-responsive.css">
		<link rel="stylesheet" type="text/css" href="/_scripts/src/bower_modules/frontend/booking.css">
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

		<script type="text/javascript">

		  var _gaq = _gaq || [];
		  _gaq.push(['_setAccount', 'UA-9324019-1']);
		  _gaq.push(['_trackPageview']);

		  (function() {
			var ga = document.createElement('script'); ga.type = 'text/javascript'; ga.async = true;
			ga.src = ('https:' == document.location.protocol ? 'https://ssl' : 'http://www') + '.google-analytics.com/ga.js';
			var s = document.getElementsByTagName('script')[0]; s.parentNode.insertBefore(ga, s);
		  })();

		</script>
		<!-- Fav and touch icons -->
		<link rel="apple-touch-icon-precomposed" sizes="144x144" href="/assets/ico/apple-touch-icon-144-precomposed.png?v=2">
		<link rel="apple-touch-icon-precomposed" sizes="114x114" href="/assets/ico/apple-touch-icon-114-precomposed.png?v=2">
		<link rel="apple-touch-icon-precomposed" sizes="72x72" href="/assets/ico/apple-touch-icon-72-precomposed.png?v=2">
		<link rel="apple-touch-icon-precomposed" href="/assets/ico/apple-touch-icon-57-precomposed.png?v=2">
		<link rel="shortcut icon" href="/assets/ico/favicon.png?v=2">
	</head>


	<body>
		<!-- Environment: {{App::environment()}} -->
		<div class="navbar navbar-inverse navbar-fixed-top">
			<div class="navbar-inner">

				<div class="container-fluid">
					<div class="row-fluid">
						<div class="span3">
							<a class="brand header-social" href="/"><img src="/images/logo.jpg" /></a>
						</div>
					
						<div class="span6 pull-left">
							<h1 class="logo-header">Australia's Best Hospitality Training Courses</h1>
						</div>
					
						<div id="header-social-links" class="pull-right">
							<a class="header-social" href="http://www.facebook.com/CoffeeRSAschool" title="facebook">
								<img src="/images/face_46.png" />
							</a>
							<a class="header-social" href="http://instagram.com/coffeersaschool" title="instagram">
								<img src="/images/instagram_46.png" />
							</a>
							<a class="header-social" href="#" data-bind="click: openSendPageToFriendForm" title="Tell a Friend">
								<img src="/images/tell_friend_46.png" />
							</a>
							<button type="button" class="btn btn-navbar" data-toggle="collapse" data-target=".nav-collapse">
								<span class="icon-bar"></span>
								<span class="icon-bar"></span>
								<span class="icon-bar"></span>
							</button>
						</div>
						
						<div class="nav-collapse collapse">
							<!-- {{App::environment()}} -->

							<!-- Navigation -->
							@include('frontend/common/navigation')
						
						</div><!--/.nav-collapse -->
					</div>
				</div>
			</div>
		</div> 

		<div class="container-fluid">
			
			@include('frontend.notifications')

			<!-- Content -->
			@yield('content')

			<hr>

			<!-- footer -->	  
			@include('frontend/common/footer')
		  

		</div><!--/.fluid-container-->
		
		@include('frontend.common.send-page-to-friend')
		<script type="text/javascript" src="/_scripts/src/app/require.config.js"></script>
		<script src="/_scripts/src/app/bootstrapers/home.js"></script>
			
	</body>
</html>
