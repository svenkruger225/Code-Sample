<?php namespace App\Services;

use Config, Input, Lang, Redirect, Sentry, Validator, View, Response, Exception, Session, DateTime, DateInterval;
use CourseRepeat, Courseinstance, Order, Item, ItemType, Roster, Location;
use DB, Mail, Voucher, Log;
use Carbon\Carbon;

class CourseRepeatService {
	
	protected $course_repeat;
	protected $course_repeat_input;
	protected $course_date;

	public function __construct()
	{
		
	}
	
	public function RunAll( )
	{
		$result = array();
		$repeats = CourseRepeat::where('active', 1)->remember(Config::get('cache.minutes', 1))->get();
			
		foreach($repeats as $repeat)
		{
			//array_push($result, array('course_name' => sprintf("%s  %s  %s : %s", $instance->course->name, date('M-d-Y (D)', strtotime($instance->course_date)),$instance->start_time,$instance->end_time)));			
			$msg = $this->Run( $repeat->id );
			array_push($result, array('course_name' => $repeat->course->name, 'message' => $msg));
		}
		return $result;
	}
	
	public function Run( $repeat_id )
	{
		try 
		{
			$this->course_repeat = CourseRepeat::where('id', $repeat_id)->remember(Config::get('cache.minutes', 1))->first();
			$weekdays = array();
			$monthly = false;
			$today = new DateTime();
			$twoYears = clone $today;
			$tomorrow = clone $today;
			$tomorrow->add(new DateInterval('P01D'));
			//$twoYears->add(new DateInterval('P2Y'));
			$twoYears->add(new DateInterval('P6M'));
			$end_date = $this->course_repeat->end_date ? new DateTime($this->course_repeat->end_date) : clone $tomorrow;			
			//$start_date = $this->course_repeat->start_date ? new DateTime($this->course_repeat->start_date) : clone $tomorrow;			
			
			
			if ( $this->course_repeat->active && $end_date >= $today)
			{
				
				if ($this->course_repeat->monday == '1')
					$weekdays = array_add($weekdays, 'monday', '1');
				if ($this->course_repeat->tuesday == '1')
					$weekdays = array_add($weekdays, 'tuesday', '2');
				if ($this->course_repeat->wednesday == '1')
					$weekdays = array_add($weekdays, 'wednesday', '3');
				if ($this->course_repeat->thursday == '1')
					$weekdays = array_add($weekdays, 'thursday', '4');
				if ($this->course_repeat->friday == '1')
					$weekdays = array_add($weekdays, 'friday', '5');
				if ($this->course_repeat->saturday == '1')
					$weekdays = array_add($weekdays, 'saturday', '6');
				if ($this->course_repeat->sunday == '1')
					$weekdays = array_add($weekdays, 'sunday', '7');
				if ($this->course_repeat->monthly == '1')
					$monthly = true;
				
				$counter = 0;
				$updated = 0;
				$msg = '';
				
				$lastdate = $this->course_repeat->start_date ? new DateTime($this->course_repeat->start_date) : clone $today;
				
				$repeat = $this->course_repeat;

				
				foreach ($weekdays as $key => $value)
				{	
					//$twoYearClone =clone $twoYears;
					////get the next day for the given day of the week in two years		
					//$date = $twoYearClone->setISODate($twoYearClone->format('o'), $twoYearClone->format('W'), $value);			
					
					//$lastdate = new DateTime($this->course_repeat->last_instance_date);
					
					//get the next date to process after the last date processed
					$next_weekday = new DateTime(date('Y-m-d', strtotime(' next ' . $key, strtotime($lastdate->format('Y-m-d')))));	
					
					//if monthly checked than we check which  week of the month
					$search_text = Utils::GetDateSearchText($next_weekday->format('Y-m-d'));
					
					$stillOnRange = true;
					if ($next_weekday >= $twoYears)
						$stillOnRange = false; 
					
					while ($stillOnRange)
					{
						$found = Courseinstance::where('course_id', $repeat->course_id)
							->where('location_id', $repeat->location_id)
							->where('course_date', $next_weekday->format('Y-m-d'))
							->where(function($query) use($repeat) { $query->where('time_start', $repeat->start_time)->orWhere('time_start', $repeat->time_start);})
							->where(function($query) use($repeat) { $query->where('time_end', $repeat->end_time)->orWhere('time_end', $repeat->time_end);})
							->first();
						
						if (!$found)
						{
							$this->CreateInstance($next_weekday);	
							$counter++;
						}		
						else
						{
							$this->UpdateInstance($found, $next_weekday);	
							$updated++;
						}		
						
						if ($next_weekday < $twoYears)
						{
							if ($monthly)
							{
								$next_weekday->add(new DateInterval('P01M')); 
								$tc = strtotime("$search_text $key of " . $next_weekday->format('F Y') ); 
								$next_weekday = new DateTime(date("Y-m-d", $tc));
							}
							else
							{
								$next_weekday->add(new DateInterval('P01W')); 
							}
						}
						else
						{
							$stillOnRange = false; 
						}
					}  
				
				}
				
				return $counter . " Instance(s) created.<br>" . $updated . " Instance(s) updated.<br>";
			}
			else
			{
				return "Repeat not active or expired";
			}
		}
		catch (Exception $e)
		{
			\Log::error($end_date->getMessage());
			return Response::json(array(
				'success' => false,
				'Message' => "Problem run course reapeat <br>" . $e->getMessage()
				), 500);
		}
		
	}
	
