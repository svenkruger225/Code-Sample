<?php

class HomeController extends BaseController {

	protected $pages;
	protected $first_visit;

	public function __construct()
	{
		$this->pages = CmsPage::with('children')->where('parent_id', 0)->where('active', 1)->orderBy('order')->remember(Config::get('cache.minutes', 1))->get();
		$this->pages = $this->pages->each(function($page)
		{
			$page->children = $page->children
							->filter(function($child) {if ($child->active)	return $child;})
							->sortBy(function($child) {return $child->order;});
		});	
		$this->first_visit = true;	
	}

	public function getIndex()
	{
		return View::make('frontend/home');
	}

	public function home()
	{
        //PdfService::download('Order', 'backend.invoices.invoice', '/invoices/', 'demo-', 411699);
        //RSA agent booking 376068
        //RSA melbourne booking 376073
        //
        /*
        $this->payment['SendEmail'] = 1;
        $this->payment['SendSMS'] = 0;
        $send_data = array(
            'order_id' => 376075,
            'SendAdmin'=> false,
            'SendEmail'=>filter_var($this->payment['SendEmail'], FILTER_VALIDATE_BOOLEAN),
            'SendSMS'=>filter_var($this->payment['SendSMS'], FILTER_VALIDATE_BOOLEAN),
            'IsGroupBooking'=> false,
            'IsPublicBooking'  => false,
            'IsAgentBooking'=> true,
            'HasVouchers' => false,
            'success'=> true
        );
        $msg = new App\Services\SendMessage();
        $msg->fire(null,$send_data);

        die("i called");
        */

        return $this->content('HOME');
	}

	public function catchall($path = null, $name = null, $location = null)
	{
		if ($path && $path != 'content')
		{
			$location = $name;
			$name = $path; 
		}	
			
		if ($name)
		{
			switch ($name)
			{
				case 'sydney':
				case 'melbourne':
				case 'brisbane':
				case 'perth':
				case 'parramatta':
				case 'penrith':
					return $this->content('facebook', $path);
				case 'coffeecourse':
					return $this->content('coffee accredited',$location);
				case 'rsacourse':
					return $this->content('rsa',$location);
				case 'rcgcourse':
					return $this->content('rcg & rsg',$location);
				case 'cocktailscourse':
					return $this->content('bar & cocktail skills',$location);
				case 'foodhygienecourse':
					return $this->content('food hygiene',$location);
				case 'foodsafetycourse':
					return $this->content('food hygiene',$location);
				case 'baristacourse':
					return $this->content('coffee non accredited',$location);
				case 'coffeeartcourse':
					return $this->content('coffee non accredited',$location);
				default:
					return $this->content($name, $location);
			}
		}
		return $this->home();
	}


	public function content($name, $location = null)
	{
		if ($name == 'specials' || $name == 'bookings' || $name == 'vouchers' || $name == 'voucher')
		{
			return $this->$name($location);
		}
		
		$locations = Location::with('children')
			->where('parent_id', 0)
			->where('active', 1)
			->remember(Config::get('cache.minutes', 1))
			->get();
		$pages = $this->pages;
		$name = str_replace("_and_", " & ", $name);
		$name = str_replace("_", " ", $name);		

		$page = CmsPage::with('children','contents')
				->where('route', strtoupper($name))
				->where('parent_id', 0)
				->where('active', 1)
				->remember(Config::get('cache.minutes', 1))
				->first();	
		
		if(!$page)
		{
			\Log::info("Could not find route: $name , location: $location, url: " . Request::url() . ' - referrer: '  . Request::header('referer'));
			$page = CmsPage::with('children','contents')
					->where('route', 'LIKE', "$name%")
					->where('parent_id', 0)
					->where('active', 1)
					->remember(Config::get('cache.minutes', 1))
					->first();	
		}
			
		if($page && $location)
		{
			$child = null;
			foreach($page->children as $child_page)
			{
				if (strtolower($child_page->name) == strtolower($location) && $child_page->active)
				{
					$child = $child_page;
					break;
				}
			}
			if ($child && !empty($child->url))
				return Redirect::to($child->url);
			
			if (!$child)
				return Redirect::home();
			
		}
		
		if(!$page)
			return Redirect::home();
				
		if(!empty($location)) 
			$page->location_name = strtoupper($location);
                $data = \OnlineService::InitialiseContentData();
                if (Sentry::check())
		{
                    $user = Sentry::getUser();
                    if ($user && $user->hasAnyAccess(array('agent')))
                    {
                       $data->student = $user;
                    }
                    else{
                        $data->student =NULL;
                    }	
                }
		//\Log::info($student->customer->toJson());
		return View::make('frontend/templates/content', compact('pages', 'page', 'locations','data'));
	}

