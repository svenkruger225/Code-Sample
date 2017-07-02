<?php namespace App\Services;

use Config, Input, Lang, Redirect, Sentry, Validator, View, Response, Exception, DateTime, DateInterval;
use Customer, Courseinstance, GroupBooking, Order, Item, Roster, DB, Log;
use Location, Course;

class CalendarService {

	private $search_type;
	protected $input;
	protected $items = array();
	protected $locations = array();

	public function __construct()
	{
		$this->input = Input::all();
	}
	
	public function GetMonthClasses()
	{
		$result = array();
		if (empty($this->input['roster_year']) || empty($this->input['roster_month']))
			return $result;
			
		$this->input['from_date'] = date("Y-m-d", strtotime ($this->input['roster_year'] . '-' . $this->input['roster_month'] . '-01'));
		$this->input['to_date'] = date("Y-m-t", strtotime ($this->input['roster_year'] . '-' . $this->input['roster_month'] . '-01'));
		
		$check_date = $this->input['from_date']; 
		$stillOnDateRange = true;

		// get all the parent locations
		if( isset($this->input['location_id']) && $this->input['location_id'] != '')
			$parentLocations = Location::where('id', $this->input['location_id'])->where('active', 1)->remember(Config::get('cache.minutes', 1))->get();
		else
			$parentLocations = Location::where('parent_id', 0)->where('active', 1)->remember(Config::get('cache.minutes', 1))->get();
		
		$instances = $this->filterAllDatesInstances('CourseInstance');
		$insts = array();
		if (count($instances) > 0)
		{
			$row = 1;
			
			foreach($instances as $instance)
			{
				$trainers = array('' => 'Select one or more Instructors') + 
					($instance->course ? $instance->course->instructors()
						->where('business_state', $instance->Location->state)
						->orderBy('first_name')
						->orderBy('last_name')
						->select(\DB::raw('concat (first_name," ",last_name) as full_name,id'))
						->lists('full_name', 'id') : array());
					
					
				$class = ($row % 2 == 0 ? 'evenRow' : 'oddRow') . $instance->course_id;
				$class .= $instance->active != 1 ? ' inactive' : '';
				$insts = array_add($insts, $instance->id, 
					array(
							'type' => 'Public', 
							'class' => $class, 
							'location_id' => $instance->location_id,
							'course_id' => $instance->course_id, 
							'course' => $instance->course->name, 
							'course_date' => $instance->course_date, 
							'time_start' => $instance->start_time, 
							'time_end' => $instance->end_time, 
							'students' => $instance->students, 
							'active' => $instance->active,
							'instructors' => $instance->instructors->count() ? implode('<br>', $instance->instructors->lists('name')) : '',
							'trainerslist' => $trainers,
							'trainers' => $instance->instructors->count() ? $instance->instructors->lists('id') : ''
							)
						);
				$row++;
			}
		}

		// Get the Group entries for parent location
		$instances = $this->filterAllDatesInstances('GroupBooking');
		if (count($instances) > 0)
		{
			$row = 1;
			
			foreach($instances as $instance)
			{
				$trainers = array('' => 'Select one or more Instructors') + 
					($instance->course ? $instance->course->instructors()
					->orderBy('first_name')
					->orderBy('last_name')
					->select(\DB::raw('concat (first_name," ",last_name) as full_name,id'))
					->lists('full_name', 'id') : array());
				$class = 'oddRowGroup';
				$class .= $instance->active != 1 ? ' inactive' : '';
				$insts = array_add($insts, $instance->id, 
					array(
							'type' => 'Group', 
							'class' => $class, 
							'location_id' => $instance->location_id,
							'course_id' => $instance->course_id, 
							'course' => $instance->course->name, 
							'course_date' => $instance->course_date, 
							'time_start' => $instance->start_time, 
							'time_end' => $instance->end_time, 
							'students' => $instance->students, 
							'active' => $instance->active,
							'instructors' => $instance->instructors->count() ? implode(', ', $instance->instructors->lists('name')) : '',
							'trainerslist' => $trainers,
							'trainers' => $instance->instructors->count() ? $instance->instructors->lists('id') : ''
							)
						);
				$row++;
			}
		}

		// we have the date range to work on
		while ($stillOnDateRange)
		{ 

			$parents = array();

			foreach($parentLocations as $parent)
			{
				$list_of_locations = Location::where('id', '=',  $parent->id)
					->orWhere('parent_id', '=',  $parent->id)
					->remember(Config::get('cache.minutes', 1))
					->get();

				$location = array();
				$date_filtered = array_filter($insts, function($val) use($check_date)
					{ 
						return $val['course_date'] == $check_date; 
					});
				
			
				foreach ($list_of_locations as $loc)
				{
					$filtered = array_filter($date_filtered, function($val) use($loc)
					{ 
						return $val['location_id'] == $loc->id; 
					});
					
					$row = 1;
					foreach($filtered as &$class)
					{
						$row_class = $class['type'] == 'Public' ? (($row % 2 == 0 ? 'evenRow' : 'oddRow') . $class['course_id']) : 'oddRowGroup';
						$row_class .= $class['active'] != 1 ? ' inactive' : '';
						$class['class'] = $row_class;
						$row++;
					}
							
					if (count($filtered) > 0)
						$location = array_add($location, $loc->name, $filtered);
				}
				
				if (count($location) > 0)
					$parents = array_add($parents, $parent->name, $location);
				
			}
			
			$result = array_add($result, $check_date, $parents);
			
			if ($check_date != $this->input['to_date'])
				$check_date = date("Y-m-d", strtotime ("+1 day", strtotime($check_date))); 
			else
				$stillOnDateRange = false; 
		}  
		
		//var_dump(\Utils::q());
		//exit();
		
		//var_dump($result);
		//exit();
		
		return $result;		
	}
	
