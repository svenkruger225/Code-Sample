<?php namespace App\Services;

use Request, stdClass, DB, Sentry, Config, Response, File, Input;
use Status, PaymentMethod, ItemType, MessageType, CreditNote;

class Utils {
	
	public function IsGroupActive($name)
	{
		if (Sentry::getUser()->hasAnyAccess(array('superuser')))
			$groups = Config::get('menu.superuser', array());
		elseif (Sentry::getUser()->hasAnyAccess(array('trainer')))
			$groups = Config::get('menu.trainer', array());
		elseif (Sentry::getUser()->hasAnyAccess(array('agent')))
			$groups = Config::get('menu.agent', array());
		else
			$groups = array();
		
		
		$exists = false;
		$group = isset($groups[$name]['children']) && count($groups[$name]['children']) > 0 ?  $groups[$name]['children'] : array();
		$group += array($name =>''); // we add the parent route as well
					
		$exists = false;
		foreach ( $group as $route => $menu)
		{
			if (Request::is($route) )
			{
				$exists = true;
				break;
			}
		}
		return $exists;
	}
	
	public function HasRoute($name)
	{
		return \Route::getRoutes()->hasNamedRoute("$name");
	}	

	public function format_number($number = null)
	{
		// Check if we have a valid number.
		if (is_null($number))
			return '';

		// Remove anything that isn't a number or decimal point.
		$number = (float) $number;

		// Return the formatted number.
		return number_format($number, 2, '.', ',');
	}

	public function format_currency($number = null)
	{
		// Return the currency formatted number.
		return '$' . $this->format_number($number);
	}

	public static function q($all = true) 
	{
		$queries = DB::getQueryLog();

		if($all == false) 
		{
			$last_query = end($queries);
			return $last_query;
		}

		return $queries;
	}
	
	public function getmicrotime() { 
		list($usec, $sec) = explode(" ",microtime()); 
		return ((float)$usec + (float)$sec);
	}

	public static function GetInput($key, $default = null, $base = null, $except = array())
	{
		if(!$base)
			$base = Input::all();
		
		if(count($except) > 0)
		{
			return array_diff_key( $base, array_flip( $except ) );
		}
		
		return isset($base[$key]) ? $base[$key] : $default;
		
	}
		
	public static function StatusId($status_type, $status_name) 
	{
		$status = Status::where('status_type', $status_type)->where('name', $status_name)->remember(720)->first();

		if ($status)
			return $status->id;

		return null;
	}		
	public static function StatusName($status_type, $status_id) 
	{
		$status = Status::where('status_type', $status_type)->where('id', $status_id)->remember(720)->first();

		if ($status)
			return $status->name;

		return null;
	}		
	public static function PaymentMethodId($code) 
	{
		$method = PaymentMethod::where('code', $code)->remember(720)->first();

		if ($method)
			return $method->id;

		return null;
	}		
	public static function MessageTypeId($name) 
	{
		$type = MessageType::where('name', $name)->remember(720)->first();

		if ($type)
			return $type->id;

		return null;
	}		
	public static function ItemTypeId($name) 
	{
		$type = ItemType::where('name', $name)->remember(720)->first();

		if ($type)
			return $type->id;

		return null;
	}		
	public static function ItemTypeName($id) 
	{
		$type = ItemType::where('id', $id)->remember(720)->first();

		if ($type)
			return $type->name;

		return null;
	}	

	public static function GenerateUsername( $min, $max, $case_sensitive = false )
	{
		// Set length
		$length = rand($min, $max);
		
		// Set allowed chars (And whether they should use case)
		if ( $case_sensitive )
		{
			$chars = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ1234567890";
		}
		else
		{
			$chars = "abcdefghijklmnopqrstuvwxyz1234567890";
		}
			
		// Get string length
		$chars_length = strlen($chars);
		
		// Create username char for char
		$username = "";
		
		for ( $i = 0; $i < $length; $i++ )
		{
			$username .= $chars[mt_rand(0, $chars_length)];
		}
		
		return $username;
		
	}		
	public static function GeneratePassword( $min = 8, $max = 8)
	{
		$length = ($min + $max) / 2;
		$chars = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ123456789@#$%&";
		$password = substr( str_shuffle( $chars ), 0, $length );
		return $password;

		// Set length
		$length = rand($min, $max);
		
		// Set charachters to use
		$lower = 'abcdefghijklmnopqrstuvwxyz';
		$upper = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';
		$chars = '123456789@#$%&';
		
		// Calculate string length
		$lower_length = strlen($lower);
		$upper_length = strlen($upper);
		$chars_length = strlen($chars);
		
		// Generate password char for char
		$password = '';
		$alt = time() % 2;
		for ($i = 0; $i < $length; $i++)
		{
			if ($alt == 0)
			{
				$password .= $lower[mt_rand(0, $lower_length)]; $alt = 1;
			}
			if ($alt == 1)
			{
				$password .= $upper[mt_rand(0, $upper_length)]; $alt = 2;
			}
			else
			{
				$password .= $chars[mt_rand(0, $chars_length)]; $alt = 0;
			}
		}
		return $password;
	}
	