	public function Update( $courserepeat )
	{
		$today = new DateTime();
		$this->course_repeat_input = array_except(Input::all(), '_method');
		$this->course_repeat_input['start_date'] = empty($this->course_repeat_input['start_date']) ? $today->format('Y-m-d') : $this->course_repeat_input['start_date'];
		$this->course_repeat_input['end_date'] = empty($this->course_repeat_input['end_date']) ? null : $this->course_repeat_input['end_date'];
		$this->course_repeat_input['maximum_students'] += 0;
		$this->course_repeat_input['maximum_alert'] += 0;
		$this->course_repeat_input['time_start'] = date ('h:i A',strtotime($this->course_repeat_input['time_start']));
		$this->course_repeat_input['time_end'] = date ('h:i A',strtotime($this->course_repeat_input['time_end']));

		$this->course_repeat = $courserepeat;
		
		try
		{
			$current_weekdays = array();
			$selected_weekdays = array();
			$weekdays_to_add = array();
			$weekdays_to_delete = array();
			$twoYears = clone $today;
			$tomorrow = clone $today;
			$tomorrow->add(new DateInterval('P01D'));
			$twoYears->add(new DateInterval('P2Y'));
			$end_date = $this->course_repeat->end_date ? new DateTime($this->course_repeat->end_date) : clone $tomorrow;
			$monthly = false;			
			
			if ($this->course_repeat->monday == '1')
				$current_weekdays = array_add($current_weekdays, 'monday', '1');
			if ($this->course_repeat->tuesday == '1')
				$current_weekdays = array_add($current_weekdays, 'tuesday', '2');
			if ($this->course_repeat->wednesday == '1')
				$current_weekdays = array_add($current_weekdays, 'wednesday', '3');
			if ($this->course_repeat->thursday == '1')
				$current_weekdays = array_add($current_weekdays, 'thursday', '4');
			if ($this->course_repeat->friday == '1')
				$current_weekdays = array_add($current_weekdays, 'friday', '5');
			if ($this->course_repeat->saturday == '1')
				$current_weekdays = array_add($current_weekdays, 'saturday', '6');
			if ($this->course_repeat->sunday == '1')
				$current_weekdays = array_add($current_weekdays, 'sunday', '7');
			
			if (Input::get('monday') == '1')
				$selected_weekdays = array_add($selected_weekdays, 'monday', '1');
			if (Input::get('tuesday') == '1')
				$selected_weekdays = array_add($selected_weekdays, 'tuesday', '2');
			if (Input::get('wednesday') == '1')
				$selected_weekdays = array_add($selected_weekdays, 'wednesday', '3');
			if (Input::get('thursday') == '1')
				$selected_weekdays = array_add($selected_weekdays, 'thursday', '4');
			if (Input::get('friday') == '1')
				$selected_weekdays = array_add($selected_weekdays, 'friday', '5');
			if (Input::get('saturday') == '1')
				$selected_weekdays = array_add($selected_weekdays, 'saturday', '6');
			if (Input::get('sunday') == '1')
				$selected_weekdays = array_add($selected_weekdays, 'sunday', '7');
			if (Input::get('monthly') == '1')
				$monthly = true;

			$weekdays_to_add    = array_diff($selected_weekdays, $current_weekdays);
			$weekdays_to_delete = array_diff($current_weekdays, $selected_weekdays);
			
			$counter_to_add = 0;
			$counter_to_delete = 0;
			$counter_to_updated = 0;
			foreach ($weekdays_to_delete as $key => $value)
			{
				$next_weekday = new DateTime(date('Y-m-d', strtotime('next ' . $key)));	
				$stillOnRange = true;
				if ($next_weekday >= $twoYears)
					$stillOnRange = false; 

				while ($stillOnRange)
				{
					$found = Courseinstance::where('course_id', $this->course_repeat->course_id)
						->where('location_id', $this->course_repeat->location_id)
						->where('course_date', $next_weekday->format('Y-m-d'))
						->where(function($query) use($courserepeat) { $query->where('time_start', $courserepeat->start_time)->orWhere('time_start', $courserepeat->time_start);})
						->where(function($query) use($courserepeat) { $query->where('time_end', $courserepeat->end_time)->orWhere('time_end', $courserepeat->time_end);})
						->where('students', 0)
						->first();
					
					if ($found)
					{
						$found->delete();
						$counter_to_delete++;
						//echo sprintf("For: %s = Id: %s, Date: %s, Start: %s, End: %s <br>",$key, $found->id, $found->course_date, $found->time_start, $found->time_end);
					}
					
					if ($next_weekday < $twoYears)
						$next_weekday->add(new DateInterval('P01W')); 
					else
						$stillOnRange = false; 
				}  

			}
			
			foreach ($weekdays_to_add as $key => $value)
			{
				$next_weekday = new DateTime(date('Y-m-d', strtotime('next ' . $key)));	
				//if monthly checked than we check which  week of the month
				$search_text = Utils::GetDateSearchText($next_weekday->format('Y-m-d'));

				$stillOnRange = true;
				if ($next_weekday >= $twoYears)
					$stillOnRange = false; 
				//echo sprintf("Original:  Id: %s, Date: %s, Start: %s, End: %s <br>", $this->course_repeat->id, $next_weekday->format('Y-m-d'), $this->course_repeat->time_start, $this->course_repeat->time_end);

				while ($stillOnRange)
				{
					$found = Courseinstance::where('course_id', $this->course_repeat->course_id)
						->where('location_id', $this->course_repeat->location_id)
						->where('course_date', $next_weekday->format('Y-m-d'))
						->where(function($query) use($courserepeat) { $query->where('time_start', $courserepeat->start_time)->orWhere('time_start', $courserepeat->time_start);})
						->where(function($query) use($courserepeat) { $query->where('time_end', $courserepeat->end_time)->orWhere('time_end', $courserepeat->time_end);})
						->first();
					
					if (!$found)
					{
						$this->CreateInstance($next_weekday);	
						$counter_to_add++;
						//echo sprintf("For: %s = Id: %s, Date: %s, Start: %s, End: %s <br>",$key, $found->id, $found->course_date, $found->time_start, $found->time_end);
					}		
					else
					{
						$this->UpdateInstance($found, $next_weekday);	
						$counter_to_updated++;
					}		
					
					if ($next_weekday < $twoYears)
					{
						if ($monthly)
						{
							$next_weekday->add(new DateInterval('P01M')); 
							$tc = strtotime("$search_text $key of " . $next_weekday->format('F Y') ); 
							$next_weekday = new DateTime(date("Y-m-d", $tc));
						}
						else
						{
							$next_weekday->add(new DateInterval('P01W')); 
						}
					}
					else
					{
						$stillOnRange = false; 
					}

				}  

			}
			
			if ($this->MustUpdate())
			{
				$start_date = $this->course_repeat_input['start_date'];
				$start_date = empty($start_date) ? $today->format('Y-m-d') : $start_date;		
				
				DB::table('courseinstances')->where('course_id', $courserepeat->course_id)
					->where('location_id', $courserepeat->location_id)
					->where(function($query) use($courserepeat) { $query->where('time_start', $courserepeat->start_time)->orWhere('time_start', $courserepeat->time_start);})
					->where(function($query) use($courserepeat) { $query->where('time_end', $courserepeat->end_time)->orWhere('time_end', $courserepeat->time_end);})
					->where('course_date', '>=', $start_date)
					->update(array(
							'location_id'=>$this->course_repeat_input['location_id'], 
							'time_start'=> $this->course_repeat_input['time_start'], 
							'time_end'=> $this->course_repeat_input['time_end'], 
							'maximum_students'=> $this->course_repeat_input['maximum_students'], 
							'maximum_alert'=> $this->course_repeat_input['maximum_alert'], 
							'maximum_auto'=> $this->course_repeat_input['maximum_auto'])
					);
				}
			//echo sprintf("Added: %s, Deleted: %s <br>",$counter_to_add, $counter_to_delete);
			//
			//
			//exit();
			
			
			return true;
		}
		catch (Exception $ex)
		{
			Log::error($ex);
			return false;
		}
		
	}	
	