	public function ProcessCalendarTrainers()
	{
		$result = array();
		$today = new DateTime();
		$to_date = clone $today;
		$from_date = clone $today;			
		//$from_date = $today->setISODate($today->format('o'), $today->format('W'), 1);			
		$to_date->add(new DateInterval('P04W'));
		
		$this->input['from_date'] = $from_date->format("Y-m-d");
		$this->input['to_date'] =  $to_date->format("Y-m-d");
		$this->input['status_id'] = '1';
		$this->input['instructor'] = Sentry::getUser()->id;
		
		$check_date = $this->input['from_date']; 
		$stillOnDateRange = true;

		// we have the date range to work on
		while ($stillOnDateRange)
		{ 
			// get all the parent location
			$parentLocations = Location::where('parent_id', 0)->where('active', 1)->remember(Config::get('cache.minutes', 1))->lists('name', 'id');

			$parents = array();
			
			foreach($parentLocations as $parentId => $parentName)
			{
				$location = array();
				// Get the Public entries for parent location
				$instances = $this->filterInstances('CourseInstance', $parentId, $check_date);
				$insts = array();
				if (count($instances) > 0)
					$insts += $this->getCalendarInstance($instances, 'Public');

				// Get the Group entries for parent location
				$instances = $this->filterInstances('GroupBooking', $parentId, $check_date);
				if (count($instances) > 0)
					$insts += $this->getCalendarInstance($instances, 'Group');
			
				if (count($insts) > 0)
					$location = array_add($location, $parentName, $insts);			


				$locations = Location::where('parent_id', $parentId)->where('active', 1)->remember(Config::get('cache.minutes', 1))->lists('name', 'id');
				foreach($locations as $locationId => $locationName)
				{
					$insts = array();
					$instances = $this->filterInstances('CourseInstance', $locationId, $check_date);
					if (count($instances) > 0)
						$insts += $this->getCalendarInstance($instances, 'Public');

					// Get the Group entries for child location
					$instances = $this->filterInstances('GroupBooking', $locationId, $check_date);
					if (count($instances) > 0)
						$insts += $this->getCalendarInstance($instances, 'Group');
					
					if (count($insts) > 0)
						$location = array_add($location, $locationName, $insts);
					
				}
				
				if (count($location) > 0)
					$parents = array_add($parents, $parentName, $location);
			}
			$result = array_add($result, $check_date, $parents);
			
			if ($check_date != $this->input['to_date'])
				$check_date = date("Y-m-d", strtotime ("+1 day", strtotime($check_date))); 
			else
				$stillOnDateRange = false; 
		}  
		
		return $result;		
	}
	