	public function specials($location = null)
	{
		if($location == null)
			$location = 'sydney';
		
		$locations = Location::with('children')
			->where('parent_id', 0)
			->where('active', 1)
			->remember(Config::get('cache.minutes', 1))
			->get();
		$pages = $this->pages;
		
		$page = CmsPage::where('route', 'specials')
			->remember(Config::get('cache.minutes', 1))
			->first();	
		if($location)
			$page->location_name = strtoupper($location);
		
		$loc = Location::where('name','LIKE', "%$location%")->where('parent_id',0)->remember(Config::get('cache.minutes', 1))->first();
		if ($loc)
			$location_id = $loc->id;
		elseif ($page && $page->location_id)
			$location_id = $page->location_id;
		else
			$location_id = '1';
		
		if(empty($page->location_id))
			$page->location_id = $location_id;
		
		$list_of_locations = DB::table('locations')
			->where('id', '=',  $location_id)
			->orWhere('parent_id', '=',  $location_id)
			->remember(Config::get('cache.minutes', 1))->lists('id');
			
		
		$bundles = \Utils::GetBundles($location, $list_of_locations);
		$specials = \Utils::GetSpecials($location, $list_of_locations);
		
		return View::make('frontend/common/specials', compact('pages', 'page', 'specials', 'bundles', 'locations'));
	}
	
