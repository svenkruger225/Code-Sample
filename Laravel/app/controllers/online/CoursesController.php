<?php namespace Controllers\Online;

use OnlineAuthController;
use Cartalyst\Sentry\Users\LoginRequiredException;
use Cartalyst\Sentry\Users\PasswordRequiredException;
use Cartalyst\Sentry\Users\UserExistsException;
use Cartalyst\Sentry\Users\UserNotFoundException;
use Config;
use Input;
use Lang;
use Controller, Response;
use Redirect;
use Sentry;
use Validator;
use View;
use OnlineCourse, OnlineService;

class CoursesController extends OnlineAuthController {

	protected $course;
	protected $routes;
	protected $carousels;
	protected $ads;
	protected $goto_next_question;
	protected $current_question;
	protected $course_history;
	protected $current_step;
	protected $display_step_results;
	protected $display_module_results;
	protected $display_course_results;
	
	protected $data;

	public function __construct(OnlineCourse $course)
	{
		// Call parent
		parent::__construct();
		$this->data = OnlineService::InitialiseContentData();
		$this->course = $course;
	}

	public function index()
	{
		$student = $this->student;
		$startup = '';
		$routes = $this->routes;
		$carousels = $this->carousels;
		$ads = $this->ads;
		
		$courses = $this->course
			->where('type', 'Online')
			->where('active', 1)
			->orderBy('order')
			->remember(Config::get('cache.minutes', 1))
			->get();
		
		//return Response::json($courses);
		return View::make('online/public/courses', compact('routes', 'courses', 'startup', 'student'));
		//return Response::json($courses)->setCallback(Input::get('callback')); // jsonp response

	}

	public function getCourse($slug)
	{		


		$course = \Course::where('short_name', $slug)
			->remember(Config::get('cache.minutes', 1))
			->first();
			
		return $this->displayStep($course->id);
		
	//	$this->data = OnlineService::GetCommonDataForGivenStep($course->id, null, null, $this->data);
	//	$data = $this->data;
		//return View::make('online/public/modules', compact('data'));

	}

	public function getModule($id)
	{
		return $this->displayStep(null, $id);
		
	//	$this->data = OnlineService::GetCommonDataForGivenStep(null, $id, null, $this->data);	
	//	$data = $this->data;
		//return View::make('online/public/module', compact('data'));

	}

	public function getStep($id)
	{
		$input = Input::all();

		return $this->displayStep(null, null, $id);
	}
	
	public function postAnswer()
	{
		$input = Input::except('_token');
		$this->goto_next_question = true;
		
		$this->data->question = \OnlineQuestion::where('id', $input['question_id'])->first();

		return $this->getStep($this->data->question->step->id);
	}	
	
	public function displayCourseResults($id)
	{
		$this->display_course_results = true;
		
		return $this->displayStep($id);

		$this->student->current_online_roster = $id; 
		
		//print_r($this->student->current_online_roster->course->steps->count());
		//print_r($this->student->current_online_roster->course->steps);
		//exit();

		$step_id = $this->student->current_online_roster->course->steps->last()->id;
		
		return $this->getStep($step_id);
	}	
	
	public function displayModuleResults($id)
	{
		$this->display_module_results = true;
		return $this->displayStep(null, $id);
		
		//return $this->getStep($id);
	}
		
	public function displayStepResults($id)
	{
		$this->display_step_results = true;
		return $this->displayStep(null, null, $id);
	}		
	
	private function displayStep($course_id = null, $module_id = null, $step_id = null)
	{
		$this->data = OnlineService::GetCommonDataForGivenStep($course_id, $module_id, $step_id, $this->data);
		
		
	//	$date = \Carbon::now()->addMinutes(1);
	//	Queue::later($date, function($job) use ($this)
	//		{
	//			Account::delete($id);

////
	//			$job->delete();
			//});		
		
		
		// check if we need to display the results for the course
		if($this->data->current_step && ($this->data->display_course_results || $this->display_course_results))
		//if(!$this->data->question_id && !$this->data->answer && $this->data->current_step && ($this->data->display_course_results || $this->display_course_results))
		{
			$data = $this->data;
			return View::make('online/public/course-result', compact('data'));
		}
				
		// check if we need to display the results for the module
		if($this->data->current_step && ($this->data->display_module_results || $this->display_module_results))
		{
			$data = $this->data;

			return View::make('online/public/module-result', compact('data'));
		}
		
		// check if we need to display the results for this step
		if($this->data->current_step && ($this->data->display_step_results || $this->display_step_results))
		{
			$student = $this->data->student;
			$data = $this->data;
			return View::make('online/public/step-result', compact('data'));
		}
		
		$data = $this->data;
		//print_r($this->data->step);
		//print_r($this->data->current_step);
		//exit();
		
		
		return View::make('online/public/step', compact('data'));

	}

	private function getQuestion($questions, $index)
	{
		$desired_object = $food->filter(function($item) {
			return $item->id == 24;
		})->first();
	}

	private function getHistory($course_id) 
	{	
		$roster =  $this->data->student->CurrentOnlineRoster($course_id);
		return $roster ? $roster->history : null;
	}


	

}