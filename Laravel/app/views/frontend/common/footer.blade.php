<footer>
<div class="row-fluid coffee-footer">
	@foreach($locations as $location)
	<div class="span2 coffee-footer" style="margin:6px">
		<h6>Coffee School {{$location->name}}</h6>
		<p>{{$location->address}}<br />
		{{$location->city}} {{$location->state}} {{$location->post_code}}<br />
		Ph: {{$location->phone}}<br />
		Fax: {{$location->fax}}</p>
	</div>
	@endforeach

</div>
   
<div class="row-fluid text-center coffee-footer">


<p>Registered Training Organisation (RTO) 91614: Ton Ton Song PTY LTD trading as Coffee School</p>

<p>Copyright 2013 Coffee School</p>

<small>Privacy Policy. Personal information is collected only when a booking is made. 
The student name, phone number and email address is collected to make a booking. 
A credit card number is only collected if a student selects the pay now option to obtain a discount. 
If a student selects the pay later option, the credit card information is not required. We do not store or keep credit card information. 
Your personally identifiable information is kept secure and confidential. 
The information is not divulged or sold to anyone. The credit card information uses a secure payment gateway to allow online payments. 
No other information is transferred to third parties. 
Students can login and change or delete their bookings and information. 
There are no future communications offered	</small>
</div	
</footer>
