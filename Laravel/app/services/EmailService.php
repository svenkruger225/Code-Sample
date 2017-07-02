<?php namespace App\Services;

use Config, Input, Lang, Redirect, Sentry, Validator, View, Response, Exception, Mail, Message, DB;
use Roster,Log, App;

class EmailService {

	public function __construct()
	{
	}

	public function sendToAdmin($order, $isGroupBooking = false, $email = null, $success = true,$isVoucher=false) 
	{
        //echo "0.4";
                

		Log::info('Sending Admin Email : order: '. $order->id . ' ,isGroupBooking: ' . $isGroupBooking . '| ' . $success);

		$subject_prefix = ""; // if production the prefix is empty
		if(!App::environment('production'))
		{
			$subject_prefix = "TEST ";
		}
		
		if ($email == null)
			$email = Message::where('message_id', Utils::MessageTypeId('Admin'))
							->where('active',1)->first();

		if ($email == null)
		{
			$email = new Message();
			$email->subject = '{{end}} ' . ($isGroupBooking ? 'Group' : '') . ' Booking: {{order_id}}';
			$email->body = Config::get('mail.admin_default', null);

		}

		$invoice_attachment = array();
		$filepath = storage_path() . '/invoices/invoice-' . $order->current_invoice->id . '.pdf';
		if (!file_exists($filepath))
		{	
			\PdfService::save('Order', 'backend.invoices.invoice', '/invoices/', 'invoice-', $order->current_invoice->id);	
		}
		if (file_exists($filepath))
		{	
			$invoice_attachment = array('' => $filepath);
		}
		
		$end = $order->backend == '1' ? ($order->agent ? 'Commission Agent' : 'Backend') :  ($order->agent ? 'Commission Agent Frontend' : 'Frontend');
		
		$result = json_decode(json_encode(array(
			'end' => $end,
			'order_id' => $order->id,
			'email' => Config::get('mail.admin_email','shayansolutions@gmail.com'),
			'cc' => Config::get('mail.cc', null),
			'first_name' => 'CoffeeSchool',
			'last_name' => 'Admin',
			'agent_name' => $order->agent ? $order->agent->name : $order->agent_id,
			'company_name' => $order->company ? $order->company->name : $order->company_id,
			'customer_id' => $order->customer->id,
			'customer_name' => $order->customer->full_name,
			'customer_email' => $order->customer->email,
			'phone' => $order->customer->phone,
			'mobile' => $order->customer->mobile,
			'total' => $order->total,
			'method' => $order->payment_method,
			'question1' => $order->customer->question1,
			'question2' => $order->customer->question2,
			'question3' => $order->customer->question3,
			'eng_level' => $order->customer->lang_eng_level,
			'items' => View::make('emails.bookings.items', compact('order'))->render(),
			'payments' => View::make('emails.bookings.transactions', compact('order'))->render(),
			'attachments' => $invoice_attachment
			)));		
		
		$subject = $success ? $email->subject : 'ERROR : ' . $email->subject;
		$result->cc = $success ?  null :  Config::get('mail.admin_contact_email', null);
		
                if($success){
                    $result->cc = $isVoucher ? Config::get('mail.admin_email', null) :  null;
                }
                
                preg_match_all('#\{\{(.*?)\}\}#', $subject, $matches);
		foreach($matches[1] as $match)
			$subject = str_replace("{{" .$match. "}}", $result->$match, $subject);
		
		$result->subject = $subject_prefix . $subject;
		
		$body = $email->body;
		preg_match_all('#\{\{(.*?)\}\}#', $body, $matches);
		foreach($matches[1] as $match)
			if (property_exists($result, $match))
				$body = str_replace("{{" .$match. "}}", $result->$match, $body);

		$result->body = $body;
		$this->send($result);

	}

