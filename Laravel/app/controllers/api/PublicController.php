<?php namespace Controllers\Api;

use Controller;
use App\Services\BookingPublic;
use App\Services\BookingGroup;
use App\Services\BookingPurchase;
use App\Services\BookingOnline;
use Config, Input, Lang, Redirect, Sentry, Validator, View, DB, Response, Exception, stdClass;
use CourseBundle, CoursePrice, Agent, AgentCourse, Company, Location, Course, CourseInstance, Order, Item, Invoice;
use Customer, Status, Voucher, Product, Marketing, CmsPage, OnlineRoster;
use OrderService, SearchService, PdfService, Utils, Log;

class PublicController extends Controller {

	public function __construct(BookingPublic $public, BookingOnline $online, BookingGroup $group, BookingPurchase $purchase)
	{
		$this->getFlags();

		if($this->is_group_booking)
			$this->service = $group;
		else if($this->is_public_booking)
			$this->service = $public;
		else if($this->is_online_booking)
			$this->service = $online;
		else if($this->is_product_purchase)
			$this->service = $purchase;
		else
			$this->service = $public;

	}

	private function getFlags()
	{
		$payment = Input::get('Payment');
		if (empty($payment))
			$payment = Input::all();
		$this->is_public_booking = isset($payment['IsPublicBooking']) ?  filter_var($payment['IsPublicBooking'], FILTER_VALIDATE_BOOLEAN) : false;
		$this->is_online_booking = isset($payment['IsOnlineBooking']) ?  filter_var($payment['IsOnlineBooking'], FILTER_VALIDATE_BOOLEAN) : false;
		$this->is_group_booking = isset($payment['IsGroupBooking']) ?  filter_var($payment['IsGroupBooking'], FILTER_VALIDATE_BOOLEAN) : false;
		$this->is_product_purchase = isset($payment['IsProductPurchase']) ?  filter_var($payment['IsProductPurchase'], FILTER_VALIDATE_BOOLEAN) : false;
		$this->is_machine_hire = isset($payment['IsMachineHire']) ? filter_var($payment['IsMachineHire'], FILTER_VALIDATE_BOOLEAN) : false;
	}

	public function payWayPurchase()
	{
		$result = $this->service->payWayPurchase();		
		return Response::json($result);
	}

	public function paypalPurchase()
	{
		$orderId = $this->service->initiatePayPalPurchase();
		return Response::json(array('order_id' => $orderId));
	}

	public function submitToPayPal()
	{
		$this->service->submitToPayPal();	
	}

	public function cancelPayPalPurchase()
	{
		$payment = $this->service->cancelPayPalPurchase();		

		if($this->is_group_booking)
		{
			return Redirect::to('/backend/booking/newGroupBooking?OrderId=' . $payment['OrderId'])
			->with('error', 'Payment cancelled by user.');
		}
		else if($this->is_public_booking)
		{
			if ($payment['Backend'] == '1')	
				return Redirect::to('/backend/booking/newBooking?OrderId=' . $payment['OrderId'])
				->with('error', 'Payment cancelled by user.');
			else
				return Redirect::to('/cancelled/' . $payment['OrderId']);
				//return Redirect::to($payment['FrontendUrl'] . '?r=f&id=' . $payment['OrderId']);
		}
		else if($this->is_online_booking)
		{
			if ($payment['Backend'] == '1')	
				return Redirect::to('/backend/booking/newOnlineBooking?OrderId=' . $payment['OrderId'])
				->with('error', 'Payment cancelled by user.');
			else
				return Redirect::to($payment['FrontendUrl'] . '?r=f&id=' . $payment['OrderId']);
		}
		else
		{
			return Redirect::to('/backend/booking/newPurchase?OrderId=' . $payment['OrderId'])
			->with('error', 'Payment cancelled by user.');
		}
	}

	public function completePayPalPurchase()
	{
		$payment = $this->service->completePayPalPurchase();		

		if($this->is_group_booking)
		{
			$result = OrderService::getBookingMessage($payment['OrderId']);
			$message = $result['message'];
			return Redirect::route('backend.booking.newGroupBooking')->with('success', $message);
		}
		else if($this->is_public_booking)
		{
			if ($payment['Backend'] == '1')	
			{
				$result = OrderService::getBookingMessage($payment['OrderId']);
				$message = $result['message'];
				return Redirect::route('backend.booking.newBooking')->with('success', $message);
			}
			else
			{
				//if (parse_url($payment['FrontendUrl'], PHP_URL_QUERY))
				//	$redirect_to = $payment['FrontendUrl']  . '&r=s&id=' . $payment['OrderId'];
				//else
				//	$redirect_to = $payment['FrontendUrl']  . '?r=s&id=' . $payment['OrderId'];				
				//
				//return Redirect::to($redirect_to);
				return Redirect::to('/thankyou/' . $payment['OrderId']);
			}
		}
		else if($this->is_online_booking)
		{
			if ($payment['Backend'] == '1')	
			{
				$result = OrderService::getBookingMessage($payment['OrderId']);
				$message = $result['message'];
				return Redirect::route('backend.booking.newOnlineBooking')->with('success', $message);
			}
			else
			{
				if (parse_url($payment['FrontendUrl'], PHP_URL_QUERY))
					$redirect_to = $payment['FrontendUrl']  . '&r=s&id=' . $payment['OrderId'];
				else
					$redirect_to = $payment['FrontendUrl']  . '?r=s&id=' . $payment['OrderId'];				
				
				return Redirect::to($redirect_to);
			}
		}
		else
		{
			$result = OrderService::getBookingMessage($payment['OrderId']);
			$message = $result['message'];
			return Redirect::route('backend.booking.newPurchase')->with('success', $message);
		}

	}