	public function Delete( $courserepeat )
	{
		$this->course_repeat = $courserepeat;
		
		try
		{
			$current_weekdays = array();

			$today = new DateTime();
			$twoYears = clone $today;
			$tomorrow = clone $today;
			$tomorrow->add(new DateInterval('P01D'));
			$twoYears->add(new DateInterval('P2Y'));
			$end_date = $this->course_repeat->end_date ? new DateTime($this->course_repeat->end_date) : clone $tomorrow;			
			
			if ($this->course_repeat->monday == '1')
				$current_weekdays = array_add($current_weekdays, 'monday', '1');
			if ($this->course_repeat->tuesday == '1')
				$current_weekdays = array_add($current_weekdays, 'tuesday', '2');
			if ($this->course_repeat->wednesday == '1')
				$current_weekdays = array_add($current_weekdays, 'wednesday', '3');
			if ($this->course_repeat->thursday == '1')
				$current_weekdays = array_add($current_weekdays, 'thursday', '4');
			if ($this->course_repeat->friday == '1')
				$current_weekdays = array_add($current_weekdays, 'friday', '5');
			if ($this->course_repeat->saturday == '1')
				$current_weekdays = array_add($current_weekdays, 'saturday', '6');
			if ($this->course_repeat->sunday == '1')
				$current_weekdays = array_add($current_weekdays, 'sunday', '7');

			$counter_to_delete = 0;
			foreach ($current_weekdays as $key => $value)
			{
				$next_weekday = new DateTime(date('Y-m-d', strtotime('next ' . $key)));	
				$stillOnRange = true;
				if ($next_weekday >= $twoYears)
					$stillOnRange = false; 

				while ($stillOnRange)
				{
					$found = Courseinstance::where('course_id', $this->course_repeat->course_id)
						->where('location_id', $this->course_repeat->location_id)
						->where('course_date', $next_weekday->format('Y-m-d'))
						->where(function($query) use($courserepeat) { $query->where('time_start', $courserepeat->start_time)->orWhere('time_start', $courserepeat->time_start);})
						->where(function($query) use($courserepeat) { $query->where('time_end', $courserepeat->end_time)->orWhere('time_end', $courserepeat->time_end);})
						->where('students', 0)
						->first();
					
					if ($found)
					{
						$found->delete();
						$counter_to_delete++;
						//echo sprintf("For: %s = Id: %s, Date: %s, Start: %s, End: %s <br>",$key, $found->id, $found->course_date, $found->time_start, $found->time_end);
					}
					
					if ($next_weekday < $twoYears)
						$next_weekday->add(new DateInterval('P01W')); 
					else
						$stillOnRange = false; 
				}  

			}	
			
			return true;
		}
		catch (Exception $e)
		{
			return false;
		}
		
	}
	
