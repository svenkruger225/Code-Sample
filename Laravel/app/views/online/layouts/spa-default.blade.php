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
    <!-- build:css -->
      <link href="/onlinecourses/src/bower_modules/components-bootstrap/css/bootstrap.min.css" rel="stylesheet">
      <link href="/onlinecourses/src/bower_modules/toastr/toastr.css" rel="stylesheet">
      <link href="/onlinecourses/src/css/styles.css" rel="stylesheet">
    <!-- endbuild -->

  </head>
    <body>
		<!-- Content -->
		@yield('content')
    </body>
    <!-- build:js -->
      <script src="/onlinecourses/src/app/require.config.js"></script>
      <script data-main="app/startup" src="/onlinecourses/src/bower_modules/requirejs/require.js"></script>
    <!-- endbuild -->
</html>