	public function cancelPayWayPurchase()
	{
		$payment = $this->service->cancelPayWayPurchase();		
		$this->is_public_booking = isset($payment['IsPublicBooking']) ?  filter_var($payment['IsPublicBooking'], FILTER_VALIDATE_BOOLEAN) : false;
		$this->is_online_booking = isset($payment['IsOnlineBooking']) ?  filter_var($payment['IsOnlineBooking'], FILTER_VALIDATE_BOOLEAN) : false;
		$this->is_group_booking = isset($payment['IsGroupBooking']) ?  filter_var($payment['IsGroupBooking'], FILTER_VALIDATE_BOOLEAN) : false;
		$this->is_product_purchase = isset($payment['IsProductPurchase']) ?  filter_var($payment['IsProductPurchase'], FILTER_VALIDATE_BOOLEAN) : false;
		$this->is_machine_hire = isset($payment['IsMachineHire']) ? filter_var($payment['IsMachineHire'], FILTER_VALIDATE_BOOLEAN) : false;

		if($this->is_group_booking)
		{
			return Redirect::to('/backend/booking/newGroupBooking?OrderId=' . $payment['OrderId'])
			->with('error', 'Payment cancelled by user.');
		}
		else if($this->is_public_booking)
		{
			if ($payment['Backend'] == '1')
			{	
				return Redirect::to('/backend/booking/newBooking?OrderId=' . $payment['OrderId'])
				->with('error', 'Payment cancelled by user.');
			}
			else
			{
				return Redirect::to('/cancelled/' . $payment['OrderId']);
				//return Redirect::to($payment['FrontendUrl'] . '?r=f&id=' . $payment['OrderId']);
			}
		}
		else if($this->is_online_booking)
		{
			if ($payment['Backend'] == '1')	
				return Redirect::to('/backend/booking/newOnlineBooking?OrderId=' . $payment['OrderId'])
				->with('error', 'Payment cancelled by user.');
			else
				return Redirect::to($payment['FrontendUrl'] . '?r=f&id=' . $payment['OrderId']);
		}
		else
		{
			return Redirect::to('/backend/booking/newPurchase?OrderId=' . $payment['OrderId'])
			->with('error', 'Payment cancelled by user.');
		}
	}

	public function completePayWayPurchase()
	{
		$payment = $this->service->completePayWayPurchase();
				
		$this->is_public_booking = isset($payment['IsPublicBooking']) ?  filter_var($payment['IsPublicBooking'], FILTER_VALIDATE_BOOLEAN) : false;
		$this->is_online_booking = isset($payment['IsOnlineBooking']) ?  filter_var($payment['IsOnlineBooking'], FILTER_VALIDATE_BOOLEAN) : false;
		$this->is_group_booking = isset($payment['IsGroupBooking']) ?  filter_var($payment['IsGroupBooking'], FILTER_VALIDATE_BOOLEAN) : false;
		$this->is_product_purchase = isset($payment['IsProductPurchase']) ?  filter_var($payment['IsProductPurchase'], FILTER_VALIDATE_BOOLEAN) : false;
		$this->is_machine_hire = isset($payment['IsMachineHire']) ? filter_var($payment['IsMachineHire'], FILTER_VALIDATE_BOOLEAN) : false;

		if($this->is_group_booking)
		{
			$result = OrderService::getBookingMessage($payment['OrderId']);
			$message = $result['message'];
			return Redirect::route('backend.booking.newGroupBooking')->with('success', $message);
		}
		else if($this->is_public_booking)
		{
			if ($payment['Backend'] == '1')	
			{
				$result = OrderService::getBookingMessage($payment['OrderId']);
				$message = $result['message'];
				return Redirect::route('backend.booking.newBooking')->with('success', $message);
			}
			else
			{
				
				//if (parse_url($payment['FrontendUrl'], PHP_URL_QUERY))
				//	$redirect_to = $payment['FrontendUrl']  . '&r=s&id=' . $payment['OrderId'];
				//else
				//	$redirect_to = $payment['FrontendUrl']  . '?r=s&id=' . $payment['OrderId'];				
								
				return Redirect::to('/thankyou/' . $payment['OrderId']);
			}
		}
		else if($this->is_online_booking)
		{
			if ($payment['Backend'] == '1')	
			{
				$result = OrderService::getBookingMessage($payment['OrderId']);
				$message = $result['message'];
				return Redirect::route('backend.booking.newOnlineBooking')->with('success', $message);
			}
			else
			{
				if (parse_url($payment['FrontendUrl'], PHP_URL_QUERY))
					$redirect_to = $payment['FrontendUrl']  . '&r=s&id=' . $payment['OrderId'];
				else
					$redirect_to = $payment['FrontendUrl']  . '?r=s&id=' . $payment['OrderId'];				
				
				return Redirect::to($redirect_to);
			}
		}
		else
		{
			$result = OrderService::getBookingMessage($payment['OrderId']);
			$message = $result['message'];
			return Redirect::route('backend.booking.newPurchase')->with('success', $message);
		}

	}