	public function UpdateInstance($instance, $course_date )
	{
		//$instance_data = array(
		//	'course_date'=> $course_date,
		//	'time_start'=> date('h:i A', strtotime($this->course_repeat->time_start)),
		//	'time_end'=> date('h:i A', strtotime($this->course_repeat->time_end)),
		//	'maximum_students'=> $instance->maximum_students > $this->course_repeat->maximum_students ? $instance->maximum_students : $this->course_repeat->maximum_students ,
		//	'maximum_alert'=> $this->course_repeat->maximum_alert,
		//	'maximum_auto'=> $this->course_repeat->maximum_auto,
		//);
	
////
		//$instance->update($instance_data);
		
	}
	
	public function CreateInstance( $course_date )
	{
		$instance_data = array(
			'course_id'=> $this->course_repeat->course_id,
			'location_id'=> $this->course_repeat->location_id,
			'course_date'=> $course_date,
			'time_start'=> date('h:i A', strtotime($this->course_repeat->time_start)),
			'time_end'=> date('h:i A', strtotime($this->course_repeat->time_end)),
			'students'=> '0',
			'maximum_students'=> $this->course_repeat->maximum_students,
			'maximum_alert'=> $this->course_repeat->maximum_alert,
			'maximum_auto'=> $this->course_repeat->maximum_auto,
			'full'=> '0',
			'cancelled'=> '0',
			'active'=> '1',
			);
		
		CourseInstance::create($instance_data);
		
	}

