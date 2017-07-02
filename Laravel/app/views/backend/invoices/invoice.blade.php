<!DOCTYPE html>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title>Coffee School Invoice</title>
    <style type="text/css">
        body, table, td {font-family:Arial, Helvetica, sans-serif; font-size:12px;}
    </style>
</head>

	<body>
		<table width="100%" cellspacing="0" cellpadding="4">
			<tr>
				<td valign="top">
					<h3>Ton Ton Song Pty Ltd t/a The Coffee and RSA Schools</h3>
					<h3 style="margin:0;">Tax Invoice</h3>
					Order ID: {{{ $order->id }}}<br />
					Invoice Number: {{ $order->current_invoice ? $order->current_invoice->id : '' }}<br />
					Date Issued: {{ $order->current_invoice ? $order->current_invoice->invoice_date : $order->order_date }}
				</td>
				<td align="right">
					<table cellpadding="0" cellspacing="0" id="contact_table">
						<tr><td valign="top" align="right">
							Postal Address:<br />
							Shop 3/107 Quay St Central Station CBD<br />
							NSW, Australia 2000
						</td></tr>
						<tr><td valign="top" align="right">ABN: 92 115 419 988</td></tr>
						<tr><td valign="top" align="right">Ph: 02 9211 7477</td></tr>
						<tr><td valign="top" align="right">Mob: 0435 607 575</td></tr>
						<tr><td valign="top" align="right">Fax: 02 8221 9511</td></tr>
						<tr><td valign="top" align="right"><a href="mailto:info@coffeeschool.com.au">info@coffeeschool.com.au</a></td></tr>
						<tr><td valign="top" align="right"><a href="http://www.coffeeschool.com.au">www.coffeeschool.com.au</a></td></tr>
					</table>
				</td>
			</tr>
		</table>
		<hr />
		<table cellspacing="0" cellpadding="4">
			<tr><td valign="top"><strong>Customer Details</strong></td></tr>
			@if (!$order->agent && !$order->company)
			<tr><td valign="top">Name: {{{ $order->customer->full_name }}}</td></tr>
			@endif
			@if ($order->company) 
			<tr><td valign="top">Company: {{{$order->company->name}}}</td></tr>
			@endif
			@if ($order->agent)
            <tr><td valign="top">Name: {{{ $order->customer->full_name }}}</td></tr>
			<tr><td valign="top">Agent: {{$order->agent->name}}</td></tr>
			@endif
			<tr><td valign="top">Phone: {{ $order->customer->phone }}, Mobile: {{ $order->customer->mobile }}</td></tr>
			@if ($order->group_booking) 
			<tr><td valign="top">Group Name: {{{$order->group_booking->group_name}}}</td></tr>
			@if (!empty($order->group_booking->notes)) 
			<tr><td valign="top">Group Notes: {{{$order->group_booking->notes}}}</td></tr>
			@endif
			@endif
		</table>
		<hr /><br />
		<table width="100%" cellspacing="0" cellpadding="4">
			<tr bgcolor="#CCCCCC">
				<td><strong>Details</strong></td>
				<td align="center"><strong>Units</strong></td>
				<td align="right"><strong>Price</strong></td>
				<td align="right"><strong>GST</strong></td>
				<td align="right"><strong>Total</strong></td>
			</tr>
			@foreach ($order->active_items as $item)
				<tr>
                    <?php
                    if(empty($item->course_instance_id))
                    {
                        $des = explode(',',$item->description);
                        unset($des[1]);
                        $description = implode(',',$des);
                        $description = $description.' - Voucher No '.substr($item->vouchers_ids, 1, -1 );
                    }
                    elseif(!empty($item->vouchers_ids))
                    {
                        $description = $item->description.' - Voucher No '.substr($item->vouchers_ids, 1, -1 );
                    }
                    else
                    {
                        $description = $item->description;
                    }
                    ?>
                    <td>
                        <b> {{{ $description }}}</b>
                        @if (!empty($item->course_instance_id))
                            @foreach ($item->rosters as $roster)<br><span style="margin-left: 20px;"> &bull; {{{ $roster->customer->name }}}</span>@endforeach
                        @endif
                    </td>
					<td valign="top" align="center">{{{ $item->qty }}}</td>
					<td valign="top" align="right">{{{ $item->price }}}</td>
					<td valign="top" align="right">{{{ $item->gst }}}</td>
					<td valign="top" align="right">{{{ $item->total }}}</td>
				</tr>
			@endforeach
			<tr>
				<td colspan="4" align="right" style="border-top:2px solid #000000;">Total:</td>
				<td align="right" style="border-top:2px solid #000000;">{{{ $order->total }}}</td>
			</tr>
			<tr>
				<td colspan="4" align="right" style="border-top:2px solid #000000;">GST Component:</td>
				<td align="right" style="border-top:2px solid #000000;">{{{ $order->gst }}}</td>
			</tr>
			<tr>
				<td colspan="4" align="right">Paid:</td>
				<td align="right">{{{ $order->paid }}}</td>
			</tr>
			<tr>
				<td colspan="4" align="right" style="border-top:2px solid #000000;">Owing:</td>
				<td align="right" style="border-top:2px solid #000000;">{{{ $order->owing }}}</td>
			</tr>
		</table>
		<br /><br /><br /><br /><br /><br />
		<table>
			<thead>
				<tr><th colspan="3">PAYMENT TERMS PRIOR TO COURSE DATE<br /></th></tr>
			</thead>
			<tbody>
				<tr>
					<th>EFT</th>
					<th>Credit Card payment</th>
					<th>Cheque</th>
				</tr>
				<tr>
					<td width="33%" valign="top">
						<p>EFT Direct Deposits</p>
						<b>Cash Deposits at any Westpac Bank</b><br />
						<b>A/C Name:</b> Ton Ton Song PTY LTD <br />
						<b>BSB:</b> 032005 <br />
						<b>A/C No:</b> 962010 <br />
						<b>Reference:</b> {{{ $order->id }}}
					</td>
					<td width="33%" align="center" valign="top">
						Please Contact 02 9211 7477
					</td>
					<td width="33%" valign="top">
						Cheques are only accepted for school bookings.<br /><br/>
						All cheque payments must be sent to<br />
						Head Office<br />
						The Coffee School<br />
						Shop 3/107 Quay St,<br />
						Haymarket NSW 2000
					</td>
				</tr>
			</tbody>
		</table>
		<br /><br /><br />
	</body>
</html>
