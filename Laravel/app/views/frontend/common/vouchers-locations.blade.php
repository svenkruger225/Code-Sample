@foreach(array_chunk($locations->toArray(), 2) as $group)
<div class="row-fluid">
<div class="span12">
	@foreach($group as $index => $location)
		<div class="span6">
			<voucher class="icon">
				<a href="/vouchers/{{$location['name']}}" title="{{$location['name']}}">
					<img class="icon1" src="/images/icons/{{$location['short_name']}}3.png" />
					<img class="icon2" src="/images/icons/{{$location['short_name']}}4.png" style="display:none;" />
				</a>
				<voucher_caption>{{$location['name']}}</voucher_caption>
			</voucher>		
		</div>
	@endforeach				
</div>
</div>
@endforeach