	public static function parseTableToArray($contents)
	{
		$arr = array();
		$DOM = new \DOMDocument;
		$DOM->loadHTML($contents);

		$items = $DOM->getElementsByTagName('tr');

		function tdrows($elements)
		{
			$str = "";
			foreach ($elements as $element)
			{
				$str .= $element->nodeValue . ", ";
			}
			return $str;
		}
		foreach ($items as $node)
		{
			$arr[] =  tdrows($node->childNodes);
		}
		
		return $arr;
	}
	
	public function human_filesize($bytes, $decimals = 2) {
		$sz = 'BKMGTP';
		$factor = floor((strlen($bytes) - 1) / 3);
		return sprintf("%.{$decimals}f", $bytes / pow(1024, $factor)) . @$sz[$factor];
	}
	
	public static function sort_by_properties(&$array_of_objs, $props)
	{
		usort($array_of_objs, function($a, $b) use ($props) {
				for($i = 1; $i < count($props); $i++) {
					if($a->$props[$i-1] == $b->$props[$i-1])
						return $a->$props[$i] > $b->$props[$i] ? 1 : -1;
				}
				return $a->$props[0] > $b->$props[0] ? 1 : -1;
			});
	}
	public static function osort(&$array, $properties)
	{
		if (is_string($properties)) {
			$properties = array($properties => SORT_ASC);
		}
		uasort($array, function($a, $b) use ($properties) {
				foreach($properties as $k => $v) {
					if (is_int($k)) {
						$k = $v;
						$v = SORT_ASC;
					}
					$collapse = function($node, $props) {
						if (is_array($props)) {
							foreach ($props as $prop) {
								$node = (!isset($node->$prop)) ? null : $node->$prop;
							}
							return $node;
						} else {
							return (!isset($node->$props)) ? null : $node->$props;
						}
					};
					$aProp = $collapse($a, $k);
					$bProp = $collapse($b, $k);
					if ($aProp != $bProp) {
						return ($v == SORT_ASC)
						? strnatcasecmp($aProp, $bProp)
						: strnatcasecmp($bProp, $aProp);
					}
				}
				return 0;
			});
	}
	
	public static function GetLocationsList()
	{
		$locations = array('' => 'Select Location');
		$parents = \Location::where('parent_id',0)->get();
		foreach ($parents as $location)
		{
			$locations = array_add($locations, $location->id, $location->name);
			foreach ($location->children as $loc)
			{
				$locations = array_add($locations, $loc->id, '...... ' . $loc->name);
			}
		}
		return $locations;		
	}
	public static function GetCoursesList()
	{
		return array('' => 'Select a Course') + \Course::OrderBy('order')->lists('name', 'id');
	}	
	public static function GetActiveCoursesList()
	{
		return array('' => 'Select a Course') + \Course::Where('active', 1)->OrderBy('order')->lists('name', 'id');
	}	
	
	public static function CreateCreditNote($order, $comments)
	{		
		if ($order->current_invoice)
		{		
			$input = array(
				'id' => null,
				'invoice_id' => $order->current_invoice->id,
				'creditnote_date' => date('Y-m-d'),
				'comments' => $comments,
				'total' => $order->total
				);
			
			CreditNote::create($input);
			$order->current_invoice->update(array('status_id'=> Utils::StatusId('Invoice', 'Credit Note')));	
		}
	}