	public function bookings($location = null)
	{
		if($location == null)
			$location = 'sydney';
		$locations = Location::with('children')->where('parent_id', 0)->where('active', 1)->remember(Config::get('cache.minutes', 1))->get();
		
		$course_id = Input::get('course');
		$instance_id = Input::get('inst');
		$bundle_id = Input::get('bundle');
		$voucher_id = Input::get('voucher_id');
		$bundles = '';
		if(!empty($bundle_id))
		{
			$bundle = CourseBundle::with('bundles')->find($bundle_id);
			$courses = array();
			if (!is_null($bundle))
			{
				foreach ($bundle->bundles as $bundle)
					$courses[] = $bundle->pivot->course_id;
			}
			$bundles = json_encode($courses);
		}
		$id = Input::get('id');
		$res = Input::get('r');
		$student_id = Input::get('s');
		
		if(App::environment('production') && !Request::secure())
		{
			return Redirect::secure(Request::getRequestUri());
		}
		
		$pages = $this->pages;
		$page = CmsPage::where('route', 'bookings')->where('parent_id', 0)->first();
		if($location)
			$page->location_name = strtoupper($location);

		$loc = Location::where('name','LIKE', "%$location%")->where('parent_id',0)->remember(Config::get('cache.minutes', 1))->first();

		if ($loc)
			$location_id = $loc->id;
		elseif ($page->content)
			$location_id = $page->location_id;
		else
			$location_id = '1';
		
		if(empty($page->location_id))
			$page->location_id = $location_id;
		
		if(empty($page->course_id))
			$page->course_id = $course_id;
		
		// data for booking form
		$list_of_locations = DB::table('locations')
			->where('id', '=',  $location_id)
			->orWhere('parent_id', '=',  $location_id)
			->remember(Config::get('cache.minutes', 1))->lists('id');
		$order_id = Input::get('OrderId');
		$backend = 0;
		$dateStart = date("Y-m-d");
		$dateEnd = date("Y-m-d", strtotime("+3 month", time()));
		
		$courses = Course::with(Array('prices', 'instances' => function($query) use($dateStart, $dateEnd, $list_of_locations){
				return $query->wherein('location_id', $list_of_locations)
				->whereBetween('course_date', array($dateStart, $dateEnd))
				->where('active', 1)
				->orderBy('course_date')
				->orderBy('location_id');
			}))
			->where('id', '!=', 9)
			->where('type', 'FaceToFace')
			->where('active', 1)
			->orderBy('order')->get();		
		
		$referrer = Request::header('referer');

		//print_r($page->location);
		//exit();

		//if(empty($course_id))
		//	$course_id = $page->course;
		
		$titles = array('' => 'Select Title') + \Config::get('utils.titles', array());;
		$languages = array('' => 'Select Language') + \AvetmissLanguage::orderBy('name')->lists('name','id');
		$countries = array('' => 'Select Country') + \AvetmissCountry::orderBy('name')->lists('name','id');
		$states = array('' => 'Select State') + \AvetmissState::orderBy('name')->lists('name','id');
		$achievements_list = \AvetmissAchievement::orderBy('name')->get(array('id','name'))->toArray();
		$disabilities_list = \AvetmissDisability::orderBy('id')->get(array('id','name'))->toArray();
		$study_reasons_list = \AvetmissStudyReason::orderBy('id')->get(array('id','name'))->toArray();
		$usi_visa_issue_countries = array('' => 'Select Country') + \UsiVisaIssueCountry::orderBy('name')->lists('name','id');
		

		if (!empty($id))
		{
			if ($res == 's')
			{
				return $this->thankyou($id, $student_id);
			}
			else
			{
				return $this->cancelled($id);
			}
		}
		else
		{
			return View::make('bookings.public.booking', compact('pages', 'page','courses', 'referrer', 'order_id', 'titles', 'locations','location_id','course_id', 'instance_id', 'bundles', 'voucher_id', 'languages','countries', 'states','achievements_list','disabilities_list','study_reasons_list','usi_visa_issue_countries'));
		}
	}
	
	public function vouchers($location = null)
	{
		if($location == null)
			$location = 'sydney';
		$locations = Location::with('children')->where('parent_id', 0)->where('active', 1)->remember(Config::get('cache.minutes', 1))->get();
		$course_id = Input::get('course');
		$instance_id = '';
		$bundles = '';
		$id = Input::get('id');
		$res = Input::get('r');
		
		if(App::environment('production') && !Request::secure())
		{
			return Redirect::secure(Request::getRequestUri());
		}
		
		$pages = $this->pages;
		$page = CmsPage::where('route', 'bookings')->where('parent_id', 0)->first();
		if($location)
			$page->location_name = strtoupper($location);

		$loc = Location::where('name','LIKE', "%$location%")->where('parent_id',0)->remember(Config::get('cache.minutes', 1))->first();

		if ($loc)
			$location_id = $loc->id;
		elseif ($page->content)
			$location_id = $page->location_id;
		else
			$location_id = '1';
		
		if(empty($page->location_id))
			$page->location_id = $location_id;
		
		if(empty($page->course_id))
			$page->course_id = $course_id;
		
		// data for booking form
		$list_of_locations = DB::table('locations')
			->where('id', '=',  $location_id)
			->orWhere('parent_id', '=',  $location_id)
			->remember(Config::get('cache.minutes', 1))->lists('id');
		$order_id = Input::get('OrderId');
		$backend = 0;
		$dateStart = date("Y-m-d");
		$dateEnd = date("Y-m-d", strtotime("+3 month", time()));
		
		$courses = Course::where('id', '!=', 9)
			->where('type', 'FaceToFace')
			->where('active', 1)
			->orderBy('order')->get();		
		
		$referrer = Request::header('referer');

		//print_r($page->location);
		//exit();

		//if(empty($course_id))
		//	$course_id = $page->course;

		if (!empty($id))
		{
			if ($res == 's')
			{
				$result = OrderService::getBookingMessage($id);
				
				$message = $result['message'];
				$order = $result['order'];
				$share_link = Request::url() . '?course=' . implode(',',$order->courses_list) . '%26inst=' . implode(',',$order->classes_list);
				
				$result = ($order->isPaid() || stripos($order->last_payment_method, 'Pay Later') !== false) ? 'well alert-success' : 'well alert-warning';
				return View::make('bookings.public.thankyou', compact('pages', 'page', 'order', 'share_link', 'result', 'message', 'locations'));
			}
			else
			{
				$result = 'well alert-error';
				$message = "<h3>There was a problem with your booking, please contact Coffee School nearest office</h3>";
				return View::make('bookings.public.cancelled', compact('pages', 'page', 'result', 'message', 'locations'));
			}
		}
		else
			return View::make('bookings.public.vouchers', compact('pages', 'page','courses', 'referrer', 'order_id', 'locations','location_id','course_id', 'instance_id', 'bundles'));
		
	}

