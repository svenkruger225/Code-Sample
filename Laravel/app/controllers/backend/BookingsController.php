<?php namespace Controllers\Backend;

use AdminController;
use Cartalyst\Sentry\Users\LoginRequiredException;
use Cartalyst\Sentry\Users\PasswordRequiredException;
use Cartalyst\Sentry\Users\UserExistsException;
use Cartalyst\Sentry\Users\UserNotFoundException;
use Config, Input, Lang, Redirect, Sentry, Validator, View, Response;
use Agent, Company, Referrer, Location, Course, CourseInstance, Order, Item, Invoice, Customer, DB, Status, Voucher, Product, PaymentMethod;
use OrderService, SearchService;

class BookingsController extends AdminController {

	protected $is_public_booking;	
	protected $is_group_booking;	
	protected $is_product_purchase;	
	protected $is_machine_hire;	
	
	public function __construct()
	{
		parent::__construct();
	}
	
	public function index() {
		$locations = array('' => 'Select Location:') + Location::all();
		$courses = array('' => 'Select Course Type:') + Course::lists('name', 'id');
		$statuses = array('' => 'Select Status:', 'active'=>'Bookings: Active', 'inactive'=>'Bookings: Inactive', 'all'=> 'Bookings: All');
		return View::make('backend.calendar.home', compact('locations', 'courses', 'statuses'));
	}
	
