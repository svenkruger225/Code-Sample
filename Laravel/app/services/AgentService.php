<?php namespace App\Services;

use Config, Input, Lang, Redirect, Sentry, Validator, View, Response, Exception, DateTime, Log, File, DB;
use CmsPage, Location, Request, App, Course;

class AgentService {

	public function __construct()
	{
		$this->first_visit = true;	
	}

    public function getAgentCode($location)
    {
        $location = strtolower($location);
        switch($location)
        {
            case 'perth':
                return 'rsa';
            case 'melbourne':
                return 'rsam';
            case 'sydney':
                return 'rsas';
            case 'brisbane':
                return 'rsab';
            default:
                return 'rsa';
        }
    }
	
	public function bookings($agent, $location = null)
	{
		$page = new CmsPage();
		if($location == null)
			$location = 'sydney';
		$locations = Location::with('children')->where('parent_id', 0)->where('active', 1)->remember(Config::get('cache.minutes', 1))->get();

		$data['layout'] = $location;
		$data['course_id'] = Input::get('course', null);
		$data['instance_id'] = Input::get('inst', null);
		$data['bundle_id'] = Input::get('bundle', null);
		$data['voucher_id'] = Input::get('voucher_id', null);
		$data['bundles'] = '';
		if(!empty($data['bundle_id']))
		{
			$bundle = CourseBundle::with('bundles')->find($data['bundle_id']);
			$courses = array();
			if (!is_null($bundle))
			{
				foreach ($bundle->bundles as $bundle)
					$courses[] = $bundle->pivot->course_id;
			}
			$data['bundles'] = json_encode($courses);
		}
		$data['referrer'] = Request::header('referer');
		$data['order_id'] = Input::get('OrderId', null);
		$data['backend'] = 0;
        $agent = $this->getAgentCode($location);
		$ag = \Agent::where('code', $agent)->get(array( 'name', 'id', 'contact_name', 'phone', 'mobile', 'email', 'contact_position'))->first();
		$parts = explode(" ", $ag->contact_name);
		$last_name = array_pop($parts);
		$first_name = implode(" ", $parts);
		$aux = array('id'=>$ag->id, 'name' => $ag->name, 'last_name' => '.', 'first_name' => $ag->name, 'contact_position' => $ag->contact_position, 'email' => $ag->email, 'mobile' => (empty($ag->mobile) ? $ag->phone : $ag->mobile));
		$data['agent_data'] = json_encode($aux);

		$id = Input::get('id');
		$res = Input::get('r');
		$student_id = Input::get('s');
		
		if(App::environment('production') && !Request::secure())
		{
			return Redirect::secure(Request::getRequestUri());
		}
		
		if($location)
			$page->location_name = strtoupper($location);

		$loc = Location::where('name','LIKE', "%$location%")->where('parent_id',0)->remember(Config::get('cache.minutes', 1))->first();

		if ($loc)
			$data['location_id'] = $loc->id;
		elseif ($page->content)
			$data['location_id'] = $page->location_id;
		else
			$data['location_id'] = '1';

		if(empty($page->location_id))
			$page->location_id = $data['location_id'];
		
		if(empty($page->course_id))
			$page->course_id = $data['course_id'];
		
		// data for booking form
		$list_of_locations = DB::table('locations')
			->where('id', '=',  $data['location_id'])
			->orWhere('parent_id', '=',  $data['location_id'])
			->remember(Config::get('cache.minutes', 1))->lists('id');
        /*
        $queries = DB::getQueryLog();
        $last_query = end($queries);
        var_dump($last_query);
        exit;
        */
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

        /*
		$courses = Course::with(Array('prices', 'instances' => function($query) use($dateStart, $dateEnd, $list_of_locations){
				return $query->whereBetween('course_date', array($dateStart, $dateEnd))
				->where('active', 1)
				->orderBy('course_date');

			}))
			->where('id', '!=', 9)
			->where('type', 'FaceToFace')
			->where('active', 1)
			->orderBy('order')->get();
        */
        /*

        $courses = Course::with(Array('prices', 'instances' => function($query) use($dateStart, $dateEnd, $list_of_locations){
            return $query->whereBetween('course_date', array($dateStart, $dateEnd))
                ->where('active', 1)
                ->orderBy('course_date')
                ->orderBy('location_id');
        }))
            ->where('id', '!=', 9)
            ->where('type', 'FaceToFace')
            ->where('active', 1)
            ->orderBy('order')->get();
        */
        /*
        $queries = DB::getQueryLog();
        $last_query = end($queries);
        var_dump($last_query);
        exit;
        */



		
		$titles = array('' => 'Select Title') + \Config::get('utils.titles', array());;
		$languages = array('' => 'Select Language') + \AvetmissLanguage::orderBy('name')->lists('name','id');
		$countries = array('' => 'Select Country') + \AvetmissCountry::orderBy('name')->lists('name','id');
		$states = array('' => 'Select State') + \AvetmissState::orderBy('name')->lists('name','id');
		$achievements_list = \AvetmissAchievement::orderBy('name')->get(array('id','name'))->toArray();
		$disabilities_list = \AvetmissDisability::orderBy('id')->get(array('id','name'))->toArray();
		$study_reasons_list = \AvetmissStudyReason::orderBy('id')->get(array('id','name'))->toArray();
		$usi_visa_issue_countries = array('' => 'Select Country') + \UsiVisaIssueCountry::orderBy('name')->lists('name','id');
        $prices = array();
        foreach($courses as $course)
        {
            $priceForLocation = $course->priceForLocation($page->location_id);
            if(!empty($priceForLocation)) {
                $prices[$course->id] = $course->priceForLocation($page->location_id)->price_online;
            }
            else
            {
                $prices[$course->id] = $course->priceForLocation(1)->price_online;
            }
        }

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
			return View::make('bookings.agents.'.$agent . '.booking', compact('page', 'courses', 'data', 'titles', 'locations', 'languages','countries', 'states','achievements_list','disabilities_list','study_reasons_list','usi_visa_issue_countries','prices'));
		}
	}

	public function share($agent, $location = null, $order_id, $student_id = null) {
		return $this->enrolment($location, $order_id, $student_id);
	}

	public function enrolment($agent, $l = null, $o_id = null, $s_id = null) {
		try
		{
			$location = Input::get('location', $l);
			$order_id = Input::get('order_id', $o_id);
			$student_id = Input::get('student_id', $s_id);
			\Order::findOrFail($order_id);
			$this->first_visit = false;
			return $this->thankyou($location, $order_id, $student_id);
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

	public function thankyou($agent, $location = null, $order_id, $student_id = null)
	{
		
		$page = new CmsPage();

		if(empty($location))
			$location = 'sydney';
		$locations = Location::with('children')->where('parent_id', 0)->where('active', 1)->remember(Config::get('cache.minutes', 1))->get();
		//$pages = $this->pages;

		$result = \OrderService::getBookingMessage($order_id);
		
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

		$data['layout'] = $location;
		
		$result = ($order->isPaid() || stripos($order->last_payment_method, 'Pay Later') !== false) ? 'well alert-success' : 'well alert-warning';
        $agentCode = $this->getAgentCode($location);
		return View::make('bookings.agents.'.$agentCode . '.thankyou',
				compact(
					'data', 'page', 'locations', 'order', 'share_link', 'result', 'message', 'first_visit', 
					'titles', 'languages','countries', 'states','achievements_list',
				'disabilities_list','study_reasons_list','student_id','usi_visa_issue_countries'
				)
		);
	}

	public function cancelled($agent, $location = null, $order_id = null)
	{
		$page = new CmsPage();

		if(empty($location))
			$location = 'sydney';
		
		$locations = Location::with('children')->where('parent_id', 0)->where('active', 1)->remember(Config::get('cache.minutes', 1))->get();
		//$pages = $this->pages;

		$data['layout'] = $location;
		
		$result = 'well alert-error';
		$message = "<h3>There was a problem with your booking, please contact Coffee School nearest office</h3>";
		return View::make('bookings.agents.'.$agent . '.cancelled', compact('page', 'data', 'result', 'message', 'locations'));
	}
	
}
