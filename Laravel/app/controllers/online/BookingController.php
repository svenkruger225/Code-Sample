<?php namespace Controllers\Online;

use OnlineAuthController;
use App\Services\BookingPublic;
use App\Services\BookingGroup;
use App\Services\BookingPurchase;
use App\Services\BookingOnline;
use Request,Config, Input, Lang, Redirect, Sentry, Validator, View, DB, Response, Exception, stdClass;
use CourseBundle, CoursePrice, Agent, Company, Location, Course, OnlineCourse, CourseInstance, Order, Item, Invoice;
use Customer, Status, Voucher, Product, Marketing, CmsPage;
use OrderService, SearchService, PdfService, Utils, Log;

class BookingController extends OnlineAuthController {

	protected $data;

	public function __construct()
	{
		parent::__construct();
		$this->data = \OnlineService::InitialiseContentData();
		$this->first_visit = true;	
	}

	public function getAssessment($course_id, $order_id = null)
	{	
		if(\App::environment('production') && !Request::secure())
		{
			return Redirect::secure(Request::getRequestUri());
		}
		
		$courses = OnlineCourse::with('prices')
			->where('type', 'Online')
			->where('active', 1)
			->orderBy('order')
			->remember(Config::get('cache.minutes', 1))
			->get();
		
		$this->data->student->current_online_roster = $course_id;
		
		if(!$this->data->student->current_online_roster)
		{
			return Redirect::to('/online')
			->with('error', 'Invalid course');
		}
		
		if ($this->data->student->current_online_roster->course->IsCompleted())
		{

			$order_type = 'PublicOnline';
			$order = null;
			if (!empty($order_id) )
			{
				$order = \Order::find($order_id);
			}
			if ($order && $order->isPaid())
			{
				$data = $this->data;					
				return View::make('online/bookings/public/booking-payment', compact('data', 'order_id', 'order_type'));
			}
			else 
			{
				if ($this->data->student->current_online_roster->course->assessment_type == 'FaceToFace')
				{
					$locations = DB::table('locations')
						->join('courseinstances','courseinstances.location_id','=','locations.id')
						->join('courses','courseinstances.course_id','=','courses.id')
						->where('courses.type', 'Online')
						->distinct()
						->remember(Config::get('cache.minutes', 1))
						->select('locations.*')->get();
						
					$dateStart = date("Y-m-d");
					$dateEnd = date("Y-m-d", strtotime("+3 month", time()));
					
					/*print_r($locations);
					exit();*/
					foreach ($locations as $location)
					{
						$course = Course::with(array('prices', 'instances' => function($query) use($dateStart, $dateEnd, $location){
								return $query->where('location_id', $location->id)
								->whereBetween('course_date', array($dateStart, $dateEnd))
								->where('active', 1)
								->orderBy('course_date');
							}))
							//->where('type', 'Online')
							->where('id', $course_id)->first();	
							
						$location->course = $course;		
						
						//print_r($location);
					}
					
					//print_r($course);
					//exit();
					$data = $this->data;					
					return View::make('online/bookings/facetoface/booking', compact('data', 'locations', 'order_id', 'order_type'));
				}
				elseif  ($this->data->student->current_online_roster->course->assessment_type == 'Review')
				{
					$data = $this->data;					
					return View::make('online/bookings/facetoface/booking', compact('data', 'course', 'order_id', 'order_type'));
				}
				elseif  ($this->data->student->current_online_roster->course->assessment_type == 'Online')
				{
					//TODO View Certificate
					$certificate = \Certificate::where('roster_id', $this->data->student->current_online_roster->id)->first();
					if (!$certificate)
					{
						$certificate = \CertificateService::updateOnlineCertificate($this->data->student);
					}
					$filename = 'certificate-' . $certificate->id . '.pdf';
					$filepath = storage_path() . '/certificates/' . $filename;
					
					if (!file_exists($filepath))
					{
						PdfService::save('Certificate', 'online.public.certificate-online', '/certificates/', 'certificate-', $certificate->id);
					}

					
					return Utils::ViewFile($filepath, $filename, 10);
				}
				else
				{
					throw new Exception("Invalid Course Assessment type, please contact Coffee School Offices");
				}
				
			}
		}
		else 
		{
			//return Redirect::to('/online/course/' . $student->current_online_roster->course->short_name)
			return Redirect::to('/online/course/results/' . $this->data->student->current_online_roster->course->id);
		}
		
	}
	
