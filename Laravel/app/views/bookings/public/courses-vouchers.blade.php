<div class="cbCourseDate">
	<div class="row-fluid">
		<span> Select Qty of Vouchers per course: <select class="ipt-mini" id="courseDateQty" data-bind="options: qtyStudents" ></select></span>
	</div>
	@foreach(array_chunk($courses->toArray(), 4) as $group)
	<div class="row-fluid">
	<div class="span12">
		@foreach($group as $index => $course)
			<div class="span3">
			<voucher class="icon">
				<a href="#" class="text-center" name="purchaseItemType{{$course['id']}}" 
					title="{{$course['id']}}" 
					data-bind="'click': selectGiftVoucherFromVouchersPage">
					<img class="icon1" src="/images/icons/{{ rawurlencode($course['short_name']) }}3.png" />
					<img class="icon2" src="/images/icons/{{ rawurlencode($course['short_name']) }}4.png" style="display:none;" />
				</a>
				<voucher_caption>{{$course['name']}}</voucher_caption>
				<input type="hidden" id="courseDate{{$course['id']}}" value="" data-bind="event: { 'change': updateSelectedInstance }" >
			</voucher>
			</div>
		@endforeach				
	</div>
	</div>
	@endforeach
</div>




