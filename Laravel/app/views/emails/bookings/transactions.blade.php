<table width="100%" bgcolor="#cccccc" border="0" cellspacing="1" cellpadding="3">
<tbody>
<tr>
<td bgcolor="#ffffff">Date</td>
<td bgcolor="#ffffff">Method</td>
<td bgcolor="#ffffff">Comments</td>
<td bgcolor="#ffffff">IP</td>
<td bgcolor="#ffffff">Status</td>
<td bgcolor="#ffffff">Total</td>
<td bgcolor="#ffffff">End</td>
<td bgcolor="#ffffff">User</td>
</tr>
@if (count($order->payments) > 0 )
@foreach ( $order->payments as $payment )
<tr>
<td bgcolor="#ffffff">{{$payment->payment_date}}</td>
<td bgcolor="#ffffff">{{$payment->method ? $payment->method->name : $payment->payment_method_id}}</td>
<td bgcolor="#ffffff">{{$payment->comments}}</td>
<td bgcolor="#ffffff">{{$payment->IP}}</td>
<td bgcolor="#ffffff">{{$payment->status->name}}</td>
<td bgcolor="#ffffff">{{$payment->total}}</td>
<td bgcolor="#ffffff">{{$payment->backend == '1' ? 'Backend' : 'Frontend'}}</td>
<td bgcolor="#ffffff">{{$payment->user ? $payment->user->name : ''}}</td>
</tr>
@endforeach
@endif
</tbody>
</table>
