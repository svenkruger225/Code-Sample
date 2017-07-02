<?php namespace App\Services;

use Config, Input, Lang, Redirect, Sentry, Validator, View, Response, Exception, DateTime, Log;
use Voucher, Purchase, Roster, Order, Item, Payment, Invoice, DB, Location, CourseInstance, GroupBooking;

class ReportsService {

	protected $input;
	protected $items = array();
	protected $transactions = array();
	protected $locations = array();
	protected $rosters = array();

	public function __construct()
	{
		$this->input = Input::all();
		if( isset($this->input['location_id']) && $this->input['location_id'] != '')
			$this->locations = DB::table('locations')
				->where('id', '=',  $this->input['location_id'])
				->orWhere('parent_id', '=',  $this->input['location_id'])
				->lists('id');		
	}
	
	public function GetAgentTotals($csv = null)
	{		
		$result = array();
		$payments = array();
		$totals = array();
		
		if(count($this->input) == 0)
			return $result;
		
		if (!isset($this->input['from_date']) && isset($this->input['to_date']))
		$this->input['from_date'] = date("Y-m-d", strtotime ($this->input['to_date']));
		
		if (!isset($this->input['from_date']))
		$this->input['from_date'] = '';
		else
			$this->input['from_date'] = date("Y-m-d", strtotime ($this->input['from_date']));

		if (!isset($this->input['to_date']))
		$this->input['to_date'] = '';
		else
			$this->input['to_date'] = date("Y-m-d", strtotime ($this->input['to_date']));

		if (isset($this->input['single_date']) && $this->input['single_date'] != '')
		{
			$this->input['from_date'] = date("Y-m-d", strtotime ($this->input['single_date']));
			$this->input['to_date'] = date("Y-m-d", strtotime ($this->input['single_date']));
		}

		$dateStart = $this->input['from_date']; 
		$dateEnd = $this->input['to_date']; 
		$agent_id = $this->input['agent_id'];
				
		$query = \DB::table('rosters as r')
			->join('orders as o', 'o.id', '=', 'r.order_id')
			->join('items as i', 'i.id', '=', 'r.item_id')
			->join('customers as c', 'c.id', '=', 'r.customer_id')
			->join('courseinstances as ci', 'ci.id', '=', 'r.course_instance_id')
			->join('courses as co', 'ci.course_id', '=', 'co.id')
			->join('locations as l', 'ci.location_id', '=', 'l.id')
			->join('agents as a', 'a.id', '=', 'o.agent_id')
			->whereBetween('o.order_date', array($this->input['from_date'], $this->input['to_date']))
			->where('o.status_id', 1)
			->where('ci.active', 1);
			
		if (!empty($this->input['course_id'])) {	
			$query = $query->where('ci.course_id', $this->input['course_id']);
		}
		
		if (!empty($this->input['location_id'])) {	
			$query = $query->where('ci.location_id', $this->input['location_id']);
		}
		
		if (!empty($agent_id)) {	
			$query = $query->where('o.agent_id', $this->input['agent_id'])
				->orderBy('a.name');
		}
		
		$query = $query->orderBy('o.order_date')
			->orderBy('o.id')
			->orderBy('ci.course_date')
			->orderBy('ci.time_start')
			->select(
				'a.name as agent_name',
				'o.order_date', \DB::raw('CASE WHEN o.backend = 1 THEN \'backend\' ELSE \'frontend\' END AS type'),
				'r.order_id', \DB::raw('CONCAT(c.first_name, \' \', c.last_name) as full_name') , 'co.name as course_name', 
				'l.name as location_name', 'i.price',
				\DB::raw('(SELECT sum(p.total) 
					FROM payments p JOIN statuses s on s.id = p.status_id 
					where p.order_id = o.id and payment_method_id != 0 and s.name = \'OK\' GROUP BY order_id)
				as paid')
				);
			$this->items = $query->get();
		
		$previous_order = 0;
		$total_left = 0.00;	
		$result = array();
		foreach($this->items as $item)
		{
			if ($previous_order != $item->order_id) {
				$total_left = floatval($item->paid);	
			}
			
			$item->paid = $total_left == 0.00 ? 0.00 : ( floatval($total_left) >= floatval($item->price) ? $item->price : floatval($total_left) );
			$previous_order = $item->order_id;
			$total_left -= floatval($item->price);
			$result[$item->agent_name][] = $item;
		}		
		
		return $csv ? $this->items : $result;
		
	}	
	
	public function GetFinancialTotalsEntries($owing_date = null)
	{		
		$result = array();
		$payments = array();
		$totals = array();
		
		if(count($this->input) == 0 && empty($owing_date))
			return $result;

		if (empty($owing_date))
		{
		
			if (!isset($this->input['from_date']) && isset($this->input['to_date']))
				$this->input['from_date'] = date("Y-m-d", strtotime ($this->input['to_date']));
		
			if (!isset($this->input['from_date']))
				$this->input['from_date'] = '';
			else
				$this->input['from_date'] = date("Y-m-d", strtotime ($this->input['from_date']));

			if (!isset($this->input['to_date']))
				$this->input['to_date'] = '';
			else
				$this->input['to_date'] = date("Y-m-d", strtotime ($this->input['to_date']));

			if (isset($this->input['single_date']) && $this->input['single_date'] != '')
			{
				$this->input['from_date'] = date("Y-m-d", strtotime ($this->input['single_date']));
				$this->input['to_date'] = date("Y-m-d", strtotime ($this->input['single_date']));
			}
			
		}
		else
		{
			//$this->input['location_id'] = '';
			//$this->input['course_id'] = '';
			$this->input['order_type'] = 7;
			$this->input['from_date'] = date("Y-m-d", strtotime ($owing_date));
			$this->input['to_date'] = date("Y-m-d", strtotime ($owing_date));
		}
		
		$check_date = $this->input['from_date']; 
		$stillOnDateRange = true;

		$total_received = 0;
		$total_owing = 0;
		$grandtotal = 0;	
		$total_students = 0;
		$total_students_paid = 0;
		$total_students_owing = 0;
		
		$sql = "SELECT DISTINCT 
				o.id as order_id, 
				o.order_date as the_date, 
				CASE WHEN ci.course_id THEN ci.`course_date` ELSE CASE WHEN gb.course_id THEN gb.`course_date` ELSE o.`order_date` END END  as order_date, 
				o.backend, 
				o.order_type,
				i.id as item_id,
				i.course_instance_id,
				i.group_booking_id,
				it.name as item_type,
				i.item_type_id,
				i.description,
				i.qty,
				i.price,
				i.total,
				(
					SELECT sum(p.total) 
					FROM payments p JOIN statuses s on s.id = p.status_id 
					where p.order_id = o.id and payment_method_id != 0 and s.name = 'OK' GROUP BY order_id
				) as paid,
				(
					SELECT GROUP_CONCAT(pm.code) As codes 
					FROM payments p 
					JOIN payment_methods pm on pm.id = p.payment_method_id
					JOIN statuses s on s.id = p.status_id 
					where p.order_id = o.id and payment_method_id != 0 and s.name = 'OK' GROUP BY order_id
				) as methods,
				(
					SELECT v.course_id FROM vouchers v where v.order_id = o.id and i.item_type_id = 2 GROUP BY order_id
				) as voucher_course_id,
				CASE WHEN ci.location_id THEN ci.location_id ELSE CASE WHEN gb.location_id THEN gb.location_id ELSE pu.location_id END END as location_id,
				CASE WHEN ci.course_id THEN ci.course_id ELSE CASE WHEN gb.course_id THEN gb.course_id ELSE null END END as course_id,
				CONCAT(cu.first_name, ' ', cu.last_name) as customer_name
				from `orders` o
				inner join `items` i on i.`order_id` = o.`id` 
				inner join `itemtypes` it on it.id = i.item_type_id
				left join `customers` cu on o.`customer_id` = cu.`id` 
				left join `courseinstances` ci on i.`course_instance_id` = ci.`id`
				left join `groupbookings` gb on i.`group_booking_id` = gb.`id`
				left join `purchases` pu on pu.id = o.purchase_id
				where i.active = 1 and o.status_id != 4 and i.price >= 0 and i.item_type_id in(1,2,3) and  
				CASE WHEN ci.course_id THEN ci.`course_date` between '" . $this->input['from_date'] . "' and '" . $this->input['to_date']  . "' ELSE
					CASE WHEN gb.course_id THEN gb.`course_date` between '" . $this->input['from_date'] . "' and '" . $this->input['to_date']  . "' 
					ELSE o.`order_date` between '" . $this->input['from_date'] . "' and '" . $this->input['to_date']  . "' END
				END and 
				CASE WHEN i.course_instance_id IS NOT NULL THEN ci.course_id != 9 AND ci.cancelled = 0 AND ci.active = 1 ELSE true END
				order by o.`order_date`, o.id";

		
		$this->items = DB::select( $sql );

		if( isset($this->input['order_type']) && $this->input['order_type'] != '' && count($this->items) > 0)
			$this->FilterByOrderType();	

		if( isset($this->input['location_id']) && $this->input['location_id'] != '' && count($this->items) > 0)
			$this->FilterByLocation();	

		if( isset($this->input['course_id']) && $this->input['course_id'] != '' && count($this->items) > 0)
			$this->FilterByCourse();	
		
		return $this->items;
		
	}	
	
	public function GetFinancialTotals()
	{		
		$result = array();
		$payments = array();
		$totals = array();
		
		if(count($this->input) == 0)
			return $result;
		
		if (!isset($this->input['from_date']) && isset($this->input['to_date']))
			$this->input['from_date'] = date("Y-m-d", strtotime ($this->input['to_date']));
		
		if (!isset($this->input['from_date']))
			$this->input['from_date'] = '';
		else
			$this->input['from_date'] = date("Y-m-d", strtotime ($this->input['from_date']));

		if (!isset($this->input['to_date']))
			$this->input['to_date'] = '';
		else
			$this->input['to_date'] = date("Y-m-d", strtotime ($this->input['to_date']));

		if (isset($this->input['single_date']) && $this->input['single_date'] != '')
		{
			$this->input['from_date'] = date("Y-m-d", strtotime ($this->input['single_date']));
			$this->input['to_date'] = date("Y-m-d", strtotime ($this->input['single_date']));
		}
		$check_date = $this->input['from_date']; 
		$stillOnDateRange = true;

		$total_received = 0;
		$total_owing = 0;
		$grandtotal = 0;	
		$total_students = 0;
		$total_students_paid = 0;
		$total_students_owing = 0;
		
		$sql = "SELECT DISTINCT 
				o.id as order_id, 
				o.order_date as the_date, 
				CASE 
				WHEN ci.course_id 
				THEN ci.`course_date` 
				ELSE 
					CASE 
					WHEN gb.course_id 
					THEN gb.`course_date` 
					ELSE o.`order_date` 
					END 
				END  as order_date, 
				o.order_type, 
				o.backend, 
				i.id as item_id,
				i.course_instance_id,
				i.group_booking_id,
				i.item_type_id,
				i.qty,
				i.price,
				i.total,
				(
					SELECT sum(p.total) 
					FROM payments p JOIN statuses s on s.id = p.status_id 
					where p.order_id = o.id and payment_method_id != 0 and s.name = 'OK' GROUP BY order_id
				) as paid,
				(
					SELECT GROUP_CONCAT(pm.code) As codes 
					FROM payments p 
					JOIN payment_methods pm on pm.id = p.payment_method_id
					JOIN statuses s on s.id = p.status_id 
					where p.order_id = o.id and payment_method_id != 0 and s.name = 'OK' GROUP BY order_id
				) as methods,
				(
					SELECT v.course_id FROM vouchers v where v.order_id = o.id and i.item_type_id = 2 GROUP BY order_id
				) as voucher_course_id,
				CASE WHEN ci.location_id THEN ci.location_id ELSE CASE WHEN gb.location_id THEN gb.location_id ELSE pu.location_id END END as location_id,
				CASE WHEN ci.course_id THEN ci.course_id ELSE CASE WHEN gb.course_id THEN gb.course_id ELSE null END END as course_id
				from `orders` o
				inner join `items` i on i.`order_id` = o.`id` 
				left join `courseinstances` ci on i.`course_instance_id` = ci.`id`
				left join `groupbookings` gb on i.`group_booking_id` = gb.`id`
				left join `purchases` pu on pu.id = o.purchase_id
				where i.active = 1 and o.status_id != 4 and i.price >= 0 and i.item_type_id in(1,2,3) and  
				CASE WHEN ci.course_id THEN ci.`course_date` between '" . $this->input['from_date'] . "' and '" . $this->input['to_date']  . "' ELSE
					CASE WHEN gb.course_id THEN gb.`course_date` between '" . $this->input['from_date'] . "' and '" . $this->input['to_date']  . "' 
					ELSE o.`order_date` between '" . $this->input['from_date'] . "' and '" . $this->input['to_date']  . "' END
				END and 
				CASE WHEN i.course_instance_id IS NOT NULL THEN ci.course_id != 9 AND ci.cancelled = 0 AND ci.active = 1 ELSE true END
				order by o.`order_date`, o.id";
		
		$this->items = DB::select( $sql );

		// we have the date range to work on
		while ($stillOnDateRange)
		{ 
			
			if( isset($this->input['order_type']) && $this->input['order_type'] != '' && count($this->items) > 0)
				$this->FilterByOrderType();	

			if( isset($this->input['location_id']) && $this->input['location_id'] != '' && count($this->items) > 0)
				$this->FilterByLocation();	

			if( isset($this->input['course_id']) && $this->input['course_id'] != '' && count($this->items) > 0)
				$this->FilterByCourse();	

			$received = 0;
			$owing = 0;	
			$total = 0;	
			$students = 0;
			$students_paid = 0;
			$students_owing = 0;
			
			$checkDateItems = array_filter($this->items, function($item) use($check_date)
				{
					return (date('Y-m-d', strtotime($item->order_date)) == $check_date);
				});
			
			//print_r($this->items);
			//print_r($checkDateItems);
			//
			//exit();
			
			//print "<br><br>\n\n----------------------------------------------------------------<br><br>\n\n";
			$current = '';
			foreach($checkDateItems as $item)
			{
				$is_agent_to_pay = strpos($item->methods, "AGENT") !== false;
				$received += $item->paid >= $item->total ? $item->total : 0;	
				$owing += $item->paid < $item->total ? $item->total : 0;	
				$total += $item->total;	
				$students += empty($item->voucher_course_id) ? $item->qty : 0;
				$students_paid += empty($item->voucher_course_id) && $item->paid >= $item->total ? $item->qty : 0;
				$students_owing += empty($item->voucher_course_id) && $item->paid  < $item->total ? $item->qty : 0;
			}

			$data = array(
				'payment_date' => $check_date,
				'received' => $received,
				'owing' => $owing,
				'total' => $total,
				'students' => $students,
				'students_paid' => $students_paid,
				'students_owing' => $students_owing
				);
			
			$total_received  += $received;;
			$total_owing += $owing;	
			$grandtotal += $total;	
			$total_students += $students;
			$total_students_paid += $students_paid;
			$total_students_owing += $students_owing;
			
			$payments = array_add($payments, $check_date, $data);

			if ($check_date != $this->input['to_date'])
				$check_date = date("Y-m-d", strtotime ("+1 day", strtotime($check_date))); 
			else
				$stillOnDateRange = false; 

		}
		
		$result = array(
			'payments' => $payments,
			'totals' => array(
					'received' => $total_received,
					'owing' => $total_owing,	
					'total' => $grandtotal,
					'students' => $total_students,
					'students_paid' => $total_students_paid,
					'students_owing' => $total_students_owing
					)
				);
		
		//var_dump(\Utils::q());
		//exit();
		
		
		return $result;
		
	}	
	protected function FilterByOrderType()
	{
		$order_types = Config::get('utils.order_types', array());
		
		switch ($order_types[$this->input['order_type']])
		{
			case 'All':
				break;
			case 'Bookings: Public (All)':
				$this->items = array_filter($this->items, function($item) 
				{
					return (substr($item->order_type, 0, strlen("Public")) === "Public");
				});
				break;
			case 'Bookings: Public (Backend)':
				$this->items = array_filter($this->items, function($item) 
				{
					return (substr($item->order_type, 0, strlen("Public")) === "Public" && $item->backend == '1');
				});
				break;
			case 'Bookings: Public (Frontend)':
				$this->items = array_filter($this->items, function($item) 
				{
					return (substr($item->order_type, 0, strlen("Public")) === "Public" && $item->backend == '0');
				});
				break;
			case 'Bookings: Online Courses':
				$this->items = array_filter($this->items, function($item) 
					{
						return (substr($item->order_type, 0, strlen("Online")) === "Online" && $item->backend == '0');
					});
				break;
			case 'Bookings: Upsell':
				$this->items = array_filter($this->items, function($item) 
				{
					return (strpos($item->order_type,'Upsell') !== false);
				});
				break;
			case 'Bookings: Group':
				$this->items = array_filter($this->items, function($item) 
				{
					return (substr($item->order_type, 0, strlen("Group")) === "Group");
				});
				break;
			case 'Bookings: Owing':
				$this->items = array_filter($this->items, function($item) 
				{
					return $item->paid < $item->total;
				});
				break;
			case 'Online Courses':
				break;
			case 'Gift Vouchers':
				$this->items = array_filter($this->items, function($item) 
				{
					return ($item->item_type_id == Utils::ItemTypeId('Voucher'));
				});
				break;
			case 'Others':
				break;
			default:
				break;
		}
	
	}
	protected function FilterByLocation()
	{
		$locations = $this->locations;
		$this->items = array_filter($this->items, function($item) use($locations)
		{
			return (!empty($item->location_id) && in_array($item->location_id, $locations));
		});
	}
	protected function FilterByCourse()
	{
		$course_id = $this->input['course_id'];
		$this->items = array_filter($this->items, function($item) use($course_id)
		{
				return ( (!empty($item->course_id) && $item->course_id == $course_id) || (!empty($item->voucher_course_id) && $item->voucher_course_id == $course_id));
		});
	}	
	
	public function GetFinancialTransactions()
	{

		$result = array();
		$transactions = array();
		$totals = array();
		
		if(count($this->input) == 0)
			return $result;
		
		if (!isset($this->input['from_date']) && isset($this->input['to_date']))
			$this->input['from_date'] = date("Y-m-d", strtotime ($this->input['to_date']));
		
		$this->input['from_date'] = (!isset($this->input['from_date'])) ? '' : date("Y-m-d", strtotime ($this->input['from_date']));
		$this->input['to_date']   = (!isset($this->input['to_date']))   ? '' : date("Y-m-d", strtotime ($this->input['to_date']));

		if (isset($this->input['single_date']) && $this->input['single_date'] != '')
		{
			$this->input['from_date'] = date("Y-m-d", strtotime ($this->input['single_date']));
			$this->input['to_date'] = date("Y-m-d", strtotime ($this->input['single_date']));
		}
		$check_date = $this->input['from_date']; 
		$stillOnDateRange = true;

		$received = 0;

		
		$sql = "select 'success' as class,
				CASE WHEN gb.id THEN '/backend/booking/newGroupBooking' ELSE CASE WHEN pu.id THEN '/backend/booking/newPurchase' ELSE '/backend/booking/newBooking' END END as link,
				p.payment_date,
				l.id as location_id,
				l.short_name as location,
				co.short_name as course,
				CONCAT(c.first_name, ' ', c.last_name) as customer,
				p.order_id,
				(
					SELECT invoices.id FROM invoices JOIN statuses s on s.id = invoices.status_id 
					WHERE invoices.order_id = o.id and s.name != 'Credit Note' GROUP BY order_id ORDER BY invoice_date DESC
				) as invoice_id,
				(
					select CASE WHEN i.course_instance_id THEN 'Public' ELSE CASE WHEN i.group_booking_id THEN 'Group' ELSE 'Purchase' END END 
					from items i JOIN orders oo on oo.id = i.order_id 
					WHERE oo.id = o.id LIMIT 1
				) as order_type,
				p.id as payment_id,
				p.payment_method_id,
				pm.name as method,
				p.total as total,
				p.total as paid,
				CASE WHEN o.backend = 1 THEN 'Backend' ELSE 'Frontend' END as `end`, 
				CONCAT(u.first_name, ' ', u.last_name) as user,
				p.comments as notes
				FROM payments p
				JOIN payment_methods pm on pm.id = p.payment_method_id
				JOIN orders o on p.order_id = o.`id` 
				JOIN statuses s on s.id = p.status_id
				LEFT JOIN purchases pu on o.id = pu.order_id
				LEFT JOIN groupbookings gb on o.id = gb.order_id
				LEFT JOIN customers c on c.id = o.customer_id
				LEFT JOIN users u on u.id = p.user_id
				LEFT JOIN locations l on l.id = CASE 
					WHEN gb.location_id 
					THEN gb.location_id 
					ELSE 
						CASE 
							WHEN pu.location_id 
							THEN pu.location_id 
							ELSE 
							(select CASE WHEN ci.location_id THEN ci.location_id ELSE v.location_id END as location_id
								from items i 
								LEFT JOIN courseinstances ci on i.course_instance_id = ci.id 
								LEFT JOIN vouchers v on v.order_id = i.order_id 
								WHERE i.order_id = o.id AND ( i.course_instance_id IS NOT NULL || i.vouchers_ids IS NOT NULL) LIMIT 1) 
						END 
				END
				LEFT JOIN courses co on co.id = CASE 
					WHEN gb.course_id 
					THEN gb.course_id 
					ELSE 
						(select CASE WHEN ci.course_id THEN ci.course_id ELSE v.location_id END as course_id
							from items i 
							LEFT JOIN courseinstances ci on i.course_instance_id = ci.id 
							LEFT JOIN vouchers v on v.order_id = i.order_id 
							WHERE i.order_id = o.id AND ( i.course_instance_id IS NOT NULL || i.vouchers_ids IS NOT NULL) LIMIT 1) 
				END
				where p.total >= 0 and p.`payment_date` between :datestart and :dateend 
				order by  p.`payment_date`, p.`order_id`";
		
		
		
		$this->transactions = DB::select( $sql , array('datestart' => $this->input['from_date'],'dateend' => $this->input['to_date']));
		
		if( isset($this->input['order_type']) && $this->input['order_type'] != '' && count($this->transactions) > 0)
			$this->FilterPaymentsByOrderType();	

		if( isset($this->input['location_id']) && $this->input['location_id'] != '' && count($this->transactions) > 0)
			$this->FilterPaymentsByLocation();	

		if( isset($this->input['backend']) && $this->input['backend'] != '' && count($this->transactions) > 0)
			$this->FilterPaymentsByEnd($this->input['backend']);	

		if( isset($this->input['method_id']) && $this->input['method_id'] != '' && count($this->transactions) > 0)
			$this->FilterPaymentsByMethod($this->input['method_id']);	

		foreach($this->transactions as $payment)
		{
			array_push($transactions, $payment);
			$received += $payment->total;	
		}
		
		$result = array(
			'transactions' => $transactions,
			'totals' => array('received' => $received)
			);
		
		return $result;

		var_dump($this->transactions);
		exit();
		
	}
	protected function FilterPaymentsByOrderType()
	{
		$order_types = Config::get('utils.order_types', array());
		
		switch ($order_types[$this->input['order_type']])
		{
			case 'All':
				break;
			case 'Bookings: Public (All)':
				$this->transactions = array_filter($this->transactions, function($item) 
					{
						return ($item->order_type == 'Public');
					});
				break;
			case 'Bookings: Public (Backend)':
				$this->transactions = array_filter($this->transactions, function($item) 
					{
						return ($item->order_type == 'Public' && $item->end == 'Backend');
					});
				break;
			case 'Bookings: Public (Frontend)':
				$this->transactions = array_filter($this->transactions, function($item) 
					{
						return ($item->order_type == 'Public' && $item->end == 'Frontend');
					});
				break;
			case 'Bookings: Group':
				$this->transactions = array_filter($this->transactions, function($item) 
					{
						return ($item->order_type == 'Group');
					});
				break;
			case 'Online Courses':
				break;
			case 'Gift Vouchers':
				$this->transactions = array_filter($this->transactions, function($item) 
					{
						return ($item->order_type == 'Public' && $item->gv == 1);
					});
				break;
			case 'Others':
				break;
			default:
				break;
		}
		
	}
	protected function FilterPaymentsByLocation()
	{
		$locations = $this->locations;
		$this->transactions = array_filter($this->transactions, function($item) use($locations)
			{
				return (in_array($item->location_id, $locations));
			});
	}
	protected function FilterPaymentsByEnd($backend)
	{
		$this->transactions = array_filter($this->transactions, function($item) use($backend)
			{
				return ($backend == '2') || ($backend == '1' && $item->end == 'Backend') || ($backend == '0' && $item->end == 'Frontend');
			});
	}
	protected function FilterPaymentsByMethod($method)
	{
		$this->transactions = array_filter($this->transactions, function($item) use($method)
			{
				return ($item->payment_method_id == $method);
			});
	}
	
	public function GetStaffFinancial()
	{

		$result = array();
		$transactions = array();
		$totals = array();
		
		if(count($this->input) == 0)
			return $result;
		
		if (!isset($this->input['from_date']) && isset($this->input['to_date']))
			$this->input['from_date'] = date("Y-m-d", strtotime ($this->input['to_date']));
		
		$this->input['from_date'] = (!isset($this->input['from_date'])) ? '' : date("Y-m-d", strtotime ($this->input['from_date']));
		$this->input['to_date']   = (!isset($this->input['to_date']))   ? '' : date("Y-m-d", strtotime ($this->input['to_date']));

		if (isset($this->input['single_date']) && $this->input['single_date'] != '')
		{
			$this->input['from_date'] = date("Y-m-d", strtotime ($this->input['single_date']));
			$this->input['to_date'] = date("Y-m-d", strtotime ($this->input['single_date']));
		}
		$check_date = $this->input['from_date']; 
		$stillOnDateRange = true;

		$received = 0;


		$sql = "SELECT 	
				o.order_type,	
				CASE WHEN ci.course_id THEN 
					ci.`course_date` 
				ELSE 
					CASE WHEN gb.course_id THEN 
						gb.`course_date` 
					ELSE 
						o.`order_date` 
					END 
				END  as order_date, 
				o.order_type,
				o.id as order_id, 
				i.total as total,
				CONCAT(u.first_name, ' ', u.last_name) as name,
				CASE WHEN ci.location_id THEN ci.location_id ELSE CASE WHEN gb.location_id THEN gb.location_id ELSE pu.location_id END END as location_id,
				CASE WHEN ci.course_id THEN ci.course_id ELSE CASE WHEN gb.course_id THEN gb.course_id ELSE null END END as course_id
				from `orders` o
				inner join `items` i on i.`order_id` = o.`id` 
				left join `courseinstances` ci on i.`course_instance_id` = ci.`id`
				left join `groupbookings` gb on i.`group_booking_id` = gb.`id`
				left join `purchases` pu on pu.id = o.purchase_id
				LEFT JOIN users u on u.id = o.user_id
				where i.active = 1 and o.status_id != 4 and i.price > 0 and i.item_type_id in(1,2,3) and  
				CASE WHEN ci.course_id THEN ci.`course_date` between '" . $this->input['from_date'] . "' and '" . $this->input['to_date']  . "' ELSE
					CASE WHEN gb.course_id THEN gb.`course_date` between '" . $this->input['from_date'] . "' and '" . $this->input['to_date']  . "' 
					ELSE o.`order_date` between '" . $this->input['from_date'] . "' and '" . $this->input['to_date']  . "' END
				END and 
				CASE WHEN i.course_instance_id IS NOT NULL THEN ci.course_id != 9 AND ci.cancelled = 0 AND ci.active = 1 ELSE true END
				order by u.first_name, u.last_name, CASE WHEN ci.course_id THEN ci.`course_date` ELSE CASE WHEN gb.course_id THEN gb.`course_date` ELSE o.`order_date` END END, o.id";

		//group by o.user_id, CASE WHEN ci.course_id THEN ci.`course_date` ELSE CASE WHEN gb.course_id THEN gb.`course_date` ELSE o.`order_date` END END


		$this->items = DB::select( $sql);
		//$this->transactions = DB::select( $sql);
				
		if( isset($this->input['order_type']) && $this->input['order_type'] != '' && count($this->items) > 0)
			$this->FilterByOrderType();	
		
		$date_transactions = array();
		$index = 0;
		// we have the date range to work on
		while ($stillOnDateRange)
		{ 
			$checkDateItems = array_filter($this->items, function($item) use($check_date)
			{
				return (date('Y-m-d', strtotime($item->order_date)) == $check_date);
			});

			$current_order = 'start';
			$current_staff = 'start';
			$staff_date_total = 0;
			$staff_date_qty_orders = 0;

			foreach($checkDateItems as $item)
			{
				if ($current_staff != $item->name && $current_staff != 'start')
				{
					if ($staff_date_qty_orders > 0)
					{
						$entry_staff = array(
							'order_date' => $check_date,
							'qty' => $staff_date_qty_orders,
							'total' => $staff_date_total,
							'name' => $current_staff,
							'location_id' => ''
							);					
						$date_transactions[$index] = $entry_staff;
						$index++;
					}
					
					$staff_date_qty_orders = 0;
					$staff_date_total = 0;
				}

				$staff_date_total += $item->total;
				if ($current_order != $item->order_id) {$staff_date_qty_orders++;}
				$current_order = $item->order_id;
				$current_staff = $item->name;
			}
			if ($staff_date_qty_orders > 0)
			{
				$entry_staff = array(
					'order_date' => $check_date,
					'qty' => $staff_date_qty_orders,
					'total' => $staff_date_total,
					'name' => $current_staff,
					'location_id' => ''
					);			
				$date_transactions[$index] = $entry_staff;
				$index++;
			}

			if ($check_date != $this->input['to_date'])
				$check_date = date("Y-m-d", strtotime ("+1 day", strtotime($check_date))); 
			else
				$stillOnDateRange = false; 
		}		
		
		//print_r($date_transactions);
		//exit();
		
		usort($date_transactions, function($a, $b) {
				if($a['name'] == $b['name'])
				return $a['order_date'] > $b['order_date'] ? 1 : -1;
				return $a['name'] > $b['name'] ? 1 : -1;
			});
		
		$this->transactions = json_decode(json_encode($date_transactions), FALSE); 
		
		$current_staff = 'start';
		$transactions = array();
		$staff_qty = 0;
		$staff_total = 0;
		$qty = 0;
		$received = 0;
		$result['staffs'] = array();
		
		foreach($this->transactions as $staff)
		{
			if ($current_staff != $staff->name && $current_staff != 'start')
			{
				$entry_staff = array(
					'name' => $current_staff,
					'transactions' => $transactions,
					'qty' => $staff_qty,
					'total' => $staff_total
					);	
				array_push($result['staffs'], $entry_staff);
				
				$staff_qty = 0;
				$staff_total = 0;
				$transactions = array();
			}
			array_push($transactions, $staff);
			$staff_total += $staff->total;	
			$received += $staff->total;	
			$staff_qty += $staff->qty;	
			$qty += $staff->qty;	
			$current_staff = $staff->name;
		}
		$entry_staff = array(
			'name' => $current_staff,
			'transactions' => $transactions,
			'qty' => $staff_qty,
			'total' => $staff_total
			);	
		array_push($result['staffs'], $entry_staff);
		
		$result['qty'] = $qty;
		$result['totals'] = $received;
		
		return $result;

		//var_dump($result);
		//exit();
		
	}
	
	public function GetStaffSales()
	{

		$result = array();
		$transactions = array();
		$totals = array();
		
		if(count($this->input) == 0)
			return $result;
		
		if (!isset($this->input['from_date']) && isset($this->input['to_date']))
			$this->input['from_date'] = date("Y-m-d", strtotime ($this->input['to_date']));
		
		$this->input['from_date'] = (!isset($this->input['from_date'])) ? '' : date("Y-m-d", strtotime ($this->input['from_date']));
		$this->input['to_date']   = (!isset($this->input['to_date']))   ? '' : date("Y-m-d", strtotime ($this->input['to_date']));

		if (isset($this->input['single_date']) && $this->input['single_date'] != '')
		{
			$this->input['from_date'] = date("Y-m-d", strtotime ($this->input['single_date']));
			$this->input['to_date'] = date("Y-m-d", strtotime ($this->input['single_date']));
		}
		$check_date = $this->input['from_date']; 
		$stillOnDateRange = true;

		$received = 0;

		
		$sql = "select 
				o.order_type,	
				o.order_date,
				o.id as order_id,
				o.total as total,
				CONCAT(u.first_name, ' ', u.last_name) as name
				FROM orders o
				LEFT JOIN users u on u.id = o.user_id
				where o.status_id != 4 and o.`order_date` between :datestart and :dateend 
				order by u.first_name, u.last_name, o.order_date";
				//group by o.user_id, o.order_date
				
		$this->items = DB::select( $sql , array('datestart' => $this->input['from_date'],'dateend' => $this->input['to_date']));
		//$this->transactions = DB::select( $sql);
				
		if( isset($this->input['order_type']) && $this->input['order_type'] != '' && count($this->items) > 0)
			$this->FilterByOrderType();	
		
		$date_transactions = array();
		$index = 0;
		// we have the date range to work on
		while ($stillOnDateRange)
		{ 
			$checkDateItems = array_filter($this->items, function($item) use($check_date)
				{
					return (date('Y-m-d', strtotime($item->order_date)) == $check_date);
				});

			$current_order = 'start';
			$current_staff = 'start';
			$staff_date_total = 0;
			$staff_date_qty_orders = 0;

			foreach($checkDateItems as $item)
			{
				if ($current_staff != $item->name && $current_staff != 'start')
				{
					if ($staff_date_qty_orders > 0)
					{
						$entry_staff = array(
							'order_date' => $check_date,
							'qty' => $staff_date_qty_orders,
							'total' => $staff_date_total,
							'name' => $current_staff,
							'location_id' => ''
							);					
						$date_transactions[$index] = $entry_staff;
						$index++;
					}
					
					$staff_date_qty_orders = 0;
					$staff_date_total = 0;
				}

				$staff_date_total += $item->total;
				if ($current_order != $item->order_id) {$staff_date_qty_orders++;}
				$current_order = $item->order_id;
				$current_staff = $item->name;
			}
			if ($staff_date_qty_orders > 0)
			{
				$entry_staff = array(
					'order_date' => $check_date,
					'qty' => $staff_date_qty_orders,
					'total' => $staff_date_total,
					'name' => $current_staff,
					'location_id' => ''
					);			
				$date_transactions[$index] = $entry_staff;
				$index++;
			}

			if ($check_date != $this->input['to_date'])
			$check_date = date("Y-m-d", strtotime ("+1 day", strtotime($check_date))); 
			else
				$stillOnDateRange = false; 
		}		
		
		//print_r($date_transactions);
		//exit();
		
		usort($date_transactions, function($a, $b) {
				if($a['name'] == $b['name'])
				return $a['order_date'] > $b['order_date'] ? 1 : -1;
				return $a['name'] > $b['name'] ? 1 : -1;
			});
		
		$this->transactions = json_decode(json_encode($date_transactions), FALSE); 
		//$this->transactions = DB::select( $sql , array('datestart' => $this->input['from_date'],'dateend' => $this->input['to_date']));
		
				
		$current_staff = 'start';
		$transactions = array();
		$staff_qty = 0;
		$staff_total = 0;
		$qty = 0;
		$received = 0;
		$result['staffs'] = array();
		
		foreach($this->transactions as $staff)
		{
			if ($current_staff != $staff->name && $current_staff != 'start')
			{
				$entry_staff = array(
					'name' => $current_staff,
					'transactions' => $transactions,
					'qty' => $staff_qty,
					'total' => $staff_total
					);	
				array_push($result['staffs'], $entry_staff);
				
				$staff_qty = 0;
				$staff_total = 0;
				$transactions = array();
			}
			array_push($transactions, $staff);
			$staff_total += $staff->total;	
			$received += $staff->total;	
			$staff_qty += $staff->qty;	
			$qty += $staff->qty;	
			$current_staff = $staff->name;
		}
		$entry_staff = array(
			'name' => $current_staff,
			'transactions' => $transactions,
			'qty' => $staff_qty,
			'total' => $staff_total
			);	
		array_push($result['staffs'], $entry_staff);
		
		$result['qty'] = $qty;
		$result['totals'] = $received;
		
		return $result;

		var_dump($result);
		exit();
		
	}

	public function GetTrainerRosters()
	{

		$this->input = Input::all();
		
		$result = array();
		$payments = array();
		$totals = array();
		
		if(count($this->input) == 0)
			return $result;
		
		if (!isset($this->input['from_date']) && isset($this->input['to_date']))
			$this->input['from_date'] = date("Y-m-d", strtotime ($this->input['to_date']));
		
		if (!isset($this->input['from_date']))
			$this->input['from_date'] = '';
		else
			$this->input['from_date'] = date("Y-m-d", strtotime ($this->input['from_date']));

		if (!isset($this->input['to_date']))
			$this->input['to_date'] = '';
		else
			$this->input['to_date'] = date("Y-m-d", strtotime ($this->input['to_date']));

		if (isset($this->input['single_date']) && $this->input['single_date'] != '')
		{
			$this->input['from_date'] = date("Y-m-d", strtotime ($this->input['single_date']));
			$this->input['to_date'] = date("Y-m-d", strtotime ($this->input['single_date']));
		}
		$check_date = $this->input['from_date']; 
		$stillOnDateRange = true;

		$total_received = 0;
		$total_owing = 0;
		$grandtotal = 0;	
		$total_students = 0;
		$total_students_paid = 0;
		$total_students_owing = 0;

		// we have the date range to work on
		while ($stillOnDateRange)
		{ 
			$instances = CourseInstance::where('course_date', $check_date)->get();
			if (count($instances) > 0)
			{
				$insts = $this->FilterInstances($instances, 'Public');
				$result += $insts;
			}		

			// Get the Group entries for parent location
			$instances = GroupBooking::where('course_date', $check_date)->get();
			$insts = array();
			if (count($instances) > 0)
			{
				$insts = $this->FilterInstances($instances, 'Group');
				$result += $insts;
			}		


			if ($check_date != $this->input['to_date'])
				$check_date = date("Y-m-d", strtotime ("+1 day", strtotime($check_date))); 
			else
				$stillOnDateRange = false; 

		}
	
		return $result;
		
	}
	
	protected function FilterInstances($instances, $entry_type = 'Public')
	{
		$insts = array();

		if( isset($this->input['trainer_id']) && $this->input['trainer_id'] != '')
		{
			$trainer_id = $this->input['trainer_id'];
			$instances = $instances->filter(function(&$item) use($trainer_id)
			{
					if (in_array($trainer_id, $item->instructors->lists('id')))
					{
						$item->instructors = $item->instructors->filter( function($instructor)  use($trainer_id){
									if(	$instructor->id == $trainer_id)
								return $instructor;	
						});
						return $item;
					}
			});
		}

		if( isset($this->input['location_id']) && $this->input['location_id'] != '')
		{
			$locations = $this->locations;
			$instances = $instances->filter(function($item) use($locations)
			{
				if (in_array($item->location_id, $locations))
					return $item;
			});
		}

		if( isset($this->input['course_id']) && $this->input['course_id'] != '')
		{
			$course_id = $this->input['course_id'];
			$instances = $instances->filter(function($item) use($course_id)
			{
				if ($item->course_id == $course_id)
					return $item;
			});
		}

	
		foreach($instances as $instance)
		{
			$instructors = array();
			if (count($instance->instructors) > 0 )
				foreach ($instance->instructors as $instructor)
					array_push($instructors, $instructor->name);							

			$insts = array_add($insts, $instance->id, array(
				'location' => $instance->location->name, 
				'trainer' => implode(", ", $instructors), 
				'course_type' => $instance->course->short_name, 
				'course_date' => date('d/m/Y', strtotime($instance->course_date)), 
				'course_time' => date('h:i A', strtotime($instance->time_start)) . ' - ' . date('h:i A', strtotime($instance->time_end)), 
				'type' => $entry_type
			));
		}
		
		return $insts;
		
	}

	
	public function ExportMyob()
	{
		$result = array();
		$transactions = array();
		$totals = array();
		
		if(count($this->input) == 0)
			return $result;
		
		if (!isset($this->input['from_date']) && isset($this->input['to_date']))
			$this->input['from_date'] = date("Y-m-d", strtotime ($this->input['to_date']));
		
		if (!isset($this->input['from_date']))
			$this->input['from_date'] = '';
		else
			$this->input['from_date'] = date("Y-m-d", strtotime ($this->input['from_date']));

		if (!isset($this->input['to_date']))
			$this->input['to_date'] = '';
		else
			$this->input['to_date'] = date("Y-m-d", strtotime ($this->input['to_date']));

		if (isset($this->input['single_date']) && $this->input['single_date'] != '')
		{
			$this->input['from_date'] = date("Y-m-d", strtotime ($this->input['single_date']));
			$this->input['to_date'] = date("Y-m-d", strtotime ($this->input['single_date']));
		}

		if (empty($this->input['report_type'])) $this->input['report_type'] = 'All';
		
		$dateStart = $this->input['from_date'];
		$dateEnd = $this->input['to_date'];

		if ( $this->input['report_type'] == 'CourseInstance' ||  
			$this->input['report_type'] == 'Rebooking' || 
			$this->input['report_type'] == 'All' || 
			$this->input['report_type'] == 'No Show' || 
			$this->input['report_type'] == 'VouchersUsed')
		{

			$sql = "SELECT 
			CASE WHEN ag.name IS NOT NULL THEN ag.name ELSE CASE WHEN cp.name IS NOT NULL THEN cp.name ELSE 'Individuals' END END as `Co./Last Name`,
			'X' as `Inclusive`,
			CONCAT('P', r.order_id) as `Invoice #`, 
			ci.course_date as `Date`,
			c.id as `Customer PO`,
			'E' as `Delivery Status`,
			CONCAT(c.first_name, ' ', c.last_name) as `Description`,
			CASE WHEN i.item_type_id = 4 THEN '41850' ELSE co.myob_code END as `Account #`,		
			i.price as `Amount`,
			i.price as `Inc-Tax Amount`,
			CASE WHEN i.item_type_id = 4 THEN 'Admin' ELSE CONCAT(l.myob_job_code, '-', co.myob_job_code) END as `Job`,
			'' as `Journal Memo`,
			u.last_name as `Salesperson Last Name`,	
			u.first_name as `Salesperson First Name`, 
			rf.name as `Referral Source`, 
			CASE WHEN i.gst > 0 THEN 'GST' ELSE 'FRE' END as `Tax Code`, 
			i.gst / i.qty as `Tax Amount`, 
			'0' as `Terms - Payment is Due`,		
			'0' as `Discount Days`,		
			'0' as `Balance Due Days`,		
			'0' as `Discount`,		
			'0' as `Monthly Charge`,		
			(
				SELECT sum(p.total) 
				FROM payments p 
				JOIN statuses s on s.id = p.status_id 
				WHERE p.order_id = o.id and payment_method_id != 0 and s.name = 'OK' GROUP BY order_id
			) as `Amount Paid`,		
			(
				SELECT GROUP_CONCAT(pm.name ORDER BY pm.name ASC SEPARATOR ', ') as payment_method 
				FROM payments p 
				JOIN payment_methods pm on pm.id = p.payment_method_id 
				JOIN statuses s on s.id = p.status_id
				WHERE p.order_id = o.id and payment_method_id != 0 and s.name = 'OK' GROUP BY order_id
			) as `Payment Method`,
			(
				SELECT GROUP_CONCAT(p.comments SEPARATOR ', ') 
				FROM payments p 
				JOIN statuses s on s.id = p.status_id 
				WHERE p.order_id = o.id and payment_method_id != 0 and s.name = 'OK' GROUP BY order_id
			) as `Payment Notes`
			FROM rosters r
			JOIN courseinstances ci on ci.id = r.course_instance_id
			JOIN courses co on co.id = ci.course_id
			JOIN locations l on l.id = ci.location_id
			JOIN customers c on c.id = r.customer_id
			JOIN orders o on o.id = r.order_id
			JOIN items i on i.order_id = o.id and i.course_instance_id = r.course_instance_id
			LEFT JOIN users u on u.id = o.user_id
			LEFT JOIN referrers_log rl on rl.order_id = o.id
			LEFT JOIN referrers rf on rl.referrer_id = rf.id
			LEFT JOIN agents ag on ag.id = o.agent_id
			LEFT JOIN companies cp on cp.id = o.company_id
			WHERE ci.course_date between :datestart AND :dateend AND 
				i.active = 1 and o.status_id != 4 and i.price > 0 AND 
				ci.cancelled = 0 AND ci.active = 1 ";
				
			
			if ( $this->input['report_type'] == 'VouchersUsed')
			{
				$sql .= "and (
					SELECT GROUP_CONCAT(pm.name ORDER BY pm.name ASC SEPARATOR ', ') as payment_method 
					FROM payments p 
					JOIN payment_methods pm on pm.id = p.payment_method_id 
					JOIN statuses s on s.id = p.status_id
					WHERE p.order_id = o.id and payment_method_id != 0 and s.name = 'OK' GROUP BY order_id
				) LIKE '%Voucher%' ";
			}
			
			if ( $this->input['report_type'] == 'Rebooking')
				$sql .= " and i.item_type_id = 4 ";
			else
				$sql .= "and (i.item_type_id = 1 OR i.item_type_id = 4) ";
						
			if ( $this->input['report_type'] == 'No Show')
				$sql .= "and ci.course_id = 9 ";
			else
				$sql .= "and ci.course_id != 9 ";
			
			$sql .= "ORDER BY course_date, r.order_id";
			
			//Log::info('sql : '. $sql);
			
			$results = DB::select( DB::raw( $sql ), array('datestart' => $dateStart,'dateend' => $dateEnd));

			$transactions = array();
			$current_id = '';
	
			foreach($results as $result)
			{
				$result = (array) $result;
				if($result['Invoice #'] != $current_id && $current_id != '')
					$transactions[] = array(
						'Co./Last Name' => '','Inclusive'=>'', 'Invoice #' => '','Date' => '','Customer PO' => '','Delivery Status'=>'','Description' => '','Account #' => '','Amount' => '',
						'Inc-Tax Amount' => '','Job' => '','Journal Memo'=>'','Salesperson Last Name' => '','Salesperson First Name' => '',
						'Referral Source' => '','Tax Code' => '','Tax Amount' => '','Terms - Payment is Due' => '',	'Discount Days' => '', 'Balance Due Days' => '', 'Discount' => '', 'Monthly Charge' => '', 
						'Amount Paid' => '', 'Payment Method' => '','Payment Notes' => ''
					);

				$result['Amount Paid'] = $result['Amount Paid'] > 0 ? $result['Inc-Tax Amount'] : 0;
				$current_id = $result['Invoice #'];
				$transactions[] =  $result;
			}
			
			$transactions[] = array(
				'Co./Last Name' => '','Inclusive'=>'', 'Invoice #' => '','Date' => '','Customer PO' => '','Delivery Status'=>'','Description' => '','Account #' => '','Amount' => '',
				'Inc-Tax Amount' => '','Job' => '','Journal Memo'=>'','Salesperson Last Name' => '','Salesperson First Name' => '',
				'Referral Source' => '','Tax Code' => '','Tax Amount' => '','Terms - Payment is Due' => '',	'Discount Days' => '', 'Balance Due Days' => '', 'Discount' => '', 'Monthly Charge' => '', 
				'Amount Paid' => '', 'Payment Method' => '','Payment Notes' => ''
			);
		}

		if ( $this->input['report_type'] == 'GroupBooking' || 
			$this->input['report_type'] == 'Rebooking' || 
			$this->input['report_type'] == 'All' || 
			 $this->input['report_type'] == 'No Show')
		{
			$sql = "SELECT 
				CASE WHEN ag.name IS NOT NULL THEN ag.name ELSE CASE WHEN cp.name IS NOT NULL THEN cp.name ELSE 'Individuals' END END as `Co./Last Name`,
				'X' as `Inclusive`,
				CONCAT('G', gb.order_id) as `Invoice #`, 
				gb.course_date as `Date`,
				c.id as `Customer PO`,
				'E' as `Delivery Status`,
				CONCAT(c.first_name, ' ', c.last_name) as `Description`,
				co.myob_code as `Account #`,		
				CASE WHEN i.item_type_id = 4
					THEN (SELECT total FROM items WHERE group_booking_id = gb.id and item_type_id = 4 and active = 1)
					ELSE (SELECT sum(CASE WHEN item_type_id = 1 THEN total ELSE CASE WHEN item_type_id = 5 THEN total* -1 ELSE 0 END END) 
						FROM items WHERE group_booking_id = gb.id and active = 1)
				END as `Amount`,	
				CASE WHEN i.item_type_id = 4
					THEN (SELECT total FROM items WHERE group_booking_id = gb.id and item_type_id = 4 and active = 1)
					ELSE (SELECT sum(CASE WHEN item_type_id = 1 THEN total ELSE CASE WHEN item_type_id = 5 THEN total* -1 ELSE 0 END END) 
						FROM items WHERE group_booking_id = gb.id and active = 1)
				END as `Inc-Tax Amount`,	
				CONCAT(l.myob_job_code, '-', co.myob_job_code) as `Job`,
				'' as `Journal Memo`,
				u.last_name as `Salesperson Last Name`,	
				u.first_name as `Salesperson First Name`, 
				rf.name as `Referral Source`, 
				CASE WHEN o.gst > 0 THEN 'GST' ELSE 'FRE' END as `Tax Code`, 
				o.gst as `Tax Amount`, 
				'0' as `Terms - Payment is Due`,		
				'0' as `Discount Days`,		
				'0' as `Balance Due Days`,		
				'0' as `Discount`,		
				'0' as `Monthly Charge`,		
				(SELECT CASE WHEN i.item_type_id = 1 THEN CASE WHEN sum(p.total) > `Amount` THEN `Amount` ELSE sum(p.total) END ELSE i.total END
				FROM payments p JOIN statuses s on s.id = p.status_id WHERE p.order_id = o.id and payment_method_id != 0 and s.name = 'OK' GROUP BY order_id) as `Amount Paid`,		
				(SELECT GROUP_CONCAT(pm.name ORDER BY pm.name ASC SEPARATOR ', ') as payment_method 
				FROM payments p JOIN payment_methods pm on pm.id = p.payment_method_id JOIN statuses s on s.id = p.status_id
				where p.order_id = o.id and payment_method_id != 0 and s.name = 'OK' GROUP BY order_id) as `Payment Method`,
				(SELECT GROUP_CONCAT(p.comments SEPARATOR ', ') FROM payments p JOIN statuses s on s.id = p.status_id where p.order_id = o.id and payment_method_id != 0 and s.name = 'OK' GROUP BY order_id) as `Payment Notes`
				FROM groupbookings gb
				JOIN courses co on co.id = gb.course_id
				JOIN locations l on l.id = gb.location_id
				JOIN customers c on c.id = gb.customer_id
				JOIN orders o on o.id = gb.order_id
				JOIN items i on i.order_id = o.id and i.group_booking_id = gb.id
				LEFT JOIN users u on u.id = o.user_id
				LEFT JOIN referrers_log rl on rl.order_id = o.id
				LEFT JOIN referrers rf on rl.referrer_id = rf.id
				LEFT JOIN agents ag on ag.id = o.agent_id
				LEFT JOIN companies cp on cp.id = o.company_id
				WHERE gb.course_date between :datestart AND :dateend AND 
				i.active = 1 and o.status_id != 4 and i.price > 0 and gb.active = 1 ";
				
			if ( $this->input['report_type'] == 'Rebooking')
				$sql .= " and i.item_type_id = 4 ";
			else
				$sql .= "and (i.item_type_id = 1 OR i.item_type_id = 4) ";
			
			if ( $this->input['report_type'] == 'No Show')
				$sql .= "and gb.course_id = 9 ";
			else
				$sql .= "and gb.course_id != 9 ";

			$sql .= "ORDER BY course_date, gb.order_id";
			
			//Log::info('sql : '. $sql);
			
			$results = DB::select( DB::raw( $sql ), array('datestart' => $dateStart,'dateend' => $dateEnd));

			$current_id = '';
			foreach($results as $result)
			{
				$result = (array) $result;
				if($result['Invoice #'] != $current_id && $current_id != '')
					$transactions[] = array(
						'Co./Last Name' => '','Inclusive'=>'', 'Invoice #' => '','Date' => '','Customer PO' => '','Delivery Status'=>'','Description' => '','Account #' => '','Amount' => '',
						'Inc-Tax Amount' => '','Job' => '','Journal Memo'=>'','Salesperson Last Name' => '','Salesperson First Name' => '',
						'Referral Source' => '','Tax Code' => '','Tax Amount' => '','Terms - Payment is Due' => '',	'Discount Days' => '', 'Balance Due Days' => '', 'Discount' => '', 'Monthly Charge' => '', 
						'Amount Paid' => '', 'Payment Method' => '','Payment Notes' => ''
						);
					
				$result['Amount Paid'] = $result['Amount Paid'] > 0 ? $result['Inc-Tax Amount'] : 0;
				$current_id = $result['Invoice #'];
				$transactions[] =  $result;
			}
			
				$transactions[] = array(
					'Co./Last Name' => '','Inclusive'=>'', 'Invoice #' => '','Date' => '','Customer PO' => '','Delivery Status'=>'','Description' => '','Account #' => '','Amount' => '',
					'Inc-Tax Amount' => '','Job' => '','Journal Memo'=>'','Salesperson Last Name' => '','Salesperson First Name' => '',
					'Referral Source' => '','Tax Code' => '','Tax Amount' => '','Terms - Payment is Due' => '',	'Discount Days' => '', 'Balance Due Days' => '', 'Discount' => '', 'Monthly Charge' => '', 
					'Amount Paid' => '', 'Payment Method' => '','Payment Notes' => ''
				);
		}

		if ($this->input['report_type'] == 'Purchase' || $this->input['report_type'] == 'All')
		{		
			// purchases
			$query = Purchase::select('purchases.*')
				->join('orders', 'orders.id', '=', 'purchases.order_id')
				->whereBetween('orders.order_date', array($dateStart, $dateEnd))
				->where('purchases.active', 1);

			$this->rosters = $query->get();	

			$trans =  $this->getPurchasesTransactions();
			$transactions = array_merge($transactions, $trans);
		}

		if ($this->input['report_type'] == 'VouchersSold')
		{		
			// purchases
			$query = Voucher::select('vouchers.id', 'vouchers.order_id','vouchers.customer_id', 
				'courses.myob_code', 
				'locations.myob_job_code as l_job_code', 
				'courses.myob_job_code as c_job_code',
				'items.gst as voucher_gst',
				'items.total as voucher_total',
				'orders.order_date',
				'orders.user_id',
				'users.first_name as user_first_name',
				'users.last_name as user_last_name',
				DB::raw('CONCAT(customers.first_name, \' \', customers.last_name) as customer_name'))
				->join('orders', 'orders.id', '=', 'vouchers.order_id')
				->join('items', 'items.vouchers_ids', 'LIKE', DB::raw('CONCAT(\'%\', vouchers.id, \'%\')'))
				->join('courses', 'courses.id', '=', 'vouchers.course_id')
				->join('locations', 'locations.id', '=', 'vouchers.location_id')
				->join('customers', 'customers.id', '=', 'vouchers.customer_id')
				->leftJoin('users', 'users.id', '=', 'orders.user_id')
				->whereBetween('orders.order_date', array($dateStart, $dateEnd))
				->where('vouchers.active', 1);

			$this->rosters = $query->get();	

			$trans = $this->getVouchersTransactions();
			$transactions = array_merge($transactions, $trans);
		}
		
		$lookup = Config::get('utils.job_list_lookup', array());
		if(count($lookup) > 0)
		{
			foreach($transactions as &$transaction)
			{
				$transaction['Job'] = empty($lookup[$transaction['Job']]) ? $transaction['Job'] : $lookup[$transaction['Job']];
			}
		}

		return json_decode(json_encode($transactions));
		
	}

