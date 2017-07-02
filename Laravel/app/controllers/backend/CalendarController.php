<?php namespace Controllers\Backend;

use AdminController;
use Cartalyst\Sentry\Users\LoginRequiredException;
use Cartalyst\Sentry\Users\PasswordRequiredException;
use Cartalyst\Sentry\Users\UserExistsException;
use Cartalyst\Sentry\Users\UserNotFoundException;
use Config, Input, Lang, Redirect, Sentry, Validator, View, DB, Response;
use Location, CourseInstance, Course, Roster, GroupBooking, Utils;
use CalendarService;

class CalendarController extends AdminController {
	
	
	public function index() 
	{
		return $this->search();
	}
	
	public function search() 
	{
		
		$start = Utils::getmicrotime();
		$result = CalendarService::ProcessCalendarSearch();	
		
		//var_dump(\Utils::q());
		//exit();
		
		$locations = array('' => 'All Locations','On Site' => 'On Site') + Location::where('parent_id', 0)->remember(Config::get('cache.minutes', 1))->lists('name', 'id');		
		$courses = array('' => 'Courses: All Types','0' => 'Group Course Bookings') + Course::where('active', 1)->remember(Config::get('cache.minutes', 1))->orderBy('name')->lists('name', 'id') + array('99' => 'Purchases') ;		
		$statuses = array('1'=>'Bookings: Active', '0'=>'Bookings: Inactive', ''=> 'Bookings: All');
		
		$methods = array('' => 'Select Method') + \PaymentMethod::remember(Config::get('cache.minutes', 1))->lists('name', 'id');
		$pay_statuses = array('' => 'Select Status') + \Status::where('status_type','Payment')->remember(Config::get('cache.minutes', 1))->lists('name', 'id');
		$agents = array('' => 'Select Agent') + \Agent::remember(Config::get('cache.minutes', 1))->lists('name', 'id');
		$methodsCode = \PaymentMethod::where('active', 1)->orderBy('name')->remember(Config::get('cache.minutes', 1))->lists('name', 'code');

		$types = Config::get('utils.document_types', array());

		Input::flash();
		
		$titles = array('' => 'Select Title') + \Config::get('utils.titles', array());;
		$languages = array('' => 'Select Language') + \AvetmissLanguage::orderBy('name')->lists('name','id');
		$countries = array('' => 'Select Country') + \AvetmissCountry::orderBy('name')->lists('name','id');
		$states = array('' => 'Select State') + \AvetmissState::orderBy('name')->lists('name','id');
		$achievements_list = \AvetmissAchievement::orderBy('id')->get(array('id','name'))->toArray();
		$disabilities_list = \AvetmissDisability::orderBy('id')->get(array('id','name'))->toArray();
		$study_reasons_list = \AvetmissStudyReason::orderBy('id')->get(array('id','name'))->toArray();
		$usi_visa_issue_countries = array('' => 'Select Country') + \UsiVisaIssueCountry::orderBy('name')->lists('name','id');
		return View::make('backend.calendar.search-result', compact('start', 'locations', 'courses', 'statuses','methods', 'pay_statuses', 'agents', 'result', 'methodsCode',
			'titles', 'types', 'languages','countries','states','achievements_list','disabilities_list','study_reasons_list','usi_visa_issue_countries'));
	}
	
	public function trainers() {
		
		$result = CalendarService::ProcessCalendarTrainers();	
		$methodsCode = \PaymentMethod::where('active', 1)->orderBy('name')->lists('name', 'code');
		
		$titles = array('' => 'Select Title') + \Config::get('utils.titles', array());;
		$languages = array('' => 'Select Language') + \AvetmissLanguage::orderBy('name')->lists('name','id');
		$countries = array('' => 'Select Country') + \AvetmissCountry::orderBy('name')->lists('name','id');
		$states = array('' => 'Select State') + \AvetmissState::orderBy('name')->lists('name','id');
		$achievements_list = \AvetmissAchievement::orderBy('name')->get(array('id','name'))->toArray();
		$disabilities_list = \AvetmissDisability::orderBy('id')->get(array('id','name'))->toArray();
		$study_reasons_list = \AvetmissStudyReason::orderBy('id')->get(array('id','name'))->toArray();
		$usi_visa_issue_countries = array('' => 'Select Country') + \UsiVisaIssueCountry::orderBy('name')->lists('name','id');
		
		return View::make('backend.calendar.trainers-result', compact('result', 'methodsCode', 'languages',
			'titles', 'countries', 'states','achievements_list','disabilities_list','study_reasons_list','usi_visa_issue_countries'));
	}
	
