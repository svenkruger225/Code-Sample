<?php namespace Controllers\Backend;

use AdminController;
use Cartalyst\Sentry\Users\LoginRequiredException;
use Cartalyst\Sentry\Users\PasswordRequiredException;
use Cartalyst\Sentry\Users\UserExistsException;
use Cartalyst\Sentry\Users\UserNotFoundException;
use Config,Input,Lang,Redirect,Sentry,Validator,View, Utils;
use Session, Location, Course, Message, Attachment, EmailService, MessageType;

class MessagesController extends AdminController {

	/**
	 * Message Repository
	 *
	 * @var Message
	 */
	protected $message;

	public function __construct(Message $message)
	{
		parent::__construct();
		$this->message = $message;
	}

	/**
	 * Display a listing of the resource.
	 *
	 * @return Response
	 */
	public function index()
	{
		$input = Session::get('_old_input');

		$messages_location = (array_key_exists('messages_location', Input::all())) ? Input::get('messages_location') : ( ( $input && array_key_exists('messages_location', $input)) ? $input['messages_location'] : ''  );  
		$messages_course = (array_key_exists('messages_course', Input::all())) ? Input::get('messages_course') : ( ( $input && array_key_exists('messages_course', $input)) ? $input['messages_course'] : ''  );  
		$messages_type = (array_key_exists('messages_type', Input::all())) ? Input::get('messages_type') : ( ( $input && array_key_exists('messages_type', $input)) ? $input['messages_type'] : ''  );  


		Session::flashInput(array('messages_location'=>$messages_location, 'messages_course'=>$messages_course,'messages_type'=>$messages_type));

		//if( (!isset($location) || empty($location)) &&  (!isset($course) || empty($course) &&  (!isset($message) || empty($message))))
		//	$messages = array();
		//else
		//{
		$query = $this->message;
		if(isset($messages_location) && !empty($messages_location))
			$query = $query->where('location_id', $messages_location);
		
		if(isset($messages_course) && !empty($messages_course))
			$query = $query->where('course_id', $messages_course);
			
		if(isset($messages_type) && !empty($messages_type))
			$query = $query->where('message_id', $messages_type);

		$messages = $query->orderBy('message_id')->orderBy('course_id')->orderBy('location_id')->get();

		//}
			
		$locations = array('' => 'Select a Location') + Location::lists('name', 'id');
		$courses = array('' => 'Select a Course') + Course::lists('name', 'id');
		$messagetypes = array('' => 'Select a Msg Type') + MessageType::lists('name', 'id');

		return View::make('backend.messages.index', compact('messages','courses', 'locations', 'messagetypes'));
	}

	/**
	 * Show the form for creating a new resource.
	 *
	 * @return Response
	 */
	public function create()
	{
		$locations = array('' => 'Select a Location') + Location::lists('name', 'id');
		$courses = array('' => 'Select a Course') + Course::lists('name', 'id');
		$attachments = array('' => 'Select one or more Attachemnts') + Attachment::where('type','message')->lists('name', 'id');
		$messagetypes = array('' => 'Select a Msg Type') + MessageType::lists('name', 'id');
		Session::reflash();
		return View::make('backend.messages.create', compact('courses', 'locations', 'attachments', 'messagetypes'));
	}

	/**
	 * Store a newly created resource in storage.
	 *
	 * @return Response
	 */
	public function store()
	{
		$input = Input::except('attachments','files');
		$attachments = Input::get('attachments', array());
		foreach($attachments as $key => $value)
			if($value == "") 
				unset($attachments[$key]); 
			
		if ($input['location_id'] == '' || $input['message_id'] == Utils::MessageTypeId('Admin'))
			$input['location_id'] = null;
		if ($input['course_id'] == '' || $input['message_id'] == Utils::MessageTypeId('Admin'))
			$input['course_id'] = null;


		$validation = Validator::make($input, Message::$rules);
		Session::reflash();

		if ($validation->passes())
		{
			$message = $this->message->create($input);
			$message->attachments()->sync($attachments);

			return Redirect::route('backend.messages.edit', $message->id)->with('success','Message created successfully');
		}

		return Redirect::route('backend.messages.create')
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
			return Redirect::route('backend.messages.index')->with('message', 'Could not clone message.');
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

			return Redirect::route('backend.messages.edit', $message->id)->with('success','Message Cloned successfully');
		}

		return View::make('backend.messages.show', compact('message'));
	}

	/**
	 * Show the form for editing the specified resource.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function edit($id)
	{
		$message = $this->message->find($id);

		if (is_null($message))
		{
			return Redirect::route('backend.messages.index');
		}
		
		Session::reflash();
		$locations = array('' => 'Select a Location') + Location::lists('name', 'id');
		$courses = array('' => 'Select a Course') + Course::lists('name', 'id');
		$attachments = array('' => 'Select one or more Attachemnts') + Attachment::where('type','message')->lists('name', 'id');
		$messagetypes = array('' => 'Select a Msg Type') + MessageType::lists('name', 'id');
		$fields = array(''=>'select a Field to Insert', '{{first_name}}'=> 'First Name', '{{last_name}}'=> 'Last Name', '{{course_name}}'=> 'Course Name');
		return View::make('backend.messages.edit', compact('message', 'courses', 'locations', 'attachments', 'messagetypes', 'fields'));
	}

	/**
	 * Update the specified resource in storage.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function update($id)
	{
		$input = Input::except('attachments','files', '_method');
		$attachments = Input::get('attachments', array());
		foreach($attachments as $key => $value)
			if($value == "") 
				unset($attachments[$key]); 

		if ($input['location_id'] == '' || $input['message_id'] == Utils::MessageTypeId('Admin'))
			$input['location_id'] = null;
		if ($input['course_id'] == '' || $input['message_id'] == Utils::MessageTypeId('Admin'))
			$input['course_id'] = null;

		$validation = Validator::make($input, Message::$rules);
		Session::reflash();

		if ($validation->passes())
		{
			$message = $this->message->find($id);
			$message->update($input);

			$message->attachments()->sync($attachments);

			return Redirect::route('backend.messages.edit', $message->id)->with('success','Message updated successfully');
		}

		return Redirect::route('backend.messages.edit', $id)
			->withInput()
			->withErrors($validation)
			->with('message', 'There were validation errors.');
	}

	/**
	 * Remove the specified resource from storage.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function destroy($id)
	{
		$message = $this->message->find($id);
		$message->attachments()->sync(array());
		$message->delete();
		Session::reflash();

		return Redirect::route('backend.messages.index');
	}

}