	public function queuePayWayServerResponse()
	{
		$this->service->queuePayWayServerResponse();		
	}

	public function checkPayWayPurchase()
	{
		$this->service->checkPayWayPurchase();		
	}

	public function processPurchase()
	{
		$orderId = $this->service->processPurchase();		
	
		return Response::json(array('order_id' => $orderId));
	}

	public function completeCcPurchase()
	{
		$orderId = $this->service->completeCcPurchase();		
		
		if($this->is_group_booking)
		{
			return Redirect::route('backend.booking.newGroupBooking');
		}
		else if($this->is_public_booking)
		{
			return Redirect::route('backend.booking.newBooking');
		}
		else if($this->is_online_booking)
		{
			return Redirect::route('backend.booking.newOnlineBooking');
		}
		else
		{
			return Redirect::route('backend.booking.newPurchase');
		}
		
	}
	
	public function completeBooking()
	{
		$layout = 'public';
		$order_id = Input::get('id');
		$order = Order::find($order_id);
		$pages = array();
		if ($layout == 'public')
			return View::make('bookings.public.thankyou', compact('pages', 'order', 'order_id'));
		else
			return View::make('bookings.backend.thankyou', compact('order', 'order_id'));
	}



	
	public function download($id)
	{
		return PdfService::download('Order', 'backend.invoices.invoice', '/invoices/', 'invoice-', $id);	
	}
	
	public function voucherDownload($id)
	{
		return PdfService::download('Voucher', 'backend.vouchers.voucher', '/vouchers/', 'voucher-', $id);	
	}

	public function getBundles()
	{
		
		$bundles = CourseBundle::with('location','bundles')
						->remember(Config::get('cache.minutes', 1))
						->get();
		
		$list = array();
		$comboId = '';
		$comboOff = 0;
		$comboOn = 0;
		$comboOffTotal = 0;
		$comboOnTotal = 0;
		
		if ($bundles)
		{

			$last_bundle_id = 0;
			
			foreach ($bundles as $bundle)
			{
				$comboId = '';
				$comboOff = '';
				$comboOn = '';
				//$comboOffTotal = 0;
				//$comboOnTotal = 0;
				$b = new stdClass();
				$b->id = $bundle->id;
				$b->location = $bundle->location_id;
				$b->minimum = $bundle->students_min;

				foreach ($bundle->bundles as $course)
				{
					$comboId .= $course->pivot->course_id . ",";
					$comboOff .= $course->pivot->price_offline == "0" && $course->pivot->price_online == "0" ? "" : $course->pivot->price_offline . ",";
					$comboOn .= $course->pivot->price_offline == "0" && $course->pivot->price_online == "0" ? "" : $course->pivot->price_online . ",";
					//$comboOffTotal += $course->pivot->price_offline;
					//$comboOnTotal += $course->pivot->price_online;
				}
				
				//$b->courseBundle = $previous['id'];
				$b->bundleIds = substr($comboId, 0, -1);
				$b->bundleOffs = substr($comboOff, 0, -1);
				$b->bundleOns = substr($comboOn, 0, -1);
				//$b->priceOffLine = $comboOffTotal;
				//$b->priceOnLine = $comboOnTotal;
				array_push($list,$b);
				
				$last_bundle_id = $bundle->id;
				
			}

			$defaultList = array();
			
			$prices = CoursePrice::with('location')->remember(Config::get('cache.minutes', 1))->get();
			//$all_parents = Location::with('children')->where('parent_id', 0)->get();
			//$parents = array();
			//foreach($all_parents as $parent)
			//{
			//	$parents = array_add($parents, $parent->id, $parent->children);			
			//}
			foreach ($prices as $price)
			{
				$b = new stdClass();
				$b->id = $last_bundle_id + $price->id;
				$b->location = $price->location_id;
				$b->minimum = 1;
				$b->bundleIds = $price->course_id;
				$b->bundleOffs = $price->price_offline;
				$b->bundleOns = $price->price_online;
				$b->bundleDisc = "$price->students_min|$price->discount_type|$price->discount";
				//$b->priceOffLine = $price->price_offline;
				//$b->priceOnLine = $price->price_online;
				array_push($defaultList,$b);
				
				//foreach ($parents[$price->location_id] as $child_location)
				//{
				//	$b2 = clone $b;
				//	$b2->id .=  '-'.$child_location->id;
				//	$b2->location = $child_location->id;
				//	array_push($defaultList,$b2);
				//}
				
				foreach ($price->location->children as $child_location)
				{
					$b2 = clone $b;
					$b2->id .=  '-'.$child_location->id;
					$b2->location = $child_location->id;
					array_push($defaultList,$b2);
				}
				
			}
			//print_r($defaultList);
			
			$exists = false;
			foreach ($defaultList as $bundle) {
				foreach ($list as $b) {
					if($b->location == $bundle->location && (string)$b->bundleIds == (string)$bundle->bundleIds){
						$exists = true;
						break;
					}
				}
				if (!$exists) {
					array_push($list,$bundle);
				}
				$exists = false;
			}
            if (Sentry::check())
            {
                $user = Sentry::getUser();
                $agent = Agent::where('user_id', '=' ,$user->id)->first();
                if (Sentry::getUser()->hasAnyAccess(array('agent')) && !empty($agent))
                {
                    $agentCourses = AgentCourse::with('location')->where('agent_id', '=' ,$agent->id)->get();

                    $agentList = array();
                    foreach ($agentCourses as $coursePrice)
                    {
                        $b = new stdClass();
                        $b->id = $last_bundle_id + $coursePrice->id;
                        $b->location = $coursePrice->location_id;
                        $b->minimum = 1;
                        $b->bundleIds = $coursePrice->course_id;
                        $b->bundleOffs = $coursePrice->price_offline;
                        $b->bundleOns = $coursePrice->price_online;
                        $b->bundleDisc = "";
                        array_push($agentList,$b);
                    }
                    foreach ($agentList as $bundle) {
                            foreach ($list as $key=>$b) {
                                    if($b->location == $bundle->location && (string)$b->bundleIds == (string)$bundle->bundleIds){
                                        unset($list[$key]);
                                    }
                            }
                            array_push($list,$bundle);
                    }
                }
            }
			
			return Response::json($list);
		}
		else
		{
			return Response::json(array(
				'success' => false,
				'Message' => "Problem loading bundles from database "
				), 500);
		}
	}
	