	public function sendToCustomer($roster, $isGroupBooking = false, $email = null) 
	{
        //echo "1.3";
		try 
		{
			$subject_prefix = ""; // if production the prefix is empty
			if(!App::environment('production'))
			{
				$subject_prefix = "TEST ";
			}

			if ($isGroupBooking)
				$instance = $roster->groupbooking;
			else
				$instance = $roster->instance;

			if (!is_object($email))
			$email = Message::where('message_id', Utils::MessageTypeId('Email'))
				->where('location_id', $instance->location_id)
				->where('course_id',$instance->course_id)
				->where('active',1)->first();

			if (!is_object($email))
			$email = Message::where('message_id', Utils::MessageTypeId('Email'))
				->whereNull('location_id')
				->where('course_id',$instance->course_id)
				->where('active',1)->first();


			if (!is_object($email))
			$email = Message::where('message_id', Utils::MessageTypeId('Email'))
				->whereNull('location_id')
				->whereNull('course_id')
				->where('active',1)->first();

            $queries = DB::getQueryLog();
            $last_query = end($queries);
            //@TODO comment before deployment
            //var_dump($last_query);
            //exit;

            if (!is_object($email))
			{
				$email = new Message();
				if ($isGroupBooking)
				{
					$email->subject = 'Coffee School Group Enrolment';
					$email->body = Config::get('mail.group_default', null);
				}
				else
				{
					$email->subject = 'Coffee School Enrolment';
					$email->body = Config::get('mail.course_default', null);
				}
			}
		
			if (!is_object($email))
			{
				throw new Exception("Could not find email body");
			}


			$invoice_attachment = array();
			$filepath = storage_path() . '/invoices/invoice-' . $roster->order->current_invoice->id . '.pdf';
			if (!file_exists($filepath))
			{	
				\PdfService::save('Order', 'backend.invoices.invoice', '/invoices/', 'invoice-', $roster->order->current_invoice->id);	
			}
			if (file_exists($filepath))
			{	
				$invoice_attachment = array('' => $filepath);
			}

			$share_link = 'https://' . $_SERVER["HTTP_HOST"] . '/share/' . $roster->order_id;
			
			$enrolment_usi_text = '';
			if ($roster->is_course_accredited)
			{
				$enrolment_usi_link ='https://' . $_SERVER["HTTP_HOST"] . '/enrolment/form/' . $roster->order_id . '/' . $roster->customer->id;
				$enrolment_usi_text = Config::get('mail.enrolment_usi_text', '');
				$enrolment_usi_text .= '<a href="' . $enrolment_usi_link . '">ENRONLMENT / USI FORM</a>';
			}

			$result = json_decode(json_encode(array(
				'subject' => $subject_prefix . $email->subject . ' ' . $instance->course->name,
				'order_id' => $roster->order_id,
				'first_name' => $roster->customer->first_name,
				'last_name' => $roster->customer->last_name,
				'email' => $roster->customer->email,
				'locationName' => $instance->location->name,
				'locationParentName' => ($instance->location->parent_id !== null && $instance->location->parent_id > 0) ? $instance->location->parent->name : $instance->location->name,
				'address' => $instance->location->address,
				'city' => $instance->location->city,
				'state' => $instance->location->state,
				'post_code' => $instance->location->post_code,
				'locationEmail' => $instance->location->email,
				'locationPhone' => $instance->location->phone,
				'locationMobile' => $instance->location->mobile,
				'courseName' => $instance->course->name, 
				'course_date' => date('M-d-Y (D)', strtotime($instance->course_date)),
				'time_start' => $instance->time_start,
				'time_end' => $instance->time_end,
				'shareLink' => '<a href="' . $share_link . '">' . $share_link . '</a>',
				'enrolment_usi_link' => $enrolment_usi_text,
				'attachments' => $invoice_attachment + $email->attachments->lists('path', 'id')
				)));		

			
			$subject = $result->subject;
			preg_match_all('#\{\{(.*?)\}\}#', $subject, $matches);
			foreach($matches[1] as $match)
				$subject = str_replace("{{" .$match. "}}", $result->$match, $subject);
			$result->subject = $subject;
		
			$body = $email->body;
			preg_match_all('#\{\{(.*?)\}\}#', $body, $matches);
			foreach($matches[1] as $match)
				if (property_exists($result, $match))
					$body = str_replace("{{" .$match. "}}", $result->$match, $body);

			$result->body = $body;
				
			$this->send($result);
		}
		catch (Exception $ex)
		{
			Log::error($ex);
		}
		
		
	}

	public function sendToGroupBookingOwner($rosters) 
	{
        //echo "1.2";
		try 
		{
			$subject_prefix = ""; // if production the prefix is empty
			if(!App::environment('production'))
			{
				$subject_prefix = "TEST ";
			}

			$instance = $rosters->first()->groupbooking;

			$email = Message::where('message_id', Utils::MessageTypeId('Group'))
				->where('location_id', $instance->location_id)
				->where('course_id',$instance->course_id)
				->where('active',1)->first();

			if (!is_object($email))
			$email = Message::where('message_id', Utils::MessageTypeId('Group'))
				->whereNull('location_id')
				->where('course_id',$instance->course_id)
				->where('active',1)->first();

			if (!is_object($email))
			$email = Message::where('message_id', Utils::MessageTypeId('Group'))
				->whereNull('location_id')
				->whereNull('course_id')
				->where('active',1)->first();


			if (!is_object($email))
			{
				$email = new Message();
				$email->subject = 'Coffee School Group Enrolment';
				$email->body = Config::get('mail.group_default', null);
			}
			
			if (!is_object($email))
			{
				throw new Exception("Could not find email body");
			}


			$invoice_attachment = array();
			$share_link ='https://' . $_SERVER["HTTP_HOST"] . '/share/' . $instance->order_id;
			
			$enrolment_usi_text = '';
			
			if ($instance->course->is_accredited)
			{
				$enrolment_usi_link ='https://' . $_SERVER["HTTP_HOST"] . '/enrolment/form/' . $instance->order_id;
				$enrolment_usi_text = Config::get('mail.enrolment_usi_text', '');
				$enrolment_usi_text .= '<a href="' . $enrolment_usi_link . '">ENROLMENT / USI FORM</a>';
				$enrolment_usi_text .= '<p>List of links for Students in Group: </p>';
				$enrolment_usi_text .= '<ul>';

				foreach($rosters as $roster) 
				{
					$enrolment_usi_text .= '<li>' . $roster->customer->name . ' link  -- ';
					$enrolment_usi_link = 'https://' . $_SERVER["HTTP_HOST"] . '/enrolment/form/' . $roster->order_id . '/' . $roster->customer->id;
					$enrolment_usi_text .= '<a href="'. $enrolment_usi_link . '">' . $enrolment_usi_link . '</a>';
					$enrolment_usi_text .= '</li>';
				}
				$enrolment_usi_text .= '</ul>';
			}
			
			$result = json_decode(json_encode(array(
				'subject' => $subject_prefix . $email->subject . ' ' . $instance->course->name,
				'order_id' => $instance->order_id,
				'group_id' => $instance->id,
				'first_name' => $instance->customer->first_name,
				'last_name' => $instance->customer->last_name,
				'email' => $instance->customer->email,
				'locationName' => $instance->location->name,
				'locationParentName' => ($instance->location->parent_id !== null && $instance->location->parent_id > 0) ? $instance->location->parent->name : $instance->location->name,
				'address' => $instance->location->address,
				'city' => $instance->location->city,
				'state' => $instance->location->state,
				'post_code' => $instance->location->post_code,
				'locationEmail' => $instance->location->email,
				'locationPhone' => $instance->location->phone,
				'locationMobile' => $instance->location->mobile,
				'courseName' => $instance->course->name, 
				'course_date' => date('M-d-Y (D)', strtotime($instance->course_date)),
				'time_start' => $instance->time_start,
				'time_end' => $instance->time_end,
				'total_students' => $rosters->count(),
				'total' => $instance->order->total,
				'cost_per_student' => $instance->order->total / $rosters->count(),
				'shareLink' => '<a href="' . $share_link . '">' . $share_link . '</a>',
				'enrolment_usi_link' => $enrolment_usi_text,
				'attachments' => $invoice_attachment + $email->attachments->lists('path', 'id')
				)));		
			
			$subject = $result->subject;
			preg_match_all('#\{\{(.*?)\}\}#', $subject, $matches);
			foreach($matches[1] as $match)
				$subject = str_replace("{{" .$match. "}}", $result->$match, $subject);
			$result->subject = $subject;
			
			$body = $email->body;
			preg_match_all('#\{\{(.*?)\}\}#', $body, $matches);
			foreach($matches[1] as $match)
				if (property_exists($result, $match))
				$body = str_replace("{{" .$match. "}}", $result->$match, $body);

			$result->body = $body;
			
			Log::debug('email data: ' . json_encode($result));
			
			$this->send($result);
		}
		catch (Exception $ex)
		{
			Log::error($ex);
		}
		
		
	}

