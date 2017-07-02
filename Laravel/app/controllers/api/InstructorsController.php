<?php namespace Controllers\Api;

use AdminController;
use Cartalyst\Sentry\Groups\GroupExistsException;
use Cartalyst\Sentry\Groups\GroupNotFoundException;
use Cartalyst\Sentry\Groups\NameRequiredException;
use Config, Input, Lang, Redirect, Sentry, Validator, View, DB, Response, Exception, stdClass;
use CourseBundle, Location, CoursePrice, CourseInstance, Course;

class InstructorsController extends AdminController {

	public function getInstructors()
	{	
		return Response::json(array());
	}
	
	public function updateInstructors()
	{
		try
		{
			$type = Input::json()->get('type', null);
			$instance = Input::json()->get('instance', null);
			$instructors = Input::json()->get('instructor', array());
			$instructors = !$instructors ? array() : $instructors;
			//return Response::json(array('msg'=>"Successfully updated"));
			
			
			
			if (!empty($instance))
			{
				$class = $type == 'Group' ? 'GroupBooking' : 'CourseInstance';
				
				$instance = $class::find($instance);
				$instance->instructors()->sync($instructors);
				return Response::json(array('msg'=> 'Class trainers updated successfully'));
			}
			else
			{
				$msg = "Make a selection first";
				throw new Exception($msg);				
			}
			
		}
		catch (Exception $ex)
		{
			return Response::json(array(
				'success' => false,
				'Message' => "Problem updating instructors <br>" . $ex->getMessage()
				), 500);
		}
	}

}