	public function agents() {
		
		$result = CalendarService::ProcessCalendarAgents();	
		
		return View::make('backend.calendar.agents-result', compact('result'));
	}
	
	public function getClassList($id, $type) 
	{
		$class_name = $type == 'public' ? 'CourseInstance' : 'GroupBooking';
		$id_name = $type == 'public' ? 'course_instance_id' : 'group_booking_id';
		$result = array();
		$all = array();
		$paid_entries = array();
		$owing_entries = array();
		$class_details = array();
		
		$courseInstance = $class_name::find($id);
		$instructors = array();
		if (count($courseInstance->instructors) > 0 )
		{
			foreach ($courseInstance->instructors as $instructor)
			{
				array_push($instructors, $instructor->name . '(' . $instructor->mobile . ')');							
			}
		}
		$class_details = array(
			'id' => $id,
			'type' => $type,
			'course_id' => $courseInstance->course_id,
			'location' => $courseInstance->location->name,
			'location_fax' => $courseInstance->location->fax,
			'name' => $courseInstance->course->name, 
			'course_pair' => $courseInstance->course->pair_course_id,
			'course_date'=> $courseInstance->course_date, 
			'time_start'=>$courseInstance->time_start, 
			'time_end'=>$courseInstance->time_end, 
			'instructors'=> $instructors
		);
		
		if ($type == 'group')
		{
			$class_details['groupname'] = $courseInstance->group_name;
			$class_details['groupcontact'] = $courseInstance->customer ? $courseInstance->customer->full_name : '';
			$class_details['groupphone'] = $courseInstance->customer ? $courseInstance->customer->phone . ' - Mob: (' . $courseInstance->customer->mobile . ')' : '';
			$class_details['groupfax'] = $courseInstance->customer ? $courseInstance->customer->fax : '';
			$class_details['groupcourse'] = $courseInstance->customer ? $courseInstance->course->short_name : '';
			$class_details['groupstudents'] = $courseInstance->students;
			$class_details['groupnotes'] = $courseInstance->notes;
		}

		$students = Roster::where($id_name, $id)->get();
		$students = $students->filter(function($student)
			{
				if(!$student->order->isCancelled())
				{
					return $student;
				}
			});
		
		
		$students = $students->sortBy(function($student)
			{
				return $student->customer ? $student->customer->full_name : $student->id;
			});

		$paid_usi = 0;
		$notpaid_usi = 0;
		foreach($students as $student)
		{
			$items = $student->order->active_items;
			$instance = $items->filter(function($item) use($student, $id_name)
			{
				if($item->$id_name == $student->$id_name)
				{
					return $item;
				}
			})->first();
			
			$is_agent_to_pay = strpos($student->order->payment_method, "Agent To Pay") !== false;
			//if ($student->order && $student->order->status_id == 1 && $student->order->invoice && $student->order->invoice->status_id == 3)
			//\Log::debug('ROSTER: ' . $student->item_id . ' : ' .  $student->instance->id . ' : ' . $student->order->id );
			if ($student->order && ($student->paid >= $student->price || $is_agent_to_pay))
			{
				$paid_usi += $student->customer ? ($student->customer->usi_verified ? 1 : 0) : 0;
				$paid_entries = array_add($paid_entries, $student->id, array(
					'name' => $student->customer ? $student->customer->full_name : '', 
					'phone'=> $student->customer ? $student->customer->mobile . ',' . $student->customer->phone : '', 
					'paid' => $is_agent_to_pay ? $student->paid : $student->price, 
					'owing'=> $is_agent_to_pay ? $student->owing : 0.00, 
					'fh'=> $student->food_hygiene, 
					'usi'=> $student->customer ? ($student->customer->usi_verified ? 'YES' : 'NO') : 'NO', 
					'avetmiss'=> $student->customer ? ($student->customer->avetmiss_done ? 'YES' : 'NO') : 'NO', 
					'notes'=> ($is_agent_to_pay && $student->order->paid < $student->order->total ? '<small>AGENT</small>' : '') . $student->notes_class, 
					'needs'=> $student->customer ? $student->customer->question3 . ', English Level: ' . $student->customer->lang_eng_level : '', 
					'cert' => '/backend/certificates/' . $student->customer_id . '/' .  $id,
					'order_id' => $student->order_id
					));
			}
			else
			{
				$notpaid_usi += $student->customer ? ($student->customer->usi_verified ? 1 : 0) : 0;
				$owing_entries = array_add($owing_entries, $student->id, array(
					'name' => $student->customer ? $student->customer->full_name : '', 
					'phone'=> $student->customer ? $student->customer->mobile . ',' . $student->customer->phone : '', 
					'paid' => $student->paid, 
					'owing'=> $student->owing, 
					'fh'=> $student->food_hygiene, 
					'usi'=> $student->customer ? ($student->customer->usi_verified ? 'YES' : 'NO') : 'NO', 
					'avetmiss'=> $student->customer ? ($student->customer->avetmiss_done ? 'YES' : 'NO') : 'NO', 
					'notes'=> $student->notes_class, 
					'needs'=> $student->customer ? $student->customer->question3 . ', English Level: ' . $student->customer->lang_eng_level : '', 
					'cert' => '/backend/certificates/' . $student->customer_id . '/' .  $id,
					'order_id' => $student->order_id
					));
			}
			if ($type == 'group')
			{
				$all = array_add($all, $student->id, array(
					'name' => $student->customer ? $student->customer->full_name : '', 
					'fh'=> $student->food_hygiene, 
					'usi'=> $student->customer ? ($student->customer->usi_verified ? 'YES' : 'NO') : 'NO', 
					'avetmiss'=> $student->customer ? ($student->customer->avetmiss_done ? 'YES' : 'NO') : 'NO', 
					'notes'=> $student->notes_class, 
					'needs'=> $student->customer ? $student->customer->question3 . ', English Level: ' . $student->customer->lang_eng_level : '', 
					'cert' => '/backend/certificates/' . $student->customer_id . '/' .  $id,
					'order_id' => $student->order_id
					));
			}			
		}
		$class_details['paid_usi'] = $paid_usi;
		$class_details['paid_usi_perc'] = count($paid_entries ) > 0 ? round( ($paid_usi/ count($paid_entries )*100), 2 ) : 0;
		$class_details['notpaid_usi'] = $notpaid_usi;
		$class_details['notpaid_usi_perc'] = count($owing_entries) > 0 ? round( ($notpaid_usi / count($owing_entries)*100), 2 ) : 0;
		
		$result = array_add($result, 'details', $class_details);
		$result = array_add($result, 'paid', $paid_entries);
		$result = array_add($result, 'owing', $owing_entries);
		$result = array_add($result, 'all', $all);
		
		
		//var_dump($result);
		//exit();
		if ($type == 'public')
			return View::make('backend.calendar.class-list', compact('result'));
		else
			return View::make('backend.calendar.class-list-group', compact('result'));
		
	}
	
