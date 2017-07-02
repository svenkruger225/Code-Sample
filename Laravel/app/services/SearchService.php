<?php namespace App\Services;

use Config, Input, Lang, Redirect, Sentry, Validator, View, Response, Exception;
use Customer, GroupBooking, Order, Item, Roster, DB;
use Location, Course, Voucher, Utils, Session, CourseRepeat, CourseBundle, CourseInstance;

class SearchService {

	private $input;

	public function __construct()
	{
		$this->input = Input::all();
		//if (count(Input::all()) == 0)
		//	Session::forget('_old_input');
	}

	public function ProcessBookingSearch()
	{
		$orders = array();
		if (count(Input::all()) == 0)
		{
			Session::forget('_old_input');
			return $orders;
		}

		$query = Order::orderBy('orders.updated_at', 'desc');
		
		if (!empty($this->input['search_text']))
		{
			if ($this->input['search_type'] == 'Order')
			{
				$query = $query->where('id',$this->input['search_text']);
			}
			elseif( $this->input['search_type'] == 'invoice')
			{
				$table = $this->input['search_type'] . 's';
				$query = $query->select('orders.*')->join($table, function($join) use($table)
					{
						$join->on($table .'.order_id', '=', 'orders.id');
					});
				$query = $query->where($table .'.id',$this->input['search_text']);
			}
			elseif( $this->input['search_type'] == 'agent' || $this->input['search_type'] == 'company')
			{
				$table = $this->input['search_type'] == 'company' ? 'companies' : $this->input['search_type'] . 's';
				$key = $this->input['search_type'] . '_id';
				$query = $query->select('orders.*')->join($table, function($join) use($table, $key)
					{
						$join->on($table .'.id', '=', 'orders.' . $key);
					});
				$query = $query->where($table .'.id', $this->input['search_text'])
					->orWhere($table .'.name', 'like', '%' . $this->input['search_text'] .'%');
			}
			elseif( $this->input['search_type'] == 'voucher' || $this->input['search_type'] == 'groupbooking')
			{
				$table = $this->input['search_type'] . 's';
				$key = $this->input['search_type'] == 'groupbooking' ? 'group_booking_id' : $this->input['search_type'] . '_id';
				$query = $query->select('orders.*')->join($table, function($join) use($table, $key)
					{
						$join->on($table .'.order_id', '=', 'orders.id');
					});
				if( $this->input['search_type'] == 'voucher')
					$query = $query->where($table .'.id', $this->input['search_text']);
				
				if( $this->input['search_type'] == 'groupbooking')
					$query = $query->where($table .'.group_name', 'like', '%' . $this->input['search_text'] .'%')->where($table .'.active', 1);

			}
			else
			{			
				$query = $query->select('orders.*')
					->join('rosters', function($join)
						{ $join->on('orders.id', '=', 'rosters.order_id'); })
					->join('customers as c', function($join)
						{ $join->on('c.id', '=', 'orders.customer_id'); })
					->join('customers as c1', function($join)
						{ $join->on('c1.id', '=', 'rosters.customer_id'); });
				
				if ($this->input['search_type'] == 'name') 
				{
					$query = $query
						->where(DB::raw('CONCAT(c.first_name, " ", c.last_name)'), 'like', '%' . $this->input['search_text']  . '%')
						->orWhere(DB::raw('CONCAT(c1.first_name, " ", c1.last_name)'), 'like', '%' . $this->input['search_text']  . '%')
						->distinct();
				}
				elseif ($this->input['search_type'] == 'id')
				{
					$query = $query
						->where('c.id', $this->input['search_text'])
						->orWhere('c1.id', $this->input['search_text'])
						->distinct();
				}
				elseif ($this->input['search_type'] == 'mobile')
				{
					$query = $query
						->where('c.' . $this->input['search_type'], 'like', '%' . str_replace(" ", "", $this->input['search_text']) .'%')
						->orWhere('c1.' . $this->input['search_type'], 'like', '%' . str_replace(" ", "", $this->input['search_text']) .'%')
						->distinct();
				}
				else 
				{
					$query = $query
						->where('c.' . $this->input['search_type'], 'like', '%' . $this->input['search_text'] .'%')
						->orWhere('c1.' . $this->input['search_type'], 'like', '%' . $this->input['search_text'] .'%')
						->distinct();
				}
			}
		}
		
		if( !empty($this->input['status_id']) )
			$query = $query->where('orders.status_id', $this->input['status_id']);
		
		if( !empty($this->input['search_date']) )
			$query = $query->where('orders.order_date', $this->input['search_date']);
		
		if( !empty($this->input['owing']) )
		{
			$query = $query->where('orders.owing', '>', 0);
		}

		//$orders = $query->get();
		//print (count($orders) . "<br>");
		//print_r($orders);
		//			exit();
		
		
		$orders = $query
			->paginate(20)
			->appends(array('status_id'=>$this->input['status_id'], 'search_type'=> $this->input['search_type'], 'search_text'=> $this->input['search_text'], 'search_date'=> $this->input['search_date']));

		return $orders;

	}
	