	public function voucher()
	{
		$location = 'brisbane';
		$id = Input::get('voucher_id');
		$voucher = Voucher::find($id);
		if ($voucher)
			$location = $voucher->Location->name;
		
		return $this->bookings($location);
	}

	public function contact() {
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

	public function share($order_id, $student_id = null) {
		return $this->enrolment($order_id, $student_id);
	}

	public function enrolment($o_id = null, $s_id = null) {
		try
		{
			$order_id = Input::get('order_id', $o_id);
			$student_id = Input::get('student_id', $s_id);
			\Order::findOrFail($order_id);
			$this->first_visit = false;
			return $this->thankyou($order_id, $student_id);
		}
		catch (Exception $e)
		{
			// Ooops.. something went wrong
			Log::error($e);
			try
			{
				return Redirect::back()->withInput()->with('error', $e->getMessage());
			}
			catch (Exception $ex)
			{
				Log::error("Inside try: " . $e);
				// Ooops.. something went wrong
				return Redirect::home()->withInput()->with('error', $e->getMessage());
			}
		}
		
	}

	public function thankyou($order_id, $student_id = null)
	{

		if(empty($location))
			$location = 'sydney';
		$locations = Location::with('children')->where('parent_id', 0)->where('active', 1)->remember(Config::get('cache.minutes', 1))->get();
		$pages = $this->pages;
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
        // have added the data variable as it was missing
        $data['layout'] = '';

        $result = ($order->isPaid() || stripos($order->last_payment_method, 'Pay Later') !== false) ? 'well alert-success' : 'well alert-warning';
		return View::make('bookings.public.thankyou', 
				compact('data',
					'pages', 'page', 'locations', 'order', 'share_link', 'result', 'message', 'first_visit', 
					'titles', 'languages','countries', 'states','achievements_list',
				'disabilities_list','study_reasons_list','student_id','usi_visa_issue_countries'
				)
		);
	}

	public function cancelled($order_id)
	{
		if(empty($location))
			$location = 'sydney';
		$locations = Location::with('children')->where('parent_id', 0)->where('active', 1)->remember(Config::get('cache.minutes', 1))->get();
		$pages = $this->pages;
		$page = CmsPage::where('route', 'bookings')->where('parent_id', 0)->first();
		
		$result = 'well alert-error';
		$message = "<h3>There was a problem with your booking, please contact Coffee School nearest office</h3>";
		return View::make('bookings.public.cancelled', compact('pages', 'page', 'result', 'message', 'locations'));
	}


	public function agents($agent, $location, $action = null, $order_id = null, $student_id = null, $extra = null)
	{
		//Route::get('/{agent}/{location}/share/{order_id}/{student_id?}', array('as' => 'agent.share', 'uses' => 'HomeController@share'));
		//Route::get('/{agent}/{location}/thankyou/{order_id}/{student_id?}', array('as' => 'agent.thankyou', 'uses' => 'HomeController@thankyou'));
		//Route::get('/{agent}/{location}/cancelled/{order_id}', array('as' => 'agent.cancelled', 'uses' => 'HomeController@cancelled'));
		//Route::any('/{agent}/{l}/enrolment/form/{o_id?}/{s_id?}', array('as' => 'agent.enrolment.form', 'uses' => 'HomeController@enrolment'));

		$location = strtolower($location);
		switch ($action)
		{
			case 'share':
				return \AgentService::share($agent, $location, $order_id, $student_id);
			case 'thankyou':
				return \AgentService::thankyou($agent, $location, $order_id, $student_id);
			case 'cancelled':
				return \AgentService::cancelled($agent, $location, $order_id);
			case 'enrolment':
				return \AgentService::enrolment($agent, $location, $student_id, $extra);
			default:
				return \AgentService::bookings($agent, $location);
		}

	}



	// ALIASES
	public function aliases($location = null)
	{
		$path = Request::path();
		
		if ($location)
		{
			list($path, $location) = explode('/', $path);
		}
		
		switch ($path)
		{
			case 'sydney':
			case 'melbourne':
			case 'brisbane':
			case 'perth':
			case 'parramatta':
			case 'penrith':
				return $this->content('facebook', $path);
			case 'coffeecourse':
				return $this->content('coffee',$location);
			case 'rsacourse':
				return $this->content('rsa',$location);
			case 'rcgcourse':
				return $this->content('rcg & rsg',$location);
			case 'cocktailscourse':
				return $this->content('bar & cocktail skills',$location);
			case 'foodhygienecourse':
				return $this->content('food hygiene',$location);
			case 'foodsafetycourse':
				return $this->content('food hygiene',$location);
			case 'baristacourse':
				return $this->content('coffee non accredited',$location);
			case 'coffeeartcourse':
				return $this->content('coffee non accredited',$location);
			default:
				return $this->home();
		}
	
	}
	
	private function getAddress($location_name)
	{
		$location = Location::where('name', $location_name)->remember(Config::get('cache.minutes', 1))->first();
		return ucfirst(strtolower($location_name)) . ' location: ' . $location->address . ', ' . $location->city . ', ' . $location->state;
	}
	
	public function participant_handbook()
	{
		$filepath = \Attachment::where('name', 'Student Handbook')->first()->path;
		return Utils::ViewFile($filepath, 'coffeeschool_particpant_handbook.pdf', 10);
		//return Response::download($filepath, 'coffeeschool_particpant_handbook.pdf', array('content-type' => 'application/octet-stream'));
	}
	
	public function student_handbook()
	{
		$filepath = \Attachment::where('name', 'Student Handbook')->first()->path;
		return Utils::ViewFile($filepath, 'coffeeschool_student_handbook.pdf', 10);
		//return Response::download($filepath, 'coffeeschool_particpant_handbook.pdf', array('content-type' => 'application/octet-stream'));
	}
	
	public function usi_privacy_notice()
	{
		$filepath = \Attachment::where('name', 'USI Privacy Notice')->first()->path;
		return Utils::ViewFile($filepath, 'usi_privacy_notice.pdf', 10);
		//return Response::download($filepath, 'coffeeschool_particpant_handbook.pdf', array('content-type' => 'application/octet-stream'));
	}
	
	public function privacy_terms_conditions()
	{
		$filepath = \Attachment::where('name', 'Terms And Conditions')->first()->path;
		return Utils::ViewFile($filepath, 'coffeeschool_privacy_terms_conditions.pdf', 10);
		//return Response::download($filepath, 'coffeeschool_privacy_terms_conditions.pdf', array('content-type' => 'application/octet-stream'));
	}

    public function secret()
    {
        die("i am secret");
    }

}