	public function getClassOlgrList($id, $type, $csv = null) 
    {
		if ($csv)
		{
			return $this->getOlgrClassList($id, $type, $csv);
		}	
		
		$class_name = $type == 'public' ? 'CourseInstance' : 'GroupBooking';
		$id_name = $type == 'public' ? 'course_instance_id' : 'group_booking_id';
		$result = array();
		$all = array();
		$paid_entries = array();
		$owing_entries = array();
		$class_details = array();
		
		$courseInstance = $class_name::find($id);
		$instructors = array();
		if (count($courseInstance->instructors) > 0 )
		{
			foreach ($courseInstance->instructors as $instructor)
			{
				array_push($instructors, $instructor->name . '(' . $instructor->mobile . ')');							
			}
		}
		$class_details = array(
			'id' => $id,
			'type' => $type,
			'course_id' => $courseInstance->course_id,
			'course_name' => $courseInstance->course->short_name,
			'state' => $courseInstance->location->state,
			'location' => $courseInstance->location->name,
			'location_fax' => $courseInstance->location->fax,
			'name' => $courseInstance->course->name, 
			'course_pair' => $courseInstance->course->pair_course_id,
			'course_date'=> $courseInstance->course_date, 
			'time_start'=>$courseInstance->time_start, 
			'time_end'=>$courseInstance->time_end, 
			'instructors'=> $instructors
			);
		
		if ($type == 'group')
		{
			$class_details['groupname'] = $courseInstance->group_name;
			$class_details['groupcontact'] = $courseInstance->customer ? $courseInstance->customer->full_name : '';
			$class_details['groupphone'] = $courseInstance->customer ? $courseInstance->customer->phone . ' - Mob: (' . $courseInstance->customer->mobile . ')' : '';
			$class_details['groupfax'] = $courseInstance->customer ? $courseInstance->customer->fax : '';
			$class_details['groupcourse'] = $courseInstance->customer ? $courseInstance->course->short_name : '';
			$class_details['groupstudents'] = $courseInstance->students;
			$class_details['groupnotes'] = $courseInstance->notes;
		}

		$students = Roster::where($id_name, $id)->get();
		$students = $students->filter(function($student)
			{
				if(!$student->order->isCancelled())
				{
					return $student;
				}
			});
		
		
		$students = $students->sortBy(function($student)
			{
				return $student->customer ? strtolower($student->customer->full_name) : strtolower($student->full_name);
			});

		$paid_usi = 0;
		$notpaid_usi = 0;
		$paid_no_usi = 0;
		$notpaid_no_usi = 0;
		$paidTotal = 0;
		$notPaidTotal = 0;
		
		foreach($students as $student)
		{
			$items = $student->order->active_items;
			$instance = $items->filter(function($item) use($student, $id_name)
				{
					if($item->$id_name == $student->$id_name)
					{
						return $item;
					}
				})->first();
			
			$is_agent_to_pay = strpos($student->order->payment_method, "Agent To Pay") !== false;
			//if ($student->order && $student->order->status_id == 1 && $student->order->invoice && $student->order->invoice->status_id == 3)
			//\Log::debug('ROSTER: ' . $student->item_id . ' : ' .  $student->instance->id . ' : ' . $student->order->id );
		
			$olgr_data = array(
				'title' => $student->customer->title, 
				'gender' => $student->customer->gender, 
				'surname' => $student->customer ? $student->customer->last_name : '', 
				'middle' => $student->customer ? $student->customer->middle_name : '', 
				'given' => $student->customer ? $student->customer->first_name : '', 
				'dob' => $student->customer ? $student->customer->dob : '', 
				'country'=>  $student->customer && $student->customer->country_birth ? $student->customer->country_birth->name : '', 
				'mobile' => $student->customer ? $student->customer->mobile : '', 
				'email'=> $student->customer ? $student->customer->email : '', 
				
				'unit'=> $student->customer ? $student->customer->address_unit_details : '', 
				'number'=> $student->customer ? $student->customer->address_building_name .  (empty($student->customer->address_building_name) ? '' : '/') . $student->customer->address_street_number : '', 
				'address'=> $student->customer ? $student->customer->address_street_name : '', 
				'suburb'=> $student->customer ? $student->customer->city : '', 
				'state'=> $student->customer && $student->customer->state_obj ? $student->customer->state_obj->code : '', 
				'post_code' => $student->customer ? $student->customer->post_code : ''
			);

			if ($student->order && ($student->paid >= $student->price || $is_agent_to_pay))
			{
				$paid_usi += $student->customer ? ($student->customer->usi_verified ? 1 : 0) : 0;
				$paid_no_usi += $student->customer ? (!$student->customer->usi_verified && !$student->customer->avetmiss_done ? 1 : 0) : 1;
				$paidTotal +=  ($is_agent_to_pay ? $student->paid : $student->price) + ($is_agent_to_pay ? $student->owing : 0.00);
                $entry = $olgr_data + array(
					'name' => $student->customer ? $student->customer->full_name : '', 
					'phone'=> $student->customer ? $student->customer->mobile . ',' . $student->customer->phone : '', 
					'paid' => $is_agent_to_pay ? $student->paid : $student->price, 
					'owing'=> $is_agent_to_pay ? $student->owing : 0.00, 
					'fh'=> $student->food_hygiene, 
					'usi'=> $student->customer ? ($student->customer->usi_verified ? 'YES' : 'NO') : 'NO', 
					'avetmiss'=> $student->customer ? ($student->customer->avetmiss_done ? 'YES' : 'NO') : 'NO', 
					'notes'=> ($is_agent_to_pay && $student->order->paid < $student->order->total ? '<small>AGENT<br />'.$student->order->agent_name.'</small>' : '') . $student->notes_class,
					'needs'=> $student->customer ? $student->customer->question3 . ', English Level: ' . $student->customer->lang_eng_level : '', 
					'cert' => '/backend/certificates/' . $student->customer_id . '/' .  $id,
					'order_id' => $student->order_id
				);			
				$paid_entries = array_add($paid_entries, $student->id, $entry);
			}
			else
			{
				$notpaid_usi += $student->customer ? ($student->customer->usi_verified ? 1 : 0) : 0;
				$notpaid_no_usi += $student->customer ? (!$student->customer->usi_verified && !$student->customer->avetmiss_done ? 1 : 0) : 1;
				$notPaidTotal +=  $student->paid + $student->owing;			
				
				$entry = $olgr_data + array(					
					'name' => $student->customer ? $student->customer->full_name : '', 
					'phone'=> $student->customer ? $student->customer->mobile . ',' . $student->customer->phone : '', 
					'paid' => $student->paid, 
					'owing'=> $student->owing, 
					'fh'=> $student->food_hygiene, 
					'usi'=> $student->customer ? ($student->customer->usi_verified ? 'YES' : 'NO') : 'NO', 
					'avetmiss'=> $student->customer ? ($student->customer->avetmiss_done ? 'YES' : 'NO') : 'NO', 
					'notes'=> $student->notes_class, 
					'needs'=> $student->customer ? $student->customer->question3 . ', English Level: ' . $student->customer->lang_eng_level : '', 
					'cert' => '/backend/certificates/' . $student->customer_id . '/' .  $id,
					'order_id' => $student->order_id
					);			

				$owing_entries = array_add($owing_entries, $student->id, $entry);
			}
			if ($type == 'group')
			{		
				$entry = $olgr_data + array(					
					'name' => $student->customer ? $student->customer->full_name : '', 
					'fh'=> $student->food_hygiene, 
					'usi'=> $student->customer ? ($student->customer->usi_verified ? 'YES' : 'NO') : 'NO', 
					'avetmiss'=> $student->customer ? ($student->customer->avetmiss_done ? 'YES' : 'NO') : 'NO', 
					'notes'=> $student->notes_class, 
					'needs'=> $student->customer ? $student->customer->question3 . ', English Level: ' . $student->customer->lang_eng_level : '', 
					'cert' => '/backend/certificates/' . $student->customer_id . '/' .  $id,
					'order_id' => $student->order_id
					);			
				$all = array_add($all, $student->id, $entry);
			}			
		}
		
		$class_details['paid_usi'] = $paid_usi;
		$class_details['paid_usi_perc'] = count($paid_entries ) > 0 ? round( ($paid_usi/ count($paid_entries )*100), 2 ) : 0;
		$class_details['paid_no_usi'] = $paid_no_usi;
		$class_details['paid_no_usi_perc'] = count($paid_entries ) > 0 ? round( ($paid_no_usi/ count($paid_entries )*100), 2 ) : 0;
		
		$class_details['notpaid_usi'] = $notpaid_usi;
		$class_details['notpaid_usi_perc'] = count($owing_entries) > 0 ? round( ($notpaid_usi / count($owing_entries)*100), 2 ) : 0;
		$class_details['notpaid_no_usi'] = $notpaid_no_usi;
		$class_details['notpaid_no_usi_perc'] = count($owing_entries) > 0 ? round( ($notpaid_no_usi / count($owing_entries)*100), 2 ) : 0;
		$class_details['paidtotal'] = $paidTotal;
		$class_details['notPaidTotal'] = $notPaidTotal;
		
		$result = array_add($result, 'details', $class_details);
		$result = array_add($result, 'paid', $paid_entries);
		$result = array_add($result, 'owing', $owing_entries);
		$result = array_add($result, 'all', $all);
		
		
		//\Log::debug(json_encode($result));
		//exit();
		if ($type == 'public')
		{
			return View::make('backend.calendar.class-olgr-list', compact('result'));
		}
		else
		{
			return View::make('backend.calendar.class-olgr-list-group', compact('result'));
		}
		
	}
	
