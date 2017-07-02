@extends('frontend/layouts/default')

{{-- Page title --}}
@section('title')
{{ $page->page_title }}
@stop

{{-- Page content --}}
@section('content')

	<div class="row-fluid">

		{{ $page->content }}

	</div><!--/row-->
    <!--<script>
        var actn = 'online';
        var back = '0';
        var loc_id = '';
        var order_id = null;
        var order_type = 'online';
        var voucher_id = '';
        var ref = '';
        var act_course = '';
        var act_instance = '';
        var act_bundle = '';
    </script>

    <script src="/_scripts/src/app/require.config.js"></script>
    <script data-main="/_scripts/src/app/bootstrapers/booking.public.js" src="/_scripts/src/bower_modules/requirejs/require.js"></script>
    -->
    <script src="/_scripts/src/bower_modules/select2/select2.min.js"></script>
    <script>
        var cityName="{{$page->location_name}}";
        $(".courseDate").select2({ dropdownAutoWidth: true });
        $(".courseDate").change(function(e){
            course=$(this).attr('id');
            courseId=course.substring(10, course.length);
            location.href = "/bookings/"+cityName+"?course="+courseId+"&inst="+$(this).val();
        })
    </script>
@stop