	public function sendToAgentBookingOwner($order) 
	{
        //backend > select company
        //echo "1.1";
		try 
		{
			$subject_prefix = ""; // if production the prefix is empty
			if(!App::environment('production')) {
				$subject_prefix = "TEST ";
			}

			$instance = $order->rosters->first()->instance;

			$email = Message::where('message_id', Utils::MessageTypeId('Agent'))
				->where('location_id', $instance->location_id)
				->where('course_id',$instance->course_id)
				->where('active',1)->first();

			if (!is_object($email)) {
				$email = Message::where('message_id', Utils::MessageTypeId('Agent'))
					->whereNull('location_id')
					->where('course_id',$instance->course_id)
					->where('active',1)->first();
			}
			if (!is_object($email)) {
				$email = Message::where('message_id', Utils::MessageTypeId('Agent'))
					->whereNull('location_id')
					->whereNull('course_id')
					->where('active',1)->first();
			}

            $queries = DB::getQueryLog();
            $last_query = end($queries);
            //var_dump($last_query);

			if (!is_object($email)) {
				$email = new Message();
				$email->subject = 'Coffee School Agent Enrolment';
				$email->body = Config::get('mail.agent_default', null);
			}
			
			if (!is_object($email))
			{
				throw new Exception("Could not find email body");
			}

			$invoice_attachment = array();
			$filepath = storage_path() . '/invoices/invoice-' . $order->current_invoice->id . '.pdf';
			if (!file_exists($filepath)) {	
				\PdfService::save('Order', 'backend.invoices.invoice', '/invoices/', 'invoice-', $order->current_invoice->id);	
			}
			if (file_exists($filepath)) {	
				$invoice_attachment = array('' => $filepath);
			}

			$agent_location = ($instance->location->parent_id !== null && $instance->location->parent_id > 0) ? $instance->location->parent->name : $instance->location->name;
			$share_link ='https://' . $_SERVER["HTTP_HOST"] . '/' . ($order->backend ? '' : 'agent/' . $order->agent->code . '/' . $agent_location . '/') . 'share/' . $instance->order_id;
			
			$enrolment_usi_text = '';
			
	//		if ($instance->course->is_accredited)
	//		{
	//			
	//			$enrolment_usi_link ='https://' . $_SERVER["HTTP_HOST"] . '/enrolment/form/' . $order->id;
	//			$enrolment_usi_text = Config::get('mail.enrolment_usi_text', '');
	//			$enrolment_usi_text .= '<a href="' . $enrolment_usi_link . '">ENROLMENT / USI FORM</a>';
	//			$enrolment_usi_text .= '<p>List of links for Students in Booking: </p>';
	//			$enrolment_usi_text .= '<ul>';

////
	//			foreach($order->rosters as $roster) 
	//			{
	//				$enrolment_usi_text .= '<li>' . $roster->customer->name . ' link  -- ';
	//				$enrolment_usi_link = 'https://' . $_SERVER["HTTP_HOST"] . '/enrolment/form/' . $roster->order_id . '/' . $roster->customer->id;
	//				$enrolment_usi_text .= '<a href="'. $enrolment_usi_link . '">' . $enrolment_usi_link . '</a>';
	//				$enrolment_usi_text .= '</li>';
	//			}
	//			$enrolment_usi_text .= '</ul>';
	//		}
	//		else
			//{
				
				$enrolment_usi_text = '<p>List of Students in Booking: </p><ul>';
				foreach($order->rosters as $roster) 
				{
					$enrolment_usi_text .= '<li>' . $roster->customer->name . '</li>';
				}
				$enrolment_usi_text .= '</ul>';
			//}
			
			$result = json_decode(json_encode(array(
				'subject' => $subject_prefix . $email->subject,
				'order_id' => $order->id,
                'company_name' => $order->company ? $order->company->name : $order->company_id,
				'first_name' => $order->backend ? $order->customer->first_name : $order->agent->name,
				'last_name' => $order->backend ? $order->customer->last_name : '',
				'email' => $order->backend ? $order->customer->email : $order->agent->email,
				'locationName' => $instance->location->name,
				'locationParentName' => ($instance->location->parent_id !== null && $instance->location->parent_id > 0) ? $instance->location->parent->name : $instance->location->name,
				'address' => $instance->location->address,
				'city' => $instance->location->city,
				'state' => $instance->location->state,
				'post_code' => $instance->location->post_code,
				'locationEmail' => $instance->location->email,
				'locationPhone' => $instance->location->phone,
				'locationMobile' => $instance->location->mobile,
				'courseName' => $instance->course->name, 
				'course_date' => date('M-d-Y (D)', strtotime($instance->course_date)),
				'time_start' => $instance->time_start,
				'time_end' => $instance->time_end,
				'total_students' => $order->rosters->count(),
				'total' => $order->total,
				'cost_per_student' => $order->total / $order->rosters->count(),
				'shareLink' => '<a href="' . $share_link . '">' . $share_link . '</a>',
				'enrolment_usi_link' => $enrolment_usi_text,
				'attachments' => $invoice_attachment + $email->attachments->lists('path', 'id'),
                'agent_name' => $order->agent->name
				)));		
			
			$subject = $result->subject;
			preg_match_all('#\{\{(.*?)\}\}#', $subject, $matches);
			foreach($matches[1] as $match)
				$subject = str_replace("{{" .$match. "}}", $result->$match, $subject);
			$result->subject = $subject;
			
			$body = $email->body;
			preg_match_all('#\{\{(.*?)\}\}#', $body, $matches);
			foreach($matches[1] as $match)
				if (property_exists($result, $match))
				$body = str_replace("{{" .$match. "}}", $result->$match, $body);

            //@TODO comment before deployment
            //echo $body;
            //exit;
			$result->body = $body;

            //echo $result->subject;

			Log::debug('email data: ' . json_encode($result));

			$this->send($result);
		}
		catch (Exception $ex)
		{
			Log::error($ex);
		}
		
		
	}