	public function getBookingDetails($id) {

		$order = Order::find($id);
		$main_location = 0;
		
		$invoice = array();
		if ($order && $order->current_invoice)
		{
			$invoice = array(
				'id'=> $order->current_invoice->id,
				'order_id' => $order->id,
				'invoice_date' => $order->current_invoice->invoice_date,
				'total' => $order->total,
				'paid' => $order->paid,
				'owing' => $order->owing
				);
		}
		
		$instances = array();
		if ($order && $order->active_items->count())
		{
			foreach ($order->active_items as $item)
			{
				if (!$item->active) 
				continue;
				
				$notes_admin = '';
				$notes_class = '';
				$students = array();
				if (Utils::ItemTypeName($item->item_type_id) == 'OnlineCourse' )
				{
					$inst = Course::find($item->course_instance_id);
					$main_location = $main_location != 0 ? $main_location : $inst->location_id;
					$rosters = \OnlineRoster::with('customer')
						->where('order_id', $item->order_id)
						->where('course_id',$item->course_instance_id)
						->get();
					$student_count = 1;
					foreach ($rosters as $roster)
					{
						$notes_admin = $roster->notes_admin;
						$notes_class = $roster->notes_class;
						$student = array(
							'id'=> $roster->customer_id,
							'order' => $student_count,
							'courseInstance' => $roster->course_id,
							'FirstName' => $roster->customer->first_name,
							'LastName' => $roster->customer->last_name,
							'Dob' => $roster->customer->dob,
							'Phone' => $roster->customer->phone,
							'Mobile' => $roster->customer->mobile,
							'Email' => $roster->customer->email,
							'LangLevel' => !empty($roster->customer->lang_eng_level) ? $roster->customer->lang_eng_level : '.',
							'mail_out' => $roster->customer->mail_out_email == '1' ? true : false
							);
						array_push($students, $student);
						$student_count++;
					}

					$instance = array(
						'id' => $item->id,
						'courseType' => $inst->course_id,
						'courseInstance' => $item->course_instance_id,
						'itemType' => $inst->itemtype ? $inst->itemtype->name : 'OnlineCourse',
						'location' => 0,
						'parentLocation' => 0,
						'parentLocationName' => 'Online',
						'full' => 0,
						'courseName' => $inst->name . ' Online',
						'courseAddress' => 'Online',
						'courseDate' => '',
						'time_start' => '',
						'time_end' => '',
						
						'studentQty' => $item->qty,
						'discount' => $item->discount,
						'specialOffLine' => $inst->special ? $inst->special->price_offline: 0,
						'specialOnLine' => $inst->special ? $inst->special->price_online : 0,
						'priceOffLine' => $item->price,
						'priceOnLine' => $item->price,
						'priceOff' => $item->price,
						'priceOn' => $item->price,
						'applyGst' => $inst->gst == '1' ? true : false,
						'isPaid' => false, //$item->isPaid(),
						'feeRebook' => 0,
						'notesAdmin' => $notes_admin,
						'notesClass' => $notes_class,
						'Students' => $students,
						'isVoucher' => false
						);
					array_push($instances, $instance);
				}
				elseif (Utils::ItemTypeName($item->item_type_id) == 'OnlineFaceToFace' )
				{
					$inst = CourseInstance::find($item->course_instance_id);
					$main_location = $main_location != 0 ? $main_location : $inst->location_id;
					$rosters = \Roster::with('customer')
						->where('order_id', $item->order_id)
						->where('course_instance_id',$item->course_instance_id)
						->get();
					$student_count = 1;
					foreach ($rosters as $roster)
					{
						$notes_admin = $roster->notes_admin;
						$notes_class = $roster->notes_class;
						$student = array(
							'id'=> $roster->customer_id,
							'order' => $student_count,
							'courseInstance' => $roster->course_instance_id,
							'FirstName' => $roster->customer->first_name,
							'LastName' => $roster->customer->last_name,
							'Dob' => $roster->customer->dob,
							'Phone' => $roster->customer->phone,
							'Mobile' => $roster->customer->mobile,
							'Email' => $roster->customer->email,
							'LangLevel' => !empty($roster->customer->lang_eng_level) ? $roster->customer->lang_eng_level : '.',
							'mail_out' => $roster->customer->mail_out_email == '1' ? true : false
							);
						array_push($students, $student);
						$student_count++;
					}

					$instance = array(
						'id' => $item->id,
						'courseType' => $inst->course_id,
						'courseInstance' => $item->course_instance_id,
						'itemType' => $inst->itemtype ? $inst->itemtype->name : 'OnlineCourse',
						
						'location' => $inst->location_id,
						'parentLocation' => $inst->location->parent_id == 0 ? $inst->location_id : $inst->location->parent_id,
						'parentLocationName' => $inst->parent_location->name,
						'full' => $inst->full,
						'courseName' => $inst->course->name . ' Online FaceToFace',
						'courseAddress' => $inst->location->address . ',<br>' . $inst->location->city . ', ' . $inst->location->state,
						'courseDate' => date('Y-m-d', strtotime($inst->course_date)),
						'time_start' => date('h:i A', strtotime($inst->time_start)),
						'time_end' => date('h:i A', strtotime($inst->time_end)),
						
						'studentQty' => $item->qty,
						'discount' => $item->discount,
						'specialOffLine' => $item->price,
						'specialOnLine' => $item->price,
						'priceOffLine' => $item->price,
						'priceOnLine' => $item->price,
						'priceOff' => $item->price,
						'priceOn' => $item->price,
						'applyGst' => $inst->gst == '1' ? true : false,
						'isPaid' => false, //$item->isPaid(),
						'feeRebook' => 0,
						'notesAdmin' => $notes_admin,
						'notesClass' => $notes_class,
						'Students' => $students,
						'isVoucher' => false
						);
					array_push($instances, $instance);
				}
				else if(Utils::ItemTypeName($item->item_type_id) == 'Voucher' )
				{
					$loc = 0;
					$main_location = 0;
					$c_id = $item->vouchers[0]->course_id;
					//$query = Course::with('special');
					$locations = \DB::table('locations')
						->where('id', $loc)
						->orWhere('parent_id', $loc)
						->lists('id');
					//$query = $query->wherein('location_id', $locations);
					$inst = Course::where('id', $c_id)->first();
					//$query = $query->where('course_date','>=', date("Y-m-d"));
					//$query = $query->where('course_date','>=', $item->created_at);
					//$inst = $query->orderBy('course_date')->first();

					$instance = array(
						'id' => 'gv' . $c_id,
						'courseType' => $c_id,
						'courseInstance' => 'gv' . $c_id,
						'itemType' =>'Voucher',
						'location' => 0,
						'parentLocation' => 0,
						'parentLocationName' => 'Online',
						'full' => 0,
						'courseName' => $inst->name,
						'courseAddress' => 'Online',
						'courseDate' => 'Gift Voucher: (Online)',
						'time_start' => '',
						'time_end' => '',
						
						'studentQty' => $item->qty,
						'discount' => $item->discount,
						'specialOffLine' => $inst->special->price_offline,
						'specialOnLine' => $inst->special->price_offline,
						'priceOffLine' => $item->price,
						'priceOnLine' => $item->price,
						'priceOff' => $item->price,
						'priceOn' => $item->price,
						'applyGst' => $inst->gst == '1' ? true : false,
						'isPaid' => false, //$item->isPaid(),
						'feeRebook' => 0,
						'notesAdmin' => $notes_admin,
						'notesClass' => $notes_class,
						'Students' => $students,
						'isVoucher' => true
						);
					array_push($instances, $instance);
				}
				else if(Utils::ItemTypeName($item->item_type_id) == 'RebookFee' )
				{
					if ($main_location == 0)
					{
						$inst = CourseInstance::with('special')->find($item->course_instance_id);
						$main_location = $inst->location_id;
					}
					foreach($instances as &$course)
					{
						if($course['courseInstance'] == $item->course_instance_id)
						$course['feeRebook'] = $item->price;	
					}
				}
				else if(Utils::ItemTypeName($item->item_type_id) == 'Discount' )
				{
					if ($main_location == 0)
					{
						$inst = CourseInstance::with('special')->find($item->course_instance_id);
						$main_location = $inst->location_id;
					}
					foreach($instances as &$course)
					{
						if($course['courseInstance'] == $item->course_instance_id)
						$course['discount'] = $item->price;	
					}
				}
			}
		}
		
		$payment = array();	
		if ($order->customer)
		{
			$payment = array(
				'id'=> $order->customer->id,
				'FirstName'=> $order->customer->first_name,
				'LastName' => $order->customer->last_name,
				'Dob' => $order->customer->dob,
				'Mobile' => $order->customer->mobile,
				'Email' => $order->customer->email,
				'LangLevel' => !empty($order->customer->lang_eng_level) ? $order->customer->lang_eng_level : '.',
				'mail_out' => $order->customer->mail_out_email == '1' ? true : false
				);
		}
		
		//$parentlocation = Location::find($main_location);		
		//if ($parentlocation)
		$parentlocation_id = 0;
		
		$result = array(
			'id' => $order->id,
			'order_id' => $order->id,
			'parentlocation_id' => 0,
			'location_id' => 0,
			'FirstName'=> $order->customer->first_name,
			'LastName' => $order->customer->last_name,
			'Dob' => $order->customer->dob,
			'Mobile' => $order->customer->mobile,
			'Email' => $order->customer->email,
			'LangLevel' => !empty($order->customer->lang_eng_level) ? $order->customer->lang_eng_level : '.',
			'mail_out' => $order->customer->mail_out_email == '1' ? true : false,
			'q1' => $order->customer->question1,
			'q2' => $order->customer->question2,
			'q3' => $order->customer->question3,
			'referrer' => $order->referrer ? $order->referrer->referrer_id : '',
			'Instances' => $instances,
			'Payment' => $payment,
			'Invoice' => $invoice
			);
		
		if (!empty($order->agent_id))
		$result['Agent'] = array('id'=>$order->agent->id, 'name'=>$order->agent->name);
		
		if (!empty($order->company_id))
		$result['Company'] = array('id'=>$order->company->id, 'name'=>$order->company->name);
		

		//dd(\Utils::q());
		//
		//exit;
		
		echo json_encode($result);


	}