	public function getOlgrClassList($id, $type, $csv = null) 
	{
		$class_name = $type == 'public' ? 'CourseInstance' : 'GroupBooking';
		$id_name = $type == 'public' ? 'course_instance_id' : 'group_booking_id';
		$result = array();
		$all_entries = array();
		$class_details = array();
		
		$courseInstance = $class_name::find($id);
		$instructors = array();
		if (count($courseInstance->instructors) > 0 )
		foreach ($courseInstance->instructors as $instructor)
		{
			array_push($instructors, $instructor->name . '(' . $instructor->mobile . ')');							
		}
		$class_details = array(
			'id' => $id,
			'type' => $type,
			'course_id' => $courseInstance->course_id,
			'course_name' => $courseInstance->course->short_name,
			'state' => $courseInstance->location->state,
			'location' => $courseInstance->location->name,
			'location_fax' => $courseInstance->location->fax,
			'name' => $courseInstance->course->name, 
			'course_pair' => $courseInstance->course->pair_course_id,
			'course_date'=> $courseInstance->course_date, 
			'time_start'=>$courseInstance->time_start, 
			'time_end'=>$courseInstance->time_end, 
			'instructors'=> $instructors
			);
		
		if ($type == 'group')
		{
			$class_details['groupname'] = $courseInstance->group_name;
			$class_details['groupcontact'] = $courseInstance->customer ? $courseInstance->customer->full_name : '';
			$class_details['groupphone'] = $courseInstance->customer ? $courseInstance->customer->phone . ' - Mob: (' . $courseInstance->customer->mobile . ')' : '';
			$class_details['groupfax'] = $courseInstance->customer ? $courseInstance->customer->fax : '';
			$class_details['groupcourse'] = $courseInstance->customer ? $courseInstance->course->short_name : '';
			$class_details['groupstudents'] = $courseInstance->students;
			$class_details['groupnotes'] = $courseInstance->notes;
		}

		$students = Roster::where($id_name, $id)->get();
		$students = $students->filter(function($student)
			{
				if(!$student->order->isCancelled())
				{
					return $student;
				}
			});
		
		
		$students = $students->sortBy(function($student)
			{
				return $student->customer ? $student->customer->full_name : $student->id;
			});

		foreach($students as $student)
		{
			$items = $student->order->active_items;
			$instance = $items->filter(function($item) use($student, $id_name)
				{
					if($item->$id_name == $student->$id_name)
					{
						return $item;
					}
				})->first();
			
			$is_agent_to_pay = strpos($student->order->payment_method, "Agent To Pay") !== false;
			//if ($student->order && $student->order->status_id == 1 && $student->order->invoice && $student->order->invoice->status_id == 3)
			$all_entries = array_add($all_entries, $student->id, array(
				'title' => $student->customer->title, 
				'gender' => $student->customer->gender, 
				'surname' => $student->customer ? $student->customer->last_name : '', 
				'given' => $student->customer ? $student->customer->first_name : '', 
				'middle' => $student->customer ? $student->customer->middle_name : '', 
				'dob' => $student->customer ? $student->customer->dob : '', 
				'country'=>  $student->customer && $student->customer->country_birth ? $student->customer->country_birth->name : '', 
				'mobile' => $student->customer ? $student->customer->mobile : '', 
				'email'=> $student->customer ? $student->customer->email : '', 
				
				'unit'=> $student->customer ? $student->customer->address_unit_details : '', 
				'number'=> $student->customer ? $student->customer->address_building_name .  (empty($student->customer->address_building_name) ? '' : '/') . $student->customer->address_street_number : '', 
				'address'=> $student->customer ? $student->customer->address_street_name : '', 
				'suburb'=> $student->customer ? $student->customer->city : '', 
				'state'=> $student->customer && $student->customer->state_obj ? $student->customer->state_obj->code : '', 
				'post_code' => $student->customer ? $student->customer->post_code : '', 
				));
			
		}
		$result = array_add($result, 'details', $class_details);
		$result = array_add($result, 'all', $all_entries);
		
		
		//var_dump($result);
		//exit();

		if ($csv)
		{
			$filename = 'olgr-' . $class_details['course_name'] . '-' . $class_details['course_date'] . '.csv';
			return \CSV::fromArray($result['all'])->stream( $filename);
		}
		else
		{
			return View::make('backend.calendar.olgr-class-list', compact('result'));
		}
		
	}
	