    /**
     * Email for Commission and Non commission agent other than RSA
     * @param $roster
     * @param $order
     */
    public function sendToCompanyBookingStudent($roster, $order)
    {
        //echo "1";
        try
        {
            $company_name = "";
            if(isset($order->agent))
            {
                $company_name =  $order->agent->name == '' ? $order->agent_id:$order->agent->name;
            }
            else if(isset($order->company))
            {
                $company_name =  $order->company->name == '' ? $order->company_id:$order->company->name;
            }
            $subject_prefix = ""; // if production the prefix is empty
            if(!App::environment('production')) {
                $subject_prefix = "TEST ";
            }

            $instance = $roster->instance;

            $email = Message::where('message_id', Utils::MessageTypeId('CompanyStudent'))
                ->where('location_id', $instance->location_id)
                ->where('course_id',$instance->course_id)
                ->where('active',1)->first();

            if (!is_object($email)) {
                $email = Message::where('message_id', Utils::MessageTypeId('CompanyStudent'))
                    ->whereNull('location_id')
                    ->where('course_id',$instance->course_id)
                    ->where('active',1)->first();
            }
            if (!is_object($email)) {
                $email = Message::where('message_id', Utils::MessageTypeId('CompanyStudent'))
                    ->whereNull('location_id')
                    ->whereNull('course_id')
                    ->where('active',1)->first();
            }
            $queries = DB::getQueryLog();
            $last_query = end($queries);

            if (!is_object($email))	{
                $email = new Message();
                $email->subject = 'Coffee School Enrolment';
                $email->body = Config::get('mail.agent_student_default', null);
            }

            if (!is_object($email)) {
                throw new Exception("Could not find email body");
            }


            $invoice_attachment = array();
            if (!$order->backend)
            {
                $filepath = storage_path() . '/invoices/invoice-' . $order->current_invoice->id . '.pdf';
                if (!file_exists($filepath))
                {
                    \PdfService::save('Order', 'backend.invoices.invoice', '/invoices/', 'invoice-', $order->current_invoice->id);
                }
                if (file_exists($filepath))
                {
                    $invoice_attachment = array('' => $filepath);
                }
            }

            //$agent_location = ($instance->location->parent_id !== null && $instance->location->parent_id > 0) ? $instance->location->parent->name : $instance->location->name;

            //$share_link = 'https://' . $_SERVER["HTTP_HOST"] . '/' . ($order->backend ? '' : 'agent/' . $order->agent->code . '/' . $agent_location . '/') . 'share/' . $roster->order_id;
            //$share_link = 'https://' . $_SERVER["HTTP_HOST"] . '/share/'. $roster->order_id;
            $share_link = 'https://' . 'www.coffeeschool.com.au' . '/share/'. $roster->order_id;



            $enrolment_usi_text = '';
            /*
            if ($roster->is_course_accredited)
            {
                $enrolment_usi_link ='https://' . $_SERVER["HTTP_HOST"] . '/' . ($order->backend ? '' : 'agent/' . $order->agent->code . '/' . $agent_location . '/') . 'enrolment/form/' . $roster->order_id . '/' . $roster->customer->id;
                $enrolment_usi_text = Config::get('mail.enrolment_usi_text', '');
                $enrolment_usi_text .= '<a href="' . $enrolment_usi_link . '">ENRONLMENT / USI FORM</a>';
            }
            */

            //$agent_url = '<a href=\'http://www.' . $order->agent->code . $agent_location . '.com\' target=\'_blank\'>' . $order->agent->code . $agent_location . '</a>';

            $result = json_decode(json_encode(array(
                'subject' => $subject_prefix . $email->subject . ' ' . $instance->course->name,
                'company_name' => $company_name,
                'order_id' => $roster->order_id,
                'first_name' => $roster->customer->first_name,
                'last_name' => $roster->customer->last_name,
                'email' => $roster->customer->email,
                'locationName' => $instance->location->name,
                'locationParentName' => ($instance->location->parent_id !== null && $instance->location->parent_id > 0) ? $instance->location->parent->name : $instance->location->name,
                'address' => $instance->location->address,
                'city' => $instance->location->city,
                'state' => $instance->location->state,
                'post_code' => $instance->location->post_code,
                'locationEmail' => $instance->location->email,
                'locationPhone' => $instance->location->phone,
                'locationMobile' => $instance->location->mobile,
                'courseName' => $instance->course->name,
                'course_date' => date('M-d-Y (D)', strtotime($instance->course_date)),
                'time_start' => $instance->time_start,
                'time_end' => $instance->time_end,
                'shareLink' => '<a href="' . $share_link . '">' . $share_link . '</a>',
                'enrolment_usi_link' => $enrolment_usi_text,
                /*'agent_url' => $order->backend ? '' : $agent_url,*/
                'attachments' => $invoice_attachment + $email->attachments->lists('path', 'id')
            )));

            $subject = $result->subject;
            preg_match_all('#\{\{(.*?)\}\}#', $subject, $matches);
            foreach($matches[1] as $match)
                $subject = str_replace("{{" .$match. "}}", $result->$match, $subject);
            $result->subject = $subject;

            $body = $email->body;
            preg_match_all('#\{\{(.*?)\}\}#', $body, $matches);
            foreach($matches[1] as $match)
                if (property_exists($result, $match))
                    $body = str_replace("{{" .$match. "}}", $result->$match, $body);
            $result->body = $body;

            $this->send($result);
        }
        catch (Exception $ex)
        {
            //var_dump($ex);
            //echo "exception occured";
            Log::error($ex);
        }

    }