	public function thankyou($order_id, $student_id = null)
	{
		
		if(empty($location))
		$location = 'sydney';
		$locations = Location::with('children')->where('parent_id', 0)->where('active', 1)->remember(Config::get('cache.minutes', 1))->get();
		$pages = null;
		$page = CmsPage::where('route', 'bookings')->where('parent_id', 0)->first();

		$result = OrderService::getBookingMessage($order_id);
		
		$message = $result['message'];
		$order = $result['order'];
		
		// if this is call within the first 5 minutes of the order being updated than is the first time
		$date_order_updated = strtotime($order->updated_at);
		$five_minutes_ago = strtotime("-5 minutes");
		$first_item = $order->items->first();
		
		$first_visit = $order->isGroupBooking() ? false : $this->first_visit;
		//if ($first_item && (empty($first_item->group_booking_id) || $date_order_updated >= $five_minutes_ago )) {
		//	// less than 5 minutes ago, should be the first visit
		//	$first_visit = true;
		//}

		$share_link = Request::url() . '?course=' . implode(',',$order->courses_list) . '%26inst=' . implode(',',$order->classes_list);
		
		$titles = array('' => 'Select Title') + \Config::get('utils.titles', array());;
		$languages = array('' => 'Select Language') + \AvetmissLanguage::orderBy('name')->lists('name','id');
		$countries = array('' => 'Select Country') + \AvetmissCountry::orderBy('name')->lists('name','id');
		$states = array('' => 'Select State') + \AvetmissState::orderBy('name')->lists('name','id');
		$achievements_list = \AvetmissAchievement::orderBy('name')->get(array('id','name'))->toArray();
		$disabilities_list = \AvetmissDisability::orderBy('id')->get(array('id','name'))->toArray();
		$study_reasons_list = \AvetmissStudyReason::orderBy('id')->get(array('id','name'))->toArray();
		$usi_visa_issue_countries = array('' => 'Select Country') + \UsiVisaIssueCountry::orderBy('name')->lists('name','id');
		
		$result = ($order->isPaid() || stripos($order->last_payment_method, 'Pay Later') !== false) ? 'well alert-success' : 'well alert-warning';

		
		$data = \OnlineService::getStaticOnlineData();
		$data->student = $this->student;

		return View::make('online.public.thankyou', 
			compact(
				'data', 'pages', 'page', 'locations', 'order', 'share_link', 'result', 'message', 'first_visit', 
				'titles', 'languages','countries', 'states','achievements_list',
				'disabilities_list','study_reasons_list','student_id','usi_visa_issue_countries'
				)
			);
	}



}