	public function ProcessBookingSearchByOrderId($id)
	{
		$orders = array();		

		$query = Order::orderBy('orders.updated_at', 'desc');
		
		if (!empty($id))
		{			
			$query = $query->where('id',$id);		
		}
				
		//$orders = $query->get();
		//print (count($orders) . "<br>");
		//print_r($orders);
		//			exit();		
		
		$orders = $query->paginate(20);		
		return $orders;
	}

	public function ProcessVoucherSearch($voucher)
	{
		$vouchers = array();
		if (count(Input::all()) == 0)
		{
			Session::forget('_old_input');
			return $vouchers;
		}
		
		
		$query = $voucher;
			
		if (!empty($this->input['search_text']) && $this->input['search_type'] == 'Voucher')
		{
			$query = $query->where('id',$this->input['search_text']);
		}
		elseif (!empty($this->input['search_text']) && $this->input['search_type'] == 'order_id')
		{
			$query = $query->where('order_id',$this->input['search_text']);
		}
		elseif( !empty($this->input['l_id']) || !empty($this->input['c_id']) || !empty($this->input['from']) || !empty($this->input['to']) || !empty($this->input['status_id']))
		{

			if( !empty($this->input['l_id']) )
			{
				$locations = DB::table('locations')
					->where('id', '=',  $this->input['l_id'])
					->orWhere('parent_id', '=',  $this->input['l_id'])
					->lists('id');
				$query = $query->wherein('location_id', $locations);
			}
			if( !empty($this->input['c_id']) )
				$query = $query->where('course_id', $this->input['c_id']);
				
			if( !empty($this->input['from']) && empty($this->input['to']))
				$this->input['to'] = $this->input['from'];
			if( empty($this->input['from']) && !empty($this->input['to']))
				$this->input['from'] = $this->input['to'];
			if( !empty($this->input['from']) && !empty($this->input['to']) )
				$query = $query->whereBetween('expiry_date', array($this->input['from'], $this->input['to']));

			if( !empty($this->input['status_id']) )
				$query = $query->where('status_id', $this->input['status_id']);

		}

		if (!empty($this->input['search_text']) && $this->input['search_type'] != 'Voucher' && $this->input['search_type'] != 'order_id')
		{			
			$query = $query->select('vouchers.*')->join('customers', function($join)
			{
				$join->on('customers.id', '=', 'vouchers.customer_id');
			});
			if ($this->input['search_type'] == 'name')
				$query = $query->where('first_name', 'like', '%' . $this->input['search_text'] .'%')->orWhere('last_name', 'like', '%' . $this->input['search_text'] .'%');
			elseif ($this->input['search_type'] == 'id')
				$query = $query->where('customers.id', $this->input['search_text']);
			else 
				$query = $query->where($this->input['search_type'], 'like', '%' . $this->input['search_text'] .'%');
		}

		//$vouchers = $query->get();
		//var_dump($vouchers);
		//exit();

		$vouchers = $query
			->orderBy('expiry_date', 'desc')
			->paginate(20)
			->appends(array('status_id'=>$this->input['status_id'], 'search_type'=> $this->input['search_type'], 'search_text'=> $this->input['search_text'],'l_id'=>$this->input['l_id'], 'c_id'=>$this->input['c_id'],'from'=>$this->input['from'],'to'=>$this->input['to']));

		return $vouchers;

	}

