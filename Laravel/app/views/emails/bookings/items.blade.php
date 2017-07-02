
<table width="100%" bgcolor="#cccccc" border="0" cellspacing="1" cellpadding="3">
<tbody>
<tr>
<td bgcolor="#ffffff">Ids</td>
<td bgcolor="#ffffff">Description</td>
<td bgcolor="#ffffff">type id</td>
<td bgcolor="#ffffff">qty</td>
<td bgcolor="#ffffff">price</td>
<td bgcolor="#ffffff">total</td>
<td bgcolor="#ffffff">active</td>
</tr>
@foreach ( $order->items as $item )
<tr>
<td bgcolor="#ffffff">
@if ($item->course_instance_id) {{$item->course_instance_id}} 
@elseif ($item->group_booking_id) {{$item->group_booking_id}} 
@elseif ($item->product_id) {{$item->product_id}} 
@elseif ($item->vouchers_ids) {{$item->vouchers_ids}} 
@endif
</td>
<td bgcolor="#ffffff">
{{$item->description}}
@if (!empty($item->course_instance_id)) 
	@foreach ($item->rosters as $roster)<br><span style="margin-left: 20px;"> &bull; {{{ $roster->customer->name }}} - {{{ $roster->customer->mobile }}} - {{{ $roster->customer->email }}}</span>@endforeach
@endif
</td>
<td bgcolor="#ffffff">{{$item->item_type_id}}</td>
<td bgcolor="#ffffff">{{$item->qty}}</td>
<td bgcolor="#ffffff">{{$item->price}}</td>
<td bgcolor="#ffffff">{{$item->total}}</td>
<td bgcolor="#ffffff">{{$item->active}}</td>
</tr>
@endforeach
</tbody>
</table>