	protected function getPurchasesTransactions()
	{
		$transactions = array();
		$current_order = '';	
		
		foreach($this->rosters as $roster)
		{
			if ($current_order != '' && $current_order != $roster->order_id)
				$transactions[] = array(
					'Co./Last Name' => '','Inclusive'=>'', 'Invoice #' => '','Date' => '','Customer PO' => '','Delivery Status'=>'','Description' => '','Account #' => '','Amount' => '',
					'Inc-Tax Amount' => '','Job' => '','Journal Memo'=>'','Salesperson Last Name' => '','Salesperson First Name' => '',
					'Referral Source' => '','Tax Code' => '','Tax Amount' => '','Terms - Payment is Due' => '',	'Discount Days' => '', 'Balance Due Days' => '', 'Discount' => '', 'Monthly Charge' => '', 
					'Amount Paid' => '', 'Payment Method' => '','Payment Notes' => ''
				);
			
			$current_order = $roster->order_id;

			$data = array(
				'Co./Last Name' => 'Individuals',
				'Inclusive'=>'X', 
				'Invoice #' => 'O' . $roster->order_id,
				'Date' => $roster->order->order_date,
				'Customer PO' => $roster->customer_id,
				'Delivery Status'=>'E',
				'Description' => $roster->customer ? $roster->customer->name : 'N/A',
				'Account #' => $roster->myob_code,
				'Amount' => $roster->order->total,
				'Inc-Tax Amount' => $roster->order->total,
				'Job' => $roster->myob_job_code,
				'Journal Memo'=>'',
				'Salesperson Last Name' => $roster->order->user ? $roster->order->user->last_name  : '',
				'Salesperson First Name' => $roster->order->user ? $roster->order->user->first_name : '',
				'Referral Source' => $roster->Referrer ? $roster->referrer->id : '',
				'Tax Code' => 'GST',
				'Tax Amount' => $roster->order->gst,
				'Terms - Payment is Due' => '',	
				'Discount Days' => '', 
				'Balance Due Days' => '', 
				'Discount' => '', 
				'Monthly Charge' => '', 
				'Amount Paid' => $roster->order->paid, 
				'Payment Method' => $roster->order->payment_method,
				'Payment Notes' => ''
			);
			array_push($transactions, $data);
			
		}
		if ($this->rosters->count())
				$transactions[] = array(
					'Co./Last Name' => '','Inclusive'=>'', 'Invoice #' => '','Date' => '','Customer PO' => '','Delivery Status'=>'','Description' => '','Account #' => '','Amount' => '',
					'Inc-Tax Amount' => '','Job' => '','Journal Memo'=>'','Salesperson Last Name' => '','Salesperson First Name' => '',
					'Referral Source' => '','Tax Code' => '','Tax Amount' => '','Terms - Payment is Due' => '',	'Discount Days' => '', 'Balance Due Days' => '', 'Discount' => '', 'Monthly Charge' => '', 
				'Amount Paid' => '', 'Payment Method' => '','Payment Notes' => ''
				);
		
		return $transactions;

	}