    /**
     * Email for RSA perth agent
     * @param $roster
     * @param $order
     */
	public function sendToAgentBookingStudent($roster, $order) 
	{
        //echo "1";

		try 
		{
			$subject_prefix = ""; // if production the prefix is empty
			if(!App::environment('production')) {
				$subject_prefix = "TEST ";
			}

			$instance = $roster->instance;

			$email = Message::where('message_id', Utils::MessageTypeId('AgentStudent'))
				->where('location_id', $instance->location_id)
				->where('course_id',$instance->course_id)
				->where('active',1)->first();

			if (!is_object($email)) {
				$email = Message::where('message_id', Utils::MessageTypeId('AgentStudent'))
					->whereNull('location_id')
					->where('course_id',$instance->course_id)
					->where('active',1)->first();
			}
            if (!is_object($email)) {
                $email = Message::where('message_id', Utils::MessageTypeId('AgentStudent'))
                    ->whereNull('course_id')
                    ->where('location_id',$instance->location_id)
                    ->where('active',1)->first();
            }
			if (!is_object($email)) {
				$email = Message::where('message_id', Utils::MessageTypeId('AgentStudent'))
					->whereNull('location_id')
					->whereNull('course_id')
					->where('active',1)->first();
			}
            $queries = DB::getQueryLog();
            $last_query = end($queries);
            //@TODO comment before deployment
            //var_dump($last_query);
            //exit;


            if (!is_object($email))	{
				$email = new Message();
				$email->subject = 'Coffee School Enrolment';
				$email->body = Config::get('mail.agent_student_default', null);
			}
			
			if (!is_object($email)) {
				throw new Exception("Could not find email body");
			}


			$invoice_attachment = array();
			if (!$order->backend)
			{
				$filepath = storage_path() . '/invoices/invoice-' . $order->current_invoice->id . '.pdf';
				if (!file_exists($filepath))
				{	
					\PdfService::save('Order', 'backend.invoices.invoice', '/invoices/', 'invoice-', $order->current_invoice->id);	
				}
				if (file_exists($filepath))
				{	
					$invoice_attachment = array('' => $filepath);
				}
			}

			$agent_location = ($instance->location->parent_id !== null && $instance->location->parent_id > 0) ? $instance->location->parent->name : $instance->location->name;

			//$share_link = 'https://' . $_SERVER["HTTP_HOST"] . '/' . ($order->backend ? '' : 'agent/' . $order->agent->code . '/' . $agent_location . '/') . 'share/' . $roster->order_id;
            //$share_link = 'https://' . $_SERVER["HTTP_HOST"] . '/share/'. $roster->order_id;
            $share_link = 'https://' . 'www.coffeeschool.com.au' . '/share/'. $roster->order_id;
			$enrolment_usi_text = '';
			if ($roster->is_course_accredited)
			{
				$enrolment_usi_link ='https://' . $_SERVER["HTTP_HOST"] . '/' . ($order->backend ? '' : 'agent/' . $order->agent->code . '/' . $agent_location . '/') . 'enrolment/form/' . $roster->order_id . '/' . $roster->customer->id;
				$enrolment_usi_text = Config::get('mail.enrolment_usi_text', '');
				$enrolment_usi_text .= '<a href="' . $enrolment_usi_link . '">ENRONLMENT / USI FORM</a>';
			}
			
			$agent_url = '<a href=\'http://www.' . $order->agent->code . $agent_location . '.com\' target=\'_blank\'>' . substr($order->agent->code,0,3) . $agent_location . '</a>';

			$result = json_decode(json_encode(array(
				'subject' => $subject_prefix . $email->subject . ' ' . $instance->course->name,
				'order_id' => $roster->order_id,
				'first_name' => $roster->customer->first_name,
				'last_name' => $roster->customer->last_name,
				'email' => $roster->customer->email,
				'locationName' => $instance->location->name,
				'locationParentName' => ($instance->location->parent_id !== null && $instance->location->parent_id > 0) ? $instance->location->parent->name : $instance->location->name,
				'address' => $instance->location->address,
				'city' => $instance->location->city,
				'state' => $instance->location->state,
				'post_code' => $instance->location->post_code,
				'locationEmail' => $instance->location->email,
				'locationPhone' => $instance->location->phone,
				'locationMobile' => $instance->location->mobile,
				'courseName' => $instance->course->name, 
				'course_date' => date('M-d-Y (D)', strtotime($instance->course_date)),
				'time_start' => $instance->time_start,
				'time_end' => $instance->time_end,
				'shareLink' => '<a href="' . $share_link . '">' . $share_link . '</a>',
				'enrolment_usi_link' => $enrolment_usi_text,
				'agent_url' => $order->backend ? '' : $agent_url,
				'attachments' => $invoice_attachment + $email->attachments->lists('path', 'id')
				)));

			$subject = $result->subject;
			preg_match_all('#\{\{(.*?)\}\}#', $subject, $matches);
			foreach($matches[1] as $match)
				$subject = str_replace("{{" .$match. "}}", $result->$match, $subject);
			$result->subject = $subject;
			
			$body = $email->body;
			preg_match_all('#\{\{(.*?)\}\}#', $body, $matches);
			foreach($matches[1] as $match)
				if (property_exists($result, $match))
				$body = str_replace("{{" .$match. "}}", $result->$match, $body);
				
			$codelocation = $order->agent->code . $agent_location;	
			if($codelocation == "RSAPerth")
			{
				//$body = str_replace("www.coffeeschool.com.au/cs-map.html", "www.rsaperth.com/cs-map.html", $body);
				$body = str_replace("info@coffeeschool.com.au", "info@rsaperth.com", $body);
				$body = str_replace("Thanks for booking with Coffee School. <a href='http://www.RSAPerth.com' target='_blank'>RSAPerth</a>", "Thanks for booking with <a href='http://www.RSAPerth.com' target='_blank'>RSAPerth</a>", $body);
				$body = str_replace("Kind Regards,", "Best Regards,", $body);
				$body = str_replace("Coffee School", "RSA Perth", $body);
				$body = str_replace("www.coffeeschool.com.au/LOCATIONS/Perth", "www.rsaperth.com/cs-map.html", $body);
				$body = str_replace("www.coffeeschool.com.au/locations/Perth", "www.rsaperth.com/cs-map.html", $body);
				$body = str_replace("<p><strong>Share your Booking</strong>&nbsp;with friends via Facebook, Email &amp; SMS so they can join you!&nbsp;<strong><u>Book a friend and get a discount</u></strong><u>!</u>&nbsp;", "", $body);			
				$body = str_replace("www.facebook.com/CoffeeRSAschool", "www.facebook.com/rsaperth", $body);	
				$body = str_replace("http://www.rsaperth.com/cs-map.html", "www.rsaperth.com/cs-map.html", $body);
			}
            //echo $body;exit;
			$result->body = $body;
			$this->send($result);
		}
		catch (Exception $ex)
		{
			Log::error($ex);
		}
		
	}