	public function getClassListUpdate($id, $type) 
	{
		$class_name = $type == 'public' ? 'CourseInstance' : 'GroupBooking';
		$id_name = $type == 'public' ? 'course_instance_id' : 'group_booking_id';
		$result = array();
		$paid_entries = array();
		$owing_entries = array();
		$class_details = array();
		
		$courseInstance = $class_name::find($id);
		$instructors = array();
		if (count($courseInstance->instructors) > 0 )
			foreach ($courseInstance->instructors as $instructor)
			{
				array_push($instructors, $instructor->name . '(' . $instructor->mobile . ')');							
			}
		$class_details = array(
			'location' => $courseInstance->location->name,
			'name' => $courseInstance->course->name, 
			'course_date'=> $courseInstance->course_date, 
			'time_start'=>$courseInstance->time_start, 
			'time_end'=>$courseInstance->time_end, 
			'instructors'=> $instructors
			);
		
		if ($type == 'group')
		{
			$class_details['groupname'] = $courseInstance->group_name;
			$class_details['groupcontact'] = $courseInstance->customer ? $courseInstance->customer->full_name : '';
			$class_details['groupphone'] = $courseInstance->customer ? $courseInstance->customer->phone . ' - Mob: (' . $courseInstance->customer->mobile . ')' : '';
			$class_details['groupfax'] = $courseInstance->customer ? $courseInstance->customer->fax : '';
			$class_details['groupcourse'] = $courseInstance->customer ? $courseInstance->course->short_name : '';
			$class_details['groupstudents'] = $courseInstance->students;
			$class_details['groupnotes'] = $courseInstance->notes;
		}

		$students = Roster::where($id_name, $id)->get();
		$students = $students->filter(function($student)
			{
				if(!$student->order->isCancelled())
				{
					return $student;
				}
			});
		
		
		$students = $students->sortBy(function($student)
			{
				return $student->customer ? $student->customer->full_name : $student->id;
			});

		foreach($students as $student)
		{
			$items = $student->order->active_items;
			$instance = $items->filter(function($item) use($student, $id_name)
				{
					if($item->$id_name == $student->$id_name)
					{
						return $item;
					}
				})->first();
			
			$is_agent_to_pay = strpos($student->order->payment_method, "Agent To Pay") !== false;
			//if ($student->order && $student->order->status_id == 1 && $student->order->invoice && $student->order->invoice->status_id == 3)
			if ($student->order && ($student->paid >= $student->price || $is_agent_to_pay))
			{
				$paid_entries = array_add($paid_entries, $student->id, array(
					'name' => $student->customer ? $student->customer->full_name : '', 
					'phone'=> $student->customer ? $student->customer->mobile . ',' . $student->customer->phone : '', 
					'paid' => $is_agent_to_pay ? $student->paid : $student->price, 
					'owing'=> $is_agent_to_pay ? $student->owing : 0.00, 
					'notes'=> $student->notes_class, 
					'needs'=> $student->customer ? $student->customer->question1.','.$student->customer->question2.','.$student->customer->question3 : '', 
					'cert' => '/backend/certificates/' . $student->customer_id . '/' .  $id,
					'order_id' => $student->order_id
					));
			}
			else
			{
				$owing_entries = array_add($owing_entries, $student->id, array(
					'name' => $student->customer ? $student->customer->full_name : '', 
					'phone'=> $student->customer ? $student->customer->mobile . ',' . $student->customer->phone : '', 
					'paid' => $student->paid, 
					'owing'=> $student->owing, 
					'notes'=> $student->notes_class, 
					'needs'=> $student->customer ? $student->customer->question1.','.$student->customer->question2.','.$student->customer->question3 : '', 
					'cert' => '/backend/certificates/' . $student->customer_id . '/' .  $id,
					'order_id' => $student->order_id
					));
			}
			
		}
		$result = array_add($result, 'details', $class_details);
		$result = array_add($result, 'paid', $paid_entries);
		$result = array_add($result, 'owing', $owing_entries);
		
		
		//var_dump($result);
		//exit();
		if ($type == 'public')
			return View::make('backend.calendar.class-list-update', compact('result'));
		else
			return View::make('backend.calendar.class-list-group', compact('result'));
		
	}
	