	//not in use yet (04/03/2015)
	public function getAllBundles()
	{
		
		$bundles = CourseBundle::with('location','bundles')
			->remember(Config::get('cache.minutes', 1))
			->get();
		//return Response::json($bundles);
		
		$list = array();
		$last_bundle_id = 0;
		
		if ($bundles)
		{			
			foreach ($bundles as $bundle)
			{
				$b = new stdClass();
				$b->id = $bundle->id;
				$b->location = $bundle->location_id;
				$b->minimum = $bundle->students_min;
				$b->total_online = $bundle->total_online;
				$b->total_offline = $bundle->total_offline;
				$b->bundles = array();
				foreach ($bundle->bundles as $course)
				{
					$c = new stdClass();
					$c->course_id = $course->pivot->course_id;
					$c->price_offline = $course->pivot->price_offline == "0" && $course->pivot->price_online == "0" ? "" : $course->pivot->price_offline;
					$c->price_online = $course->pivot->price_offline == "0" && $course->pivot->price_online == "0" ? "" : $course->pivot->price_online;
					
					$b->bundles[] = $c;
				}				
				array_push($list,$b);
				$last_bundle_id = $bundle->id;
			}

			$defaultList = array();
			
			$prices = CoursePrice::with('location')->remember(Config::get('cache.minutes', 1))->get();
			foreach ($prices as $price)
			{
				$b = new stdClass();
				$b->id = $last_bundle_id + $price->id;
				$b->location = $price->location_id;
				$b->minimum = 1;

				$b->total_offline = $price->price_offline;
				$b->totla_online = $price->price_online;
				$b->bundleDisc = "$price->students_min|$price->discount_type|$price->discount";

				$c->course_id = $price->course_id;
				$c->price_offline = $price->price_offline;
				$c->price_online = $price->price_online;
				
				$b->bundles[] = $c;
				
				array_push($defaultList,$b);
				
				
				foreach ($price->location->children as $child_location)
				{
					$b2 = clone $b;
					$b2->id =  $b2->id.'-'.$child_location->id;
					$b2->location = $child_location->id;
					array_push($defaultList,$b2);
				}
				
			}
			
			$exists = false;
			foreach ($defaultList as $bundle) 
			{
				$bundleIds = '';
				foreach ($bundle->bundles as $bs) {
					$bundleIds .= $bs->course_id . '|';
				}
				foreach ($list as $b) 
				{
					$bIds = '';
					foreach ($b->bundles as $bs) {
						$bIds .= $bs->course_id . '|';
					}
					if($b->location == $bundle->location && (string)$bIds == (string)$bundleIds) {
						$exists = true;
						break;
					}
				}
				if (!$exists) {
					array_push($list,$bundle);
				}
				$exists = false;
			}
			
			return Response::json($list);
		}
		else
		{
			return Response::json(array(
				'success' => false,
				'Message' => "Problem loading bundles from database "
				), 500);
		}
	}
	