	public static function GetSpecials($location, $list_of_locations)
	{
		$specials = array();
		
		if (count($list_of_locations) > 0)
		{
			$today = new \DateTime();

			$sql = "SELECT ci.id, ci.course_id, CASE WHEN l.parent_id = 0 THEN ci.location_id ELSE l.parent_id END as parent_id, 
					ci.location_id,	ll.name as location_name,ci.course_date, ci.time_start, ci.time_end, cis.price_original, cis.price_offline, cis.price_online,c.name as course_name,
					DATE_FORMAT(STR_TO_DATE(CONCAT(ci.course_date, ' ',ci.time_start) , '%Y-%m-%d %l:%i %p'), '%Y-%m-%d %H:%i:%s') as course_date_time
					from `courseinstances` ci
					JOIN courseinstance_specials cis on ci.id = cis.course_instance_id
					JOIN courses c on c.id = ci.course_id
					JOIN locations l on l.id = ci.location_id
					JOIN locations ll on ll.id = CASE WHEN l.parent_id = 0 THEN ci.location_id ELSE l.parent_id END
					where DATE_FORMAT(STR_TO_DATE(CONCAT(ci.course_date, ' ',ci.time_start) , '%Y-%m-%d %l:%i %p'), '%Y-%m-%d %H:%i:%s') > NOW() and 
					ci.`location_id` in (" . implode(',',$list_of_locations) . ") and ci.active = '1' and cis.active = '1'
					order by ll.order, course_date_time";

			$specials = DB::select( $sql );
			
			$previous_location = '';
			foreach ($specials as &$special)
			{
				$special->savings = \Utils::format_number((double)$special->price_original - (double)$special->price_online);
				$special->booking = '/bookings/' . strtolower($special->location_name) . '?course=' . $special->course_id . '&inst=' . $special->id;
				$special->first = false;

				if ($previous_location != $special->location_name) 
					$special->first = true;
				$previous_location = $special->location_name;
			}
		}
	
		return $specials;
	}
	public static function GetBundles($location, $list_of_locations)
	{
		
		$list_of_bundles = array();

		if (count($list_of_locations) > 0)
		{
			//$bundles = \CourseBundle::with(Array('bundles' => 
			//	function($query) {
			//		return $query->orderBy('order');
			//})
			//)
			$bundles = \CourseBundle::wherein('location_id',$list_of_locations)
			->remember(Config::get('cache.minutes', 1))
			->get();

			
			foreach ($bundles as $bundle)
			{
				$b = new stdClass();
				$b->id = $bundle->id;
				$b->location = $bundle->location_id;
				$original = 0;
				$name = '';
				foreach ($bundle->bundles as $course)
				{
					$name .= $course->short_name . ' & ';
					foreach ($course->prices as $price)
					{
						if($price->location_id == $bundle->location_id)
							$original += (double)$price->price_offline;
					}
				}
				
				$b->name = substr($name, 0, -3);
				$b->total_online = $bundle->total_online;
				$b->total_offline = $bundle->total_offline;
				$b->total_original = \Utils::format_number($original);
				$b->savings = \Utils::format_number((double)$original - (double)$bundle->total_online);
				$b->booking = '/bookings/' . $location . '?bundle=' . $bundle->id;
				array_push($list_of_bundles,$b);
			}
		}
		return $list_of_bundles;
	}

	public static function ViewFile($path, $name=null, $lifetime=0)
	{
	
		if (is_null($name)) {$name = basename($path);}
		
		$filetime = filemtime($path);
		$etag = md5($filetime . $path);
		$time = gmdate('r', $filetime);
		$expires = gmdate('r', $filetime + $lifetime);
		$length = filesize($path);
		
		$headers = array(
			'Content-Disposition' => 'inline; filename="' . $name . '"',
			'Last-Modified' => $time,
			'Cache-Control' => 'must-revalidate',
			'Expires' => $expires,
			'Pragma' => 'public',
			'Etag' => $etag,
			);
		
		$headerTest1 = isset($_SERVER['HTTP_IF_MODIFIED_SINCE']) && $_SERVER['HTTP_IF_MODIFIED_SINCE'] == $time;
		$headerTest2 = isset($_SERVER['HTTP_IF_NONE_MATCH']) && str_replace('"', '', stripslashes($_SERVER['HTTP_IF_NONE_MATCH'])) == $etag;
		if ($headerTest1 || $headerTest2) { //image is cached by the browser, we dont need to send it again
			return Response::make('', 304, $headers);
		}
		
		$ext = strtolower(File::extension($path));
		$mime = Config::get('utils.mime_types_map', array($ext =>'text/plain'));
		
		//var_dump($mime[$ext]);
		//exit();
		$headers = array_merge($headers, array(
			'Content-Type' => $mime[$ext],
			'Content-Length' => $length,
			));
		
		return Response::make(File::get($path), 200, $headers);
	}
	
	public static function wrapText($str, $width=75, $break="\n") {
        return preg_replace_callback('#(\S{'.$width.',})#',
        function($matches) use ($width,$break)
        {
            return "chunk_split('".$matches['1']."', ".$width.", '".$break."')";
        }
        ,$str);
	}

	public static function get_all_headers()
	{
		foreach ($_SERVER as $name => $value)
		{
			if (substr($name, 0, 5) == 'HTTP_')
			{
				$headers[strtolower(str_replace(' ', '-', ucwords(strtolower(str_replace('_', ' ', substr($name, 5))))))] = $value;
				
			}
		}
		return $headers;
	}
	