	public function sendVoucherToCustomer($order, $email = null) 
	{
        //echo "2";
		try 
		{
			if (!is_object($email))
			$email = Message::where('message_id', Utils::MessageTypeId('VoucherEmail'))
				->whereNull('location_id')
				->whereNull('course_id')
				->where('active',1)->first();

			if (!is_object($email))
			{
				$email = new Message();
				$email->subject = 'Coffee School Gift Vouchers';
				$email->body = Config::get('mail.voucher_default', null);
			}
			
			if (!is_object($email))
			{
				throw new Exception("Could not find email body");
			}


			$attachments = array();
			$filepath = storage_path() . '/invoices/invoice-' . $order->current_invoice->id . '.pdf';
			if (!file_exists($filepath))
			{	
				\PdfService::save('Order', 'backend.invoices.invoice', '/invoices/', 'invoice-', $order->current_invoice->id);	
			}
			if (file_exists($filepath))
			{	
				$attachments += array($order->current_invoice->id => $filepath);
			}
			
			foreach($order->vouchers as $voucher) 
			{
				$filepath = storage_path() . '/vouchers/voucher-' . $voucher->id . '.pdf';
				
				if (!file_exists($filepath))
					\PdfService::save('Voucher', 'backend.vouchers.voucher', '/vouchers/', 'voucher-', $voucher->id);	

				if (file_exists($filepath))
					$attachments += array($voucher->id => $filepath);
			}	

			$voucher = $order->vouchers->first();
			$result = json_decode(json_encode(array(
				'subject' => $email->subject . ' ' . $voucher->location->name,
				'order_id' => $order->id,
				'first_name' => $order->customer->first_name,
				'last_name' => $order->customer->last_name,
				'email' => $order->customer->email,
				'locationName' => $voucher->location->name,
				'locationParentName' => ($voucher->location->parent_id !== null && $voucher->location->parent_id > 0) ? $voucher->location->parent->name : $voucher->location->name,
				'address' => $voucher->location->address,
				'city' => $voucher->location->city,
				'state' => $voucher->location->state,
				'post_code' => $voucher->location->post_code,
				'locationEmail' => $voucher->location->email,
				'locationPhone' => $voucher->location->phone,
				'locationMobile' => $voucher->location->mobile,
				'courseName' => $voucher->course->name, 
				'course_date' => '',
				'time_start' => '',
				'time_end' => '',
				'attachments' => $attachments
				)));		
			
			$body = $email->body;
			preg_match_all('#\{\{(.*?)\}\}#', $body, $matches);
			foreach($matches[1] as $match)
				if (property_exists($result, $match))
					$body = str_replace("{{" .$match. "}}", $result->$match, $body);

			$result->body = $body;
			
			$this->send($result);
		}
		catch (Exception $ex)
		{
			Log::error($ex);
		}
		
		
	}