	public function getInstanceById()
	{
		$data = Input::all();
		$id = Input::get('id');  //instance id
		$qty = Input::get('qty');  // quantity
		$loc = Input::get('loc'); // location id
		$validate = is_null(Input::get('validate')) ? 1 : Input::get('validate'); // validate id

		$giftVoucher = false;
		if (preg_match('/gv/', $id)) {
			$giftVoucher = true;
			$course_id = str_replace('gv', '', $id);	
		}
		
		try {
			
			if ($giftVoucher)
			{
				$query = CourseInstance::with('location','course', 'special');
				$locations = \DB::table('locations')
					->where('id', $loc)
					->orWhere('parent_id', $loc)
					->lists('id');
				$query = $query->wherein('location_id', $locations);
				$query = $query->where('course_id', $course_id);
				$query = $query->where('course_date','>=', date("Y-m-d"));
				$instance = $query->orderBy('course_date')->first();

			}
			else
			{
				$instance = CourseInstance::with('location','course', 'special')->find($id);		
				
				
				$options = array('qty'=>$qty, 'new_total'=>$instance->students + $qty, 'pre_validation'=> 0);

		\Log::debug('get instance: ' . $instance->maximum_students . ' - ' . $instance->students . ' + ' . intval($options['qty']) . ' | options: ' .json_encode($options) );
				
				// if the varible was passed or it was passed and was true then we validate
				if( $validate != 0 ) {
					$options['pre_validation'] = 1;
					$instance->validate($options);
				}

			}
			
			if (!$instance)
			{
				$msg = 'There are no available classes for this course';
				throw new Exception($msg);
			}

			$response = new stdClass();
			$response->id = $giftVoucher ? $id : $instance->id;
			$response->courseInstance = $giftVoucher ? $id : $instance->id;
			$response->courseType = $instance->course_id;
			$response->itemType = $giftVoucher ? 'Voucher' : ($instance->course->type == 'Online' ? 'OnlineFaceToFace' : 'Course');
			$response->location = $instance->location_id;
			$response->parentLocation = $instance->location->parent_id == 0 ? $instance->location_id : $instance->location->parent_id;
			$response->parentLocationName = $instance->parent_location->name;
			$response->special = $instance->special && $instance->special->active == '1' ? true : false;
			$response->vacancies = $giftVoucher ? 0 : $instance->vacancies;
			$response->full = $instance->full;
			$response->courseName = $instance->course->name;
			$response->courseAddress = $instance->location->address . ', ' . $instance->location->city . ', ' . $instance->location->state;
			$response->courseDate = $giftVoucher ? 'Gift Voucher: (Face-to-Face)' : date('Y-m-d', strtotime($instance->course_date));
			$response->time_start = $giftVoucher ? '' : date('h:i A', strtotime($instance->time_start));
			$response->time_end = $giftVoucher ? '' : date('h:i A', strtotime($instance->time_end));

			$response->studentQty = 1;
			$response->discount = 0;
			$response->specialOnLine = $instance->special && $instance->special->active && isset($instance->special->price_online) ? $instance->special->price_online : 0;
			$response->specialOffLine = $instance->special &&  $instance->special->active && isset($instance->special->price_offline) ? $instance->special->price_offline : 0;
			$response->priceOnLine = 0;
			$response->priceOffLine = 0;
			$response->applyGst = $instance->course->gst == '1' ? true : false;
			$response->feeRebook = 0;
			$response->isVoucher = $giftVoucher;
			$response->pairCourseId = '';
			$response->pairClassId = '';
			$response->pairLocations = '';
			$response->pairCourseIdToAdd = $instance->course->pair_course_id_to_add;
			$response->pairClassIdToAdd = $instance->pair_class_id_to_add;
			
			
			if (!$giftVoucher && !empty($instance->course->pair_course_id))
			{
				//$locations = \DB::table('locations')
				//	->where('id', $instance->location_id)
				//	->orWhere('parent_id', $instance->location_id)
				//	->lists('id');
				$query = CourseInstance::where('course_id', $instance->course->pair_course_id);
				//$query = $query->wherein('location_id', $locations);
				$query = $query->where('course_date', '<=', date('Y-m-d', strtotime($instance->course_date)));
				$inst = $query->orderBy('course_date')->first(array('id'));
				$response->pairCourseId = $instance->course->pair_course_id;
				$response->pairClassId = $inst->id;
				$response->pairLocations = $instance->course->pair_locations;
			}
			

			echo json_encode($response);



		}
		catch (Exception $e)
		{
			return \Response::json(array(
				'success' => false,
				'Message' => "Problem loading instance from database " . $e->getMessage()
				), 500);
		}

		
	}
	