	public function ProcessCertificateSearch($certificate)
	{
		$certificates = array();
		if (count(Input::all()) == 0)
		{
			Session::forget('_old_input');
			return $certificates;
		}
		
		
		$query = $certificate;
		
		if (!empty($this->input['search_text']))
		{
			
			if ($this->input['search_type'] == 'Certificate')
			{
				$query = $query->where('id',$this->input['search_text']);
			}

			elseif ($this->input['search_type'] == 'order_id' || $this->input['search_type'] == 'notes')
			{			
				$query = $query->select('certificates.*')->join('rosters', function($join)
					{
						$join->on('rosters.id', '=', 'certificates.roster_id');
					});
				
				if ($this->input['search_type'] == 'notes')
					$query = $query
						->where('notes_admin', 'like', '%' . $this->input['search_text'] .'%')
						->orWhere('notes_class', 'like', '%' . $this->input['search_text'] .'%');
				
				if ($this->input['search_type'] == 'order_id')	
					$query = $query->where('order_id', $this->input['search_text']);
				
			}
			if ($this->input['search_type'] != 'Certificate' && $this->input['search_type'] != 'order_id' && $this->input['search_type'] != 'groupbooking')
			{			
				$query = $query->select('certificates.*')->join('customers', function($join)
					{
						$join->on('customers.id', '=', 'certificates.customer_id');
					});
				if ($this->input['search_type'] == 'name')
					$query = $query->where('first_name', 'like', '%' . $this->input['search_text'] .'%')->orWhere('last_name', 'like', '%' . $this->input['search_text'] .'%');
				elseif ($this->input['search_type'] == 'customer_id')
					$query = $query->where('customers.id', $this->input['search_text']);
				elseif ($this->input['search_type'] == 'dob')
					$query = $query->where('customers.dob', $this->input['search_text']);
				else 
					$query = $query->where($this->input['search_type'], 'like', '%' . $this->input['search_text'] .'%');
			}

		}
		
		if( !empty($this->input['l_id']) || !empty($this->input['c_id']))
		{
			if( !empty($this->input['l_id']) )
			{
				$locations = DB::table('locations')
					->where('id', '=',  $this->input['l_id'])
					->orWhere('parent_id', '=',  $this->input['l_id'])
					->lists('id');
				$query = $query->wherein('location_id', $locations);
			}
			if( !empty($this->input['c_id']) )
				$query = $query->where('course_id', $this->input['c_id']);
			
		}
		//$certificates = $query->get();
		//var_dump($certificates);
		//exit();

		$certificates = $query
			->paginate(20)
			->appends(array('search_type'=> $this->input['search_type'], 'search_text'=> $this->input['search_text'],'l_id'=>$this->input['l_id'], 'c_id'=>$this->input['c_id']));

		return $certificates;

	}