	public function ProcessCalendarAgents()
	{
		$result = array();
		$today = new DateTime();
		$to_date = clone $today;
		$to_date->add(new DateInterval('P01W'));
		
		$this->input['from_date'] = $today->format("Y-m-d");
		$this->input['to_date'] =  $to_date->format("Y-m-d");
		$this->input['status_id'] = '1';
		$this->input['location_id'] = '1';
		$this->input['course_id'] = '1';
		$user_id= Sentry::getUser()->id;
		
		$agent = \Agent::where('user_id', $user_id)->first();
		
		//var_dump(\Utils::q());
		//exit();
		//
		
		foreach ($agent->courses as $special)
		{
			$this->input['location_id'] = $special->location_id;
			$this->input['course_id'] = $special->course_id;
			$result += $this->filterAgentInstances();
		}
	
		return $result;		
	}
	protected function filterAgentInstances() 
	{
		$instances = CourseInstance::where('location_id', $this->input['location_id'])
			->where('course_id', $this->input['course_id'])
			->whereBetween('course_date', array($this->input['from_date'], $this->input['to_date']))
			->where('cancelled', 0)
			->where('active', 1)
			->orderBy('location_id')->orderBy('course_id')->orderBy('course_date')
		->get();

		$result = array();
		foreach($instances as $instance)
		{
			$result = array_add($result, $instance->id, array(
				'course_id' => $instance->course_id, 
				'short_name' => $instance->course->short_name, 
				'location_name' => $instance->location->name, 
				'course_name' => $instance->course->name, 
				'course_date' => $instance->course_date, 
				'start_time' => $instance->start_time, 
				'vacancies' => $instance->vacancies 
				));
		}

		return $result;				
	}

	protected function filterAllDatesInstances($ClassType) 
	{
		//filter by group booking only
		if(isset($this->input['course_id']) &&  $this->input['course_id'] == '0' && $ClassType != 'GroupBooking')
			return;
		
		$date_field_name = $ClassType == 'Purchase' ? 'date_hire' : 'course_date';
		if ($ClassType == 'Purchase')
			$query = $ClassType::whereBetween($date_field_name, array($this->input['from_date'], $this->input['to_date']));
		else
			$query = $ClassType::with('course', 'rosters','instructors')->whereBetween($date_field_name, array($this->input['from_date'], $this->input['to_date']));
		
		if( !empty($this->input['status_id']))
			$query = $query->where('active', $this->input['status_id']);

		if( !empty($this->input['course_id']) && $ClassType != 'Purchase')
			$query = $query->where('course_id', $this->input['course_id']);
		
		$instances = $query->get();
		
		if( !empty($this->input['instructor']) && $ClassType != 'Purchase')
		{
			$instructor_id = $this->input['instructor'];
			$instances = $instances->filter(function($item) use($instructor_id)
				{
					if (count($item->instructors) > 0)
						foreach($item->instructors as $instructor)
						{
							if ($instructor->id == $instructor_id)
								return $item;
						}
				});
		}

		return $instances;				
	}

	protected function filterAllInstances($ClassType, $locations_list, $check_date) 
	{
		$date_field_name = $ClassType == 'Purchase' ? 'date_hire' : 'course_date';
		if ($ClassType == 'Purchase')
			$query = $ClassType::whereIn('location_id', $locations_list)->where($date_field_name, $check_date);
		else
			$query = $ClassType::with('course', 'rosters','instructors')->whereIn('location_id', $locations_list)->where($date_field_name, $check_date);
		
		if( !empty($this->input['status_id']))
			$query = $query->where('active', $this->input['status_id']);

		if( !empty($this->input['course_id']) && $ClassType != 'Purchase')
			$query = $query->where('course_id', $this->input['course_id']);
		
		$instances = $query->get();
		
		if( !empty($this->input['instructor']) && $ClassType != 'Purchase')
		{
			$instructor_id = $this->input['instructor'];
			$instances = $instances->filter(function($item) use($instructor_id)
				{
					if (count($item->instructors) > 0)
						foreach($item->instructors as $instructor)
						{
							if ($instructor->id == $instructor_id)
								return $item;
						}
				});
		}

		return $instances;				
	}