	public function sendCertificateToStudent($certificate, $email = null) 
	{
        //echo "3";
		try 
		{
			if (!is_object($email))
			$email = Message::where('message_id', Utils::MessageTypeId('CertificateEmail'))
				->whereNull('location_id')
				->whereNull('course_id')
				->where('active',1)->first();

			if (!is_object($email))
			{
				$email = new Message();
				$email->subject = 'Coffee School Gift Certificate';
				$email->body = Config::get('mail.certificate_default', null);
			}
			
			if (!is_object($email))
			{
				throw new Exception("Could not find email body");
			}

			$view = 'backend.certificates.certificate';
			if (empty($certificate->course->certificate_code))
			{
				if ($certificate->course->short_name == 'BARCKTL')
					$view = 'backend.certificates.certificate-attendance';
				else
					$view = 'backend.certificates.certificate-participant';
			}

			$attachments = array();
			$filepath = storage_path() . '/certificates/certificate-' . $certificate->id . '.pdf';
			if (!file_exists($filepath))
			{	
				\PdfService::save('Certificate', $view, '/certificates/', 'certificate-', $certificate->id);	
			}
			if (file_exists($filepath))
			{	
				$attachments += array($certificate->id => $filepath);
			}

			$result = json_decode(json_encode(array(
				'subject' => $email->subject,
				'order_id' => $certificate->roster->order_id,
				'first_name' => $certificate->customer->first_name,
				'last_name' => $certificate->customer->last_name,
				'email' => $certificate->customer->email,
				'locationName' => $certificate->location->name,
				'locationParentName' => ($certificate->location->parent_id !== null && $certificate->location->parent_id > 0) ? $certificate->location->parent->name : $certificate->location->name,
				'address' => $certificate->location->address,
				'city' => $certificate->location->city,
				'state' => $certificate->location->state,
				'post_code' => $certificate->location->post_code,
				'locationEmail' => $certificate->location->email,
				'locationPhone' => $certificate->location->phone,
				'locationMobile' => $certificate->location->mobile,
				'courseName' => $certificate->course->name, 
				'course_name' => $certificate->course->name, 
				'course_date' => '',
				'time_start' => '',
				'time_end' => '',
				'attachments' => $attachments
				)));		

			$this->replaceTokens($result, $email);
			
			$this->send($result);
		}
		catch (Exception $ex)
		{
			Log::error($ex);
		}
		
		
	}

	public function sendMarketingMessage($email) 
	{
        //echo "4";
		try 
		{
			$email = json_decode(json_encode($email));			
			$body = $email->browser_view . $email->body . $email->disclamer . $email->unsubscribe;
			preg_match_all('#\{\{(.*?)\}\}#', $body, $matches);
			foreach($matches[1] as $match)
				if (property_exists($email, $match))
					$body = str_replace("{{" .$match. "}}", $email->$match, $body);

			$email->body = $body;
			
			$result = $this->send($email, 'emails.marketing.message');

			//\Log::info(sprintf("Marketing: %s, Email: %s, Result Id: %s", $email->first_name, $email->email, $result));
			
		}
		catch (Exception $ex)
		{
			Log::error($ex);
		}
		
		
	}

	public function sendEnrolmentDataMessage($data) 
	{
        //echo "5";
		try 
		{
			$subject_prefix = ""; // if production the prefix is empty
			if(!App::environment('production'))
			{
				$subject_prefix = "TEST ";
			}
			
			$customer = \Customer::findOrFail($data['customer_id']);
			$roster = \Roster::findOrFail($data['roster_id']);	
			$email = null;

			
			if (!$roster->isPaid())
			{
				Log::error("Roster not paid, ignoring");
				return false;
			}

			
			if ($roster->customer->usi_verified)
			{
				$email = Message::where('message_id', Utils::MessageTypeId('EnrolmentDataSuccess'))
					->where('active',1)->first();
			}
			else
			{
				$email = Message::where('message_id', Utils::MessageTypeId('EnrolmentDataFail'))
					->where('active',1)->first();
			}


			//if (!is_object($email))
			//{
			//	$email = new Message();
			//	$email->subject = 'Coffee School Usi Create confirmation';
			//	$email->body = Config::get('mail.usi_confirmation', null);
			//}
			
			if (!is_object($email))
			{
				throw new Exception("Could not find email body");
			}

			if (!empty($roster->group_booking_id))
				$instance = $roster->groupbooking;
			else
				$instance = $roster->instance;

			$share_link = 'https://' . $_SERVER["HTTP_HOST"] . '/share/' . $roster->order_id;
			
			$enrolment_usi_text = '';
			if ($roster->is_course_accredited)
			{
				$enrolment_usi_link ='https://' . $_SERVER["HTTP_HOST"] . '/enrolment/form/' . $roster->order_id . '/' . $roster->customer->id;
				$enrolment_usi_text = Config::get('mail.enrolment_usi_text', '');
				$enrolment_usi_text .= '<a href="' . $enrolment_usi_link . '">ENRONLMENT / USI FORM</a>';
			}
		
			$invoice_attachment = array();
			$attachments = array();

			$result = json_decode(json_encode(array(
				'subject' => $subject_prefix . $email->subject,
				'order_id' => $roster->order_id,
				'first_name' => $roster->customer->first_name,
				'last_name' => $roster->customer->last_name,
				'email' => $roster->customer->email,
				'locationName' => $instance->location->name,
				'locationParentName' => ($instance->location->parent_id !== null && $instance->location->parent_id > 0) ? $instance->location->parent->name : $instance->location->name,
				'address' => $instance->location->address,
				'city' => $instance->location->city,
				'state' => $instance->location->state,
				'post_code' => $instance->location->post_code,
				'locationEmail' => $instance->location->email,
				'locationPhone' => $instance->location->phone,
				'locationMobile' => $instance->location->mobile,
				'course_name' =>$instance->course->name,
				'courseName' => $instance->course->name, 
				'course_date' => date('M-d-Y (D)', strtotime($instance->course_date)),
				'time_start' => $instance->time_start,
				'time_end' => $instance->time_end,
				'shareLink' => '<a href="' . $share_link . '">' . $share_link . '</a>',
				'enrolment_usi_link' => $enrolment_usi_text,
				'attachments' => $invoice_attachment + $email->attachments->lists('path', 'id')
				)));		
			
			$body = $email->body;
			preg_match_all('#\{\{(.*?)\}\}#', $body, $matches);
			foreach($matches[1] as $match)
				if (property_exists($result, $match))
				$body = str_replace("{{" .$match. "}}", $result->$match, $body);

			$result->body = $body;
			
			$this->send($result);
		}
		catch (Exception $ex)
		{
			Log::error($ex);
		}
		
		
	}

