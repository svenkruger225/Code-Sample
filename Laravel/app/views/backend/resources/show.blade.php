<html>
<head>
<link href='http://fonts.googleapis.com/css?family=Economica:400,700,400italic,700italic' rel='stylesheet' type='text/css'>
<!-- Le styles -->

<style type="text/css">

	.sidebar-nav {
		padding:10px 10px;

		float: none;
	}

	@media (max-width: 980px) {
		/* Enable use of floated navbar text */
		.navbar-text.pull-right {
			float: none;
			padding-left: 5px;
			padding-right: 5px;
		}
	}
</style>
<!-- HTML5 shim, for IE6-8 support of HTML5 elements -->
<!--[if lt IE 9]>
<script src="http://html5shim.googlecode.com/svn/trunk/html5.js"></script>
<![endif]-->

{{ Basset::show('public.css') }}
{{ Basset::show('public.js') }}
</head>
<body>.
<div class='row-fluid'>
	{{ $resource->content }}
</div>
</html>