	protected function filterInstances($ClassType, $location_id, $check_date) 
	{
		$date_field_name = $ClassType == 'Purchase' ? 'date_hire' : 'course_date';
		//$instances = $ClassType::where('location_id', $location_id)->where($date_field_name, $check_date)->get();
		$query = $ClassType::where('location_id', $location_id)->where($date_field_name, $check_date);
		
		if( !empty($this->input['status_id']))
			$query = $query->where('active', $this->input['status_id']);

		if( !empty($this->input['course_id']) && $ClassType != 'Purchase')
			$query = $query->where('course_id', $this->input['course_id']);
		
		$instances = $query->get();
		
		if( !empty($this->input['instructor']) && $ClassType != 'Purchase')
		{
			$instructor_id = $this->input['instructor'];
			$instances = $instances->filter(function($item) use($instructor_id)
				{
					if (count($item->instructors) > 0)
						foreach($item->instructors as $instructor)
						{
							if ($instructor->id == $instructor_id)
								return $item;
						}
				});
		}

		return $instances;				
	}

	protected function getCalendarInstance($instances, $entry_type) 
	{
		$insts = array();
		$row = 1;
		
		foreach($instances as $instance)
		{
			$paid = 0;
			if($instance->course_id != '9')
				foreach($instance->rosters as $roster)
					if ($roster->paid > 0)
						$paid++;
			
			$class = $entry_type == 'Public' ? (($row % 2 == 0 ? 'evenRow' : 'oddRow') . $instance->course_id) : 'oddRowGroup';
			$class .= $instance->active != 1 ? ' inactive' : '';
			$insts = array_add($insts, $instance->id, 
				array(
					'type' => $entry_type, 
					'location_id' => $instance->location_id,
					'class' => $class, 
					'course_id' => $instance->course_id, 
					'course' => $instance->course->name, 
					'course_date' => $instance->course_date, 
					'time_start' => $instance->start_time, 
					'time_end' => $instance->end_time, 
					'students' => $instance->students, 
					'is_course_accredited' => $instance->is_course_accredited,
					'paid' => $paid, 
					'maximum_students' => $entry_type == 'Public' ? $instance->maximum_students : 0, 
					'maximum_auto' => $entry_type == 'Public' ? $instance->maximum_auto : 0, 
					'full' => $entry_type == 'Public' ? $instance->full : true,
					'special' => $instance->special && $instance->special->active == '1' ? 1 : 0,
					'active' => $instance->active,
					'instructors' => $instance->instructors->count() ? $instance->instructors->lists('name') : array()
				)
			);
			$row++;
		}
		
		return $insts;				
	}

	protected function getCalendarPurchase($instances, $entry_type) 
	{
		$insts = array();
		$row = 1;
		
		foreach($instances as $instance)
		{
			$class = 'oddRowPurchase';
			$class .= $instance->active != 1 ? ' inactive' : '';
			$insts = array_add($insts, $instance->id, array(
				'type' => $entry_type, 
				'location_id' => $instance->location->id,
				'class' => $class, 
				'course_id' => $instance->id, 
				'course' => 'Machine Hire', 
				'course_date' => $instance->date_hire, 
				'time_start' => null, 
				'time_end' => null, 
				'students' => null, 
				'is_course_accredited' => false,
				'paid' => null, 
				'maximum_students' =>null, 
				'maximum_auto' => null, 
				'full' => null,
				'special' => $instance->special && $instance->special->active ? 1 : 0,
				'active' => $instance->active,
				'instructors' => array()
				));
			$row++;
		}
		
		return $insts;				
	}
	