	public function sendEnrolmentDataReminder($data) 
	{
        //echo "6";
		try 
		{
			$subject_prefix = ""; // if production the prefix is empty
			if(!App::environment('production'))
			{
				$subject_prefix = "TEST ";
			}

			$email = Message::where('message_id', Utils::MessageTypeId('EnrolmentReminder'))
				->where('active',1)->first();

			//if (!is_object($email))
			//{
			//	$email = new Message();
			//	$email->subject = 'Coffee School Usi Create confirmation';
			//	$email->body = Config::get('mail.usi_confirmation', null);
			//}
			
			if (!is_object($email))
			{
				throw new Exception("Could not find email body");
			}
			
			$customer = \Customer::findOrFail($data['customer_id']);
			$roster = \Roster::findOrFail($data['roster_id']);	
				
			if (!empty($roster->group_booking_id))
				$instance = $roster->groupbooking;
			else
				$instance = $roster->instance;

			$share_link = 'https://' . $_SERVER["HTTP_HOST"] . '/share/' . $roster->order_id;
			
			$enrolment_usi_text = '';
			if ($roster->is_course_accredited)
			{
				$enrolment_usi_link ='https://' . $_SERVER["HTTP_HOST"] . '/enrolment/form/' . $roster->order_id . '/' . $roster->customer->id;
				$enrolment_usi_text = Config::get('mail.enrolment_usi_text', '');
				$enrolment_usi_text .= '<a href="' . $enrolment_usi_link . '">ENRONLMENT / USI FORM</a>';
			}

			
			$invoice_attachment = array();
			$attachments = array();

			$result = json_decode(json_encode(array(
				'subject' => $subject_prefix . $email->subject,
				'order_id' => $roster->order_id,
				'first_name' => $roster->customer->first_name,
				'last_name' => $roster->customer->last_name,
				'email' => $roster->customer->email,
				'locationName' => $instance->location->name,
				'locationParentName' => ($instance->location->parent_id !== null && $instance->location->parent_id > 0) ? $instance->location->parent->name : $instance->location->name,
				'address' => $instance->location->address,
				'city' => $instance->location->city,
				'state' => $instance->location->state,
				'post_code' => $instance->location->post_code,
				'locationEmail' => $instance->location->email,
				'locationPhone' => $instance->location->phone,
				'locationMobile' => $instance->location->mobile,
				'course_name' =>$instance->course->name,
				'courseName' => $instance->course->name, 
				'course_date' => date('M-d-Y (D)', strtotime($instance->course_date)),
				'time_start' => $instance->time_start,
				'time_end' => $instance->time_end,
				'shareLink' => '<a href="' . $share_link . '">' . $share_link . '</a>',
				'enrolment_usi_link' => $enrolment_usi_text,
				'attachments' => $invoice_attachment + $email->attachments->lists('path', 'id')
				)));		
			
			$body = $email->body;
			preg_match_all('#\{\{(.*?)\}\}#', $body, $matches);
			foreach($matches[1] as $match)
				if (property_exists($result, $match))
				$body = str_replace("{{" .$match. "}}", $result->$match, $body);

			$result->body = $body;
			
			$this->send($result);
		}
		catch (Exception $ex)
		{
			Log::error($ex);
		}
		
		
	}
	
	public function send($result, $email_view = 'emails.bookings.confirmation') 
	{

		$data = array('result'=> $result);
                if(!App::environment('production'))
                {
                    $result->email = 'dev1@shayansolutions.com';
                    //$result->email = 'shayansolutions@gmail.com';
                }
		Mail::send($email_view, $data, function($message) use ($result)
			{
                //@TODO comment before deployment
                //return false;
				$ccs = !empty($result->cc) ? $result->cc : null;
				$message
				->subject($result->subject)
				->to($result->email, $result->first_name . ' ' . $result->last_name);
				if ($ccs)
					$message->cc($ccs);
				
				if(count($result->attachments) > 0)
				{
					foreach ($result->attachments as $key => $attachment_path)
					{
						if (file_exists($attachment_path))
						{
							$message->attach($attachment_path);
						}
					}
				}
			});
		

	}

	private function replaceTokens(&$result, $email)
	{
		
		$subject = $result->subject;
		preg_match_all('#\{\{(.*?)\}\}#', $subject, $matches);
		foreach($matches[1] as $match) {
			if (property_exists($result, $match)) {
				$subject = str_replace("{{" .$match. "}}", $result->$match, $subject);
			}
		}
		$result->subject = $subject;
		
		$body = $email->body;
		preg_match_all('#\{\{(.*?)\}\}#', $body, $matches);
		foreach($matches[1] as $match) {
			if (property_exists($result, $match)) {
				$body = str_replace("{{" .$match. "}}", $result->$match, $body);
			}
		}
		$result->body = $body;		
			
	}
	
}