	private static function pkcs5_unpad($text)
	{
		$pad = ord($text{strlen($text)-1});
		if ($pad > strlen($text)) return false;
		if (strspn($text, chr($pad), strlen($text) - $pad) != $pad) return false;
		return substr($text, 0, -1 * $pad);
	}

	public static function decrypt_parameters( $base64Key, $encryptedParametersText, $signatureText )
    {
        $key = base64_decode( $base64Key );
        $iv = "\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0";
        $td = mcrypt_module_open('rijndael-128', '', 'cbc', '');

        // Decrypt the parameter text
        mcrypt_generic_init($td, $key, $iv);
        $parametersText = mdecrypt_generic($td, base64_decode( $encryptedParametersText ) );
        $parametersText = static::pkcs5_unpad( $parametersText );
        mcrypt_generic_deinit($td);

		// Decrypt the signature value
		mcrypt_generic_init($td, $key, $iv);
		$hash = mdecrypt_generic($td, base64_decode( $signatureText ) );
		$hash = bin2hex( static::pkcs5_unpad( $hash ) );
		mcrypt_generic_deinit($td);

		mcrypt_module_close($td);

		// Compute the MD5 hash of the parameters
		$computedHash = md5( $parametersText );

		// Check the provided MD5 hash against the computed one
		if ( $computedHash != $hash )
		{
			trigger_error( "Invalid parameters signature" );
		}

		$parameterArray = explode( "&", $parametersText );
		$parameters = array();

		// Loop through each parameter provided
		foreach ( $parameterArray as $parameter )
		{
			list( $paramName, $paramValue ) = explode( "=", $parameter );
			$parameters[ urldecode( $paramName ) ] = urldecode( $paramValue );
		}
		return $parameters;
	}

	public static function weeks_in_month($year, $month, $start_day_of_week)
	{
		// Start of month
		$start = mktime(0, 0, 0, $month, 1, $year);
		// End of month
		$end = mktime(0, 0, 0, $month, date('t', $start), $year);
		// Start week
		$start_week = date('W', $start);
		// End week
		$end_week = date('W', $end);
		
		if ($end_week < $start_week) { // Month wraps
			return ((52 + $end_week) - $start_week) + 1;
		}
		
		return ($end_week - $start_week) + 1;
	}

	public static function GetDateSearchText($date)
	{
		$search_text = '';
		$week_number_on_year = date('W', strtotime($date));
		$week_number_on_year_for_first = date('W', strtotime($date));
		if( $week_number_on_year < $week_number_on_year_for_first)
			$week_number_on_year_for_first = $week_number_on_year_for_first - 52;
		$week_number_on_month = ($week_number_on_year - $week_number_on_year_for_first) + 1;

		if ($week_number_on_month <= 1)
			$search_text = 'first';
		else if ($week_number_on_month == 2)
			$search_text = 'second';
		else if ($week_number_on_month == 3)
			$search_text = 'third';
		else if ($week_number_on_month == 4)
			$search_text = 'fourth';
		else
			$search_text = 'last';
		
		return $search_text;
	}

	public static function GetPercentage($amount, $total, $decimals= 2)
	{
		return round(($amount * 100 / $total), $decimals) . '%';
	}	
	
	public static function GenerateClassesTimes() {
		$start_time = 7;
		$end_time = 23;
		$times[''] = '';
		for($hour = 7; $hour <= 23; $hour++)
		{
			for($min = 0; $min <= 45; $min = $min + 15)
			{
				$am_pm = $hour < 12 ? 'AM' : 'PM';
				$h_value = str_pad($hour <= 12 ? $hour : ($hour - 12), 2, "0", STR_PAD_LEFT);
				$m_value = str_pad($min, 2, "0", STR_PAD_LEFT);
				$time = $h_value . ':' . $m_value . ' ' . $am_pm;
				$times[$time] = $time;
			}
		}
		
		return $times;
		
	}
	
	public static function array_map_recursive($callback, $array, $properties = array())
	{
		foreach ($array as $key => $value) {
			if (is_array($array[$key])) {
				$array[$key] = Utils::array_map_recursive($callback, $array[$key]);
			} else {
				if (count($properties) == 0 ||  array_key_exists($key, $properties))
				{
					$array[$key] = call_user_func($callback, $array[$key]);
				}
			}
		}

		return $array;
	}


	public static function getModelIfExists($model, $properties)
	{
		$query = $model::orderBy('id', 'desc');
		
		foreach ($properties as $key => $value) {
			$query = $query->where($key, trim($value));	
		}
		
		return $query->first();
	}


	
}


