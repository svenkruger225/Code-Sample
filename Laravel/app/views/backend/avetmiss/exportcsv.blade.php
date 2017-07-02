@if (count($results) > 0)

	<table class="table table-striped table-bordered table-hover span8">
		<thead>
			<tr>
        		<th class="span1">Co./Last Name</th>
				<th class="span1">Inclusive</th>
				<th class="span1">Order #</th>
				<th class="span1">Invoice #</th>
				<th class="span1">Credit Note</th>
				<th class="span1">Date</th>
				<th class="span1">Customer PO</th>
				<th class="span1">Delivery Status</th>
				<th class="span1">Description</th>
				<th class="span1">Account #</th>
				<th class="span1">Amount</th>
				<th class="span1">Inc-Tax Amount</th>
				<th class="span1">Job</th>
				<th class="span1">Journal Memo</th>
				<th class="span1">Salesperson Last Name</th>
				<th class="span1">Salesperson First Name</th>
				<th class="span1">Referral Source</th>
				<th class="span1">Tax Code</th>
				<th class="span1">Terms - Payment is Due</th>
				<th class="span1">Discount Days</th>
				<th class="span1">Balance Due Days</th>
				<th class="span1">Discount</th>
				<th class="span1">Monthly Charge</th>
				<th class="span1">Amount Paid</th>
				<th class="span1">Payment Method</th>
				<th class="span1">Payment Notes</th>
				<th class="span1">Category</th>
			</tr>
		</thead>
		<tbody>
		@foreach ($results as $item)
			@if ($item->type == 'break')
			<tr>
        		<td></td>
				<td></td>
				<td></td>
				<td></td>
				<td></td>
				<td></td>
				<td></td>
				<td></td>
				<td></td>
				<td></td>
				<td></td>
				<td></td>
				<td></td>
				<td></td>
				<td></td>
				<td></td>
				<td></td>
				<td></td>
				<td></td>
				<td></td>
				<td></td>
				<td></td>
				<td></td>
				<td></td>
				<td></td>
				<td></td>
				<td></td>
			@else
			<tr>
        		<td>{{$item->type}}</td>
				<td>X</td>
				<td>{{$item->order_id}}</td>
				<td>{{$item->invoice_id}}</td>
				<td>{{$item->credit_note}}</td>
				<td>{{{ date('d/m/Y', strtotime($item->course_date)) }}}</td>
				<td>{{$item->customer_id}}</td>
				<td>E</td>
				<td>{{$item->customer_name}}</td>
				<td>{{$item->myob_account}}</td>
				<td>{{$item->totalNoTax}}</td>
				<td>{{$item->total}}</td>
				<td>{{$item->job_name}}</td>
				<td></td>
				<td>{{$item->user_last_name}}</td>
				<td>{{$item->user_first_name}}</td>
				<td>{{$item->referrer}}</td>
				<td>{{$item->tax_code}}</td>
				<td>0</td>
				<td>0</td>
				<td>0</td>
				<td>0</td>
				<td>0</td>
				<td>{{$item->paid}}</td>
				<td>{{$item->payment_method}}</td>
				<td>{{$item->payment_notes}}</td>
				<td></td>
			</tr>
			@endif
		@endforeach
		</tbody>
	</table>

@endif
