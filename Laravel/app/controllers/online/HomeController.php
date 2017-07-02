<?php namespace Controllers\Online;

use Controller;
use App\Services\BookingPublic;
use App\Services\BookingGroup;
use App\Services\BookingPurchase;
use App\Services\BookingOnline;
use Request,Config, Input, Lang, Redirect, Sentry, Validator, View, DB, Response, Exception, stdClass;
use CourseBundle, CoursePrice, Agent, Company, Location, Course, OnlineCourse, CourseInstance, Order, Item, Invoice;
use Customer, Status, Voucher, Product, Marketing, CmsPage;
use OrderService, SearchService, PdfService, Utils, Log, EmailService;

class HomeController extends Controller {

	public function __construct()
	{
	}

	public function index()
	{
		//return View::make('online/public/spa-home');
		
		$dateStart = date("Y-m-d");
		$dateEnd = date("Y-m-d", strtotime("+3 month", time()));
		$data = \OnlineService::InitialiseContentData();
		$data->courses = OnlineCourse::with(array('prices', 'instances' => function($query) use($dateStart, $dateEnd){
				return $query->whereBetween('course_date', array($dateStart, $dateEnd))
				->where('active', 1)
				->orderBy('course_date');
			}))
			->where('type', 'Online')
			->where('active', 1)
			->orderBy('order')
			->remember(Config::get('cache.minutes', 1))
			->get();

		$data->locations = DB::table('locations')
			->join('courseinstances','courseinstances.location_id','=','locations.id')
			->join('courses','courseinstances.course_id','=','courses.id')
			->where('courses.type', 'Online')
			->distinct()
			->remember(Config::get('cache.minutes', 1))
			->select('locations.*')->get();
			
		
		foreach ($data->courses as $course)
		{
			$course->locations = new \Illuminate\Database\Eloquent\Collection();

			foreach ($data->locations as $location)
			{
				$loc_id = $location->id;
				$insts = $course->instances->filter(function($inst) use($loc_id) { return $inst->location_id == $loc_id; });
				if ($insts->count() > 0)
				{
					$location->instances = $insts;
					$course->locations->add($location);
				}
			}
		}
		$user = Sentry::getUser();
		if ($user && $user->hasAnyAccess(array('student')))
		{
			$data->student = $user->customer;
		}
		else{
			$data->student =NULL;
		}	
		//\Log::info($student->customer->toJson());
		return View::make('online/public/home', compact('data'));
		
	}

	public function bookings()
	{
		$id = Input::get('id');
		$res = Input::get('r');
		$order_id = null;
		$order_type = 'PublicOnline';
		
		if(\App::environment('production') && !Request::secure())
		{
			return Redirect::secure(Request::getRequestUri());
		}
		
		$data = \OnlineService::getStaticOnlineData();

		$data->courses = OnlineCourse::with('prices')
			->where('type', 'Online')
			->where('active', 1)
			->orderBy('order')
			->remember(Config::get('cache.minutes', 1))
			->get();

		$startup = '';
		
		$user = Sentry::getUser();
		$data->student = null;
		if ($user && $user->hasAnyAccess(array('student')))
		{
			$data->student = $user->customer;
		}

		if (!empty($id))
		{
			if ($res == 's')
			{
				$result = \OrderService::getBookingMessage($id);
				
				$message = $result['message'];
				$order = $result['order'];
				$share_link = Request::url() . '?course=' . implode(',',$order->courses_list) . '%26inst=' . implode(',',$order->classes_list);
				
				$result = ($order->isPaid() || stripos($order->last_payment_method, 'Pay Later') !== false) ? 'well alert-success' : 'well alert-warning';
				return View::make('online/bookings/public/thankyou', compact('data', 'pages', 'page', 'order', 'share_link', 'result', 'message', 'locations'));
			}
			else
			{
				$result = 'well alert-error';
				$message = "<h3>There was a problem with your booking, please contact Coffee School nearest office</h3>";
				return View::make('online/bookings/public/cancelled', compact('data', 'pages', 'page', 'result', 'message', 'locations'));
			}
		}
		else
		{
			return View::make('online/bookings/public/booking', compact('data', 'order_id', 'order_type'));
		}
	}


	public function clearHistory()
	{
		
		\OnlineService::deleteTimeoutHistory();
		return Redirect::back()->with('success', 'History  has been cleared');
	}

	public function populateHistory($roster_id)
	{
		\OnlineService::populateHistory($roster_id);
		return Redirect::back()->with('success', 'History  has been populated');
	}


	public function getContact()
	{
		
		$data = \OnlineService::InitialiseContentData();
		$data->courses = OnlineCourse::where('type', 'Online')
			->where('active', 1)
			->orderBy('order')
			->remember(Config::get('cache.minutes', 1))
			->get();
		
		$data->locations = array(''=>'','Sydney'=>'Sydney','Parramatta'=>'Parramatta','Penrith'=>'Penrith','Melbourne'=>'Melbourne','Brisbane'=>'Brisbane','Perth'=>'Perth','Other'=>'Other');
		$data->subjects = array(
			''=>'',
			'Course Enquiry'=>'Course Enquiry',
			'School/Group/Team Booking'=>'School/Group/Team Booking',
			'Catering/Machine Hire'=>'Catering/Machine Hire',
			'Certificate Reprint'=>'Certificate Reprint',
			'Invoice/Admin Enquiry'=>'Invoice/Admin Enquiry',
			'Other Enquiry'=>'Other Enquiry');
		return View::make('online.common.contact', compact('data'));
	}
	
	public function postContact()
	{
		try
		{
			$input = Input::all();
			$validationRules = array(
				'name'       => 'required',
				'email'            => 'required|email',
				'location'         => 'required',
				'subject'         => 'required',
				'message'         => 'required',
				'captcha' => 'required|captcha'
				);
			$validator = Validator::make(Input::all(), $validationRules);

			// If validation fails, we'll exit the operation now.
			if ($validator->fails())
			{
				return Redirect::back()
				->withInput()
				->withErrors($validator)
				->with('message', 'There were validation errors.');
				
				//var_dump($validator->getMessageBag());
				//exit();
				//$msg = "<p>Please fix the following errors:</p>";
				//$msg .= "<ul>";
				//foreach ($validator->messages()->all() as $message)
				//{
				//	$msg .= "<li>$message</li>";
				//}
				//$msg .= "</ul>";
				//// Ooops.. something went wrong
				//return Redirect::back()->with('error', $msg);
			}
			
			$result = json_decode(json_encode(array(
				'email' => Config::get('mail.admin_contact_email', 'csouza@outlook.com.au'),
				'first_name' => 'CoffeeSchool',
				'last_name' => 'Admin',
				'subject' => !empty($input['subject']) ? $input['subject'] : 'Contact Us details' ,
				'body' => View::make('emails.contact', compact('input'))->render(),
				'attachments' => array()
				)));		
			
			EmailService::send($result);
			return Redirect::back()->with('success', 'Your Email has been sent!');
		}
		catch (Exception $e)
		{
			// Ooops.. something went wrong
			return Redirect::back()->withInput()->with('error', $e->getMessage());
		}
	}
	


}