	public function getClassesWithoutTrainer()
	{
		$tomorrow = new DateTime('tomorrow');
		$results = array();
		$instances = CourseInstance::where('course_date', $tomorrow->format('Y-m-d'))->where('active', 1)->get();
		Log::info(sprintf("CourseInstance instances: %s", $instances->count()));
		
		foreach($instances as $instance)
		{
			if ($instance->instructors->count() == 0)
			{
				$result['course_type'] = 'Public';
				$result['course_name'] = $instance->course->name;
				$result['location'] = $instance->location->name;
				$result['class'] = date('d/n/Y', strtotime($instance->course_date)) . ' at ' . $instance->start_time . ' : ' . $instance->end_time;
				array_push($results, $result);
			}
		}		
		$instances = GroupBooking::where('course_date', $tomorrow->format('Y-m-d'))->where('active', 1)->get();
		
		Log::info(sprintf("GroupBooking instances: %s", $instances->count()));
		
		foreach($instances as $instance)
		{
			if ($instance->instructors->count() == 0)
			{
				$result['course_type'] = 'Group';
				$result['course_name'] = $instance->course->name;
				$result['location'] = $instance->location->name;
				$result['class'] = date('d/n/Y', strtotime($instance->course_date)) . ' at ' . $instance->start_time . ' : ' . $instance->end_time;
				array_push($results, $result);
			}
			
		}	
		
		return $results;	
		
	}

	
	public function ProcessCalendarSearch()
	{
		$result = array();
		$instances = array();
		$groups = array();
		$purchases = array();

		if (!empty($this->input['single_date']))
		{
			$this->input['from_date'] = date("Y-m-d", strtotime ($this->input['single_date']));
			$this->input['to_date'] = date("Y-m-d", strtotime ($this->input['single_date']));
		}
		else
		{
			if (empty($this->input['to_date']))
				$this->input['to_date'] = date("Y-m-d");
			else
				$this->input['to_date'] = date("Y-m-d", strtotime ($this->input['to_date']));
			
			if (empty($this->input['from_date']))
				$this->input['from_date'] = date("Y-m-d", strtotime ($this->input['to_date']));
			else
				$this->input['from_date'] = date("Y-m-d", strtotime ($this->input['from_date']));
		}
		
		$check_date = $this->input['from_date']; 
		$stillOnDateRange = true;
		
		if( !isset($this->input['course_id']) || ($this->input['course_id'] != '0' && $this->input['course_id'] != '99'))
		{	
			$instances = $this->getAllCourseInstances();	
		}

		if( !isset($this->input['course_id']) || $this->input['course_id'] != '99')
		{	
			$groups = $this->getAllGroupBookings();
		}
		$instances = array_merge($instances, $groups);
		
		if( !isset($this->input['course_id']) || $this->input['course_id'] != '0')
		{		
			$purchases = $this->getAllPurchases();
		}
		
		$instances = array_merge($instances, $purchases);
		
		Utils::osort($instances, array('course_date','parentOrder', 'childOrder'));
		
		//print_r($instances);
		//exit();

		$row = 0;
		
		$insts = array();
		foreach ($instances as $instance)
			$insts = array_add($insts, $instance->id, (array) $instance);
		
		//print_r($inst);
		//exit();
		$parentLocations = Location::where('parent_id', 0)
							->where('active', 1)
							->remember(Config::get('cache.minutes', 1))
							->get();

		while ($stillOnDateRange)
		{ 
			$parents = array();
			foreach($parentLocations as $parent)
			{
				$list_of_locations = Location::where(function($query) use($parent) { 
						$query->where('id', $parent->id)->orWhere('parent_id', $parent->id);
					})
					->remember(Config::get('cache.minutes', 1))
					->get();

				$location = array();
				$date_filtered = array_filter($insts, function($val) use($check_date) { 
						return $val['course_date'] == $check_date; 
					});
				
				
				foreach ($list_of_locations as $loc)
				{
					$filtered = array_filter($date_filtered, function($val) use($loc) { 
							return $val['location_id'] == $loc->id; 
						});
					
					$row = 1;
					foreach($filtered as &$class)
					{
						if ($class['type'] == 'Public')
							$class['class'] =  ($row % 2 == 0 ? 'evenRow' : 'oddRow') . $class['course_id'];
						
						$class['class'] .= $class['active'] != 1 ? ' inactive' : '';
						
						$row++;
					}
					
					if (count($filtered) > 0)
						$location = array_add($location, $loc->name, $filtered);
				}
				
				if (count($location) > 0)
					$parents = array_add($parents, $parent->name, $location);
				
			}
			
			$result = array_add($result, $check_date, $parents);
			
			if ($check_date != $this->input['to_date'])
				$check_date = date("Y-m-d", strtotime ("+1 day", strtotime($check_date))); 
			else
				$stillOnDateRange = false; 
		}  

		//var_dump(\Utils::q());
		//exit();
		
		//print_r($result);
		//exit();
		
		return $result;		
	}