	public function ProcessCourseRepeatSearch()
	{
		$repeats = array();
		//if (count(Input::all()) == 0)
		//	return $repeats;

		$query = CourseRepeat::orderBy('location_id')->orderBy('course_id');

		$input = Session::get('_old_input');
		
		if(Input::get('l_id') !== null)
			$l_id = Input::get('l_id');  
		elseif( $input && array_key_exists('l_id', $input))
			$l_id = $input['l_id'];  
		else
			$l_id = '';  
		
		if(Input::get('c_id') !== null)
			$c_id = Input::get('c_id');  
		elseif( $input && array_key_exists('c_id', $input))
			$c_id = $input['c_id'];  
		else
			$c_id = '';  
		
		if(Input::get('a_id') !== null)
			$a_id = Input::get('a_id');  
		elseif( $input && array_key_exists('a_id', $input))
			$a_id = $input['a_id'];  
		else
			$a_id = '';  
				
		if (isset($l_id) && $l_id != '')
			$query = $query->where('location_id', $l_id);
		
		if (isset($c_id) && $c_id != '')
			$query = $query->where('course_id', $c_id);
		
		if (isset($a_id) && $a_id != '')
			$query = $query->where('active', $a_id);
		
		$repeats = $query->get();
		Session::flashInput(array('l_id'=>$l_id, 'c_id'=>$c_id,'a_id'=>$a_id));
		
		return $repeats;

	}

	public function ProcessCourseBundleSearch()
	{
		$coursebundles = array();
		//if (count(Input::all()) == 0)
		//	return $repeats;

		$query = CourseBundle::orderBy('location_id');

		$input = Session::get('_old_input');
		
		if(Input::get('l_id') !== null)
			$l_id = Input::get('l_id');  
		elseif(count($input) > 0 && !empty($input['l_id']))
			$l_id = $input['l_id'];  
		else
			$l_id = '';  
		
		if(Input::get('c_id') !== null)
			$c_id = Input::get('c_id');  
		elseif(count($input) > 0 && !empty($input['c_id']))
			$c_id = $input['c_id'];  
		else
			$c_id = '';  
		
		$query = $query->with('location','bundles');
		
		if(	$l_id && $l_id != '')
			$query = $query->where('location_id', $l_id);
		
		$coursebundles = $query->get();
		
		if(	$c_id && $c_id != '')
			$coursebundles = $coursebundles->filter(function($course_bundle) use($c_id)
			{
					$courses = $course_bundle->bundles->lists('id');
					return in_array($c_id, $courses);
			});	

		Session::flashInput(array('l_id'=>$l_id, 'c_id'=>$c_id));
		
		return $coursebundles;

	}

	public function ProcessCourseInstanceSearch()
	{
		$courseinstances = array();
		if (count(Input::all()) == 0)
		{
			Session::forget('_old_input');
			return $courseinstances;
		}

		$query = CourseInstance::with('location', 'course', 'instructors', 'special')->orderBy('location_id')->orderBy('course_date');

		$input = Session::get('_old_input');
		
		if(array_key_exists('l_id', Input::all()))
			$l_id = Input::get('l_id');  
		elseif( $input && array_key_exists('l_id', $input))
			$l_id = $input['l_id'];  
		else
			$l_id = '';  
		
		if(array_key_exists('c_id', Input::all()))
			$c_id = Input::get('c_id');  
		elseif($input && array_key_exists('c_id', $input))
			$c_id = $input['c_id'];  
		else
			$c_id = '';  
		
		if(array_key_exists('from', Input::all()))
			$from = Input::get('from');  
		elseif($input && array_key_exists('from', $input))
			$from = $input['from'];  
		else
			$from = '';  
		
		if(array_key_exists('to', Input::all()))
			$to = Input::get('to');  
		elseif($input && array_key_exists('to', $input))
			$to = $input['to'];  
		else
			$to = '';  

		Session::flashInput(array('l_id'=>$l_id, 'c_id'=>$c_id,'from'=>$from,'to'=>$to));

		
		if( (empty($l_id)) && (empty($c_id)) && (empty($from)) &&  (empty($to)))
			$courseinstances = array();
		else
		{
			if(!isset($from) || empty($from))
				$from = date("Y-m-d");

			if(!isset($to) || empty($to))
				$to = date("Y-m-d", strtotime('+1 Week'));
			
			if(!empty($l_id))
				$query = $query->fromLocation($l_id);

			if(!empty($c_id))
				$query = $query->forCourse($c_id);

			$courseinstances = $query->whereBetween('course_date', array($from, $to))
				->paginate(20);

		}		
		return $courseinstances;

	}


	
}

