<?php namespace Controllers\Api;

use AdminController;
use Cartalyst\Sentry\Users\LoginRequiredException;
use Cartalyst\Sentry\Users\PasswordRequiredException;
use Cartalyst\Sentry\Users\UserExistsException;
use Cartalyst\Sentry\Users\UserNotFoundException;
use Config, Input, Lang, Redirect, Sentry, Validator, View, DB, Response, Exception;
use CourseBundle, Location, CoursePrice, CourseInstance, Course;
use BookingService;
use Omnipay\Common\CreditCard;
use Omnipay\Common\GatewayFactory;

class ManagerController extends AdminController {

	public function getSpecials()
	{

		//	$dateStart = date("Y-m-d");
		//	$dateAdded = strtotime(date("Y-m-d", strtotime($dateStart)) . "+2 month");
		//	$dateEnd = date('Y-m-d', $dateAdded);

		////
		//	$sql = "SELECT ci.id, cp.priceOffline, cp.priceOnline FROM courseinstances ci ";
		//	$sql .= "join courseprices cp on cp.courseInstance = ci.id ";
		//	$sql .= "WHERE ci.courseDate BETWEEN '$dateStart' AND '$dateEnd' AND ";
		//	$sql .= "ci.special = 1 AND ";
		//	$sql .= "(ci.specialFrontEnd = 1 OR ci.specialFrontEnd = 0) AND ";
		//	$sql .= "ci.active = 1 AND cp.active = 1 ";

		////
		//	$rows = mysql_query($sql, $this->conn);
		//	$list = array();
		//	
		//	if ($rows)
		//	{
		//		$list = array();
		//		while ($row = mysql_fetch_array($rows))
		//		{
		//			$u = new SpecialBundle();
		//			$u->Init($row);
		//			array_push($list,$u);
		//		}
		//	}
		Response::json(array());
	}


	public function preProcessPurchase()
	{
		
	}

}