	public function getBookingDetails($id) {

		$order = Order::find($id);
		$main_location = 0;
		
		//
		//dd(\Utils::q());
		//
		//exit;
		
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
				if (Utils::ItemTypeName($item->item_type_id) == 'Course' )
				{
					$inst = CourseInstance::with('special')->find($item->course_instance_id);
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
						'itemType' => $inst->itemtype ? $inst->itemtype->name : 'Course',
						'location' => $inst->location_id,
						'parentLocation' => $inst->location->parent_id == 0 ? $inst->location_id : $inst->location->parent_id,
						'parentLocationName' => $inst->parent_location->name,
						'full' => $inst->full,
						'courseName' => $inst->course->name,
						'courseAddress' => $inst->location->address . ',<br>' . $inst->location->city . ', ' . $inst->location->state,
						'courseDate' => date('Y-m-d', strtotime($inst->course_date)),
						'time_start' => date('h:i A', strtotime($inst->time_start)),
						'time_end' => date('h:i A', strtotime($inst->time_end)),
						
						'studentQty' => $item->qty,
						'discount' => $item->discount,
						'specialOffLine' => $inst->special ? $inst->special->price_offline: 0,
						'specialOnLine' => $inst->special ? $inst->special->price_online : 0,
						'priceOffLine' => $item->price,
						'priceOnLine' => $item->price,
						'priceOff' => $item->price,
						'priceOn' => $item->price,
						'applyGst' => $inst->course->gst == '1' ? true : false,
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
					$loc = $item->vouchers[0]->location_id;
					$main_location = $main_location != 0 ? $main_location : $loc;
					$c_id = $item->vouchers[0]->course_id;
					$query = CourseInstance::with('special');
					$locations = \DB::table('locations')
						->where('id', $loc)
						->orWhere('parent_id', $loc)
						->lists('id');
					$query = $query->wherein('location_id', $locations);
					$query = $query->where('course_id', $c_id);
					//$query = $query->where('course_date','>=', date("Y-m-d"));
					$query = $query->where('course_date','>=', $item->created_at);
					$inst = $query->orderBy('course_date')->first();

					$instance = array(
						'id' => 'gv' . $c_id,
						'courseType' => $c_id,
						'courseInstance' => 'gv' . $c_id,
						'itemType' =>'Voucher',
						'location' => $inst->location_id,
						'parentLocation' => $inst->location->parent_id == 0 ? $inst->location_id : $inst->location->parent_id,
						'parentLocationName' => $inst->parent_location->name,
						'full' => $inst->full,
						'courseName' => $inst->course->name,
						'courseAddress' => $inst->location->name,
						'courseDate' => 'Gift Voucher: (Face-to-Face)',
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
						'applyGst' => $inst->course->gst == '1' ? true : false,
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
		
		$parentlocation = Location::find($main_location);		
		if ($parentlocation)
			$parentlocation_id = $parentlocation->isParent() ? $parentlocation->id : $parentlocation->parent_id;
		
		$result = array(
			'id' => $order->id,
			'order_id' => $order->id,
			'parentlocation_id' => $parentlocation_id,
			'location_id' => $main_location,
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
	
	public function getVoucher($id)
	{
		try 
		{	
			$voucher = Voucher::find($id);
			if (!$voucher)
				throw new Exception("Problem retrieving voucher, <br>please contact Coffee School Office");
			$voucher_data = $voucher->toArray();
			$voucher_data['message'] = $voucher->message;

			return Response::json($voucher_data);
		}
		catch (Exception $e)
		{
			\Log::error($e);
			return Response::json(array(
				'success' => false,
				'Message' => $e->getMessage()
				), 500);
		}


	}

	public function createAgent()
	{
		try
		{
			$input = Input::json()->all();
			unset($input['IsValid']);
			unset($input['isNullo']);
			unset($input['errors']);
			unset($input['password']);
			unset($input['id']);
			$input['active'] = 1;
			$input['user_id'] = Sentry::getUser()->id;

			$agent = Agent::where('name', $input['name'])->first();


			if ($agent)
			{
				$response = new stdClass();
				$response->id = $agent->id;
				$response->name = $agent->name;

				echo json_encode($response);
			}
			else
			{
				$validation = Validator::make($input, Agent::$rules);

				if ($validation->passes())
				{
					$agent = Agent::create($input);
					$response = new stdClass();
					$response->id = $agent->id;
					$response->name = $agent->name;

					echo json_encode($response);
				}
				throw new Exception("Validation Error, Name, Email and Phone are compulsory");
			}

		}
		catch (Exception $e)
		{
			return \Response::json(array(
				'success' => false,
				'Message' => "Problem creating agent " . $e->getMessage()
				), 500);
		}
	}
	
	public function createCompany() 
	{
		try
		{
			$input = Input::json()->all();
			unset($input['IsValid']);
			unset($input['isNullo']);
			unset($input['errors']);
			unset($input['password']);
			unset($input['id']);
			$input['active'] = 1;
			$company = Company::where('name', $input['name'])->first();


			if ($company)
			{
				$response = new stdClass();
				$response->id = $company->id;
				$response->name = $company->name;

				echo json_encode($response);
			}
			else
			{
				$validation = Validator::make($input, Company::$rules);

				if ($validation->passes())
				{
					$company = Company::create($input);
					$response = new stdClass();
					$response->id = $company->id;
					$response->name = $company->name;

					echo json_encode($response);
				}
				throw new Exception("Validation Error, Name, Email and Phone are compulsory");
			}

		}
		catch (Exception $e)
		{
			return \Response::json(array(
				'success' => false,
				'Message' => "Problem creating Company " . $e->getMessage()
				), 500);
		}
			
	}



	public function sendFriendEmail()
	{
		$input = Input::json()->all();
		
		try 
		{	
			Log::info('Email: ' . json_encode($input));
			if (!empty($input['type']) && $input['type'] == 'SendPageToFriend')
			{
				$result = new \stdClass;
				$result->subject = 'Coffee RSA School courses';
				$result->email = $input['friend_email'];
				$result->name = $input['friend_name'];
				$result->body = "<p>Hi ". $input['friend_name'] . "</p>";
				$result->body .= "<p>I Thought you would be interested in the following page:</p>";
				$result->body .= "<p><a href='". $input['page_address'] . "' >". $input['page_address'] . "</a></p>";
				$result->body .= "<p>Regards</p><p>". $input['your_name'] . "</p>";
				
				$data = array('result'=> $result);	
				
				\Mail::send('backend.messages.custom', $data, function($message) use ($result)
					{ $message->subject($result->subject)->to($result->email, ''); });
				
				$response = '';	
			}
			else
			{		
				$order = Order::find($input['order_id']);
				
				$subject = "Join " . $order->customer->name . " at Coffee School";
				$body = "<p>Your friend " . $order->customer->name . " has just booked:</p>";
				$body .= "<ul>";
				foreach($order->items as $item)
				{
					$body .= "<li>" . $item->instance->course->name . " at " . $item->instance->location->complete_address . " - " . $item->instance->course_date_time . "</li>";
				}
				$body .= "</ul>";
				$body .= "<p><a href='" . urldecode(urlencode($input['share_link'])) ."' title='Click here to join them'>Click here to join them</a></p>";

				$result = new \stdClass;
				$result->subject = $subject;
				$result->email = $input['email'];
				$result->name = $order->customer->name;
				$result->body = $body;
				
				$data = array('result'=> $result);	
				
				\Mail::send('backend.messages.custom', $data, function($message) use ($result)
					{ $message->subject($result->subject)->to($result->email, ''); });
				
				$response = '';	
			}				
				
			return Response::json(array('msg'=>"Successfully sent email<br>$response"));

		}
		catch (Exception $e)
		{
			Log::error($e);
			return Response::json(array(
				'success' => false,
				'Message' => "Problem sending friend email <br>" . $e->getMessage()
				), 500);
		}
	}

	public function sendFriendSms()
	{
		$input = Input::json()->all();
		
		try 
		{	
			Log::info('Sms: ' . json_encode($input));
			$order = Order::find($input['order_id']);
			
			$subject = "Join " . $order->customer->name . " at Coffee School";
			$body = $order->customer->name . " has just booked: ";
			foreach($order->items as $item)
			{
				$body .= $item->instance->course->name . " at " . $item->instance->location->complete_address . " - " . $item->instance->course_date_time . ".";
			}
			$body .= "Visit " . urldecode(urlencode($input['share_link'])) ."to join them. ";
			$body .= "or call " . $order->active_items->first()->instance->location->phone;
			
			
			$data = array(
				'to' => $input['mobile'],
				'text' => $body
				);
						
			$result = \SmsService::sendmsg($data);				
			if ($result['result'] != 'success')
			{
				throw new Exception($result['message']);
			}

			$response = implode(', ', array_map(function ($k, $v) { return "$k ($v)"; },array_keys($result),array_values($result)));
			return Response::json(array('msg'=>"Successfully sent sms <br>$response"));

		}
		catch (Exception $e)
		{
			Log::error($e);
			return Response::json(array(
				'success' => false,
				'Message' => "Problem sending friend sms <br>" . $e->getMessage()
				), 500);
		}
	}

	
	public function downloadFile($filename)
	{
		$extension = pathinfo($filename, PATHINFO_EXTENSION);
		$dir = storage_path() . '/others';		
		switch (strtolower($extension))
		{
			case 'pdf' :
				$dir = storage_path() . '/pdfs/';
				break;
			case 'doc' :
			case 'docx' :
			case 'xls' :
			case 'xlsx' :
				$dir = storage_path() . '/docos/';
				break;
			case 'jpg' :
			case 'jepg' :
			case 'png' :
			case 'gif' :
				$dir = storage_path() . '/images/';
				break;
			case 'mp3' :
			case 'mp4' :
			case 'wav' :
			case 'wma' :
			case 'avi' :
			case 'flv' :
			case 'm4v' :
			case 'mpeg' :
			case 'mpg' :
				$dir = storage_path() . '/videos/';
				break;
			default :
				$dir = storage_path() . '/others/';
		}
		return Response::download($dir . $filename, $filename, array('content-type' => 'application/octet-stream'));
	}
	
	public function downloadAttachment($filename)
	{
		$dir = storage_path() . '/attachments/';
		return Response::download($dir . $filename, $filename, array('content-type' => 'application/octet-stream'));
	}
	
	public function viewFile($filename)
	{
		$extension = pathinfo($filename, PATHINFO_EXTENSION);
		$dir = storage_path() . '/others';		
		switch (strtolower($extension))
		{
			case 'pdf' :
				$dir = storage_path() . '/pdfs/';
				break;
			case 'doc' :
			case 'docx' :
			case 'xls' :
			case 'xlsx' :
				$dir = storage_path() . '/docos/';
				break;
			case 'jpg' :
			case 'jepg' :
			case 'png' :
			case 'gif' :
				$dir = storage_path() . '/images/';
				break;
			case 'mp3' :
			case 'mp4' :
			case 'wav' :
			case 'wma' :
			case 'avi' :
			case 'flv' :
			case 'm4v' :
			case 'mpeg' :
			case 'mpg' :
				$dir = storage_path() . '/videos/';
				break;
			default :
				$dir = storage_path() . '/others/';
		}
		$path = $dir . $filename;
		return Utils::ViewFile($path, $filename, 10);
	}
	
	public function viewMarketingEmail($id, $customer_id)
	{
		$result = Marketing::find($id);
		$customer = Customer::find($customer_id);
		$result->first_name = $customer->first_name;
		$result->last_name = $customer->last_name;
		$result->email = $customer->email;
		$result->mobile = $customer->mobile;
		$result->unsubscribe = "<p><a style='color: #333 !important;' href='http://" .\Request::server('SERVER_NAME') ."/api/emails/unsubscribe/" . $customer_id . "'>unsubscribe</a></p>";
		$result->disclamer = "<br><br><p>Disclaimer: Ton Ton Song Pty Ltd recognises that your privacy is very important to you and we are committed to protecting your personal information. You have received this email because you have given permission to be corresponded to by Ton Ton Song Pty Ltd. To unsubscribe please click the unsubscribe link below. Ton Ton Song Pty Ltd is trading as Coffee School. ABN 9211 541 9988</p>";		
		
		$attachments = $result->attachments->lists('path', 'id');
		$result->attachments = "<p>Attachments: </p>";
		$result->attachments .= "<ul>";
		foreach ($attachments as $attachment)
		{
			$filename = pathinfo($attachment, PATHINFO_FILENAME);
			$extension = pathinfo($attachment, PATHINFO_EXTENSION);
			$result->attachments .= "<li><a style='color: #333 !important;' href='http://" .\Request::server('SERVER_NAME') ."/api/files/downloadAttachment/" . $filename . '.' . $extension . "'>" .$filename . "</a></li>";
		}
		$result->attachments .= "</ul>";
		
		$body = (!empty($result->body) ? $result->body : $result->sms_body) . $result->attachments . $result->disclamer . $result->unsubscribe;
		preg_match_all('#\{\{(.*?)\}\}#', $body, $matches);
		foreach($matches[1] as $match)
		{
			if (!empty($result->$match))
			{
				$body = str_replace("{{" .$match. "}}", $result->$match, $body);
			}
		}

		$result->body = $body;
		
		$page = new \CmsPage;
		$page->description = "";
		$page->keywords = "";
		$pages = CmsPage::with('children')->where('parent_id', 0)->where('active', 1)->orderBy('order')->remember(Config::get('cache.minutes', 1))->get();
		$pages = $pages->each(function($page)
			{
				$page->children = $page->children
				->filter(function($child) {if ($child->active)	return $child;})
				->sortBy(function($child) {return $child->order;});
			});		
		$locations = Location::with('children')
			->where('parent_id', 0)
			->where('active', 1)
			->remember(Config::get('cache.minutes', 1))
			->get();
		$specials = $page->parseSpecials(true, true);
		
			
		return View::make('emails.marketing.message-browser', compact('result', 'pages', 'page', 'locations', 'specials'));

	}

	public function emailUnsubscribe($id)
	{
		$customer = Customer::find($id);
		$customer->update(array('mail_out_email' => 0, 'mail_out_sms' => 0));
		$page = new \CmsPage;
		$page->description = "Marketing unsubscription";
		$page->keywords = "";
		$pages = CmsPage::with('children')->where('parent_id', 0)->where('active', 1)->orderBy('order')->remember(Config::get('cache.minutes', 1))->get();
		$pages = $pages->each(function($page)
			{
				$page->children = $page->children
				->filter(function($child) {if ($child->active)	return $child;})
				->sortBy(function($child) {return $child->order;});
			});		
		$locations = Location::with('children')
			->where('parent_id', 0)
			->where('active', 1)
			->remember(Config::get('cache.minutes', 1))
			->get();

		$specials = $page->parseSpecials(true, true);
		
		return View::make('emails.marketing.message-unsubscribe', compact('pages', 'page', 'locations', 'specials'));
	}

	public function getLocationsCourses()
	{

		$locations = Location::all()->toArray();

		$courses = Course::all()->toArray();
		
		$result  = array('locations' => $locations, 'courses' => $courses);
		return Response::json($result);
	}
}