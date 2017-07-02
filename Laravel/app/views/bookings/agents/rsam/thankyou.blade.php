@extends(( $data['layout'] == 'melbourne' ) ? 'frontend/layouts/agents/rsa/melbourne' : (($data['layout'] == 'perth' ) ? 'frontend/layouts/agents/rsa/perth' : 'frontend/layouts/agents/rsa/sydney' ))

{{-- Page title --}}
@section('title')
Booking ::
@parent
@stop

{{-- Page content --}}
@section('content')
	
	@include('bookings/common/thankyou')
	
@stop