	private function getAllCourseInstances()
	{
		
		$sql = "SELECT '' as class, 
				ci.location_id, 
				CASE WHEN lp.id IS NULL THEN l.order ELSE lp.order END as parentOrder, 
				CASE WHEN lp.id IS NULL THEN l.name ELSE lp.name END as parentLocation, 
				l.id as childOrder, 
				l.name as childLocation,
				ci.id as id, 
				'Public' as type, 
				c.id as course_id, 
				c.name as course,
				CASE WHEN c.certificate_code = '' THEN 0 ELSE 1 END as is_course_accredited,
				(SELECT COUNT(*) FROM rosters r WHERE r.course_instance_id = ci.id) as students,
				(SELECT sum(case 
				when (o.total <= (select sum(total) from payments p where p.order_id = r.order_id and p.status_id = 8 )) then 1 
				when EXISTS(select 1 from payments p where p.order_id = r.order_id and p.payment_method_id = 7 limit 1 ) then 1
				else 0 end) as paid 
				FROM rosters r 
				JOIN orders o on r.order_id = o.id where r.course_instance_id = ci.id and o.status_id != 4
				) as paid,
				ci.maximum_students, ci.full, ci.maximum_auto, ci.course_date, ci.time_start, ci.time_end,
				(SELECT 1 FROM courseinstance_specials cis where cis.course_instance_id = ci.id AND cis.active limit 1) as special,
				ci.active,
				(SELECT GROUP_CONCAT(CONCAT(u.first_name, ' ', u.last_name)) As names FROM course_instance_instructor cii JOIN users u on u.id = cii.user_id where cii.course_instance_id = ci.id GROUP BY cii.course_instance_id) as instructors
				FROM courseinstances ci
				JOIN courses c on c.id = ci.course_id
				JOIN locations l on l.id = ci.location_id
				LEFT JOIN locations lp on lp.id = l.parent_id
				WHERE ci.course_date between '" .$this->input['from_date']."' and '".$this->input['to_date']."' ";

		if( isset($this->input['location_id']) && $this->input['location_id'] != '' && $this->input['location_id'] != 'On Site')
			$sql .= "AND (l.id = '". $this->input['location_id'] ."' OR l.parent_id = '". $this->input['location_id'] ."') ";	
		else if( isset($this->input['location_id']) && $this->input['location_id'] != '' && $this->input['location_id'] == 'On Site')
			$sql .= "AND l.name = 'On Site' ";	

		if( isset($this->input['course_id']) && $this->input['course_id'] != '')
			$sql .= "AND c.id = '". $this->input['course_id'] ."' ";	

		if( isset($this->input['status_id']) && $this->input['status_id'] != '')
			$sql .= "AND ci.active = '". $this->input['status_id'] ."' ";	
		
		$sql .= "ORDER BY ci.course_date, CASE WHEN lp.id IS NULL THEN l.order ELSE lp.order END, l.id;";		

		$result = DB::select( $sql );
		return $result;
		
	}