	public function newBooking() {
		$id = Input::get('id');
		$res = Input::get('r');
		$order_id = Input::get('OrderId');
		$order_type = Input::get('OrderType', '');
		
		$locations = Location::where('parent_id', 0)->get();

		//$agents = array('' => 'Select Agent:') + Agent::orderBy('name')->lists('name', 'id');
		$ags = Agent::orderBy('name')->get(array( 'name', 'id', 'contact_name', 'phone', 'mobile', 'email', 'contact_position'));
		$agents = array();
		$agents[''] = 'Select Agent:';
		foreach( $ags as $ag)
		{
			$parts = explode(" ", $ag->contact_name);
			$last_name = array_pop($parts);
			$first_name = implode(" ", $parts);
			$aux = array('id'=>$ag->id, 'name' => $ag->name, 'last_name' => '.', 'first_name' => $ag->name, 'contact_position' => $ag->contact_position, 'email' => $ag->email, 'mobile' => (empty($ag->mobile) ? $ag->phone : $ag->mobile));
			$agents[json_encode($aux)] = $ag->name;
		}
						
		//$companies =  array('' => 'Select Company - No Commissions') + Company::orderBy('name')->lists('name', 'id');		
		$cos = Company::orderBy('name')->get(array( 'name', 'id', 'contact_name', 'phone', 'mobile', 'email', 'contact_position'));
		$companies = array();
		$companies[''] = "Select Company - No Commissions'";
		foreach( $cos as $co)
		{
			$parts = explode(" ", $co->contact_name);
			$last_name = array_pop($parts);
			$first_name = implode(" ", $parts);
			$aux = array('id'=>$co->id, 'name' => $co->name, 'last_name' => '.', 'first_name' => $co->name, 'contact_position' => $co->contact_position, 'email' => $co->email, 'mobile' => (empty($co->mobile) ? $co->phone : $co->mobile));
			$companies[json_encode($aux)] = $co->name;
		}
		
		$vouchers = array('' => 'Select Gift Voucher:') + 
			Voucher::with('location', 'course')
			->where('expiry_date', '>=', DB::raw('CURDATE()'))
			->where('status_id', \Utils::StatusId('Voucher','Valid'))
			->select(DB::raw('concat (id," | ",expiry_date," | ",location_id," | ",course_id) as descr,id'))
			->orderBy('expiry_date', 'desc')
			->lists('descr', 'id');
		$referrers =  array('' => 'Select Referrer') + Referrer::orderBy('order')->orderBy('name')->lists('name', 'id');			
		$backend = 1;

		$dateStart = date("Y-m-d");
		$dateEnd = date("Y-m-d", strtotime("+4 month", time()));

		$loc_list = \DB::table('locations')
			->where('id', '=',  1)
			->orWhere('parent_id', '=',  1)
			->lists('id');
		
		$courses = Course::with(Array('instances' => 
			function($query) use($dateStart, $dateEnd, $loc_list){
				return $query->wherein('location_id', $loc_list)
				->whereBetween('course_date', array($dateStart, $dateEnd))
				->where('cancelled', 0)
				->where('active', 1)
				->orderBy('course_date')
				->orderBy('location_id');
			})
			)
			->where('type', 'FaceToFace')
			->where('active', 1)
			->orderBy('order')
			->remember(10)
			->get();

		$methods = PaymentMethod::where('active',1)->where('show_online',1)->orderBy('order')->remember(Config::get('cache.minutes', 1))->get();

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
				$result = OrderService::getBookingMessage($id);
				$message = $result['message'];
				$order = $result['order'];
				
				$result = ($order->isPaid() || stripos($order->last_payment_method, 'Pay Later') !== false) ? 'well alert-success' : 'well alert-warning';
				
				return Redirect::to('/backend/booking/newBooking')->with('success', $message);
			}
			else
			{
				$message = "<h3>There was a problem with your booking, please contact Coffee School nearest office</h3>";
				return Redirect::to('/backend/booking/newBooking?OrderId='.$id)->with('error', $message);
			}
		}
		else
		{
                    if (Sentry::getUser()->hasAnyAccess(array('admin')))
                    {
                        $group='admin';
                    }
                    else if(Sentry::getUser()->hasAnyAccess(array('agent'))){
                        $userGroup=Sentry::getUser();
                        $agentData=Agent::where('user_id', '=', $userGroup['id'])->firstOrFail();
                        $agentRecord=array_search($agentData['name'],$agents);
                        $group='agent';
                        
                    }
                    else{
                        $group='other';
                    }
			return View::make('bookings.backend.booking', compact(
                                    'group','agentData','agentRecord','methods','locations', 'courses', 'agents', 'vouchers', 'companies', 'referrers', 'order_id', 'order_type', 'languages',
				'titles','countries', 'states','achievements_list','disabilities_list','study_reasons_list','usi_visa_issue_countries'));
		}
	}
	
	public function newGroupBooking() {

		$id = Input::get('id');
		$res = Input::get('r');
		$order_id = Input::get('OrderId');
		
		//$agents = array('' => 'Select Agent:') + Agent::orderBy('name')->lists('name', 'id');
		$ags = Agent::orderBy('name')->get(array( 'name', 'id', 'contact_name', 'phone', 'mobile', 'email', 'contact_position'));
		$agents = array();
		$agents[''] = 'Select Agent:';
		foreach( $ags as $ag)
		{
			$parts = explode(" ", $ag->contact_name);
			$last_name = array_pop($parts);
			$first_name = implode(" ", $parts);
			$aux = array('id'=>$ag->id, 'name' => $ag->name, 'last_name' => $last_name, 'first_name' => $first_name, 'contact_position' => $ag->contact_position, 'email' => $ag->email, 'mobile' => (empty($ag->mobile) ? $ag->phone : $ag->mobile));
			$agents[json_encode($aux)] = $ag->name;
		}
		
		//$companies =  array('' => 'Select Company - No Commissions') + Company::orderBy('name')->lists('name', 'id');		
		$cos = Company::orderBy('name')->get(array( 'name', 'id', 'contact_name', 'phone', 'mobile', 'email', 'contact_position'));
		$companies = array();
		$companies[''] = "Select Company - No Commissions'";
		foreach( $cos as $co)
		{
			$parts = explode(" ", $co->contact_name);
			$last_name = array_pop($parts);
			$first_name = implode(" ", $parts);
			$aux = array('id'=>$co->id, 'name' => $co->name, 'last_name' => $last_name, 'first_name' => $first_name, 'contact_position' => $co->contact_position, 'email' => $co->email, 'mobile' => (empty($co->mobile) ? $co->phone : $co->mobile));
			$companies[json_encode($aux)] = $co->name;
		}
		$locations = array('' => 'Select Location');
		$locs = Location::where('parent_id', 0)->remember(Config::get('cache.minutes', 1))->get();
		foreach ($locs as $location)
		{
			$group = array();
			$group = array_add($group, $location->id , $location->name);
			foreach ($location->children as $loc)
			{	
				$group = array_add($group, $loc->id , $loc->name);
			}
			$locations = array_add($locations, $location->name, $group);
		}

		$backend = 1;
		$dateStart = $backend ? date("Y-m-d", strtotime("-6 month", time())) : date("Y-m-d");
		$dateEnd = date("Y-m-d", strtotime("+2 month", time()));
		
		$courses = Course::with(Array('instances' => function($query) use($dateStart, $dateEnd){
				return $query->fromLocation(1)
				->whereBetween('course_date', array($dateStart, $dateEnd))
				->where('cancelled', 0)
				->where('active', 1)
				->orderBy('location_id')
				->orderBy('course_date');				
			}))
			->where('id', '!=', 9)
			->where('type', 'FaceToFace')
			->where('active', 1)
			->orderBy('order')
			->remember(10)
			->get();

		$course_times = Config::get('utils.course_times', array());

		$methods = PaymentMethod::where('active',1)->where('show_online',1)->orderBy('order')->remember(Config::get('cache.minutes', 1))->get();

		if (!empty($id))
		{
			if ($res == 's')
			{
				$result = OrderService::getBookingMessage($id);
				$message = $result['message'];
				return Redirect::to('backend/booking/newGroupBooking')->with('success',$message);
			}
			else
			{
				$message = "<h3>There was a problem with your booking, please contact Coffee School nearest office</h3>";
				return Redirect::to('backend/booking/newGroupBooking?OrderId='. $id)->with('error',$message);
			}
		}
		else
			return View::make('bookings.group.group', compact('methods', 'locations', 'courses', 'course_times', 'agents', 'companies', 'order_id'));
	}
	
	public function newPurchase() {

		$id = Input::get('id');
		$res = Input::get('r');
		$order_id = Input::get('OrderId');
		
		$agents = array('' => 'Select Agent:') + Agent::lists('name', 'id');
		$companies =  array('' => 'Select Company - No Commissions') + Company::lists('name', 'id');;			
		$locations = array('' => 'Select Location');
		$locs = Location::with('children')->where('parent_id', 0)->remember(Config::get('cache.minutes', 1))->get();
		foreach ($locs as $location)
		{
			$group = array();
			$group = array_add($group, $location->id , $location->name);
			foreach ($location->children as $loc)
			{	
				$group = array_add($group, $loc->id , $loc->name);
			}
			$locations = array_add($locations, $location->name, $group);
		}

		$backend = 1;
		$products = Product::where('active', 1)->remember(Config::get('cache.minutes', 1))->get();
		$qtys = array(
			'1'=>'1','2'=>'2','3'=>'3','4'=>'4','5'=>'5','6'=>'6','7'=>'7','8'=>'8','9'=>'9','10'=>'10',
			'11'=>'11','12'=>'12','13'=>'13','14'=>'14','15'=>'15','16'=>'16','17'=>'17','18'=>'18','19'=>'19','20'=>'20',
			'21'=>'21','22'=>'22','23'=>'23','24'=>'24','25'=>'25','26'=>'26','27'=>'27','28'=>'28','29'=>'29','30'=>'30'
		);

		$methods = PaymentMethod::where('active',1)->where('show_online',1)->orderBy('order')->remember(Config::get('cache.minutes', 1))->get();

		if (!empty($id))
		{
			if ($res == 's')
			{
				$result = OrderService::getBookingMessage($id);
				$message = $result['message'];
				return Redirect::to('backend/booking/newPurchase')->with('success', $message);
			}
			else
			{
				$message = "<h3>There was a problem with your booking, please contact Coffee School nearest office</h3>";
				return Redirect::to('backend/booking/newPurchase?OrderId='. $id)->with('error', $message);
			}
		}
		else
			return View::make('bookings.purchase.purchase', compact('methods', 'locations', 'products', 'agents', 'companies', 'order_id', 'qtys'));
	}
	
	public function findBooking() {
		
		$orders = SearchService::ProcessBookingSearch();		
		
		$search_types = Config::get('utils.booking_search_types', array());
		$statuses = array(''=>'Select Order Status') + Status::where('status_type', 'Order')->remember(Config::get('cache.minutes', 1))->lists('name','id');
		$methodsCode = PaymentMethod::where('active', 1)->orderBy('name')->remember(Config::get('cache.minutes', 1))->lists('name', 'code');
		Input::flash();
		return View::make('bookings.search.home', compact('search_types','statuses','orders', 'methodsCode'));
	}
	
	public function findBookingByOrderId($id) {
		
		$orders = SearchService::ProcessBookingSearchByOrderId($id);		
		
		$search_types = Config::get('utils.booking_search_types', array());
		$statuses = array(''=>'Select Order Status') + Status::where('status_type', 'Order')->remember(Config::get('cache.minutes', 1))->lists('name','id');
		$methodsCode = PaymentMethod::where('active', 1)->orderBy('name')->remember(Config::get('cache.minutes', 1))->lists('name', 'code');
		Input::flash();
		return View::make('bookings.search.home', compact('search_types','statuses','orders', 'methodsCode'));
	}

}