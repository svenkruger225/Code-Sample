@extends('backend/layouts/default')

@section('content')
	<div class="wrapper">
		<div class="error-spacer"></div>
		<div role="main" class="main">
			<h1>Access Forbidden</h1>

			<h2>Server Error: 403 (Forbidden)</h2>

			<hr>

			<h3>What does this mean?</h3>

			<p>
				You don't have the necessary permissions to access to this page.
			</p>

			<p>
				Perhaps you would like to go to our <a href="{{ URL::route('home'); }}">home page</a>?
			</p>
		</div>
	</div>
@stop