	private function getAllGroupBookings()
	{
		$sql = "SELECT 'oddRowGroup' as class, 
				gb.location_id, CASE WHEN lp.id IS NULL THEN l.order ELSE lp.order END as parentOrder, CASE WHEN lp.id IS NULL THEN l.name ELSE lp.name END as parentLocation, l.id as childOrder, l.name as childLocation,
				gb.id as id, 'Group' as type, c.id as course_id, c.name as course,
				CASE WHEN c.certificate_code = '' THEN 0 ELSE 1 END as is_course_accredited,
				gb.students,
				(SELECT sum(case when (o.total <= (select sum(total) from payments p where p.order_id = r.order_id and p.status_id = 8 )) then 1 else 0 end) as paid FROM rosters r JOIN orders o on r.order_id = o.id where r.course_instance_id = gb.id and o.status_id != 4) as paid,
				0 as maximum_students, 0 as full, 0 as maximum_auto, gb.course_date, gb.time_start, gb.time_end,
				0 as special,
				gb.active,
				(SELECT GROUP_CONCAT(CONCAT(u.first_name, ' ', u.last_name)) As names FROM group_booking_instructor gbi JOIN users u on u.id = gbi.user_id where gbi.group_booking_id = gb.id GROUP BY gbi.group_booking_id) as instructors
				FROM groupbookings gb
				JOIN courses c on c.id = gb.course_id
				JOIN locations l on l.id = gb.location_id
				LEFT JOIN locations lp on lp.id = l.parent_id
				WHERE gb.course_date between '" .$this->input['from_date']."' and '".$this->input['to_date']."' ";

		if( isset($this->input['location_id']) && $this->input['location_id'] != '' && $this->input['location_id'] != 'On Site')
			$sql .= "AND (l.id = '". $this->input['location_id'] ."' OR l.parent_id = '". $this->input['location_id'] ."') ";	

		else if( isset($this->input['location_id']) && $this->input['location_id'] != '' && $this->input['location_id'] == 'On Site')
			$sql .= "AND l.name = 'On Site' ";	

		if( isset($this->input['course_id']) && $this->input['course_id'] != '' && $this->input['course_id'] != '0')
			$sql .= "AND c.id = '". $this->input['course_id'] ."' ";	

		if( isset($this->input['status_id']) && $this->input['status_id'] != '')
			$sql .= "AND gb.active = '". $this->input['status_id'] ."' ";	
		
		$sql .= "ORDER BY gb.course_date, CASE WHEN lp.id IS NULL THEN l.order ELSE lp.order END, l.id;";		

		$result = DB::select( $sql );
		return $result;
	}

	private function getAllPurchases()
	{
		
		$sql = "SELECT 'oddRowPurchase' as class, 
				pur.location_id, CASE WHEN lp.id IS NULL THEN l.order ELSE lp.order END as parentOrder, CASE WHEN lp.id IS NULL THEN l.name ELSE lp.name END as parentLocation, l.id as childOrder, l.name as childLocation,
				pur.id as id, 'Purchase' as type, pur.id as course_id, 'Machine Hire' as course,
				0 as is_course_accredited,
				null as students,
				null as paid,
				null as maximum_students, 0 as full, 0 as maximum_auto, pur.date_hire as course_date, null as time_start, null as time_end,
				0 as special,
				pur.active,
				'' as instructors
				FROM purchases pur
				JOIN locations l on l.id = pur.location_id
				LEFT JOIN locations lp on lp.id = l.parent_id
				WHERE pur.date_hire between '" .$this->input['from_date']."' and '".$this->input['to_date']."' ";

		if( isset($this->input['location_id']) && $this->input['location_id'] != '' && $this->input['location_id'] != 'On Site')
			$sql .= "AND (l.id = '". $this->input['location_id'] ."' OR l.parent_id = '". $this->input['location_id'] ."') ";	
		else if( isset($this->input['location_id']) && $this->input['location_id'] != '' && $this->input['location_id'] == 'On Site')
			$sql .= "AND l.name = 'On Site' ";	

		if( isset($this->input['status_id']) && $this->input['status_id'] != '')
			$sql .= "AND pur.active = '". $this->input['status_id'] ."' ";	
		
		$sql .= "ORDER BY pur.date_hire, CASE WHEN lp.id IS NULL THEN l.order ELSE lp.order END, l.id;";		

		$result = DB::select( $sql );
		return $result;
	}





	
}