	public function getReconcile($id, $type) 
	{
		$class_name = $type == 'public' ? 'CourseInstance' : 'GroupBooking';
		$id_name = $type == 'public' ? 'course_instance_id' : 'group_booking_id';
		$result = array();
		$paid_entries = array();
		$owing_entries = array();
		$class_details = array();
		
		$courseInstance = $class_name::find($id);
		$instructors = array();
		if (count($courseInstance->instructors) > 0 )
			foreach ($courseInstance->instructors as $instructor)
			{
				array_push($instructors, $instructor->name . '(' . $instructor->mobile . ')');							
			}
		$class_details = array(
			'location' => $courseInstance->location->name,
			'name' => $courseInstance->course->name, 
			'course_date'=> $courseInstance->course_date, 
			'time_start'=>$courseInstance->time_start, 
			'time_end'=>$courseInstance->time_end, 
			'instructors'=> $instructors
			);

		$students = Roster::where($id_name, $id)->get();
		$students = $students->filter(function($student) { if(!$student->order->isCancelled()) { return $student; } }); // just active
		$students = $students->sortBy(function($student) { return $student->customer ? $student->customer->full_name : $student->id; }); // order by name

		foreach($students as $student)
		{
			$items = $student->order->active_items;
			$instance = $items->filter(function($item) use($student, $id_name) { if($item->$id_name == $student->$id_name) { return $item; } })->first();
			
			$is_agent_to_pay = strpos($student->order->payment_method, "Agent To Pay") !== false;
				
			if ($student->order && ($student->paid >= $student->price || $is_agent_to_pay))
			{
				$paid_entries = array_add($paid_entries, $student->id, array(
					'type'=> '',	
					'name' => $student->customer->fullName, 
					'phone'=> $student->customer->mobile . ',' . $student->customer->phone, 
					'paid' => $is_agent_to_pay ? $student->paid : $student->price, 
					'owing'=> $is_agent_to_pay ? $student->owing : 0.00, 
					'notes'=> $student->notes_class, 
					'needs'=> $student->customer->question1.','.$student->customer->question2.','.$student->customer->question3, 
					'cert' => '/backend/certificates/' . $student->customer->id . '/' .  $id,
					'roster_id' => $student->id,
					'order_id' => $student->order_id

					));
			}
			else
			{
				$owing_entries = array_add($owing_entries, $student->id, array(
					'type'=> '',	
					'name' => $student->customer->fullName, 
					'phone'=> $student->customer->mobile . ',' . $student->customer->phone, 
					'paid' => $student->paid, 
					'owing'=> $student->owing , 
					'notes'=> $student->notes_class, 
					'needs'=> $student->customer->question1.','.$student->customer->question2.','.$student->customer->question3, 
					'cert' => '/backend/certificates/' . $student->customer->id . '/' .  $id,
					'roster_id' => $student->id,
					'order_id' => $student->order_id
					));
			}
			
		}
		$result = array_add($result, 'details', $class_details);
		$result = array_add($result, 'paid', $paid_entries);
		$result = array_add($result, 'owing', $owing_entries);
		
		
		//var_dump($result);
		//exit();
		
	return View::make('backend.calendar.reconcile-list', compact('result'));	}

	
	public function flushCache() 
	{
		\Cache::flush();
		return Redirect::back()->with('success', 'Chache  has been cleared');
	}

}