	protected function getVouchersTransactions()
	{
		$transactions = array();
		$current_order = '';	
		
		foreach($this->rosters as $roster)
		{
			if ($current_order != '' && $current_order != $roster->order_id)
				$transactions[] = array(
					'Co./Last Name' => '','Inclusive'=>'', 'Invoice #' => '','Date' => '','Customer PO' => '','Delivery Status'=>'','Description' => '','Account #' => '','Amount' => '',
					'Inc-Tax Amount' => '','Job' => '','Journal Memo'=>'','Salesperson Last Name' => '','Salesperson First Name' => '',
					'Referral Source' => '','Tax Code' => '','Tax Amount' => '','Terms - Payment is Due' => '',	'Discount Days' => '', 'Balance Due Days' => '', 'Discount' => '', 'Monthly Charge' => '', 
					'Amount Paid' => '', 'Payment Method' => '','Payment Notes' => ''
					);
			
			$current_order = $roster->order_id;

			$data = array(
				'Co./Last Name' => 'Individuals',
				'Inclusive'=>'X', 
				'Invoice #' => 'V' . $roster->order_id,
				'Date' => $roster->order_date,
				'Customer PO' => $roster->customer_id,
				'Delivery Status'=>'E',
				'Description' => $roster->customer_name,
				'Account #' => $roster->myob_code,
				'Amount' => $roster->voucher_total,
				'Inc-Tax Amount' => $roster->voucher_total,
				'Job' => $roster->l_job_code . $roster->c_job_code,
				'Journal Memo'=>'',
				'Salesperson Last Name' => $roster->user_last_name,
				'Salesperson First Name' => $roster->user_first_name,
				'Referral Source' => '',
				'Tax Code' => 'GST',
				'Tax Amount' => $roster->voucher_gst,
				'Terms - Payment is Due' => '',	
				'Discount Days' => '', 
				'Balance Due Days' => '', 
				'Discount' => '', 
				'Monthly Charge' => '', 
				'Amount Paid' => $roster->order->paid, 
				'Payment Method' => $roster->order->payment_method,
				'Payment Notes' => ''
				);
			array_push($transactions, $data);
			
		}
		if ($this->rosters->count())
			$transactions[] = array(
				'Co./Last Name' => '','Inclusive'=>'', 'Invoice #' => '','Date' => '','Customer PO' => '','Delivery Status'=>'','Description' => '','Account #' => '','Amount' => '',
				'Inc-Tax Amount' => '','Job' => '','Journal Memo'=>'','Salesperson Last Name' => '','Salesperson First Name' => '',
				'Referral Source' => '','Tax Code' => '','Tax Amount' => '','Terms - Payment is Due' => '',	'Discount Days' => '', 'Balance Due Days' => '', 'Discount' => '', 'Monthly Charge' => '', 
				'Amount Paid' => '', 'Payment Method' => '','Payment Notes' => ''
				);
		
		return $transactions;

	}

	
	
}