	private function MustUpdate()
	{
		$must_update = false;
		if ($this->course_repeat->location_id != $this->course_repeat_input['location_id'] || 
			$this->course_repeat->start_time != $this->course_repeat_input['time_start'] || 
			$this->course_repeat->end_time != $this->course_repeat_input['time_end'] || 
			$this->course_repeat->maximum_students != $this->course_repeat_input['maximum_students'] || 
			$this->course_repeat->maximum_alert != $this->course_repeat_input['maximum_alert'] || 
			$this->course_repeat->maximum_auto != $this->course_repeat_input['maximum_auto'] )
		{
			$must_update = true;
		}
		return $must_update;
	}


	public function UpdateNoShow()
	{
		try 
		{
			$counter = 0;
			$today = new DateTime();
			$last_day_of_current_month = $today->format("Y-m-t");
			$oneYear = new DateTime($last_day_of_current_month);
			$oneYear->add(new DateInterval('P1Y'));
			$next_month = new DateTime($last_day_of_current_month);
		
			$locations = Location::where('parent_id', 0)->where('active', 1)->lists('id');
			$this->course_repeat = $this->GetNewCourseRepeatInstance();
		
			$stillOnRange = true;			
			while ($stillOnRange)
			{
				foreach($locations as $location_id)
				{
					$found = Courseinstance::where('course_id', 9)
						->where('location_id', $location_id)
						->where('course_date', $next_month->format('Y-m-d'))
						->first();
				
					if (!$found)
					{
						$this->course_repeat->location_id = $location_id;
						$this->CreateInstance($next_month);	
						$counter++;
					}	
				}	
			
				if ($next_month < $oneYear)
					$next_month->modify( 'last day of next month' );
				else
					$stillOnRange = false; 
			}  
			return $counter . " Instance(s) created.<br>";
		}
		catch (Exception $e)
		{
			return Response::json(array(
				'success' => false,
				'Message' => "Problem activating order <br>" . $e->getMessage()
				), 500);
		}

	}

	private function GetNewCourseRepeatInstance()
	{
		$course_repeat = new CourseRepeat();
		$course_repeat->course_id = 9;
		$course_repeat->time_start = '10:00 AM';
		$course_repeat->time_end = '11:00 AM';
		$course_repeat->maximum_students = 1000;
		$course_repeat->maximum_alert = 0;
		$course_repeat->maximum_auto = 0;
		$course_repeat->active = 1;
		return $course_repeat;
	}
	
}