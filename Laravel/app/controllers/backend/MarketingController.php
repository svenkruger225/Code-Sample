<?php namespace Controllers\Backend;

use AdminController;
use Cartalyst\Sentry\Users\LoginRequiredException;
use Cartalyst\Sentry\Users\PasswordRequiredException;
use Cartalyst\Sentry\Users\UserExistsException;
use Cartalyst\Sentry\Users\UserNotFoundException;
use Config,Input,Lang,Redirect,Sentry,Validator,View, Utils;
use Session, Location, Course, Marketing, Attachment, EmailService;

class MarketingController extends AdminController {

	/**
	 * Marketing Repository
	 *
	 * @var Marketing
	 */
	protected $message;

	public function __construct(Marketing $message)
	{
		parent::__construct();
		$this->message = $message;
	}

	public function index()
	{
		$input = Session::get('_old_input');

		$messages_location = (array_key_exists('messages_location', Input::all())) ? Input::get('messages_location') : ( ( $input && array_key_exists('messages_location', $input)) ? $input['messages_location'] : ''  );  
		$messages_course = (array_key_exists('messages_course', Input::all())) ? Input::get('messages_course') : ( ( $input && array_key_exists('messages_course', $input)) ? $input['messages_course'] : ''  );  

		Session::flashInput(array('messages_location'=>$messages_location, 'messages_course'=>$messages_course));

		$query = $this->message;
		if(isset($messages_location) && !empty($messages_location))
			$query = $query->where('location_id', $messages_location);
		
		if(isset($messages_course) && !empty($messages_course))
			$query = $query->where('course_id', $messages_course);

		$messages = $query->
					orderBy('location_id')->
					orderBy('course_id')->
					orderBy('date_from')->
					orderBy('date_to')->
					orderBy('send_via')->get();
			
		$locations = Utils::GetLocationsList();	
		$courses = Utils::GetCoursesList();

		return View::make('backend.marketing.index', compact('messages','courses', 'locations'));
	}

	public function create()
	{
		$locations = Utils::GetLocationsList();	
		$courses = Utils::GetCoursesList();
		$attachments = array('' => 'Select one or more Attachemnts') + Attachment::where('type','marketing')->lists('name', 'id');
		$via_options = array('' => 'Select Option', 'Email'=>'Email', 'Sms'=> 'Sms', 'Both'=>'Both');
		Session::reflash();
		return View::make('backend.marketing.create', compact('courses', 'locations', 'attachments', 'via_options'));
	}

	public function store()
	{
		$input = Input::except('attachments','files');
		$input['date_from'] = $input['date_from']  == '' || $input['date_from']  == '0000-00-00' ? null : $input['date_from'];
		$input['date_to'] = $input['date_to']  == ''  || $input['date_to']  == '0000-00-00'? null : $input['date_to'];
		$attachments = Input::get('attachments', array());
		foreach($attachments as $key => $value)
			if($value == "") 
				unset($attachments[$key]); 
			
		if ($input['location_id'] == '')
			$input['location_id'] = null;
		if ($input['course_id'] == '')
			$input['course_id'] = null;

		$validation = Validator::make($input, Marketing::$rules);
		Session::reflash();

		if ($validation->passes())
		{
			$message = $this->message->create($input);
			$message->attachments()->sync($attachments);

			return Redirect::route('backend.marketing.edit', $message->id)->with('success','Message created successfully');
		}

		return Redirect::route('backend.marketing.create')
			->withInput()
			->withErrors($validation)
			->with('message', 'There were validation errors.');
	}

	public function CloneMessage($id)
	{
		$original_message = $this->message->findOrFail($id);
		Session::reflash();

		if (is_null($original_message))
		{
			return Redirect::route('backend.marketing.index')->with('message', 'Could not clone message.');
		}
		else
		{
			$input_data = $original_message->toArray();
			unset($input_data['id']);
			unset($input_data['created_at']);
			unset($input_data['updated_at']);
			$input_data['active'] = 0;
			
			$original_attachments = $original_message->attachments->lists('id');
			
			$message = $this->message->create($input_data);
			$message->attachments()->sync($original_attachments);

			return Redirect::route('backend.marketing.edit', $message->id)->with('success','Message Cloned successfully');
		}

		return View::make('backend.marketing.show', compact('message'));
	}

	public function edit($id)
	{
		$message = $this->message->find($id);

		if (is_null($message))
		{
			return Redirect::route('backend.marketing.index');
		}
		
		Session::reflash();
		$locations = Utils::GetLocationsList();	
		$courses = Utils::GetCoursesList();
		$attachments = array('' => 'Select one or more Attachemnts') + Attachment::where('type','marketing')->lists('name', 'id');
		$via_options = array('' => 'Select Option', 'Email'=>'Email', 'Sms'=> 'Sms', 'Both'=>'Both');
		$fields = array(''=>'select a Field to Insert', '{{first_name}}'=> 'First Name', '{{last_name}}'=> 'Last Name', '{{course_name}}'=> 'Course Name');
		return View::make('backend.marketing.edit', compact('message', 'courses', 'locations', 'attachments', 'via_options', 'fields'));
	}

	public function update($id)
	{
		$input = Input::except('attachments','files', '_method');
		$input['date_from'] = $input['date_from']  == '' || $input['date_from']  == '0000-00-00' ? null : $input['date_from'];
		$input['date_to'] = $input['date_to']  == ''  || $input['date_to']  == '0000-00-00'? null : $input['date_to'];
		$attachments = Input::get('attachments', array());
		foreach($attachments as $key => $value)
			if($value == "") 
				unset($attachments[$key]); 
		
		if ($input['location_id'] == '')
			$input['location_id'] = null;
		if ($input['course_id'] == '')
			$input['course_id'] = null;

		$validation = Validator::make($input, Marketing::$rules);
		Session::reflash();

		if ($validation->passes())
		{
			$message = $this->message->find($id);
			$message->update($input);

			$message->attachments()->sync($attachments);

			return Redirect::route('backend.marketing.index')->with('success','Message updated successfully');
		}

		return Redirect::route('backend.marketing.edit', $id)
			->withInput()
			->withErrors($validation)
			->with('message', 'There were validation errors.');
	}

	public function destroy($id)
	{
		$message = $this->message->find($id);
		$message->attachments()->sync(array());
		$message->delete();
		Session::reflash();

		return Redirect::route('backend.marketing.index');
	}

}