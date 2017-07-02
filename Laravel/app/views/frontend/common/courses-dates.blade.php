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

<div class="row-fluid">
    <select class="courseDate pull-left" id="courseDate{{$course->id}}" data-bind="event: { 'change': updateSelectedInstance }" >
        <option value="">Please Select a Course Date</option>
        @foreach ( $course->instances as $instance )
            <option value="{{$instance->id}}">{{$instance->courseDateDescription}}</option>
        @endforeach
    </select>
</div>
