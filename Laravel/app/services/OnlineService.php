<?php namespace App\Services;

use Config, Input, Lang, Redirect, Sentry, Validator, View, Response, Exception, Mail, Email, DB;
use Order, Roster, OnlineRoster, Item, CourseInstance, Voucher, Log;
use App\Services\payments\PaymentFactory;
use App\Services\BookingPublic;
use App\Services\BookingGroup;
use App\Services\BookingPurchase;
use App\Services\BookingOnline;

class OnlineService {

	protected $data;
	protected $student;

	public function __construct()
	{
		$this->student = null;
		$user = \Sentry::getUser();
		if ($user && $user->hasAnyAccess(array('student')))
		{
			$this->student = $user->customer;
		}
	}

	public function GetCommonDataForGivenStep($course_id = null, $module_id = null, $step_id = null, $data = null) 
	{
		
		//create result object for view
		if (!$data)
			$this->data = new \stdClass;
		else
			$this->data = $data;

		$this->data->student = $this->student;
		
		$this->data->courses = \OnlineCourse::where('type', 'Online')
			->where('active', 1)
			->orderBy('order')
			->remember(Config::get('cache.minutes', 1))
			->get();
			
		//No course id we get it from either the module id or the step id	
		if (!$course_id && !$this->data->course)
		{
			if($module_id)				$course_id = $this->getCourseIdFromModuleId($module_id);
			if($step_id && !$course_id) $course_id = $this->getCourseIdFromStepId($step_id);
		}	
		
		if ($course_id)
		{
			$this->data->course = \OnlineCourse::find($course_id);
		}
		
		$this->data->student->current_online_roster = $course_id;
		$this->data->course->roster_id = $this->data->student->current_online_roster->id;
		
		if (!$module_id && !$this->data->module)
		{
			if($step_id) $module_id = $this->getModuleIdFromStepId($step_id);
		}	
		
		if ($step_id && !$this->data->step)
		{
			$this->data->step = $this->data->course->steps->filter(function($step) use($step_id) {return $step->id == $step_id;})->first();
			if (is_null($this->data->step)) {
				throw new \Exception('Invalid step id');
			}
			$module_id = $this->data->step->module_id;
		}
		
		if ($module_id && !$this->data->module)
		{
			$this->data->module = $this->data->course->modules->filter(function($module) use($module_id) {return $module->id == $module_id;})->first();
		}
		//
		//if (!$step_id && !$this->data->step && $this->data->course)
		//{
		//	$step_id = $this->getCurrentHistoryStepId($this->data);
		//}
		
		if ($course_id && !$module_id && !$step_id)
		{
			$step_id = $this->getCurrentHistoryStepId();
			if ($step_id)
			{
				$get_next = false;
				foreach ($this->data->course->steps as $step) {
					
					if ($get_next) {
						$this->data->step = $step;
						$step_id = $step->id;
						break;
					}
					if ($step->id == $step_id) {
						$get_next = true;
					}
				}
				//$this->data->step = $this->data->course->steps->filter(function($step) use($step_id) {return $step->id == $step_id;})->first();
				$module_id = $this->data->step->module_id;
				$this->data->module = $this->data->course->modules->filter(function($module) use($module_id) {return $module->id == $module_id;})->first();
			}
			else 
			{
				$this->data->module = $this->data->course->modules->first();	
			}		
		}
		
		if (!$step_id && !$this->data->step && $this->data->module)
		{
			$step_id = $this->getCurrentHistoryStepId($this->data->module->id);
			$get_next = false;
			foreach ($this->data->module->steps as $step) {				
				if ($get_next) {
					$this->data->step = $step;
					$step_id = $step->id;
					break;
				}
				if ($step->id == $step_id) {
					$get_next = true;
				}
			}
			
			if ($get_next && !$this->data->step)
			{
				$this->data->step = $this->data->module->steps->filter(function($step) use($step_id) {return $step->id == $step_id;})->first();
			}
			
			if ($this->data->step && $this->data->step->module_id != $module_id)
			{
				$step_id = null;
			}				
		}
		
		if (!$step_id)
		{
			$this->data->step = $this->data->module->steps->first();
			$step_id = $this->data->step->id;
		}
		
		$restartstep = Input::get('restartcourse', null); 
		if ($restartstep)
		{
			$sql = "DELETE ohsa, ohs 
			FROM online_history_step_answers ohsa
			JOIN online_history_steps ohs on ohs.id = ohsa.online_history_step_id
			JOIN online_history oh on oh.id = ohs.online_history_id
			JOIN online_rosters or on or.id = oh.roster_id
			JOIN courses c on c.id = or.course_id
			WHERE c.id = " . $this->data->module->course_id;
			\DB::delete($sql);
		}
		
		$restartstep = Input::get('restartmodule', null); 
		if ($restartstep)
		{
			$sql = "DELETE ohsa, ohs 
			FROM online_history_step_answers ohsa
			JOIN online_history_steps ohs on ohs.id = ohsa.online_history_step_id
			JOIN online_steps os on os.id = ohs.step_id
			WHERE os.module_id = " . $this->data->module->id;
			\DB::delete($sql);
		}
		
		$restartstep = Input::get('restartstep', null); 
		if ($restartstep)
		{
			$sql = "DELETE ohsa 
			FROM online_history_step_answers ohsa
			JOIN online_history_steps ohs on ohs.id = ohsa.online_history_step_id
			WHERE ohs.step_id = " . $step_id;
			\DB::delete($sql);
		}
		
		if ($this->data->student->current_online_roster) {
			$this->data->student->current_online_roster->current_module_answers = $module_id;
			$this->data->student->current_online_roster->current_history_step = $step_id;
		}
		$this->data->roster = $this->data->student->current_online_roster;
		$this->data->current_module = $this->data->roster ? $this->data->roster->ModuleAnswers($module_id) : null;
		$this->data->current_step = $this->data->roster ? $this->data->roster->CurrentHistoryStep($step_id) : null;
		
		$this->data->answer = Input::get('answer', null); 
		$this->data->question_id = Input::get('question_id', null);
		$this->data->next_question_id = Input::get('nq', 0);

		if($this->data->question_id && $this->data->answer)
		{
			$this->data->question_already_answered = \OnlineHistoryAnswer::where('online_history_step_id', $this->data->current_step->id)->where('question_id', $this->data->question_id)->first();		
			//$this->data->question = \OnlineQuestion::where('id', $this->data->question_id)->first();
			//$this->data->next_question_id = $this->data->roster->current_history_step->next_question_to_answer->id;
			//$this->data->answer = null; 
			//$this->data->question_id = null;
		}
		if( !$this->data->question_id && !$this->data->answer && 
			$this->data->roster->current_history_step && !$this->data->roster->current_history_step->display_results)
		{
			//$this->data->question = $this->data->roster->current_history_step->next_question_to_answer;
			if ($this->data->roster && $this->data->roster->current_history_step && $this->data->roster->current_history_step->next_question_to_answer)
			{
				$q_id = $this->data->roster->current_history_step->next_question_to_answer->id;
				$this->data->question = $this->data->step->questions->filter(function($question) use($q_id) {return $question->id == $q_id;})->first();
			}
		}
		
		$this->data->display_course_results = false;
		$this->data->display_module_results = false;
		$this->data->display_step_results = false;
		
		//if ($this->data->student->current_online_roster && $this->data->student->current_online_roster->course->IsCompleted())
		//{
		//	$this->data->display_course_results = true;
		//}
		//elseif ($this->data->current_module && $this->data->current_module->IsCompleted())
		//{
		//	$this->data->display_module_results = true;
		//}
		//else		
		if ($this->data->student->current_online_roster->current_history_step && 
			$this->data->student->current_online_roster->current_history_step->display_results)
		{
			$this->data->display_step_results = true;
		}

		// if we should display results then we don't need to go forward
		if ($this->data->display_course_results || $this->data->display_module_results || $this->data->display_step_results)
			return $this->data;		

		// get the question
		if($this->data->next_question_id != 0)
		{
			$q_id = $this->data->next_question_id;
			$this->data->question = $this->data->step->questions->filter(function($question) use($q_id) {return $question->id == $q_id;})->first();
			//$this->data->question = \OnlineQuestion::find($this->data->next_question_id);		
		}

		if(!$this->data->question && $this->data->question_id)
		{
			$q_id = $this->data->question_id;
			$this->data->question = $this->data->step->questions->filter(function($question) use($q_id) {return $question->id == $q_id;})->first();
			//$this->data->question = \OnlineQuestion::where('id', $this->data->question_id)->first();		
		}
		else if (!$this->data->question)
		{
			$qs = $this->data->step->questions;
			//$qs = \OnlineQuestion::where('step_id', $step_id)->get();		
			$qs = $qs->sortBy(function($q){return $q->order;}); 
			$this->data->question = $qs->first();
		}

		//print_r($this->data->current_module);
		//print_r($this->data->current_step);
		//print_r($this->data->question);
		//exit();

		$correct_answer = $this->data->question ? $this->data->question->answers->filter(function($ans){ return $ans->correct;})->first() : null;
		if($this->data->question_already_answered)
		{
			$this->data->message = "Question Already answered";
			$this->data->correct = 2;
			$this->data->current_answer = $this->data->answer;
		}
		else
		{
			$this->data->message = $correct_answer && $correct_answer->id == $this->data->answer ? "Correct Answer" : "Wrong Answer";
			$this->data->correct = $correct_answer && $correct_answer->id == $this->data->answer ? 1 : 0;
			$this->data->current_answer = $this->data->answer;
			$history = $this->updateHistory();
		}
		
		$this->data->still_questions = true;
		//if($this->data->question && $this->data->question->index == $this->data->step->questions->count() )
		//{
		//	$this->data->still_questions = false;
		//}
		$this->data->goto_next_question = is_null($this->data->answer) ? false : true;

		//print_r($this->data);
		//exit();

		return $this->data;

	}

	public function InitialiseContentData($data = null)
	{

		//create result object for view
		if (!$data)
			$data = new \stdClass;
		$data->student = $this->student;
		$data->courses = null;
		$data->course = null;
		$data->module = null;
		$data->step = null;
		$data->question = null;
		$data->history = null;
		$data->current_question = null;
		$data->question_already_answered = null; 
		
		$data = \OnlineService::getStaticOnlineData($data);
	
		return $data;
		
	}

	public static function getStaticOnlineData($data = null)
	{
		if (!$data)
			$data = new \stdClass;


		$data->routes[] = (object)array('url'=>'', 'menu'=>true, 'name'=>'Home');
		$data->routes[] = (object)array('url'=>'about', 'menu'=>true, 'name'=>'About Us');
		$data->routes[] = (object)array('url'=>'courses', 'menu'=>true, 'name'=>'Courses');
		$data->routes[] = (object)array('url'=>'contact', 'menu'=>true, 'name'=>'Contact Us');
		
		$data->carousels[] = (object)array( 
			'image'=>'/onlinecourses/src/images/coffee.jpg', 
			'title'=>'Example headline.', 
			'description'=>'Cras justo odio, dapibus ac facilisis in, egestas eget quam. Donec id elit non mi porta gravida at eget metus. Nullam id dolor id nibh ultricies vehicula ut id elit.' );
		$data->carousels[] = (object)array( 
			'image'=>'/onlinecourses/src/images/coc.jpg', 
			'title'=>'Example headline.', 
			'description'=>'Cras justo odio, dapibus ac facilisis in, egestas eget quam. Donec id elit non mi porta gravida at eget metus. Nullam id dolor id nibh ultricies vehicula ut id elit.' );
		$data->carousels[] = (object)array( 
			'image'=>'/onlinecourses/src/images/coffeeARtF.jpg', 
			'title'=>'Example headline.', 
			'description'=>'Cras justo odio, dapibus ac facilisis in, egestas eget quam. Donec id elit non mi porta gravida at eget metus. Nullam id dolor id nibh ultricies vehicula ut id elit.' );
		
		$data->ads[] = (object)array( 
			'image'=>'/onlinecourses/src/images/coffee.jpg', 
			'title'=>'Example headline.', 
			'description'=>'Cras justo odio, dapibus ac facilisis in, egestas eget quam. Donec id elit non mi porta gravida at eget metus. Nullam id dolor id nibh ultricies vehicula ut id elit.' );
		$data->ads[] = (object)array( 
			'image'=>'/onlinecourses/src/images/coc.jpg', 
			'title'=>'Example headline.', 
			'description'=>'Cras justo odio, dapibus ac facilisis in, egestas eget quam. Donec id elit non mi porta gravida at eget metus. Nullam id dolor id nibh ultricies vehicula ut id elit.' );
		$data->ads[] = (object)array( 
			'image'=>'/onlinecourses/src/images/coffeeARtF.jpg', 
			'title'=>'Example headline.', 
			'description'=>'Cras justo odio, dapibus ac facilisis in, egestas eget quam. Donec id elit non mi porta gravida at eget metus. Nullam id dolor id nibh ultricies vehicula ut id elit.' );
		
		return $data;	
	}

	private function getCourseIdFromModuleId($module_id)
	{
		$course_id = DB::table('online_modules')
			->where('id', $module_id)
			->pluck('course_id');

		return $course_id;
	}
	
	private function getCourseIdFromStepId($step_id)
	{
		
		$course_id = DB::table('online_modules as om')
			->join('online_steps as os', 'om.id', '=', 'os.module_id')
			->where('os.id', $step_id)
			->pluck('om.course_id');

		return $course_id;
	}
	
	private function getModuleIdFromStepId($step_id)
	{
		$module_id = DB::table('online_steps')->where('id', $step_id)->pluck('module_id');

		return $module_id;
	}
	
	private function getCurrentHistoryStepId($module_id = null)
	{
		
		$query = DB::table('online_history_steps as ohs')
			->join('online_history as oh', 'oh.id', '=', 'ohs.online_history_id')
			->join('online_steps as os', 'os.id', '=', 'ohs.step_id')
			->join('online_modules as om', 'om.id', '=', 'os.module_id')
			->where('oh.customer_id', $this->data->student->id)
			->where('oh.roster_id', $this->data->course->roster_id);
		
		if ($module_id) {
			$query = $query->where('om.id', $module_id);
		}
			
		$result = $query->orderBy('om.order', 'desc')
			->orderBy('os.order', 'desc')
			->select('oh.roster_id', 'ohs.step_id')
			->first();

		return $result ? $result->step_id : null;
	}

	private function updateHistory()
	{
		if ($this->data->roster && !$this->data->roster->history)
		{
			$input = array(
				'customer_id' => $this->data->student->id,
				'course_id' => $this->data->module->course_id,
				'roster_id' => $this->data->roster->id
				);
			$this->data->roster->history = \OnlineHistory::create($input);
		}		
		if (!$this->data->current_step)
		{
			$input = array(
				'online_history_id' => $this->data->roster->history->id,
				'step_id' => $this->data->step->id,
				'active' => 1
				);
			$this->data->current_step = \OnlineHistoryStep::create($input);
		}

		//$answer = Input::get('answer', null); 
		$question_id = $this->data->question_id;
		
		//print_r($this->data->current_step);
		//exit();
		$current_answer = null;
		if($this->data->roster->current_history_step && $this->data->roster->current_history_step->answers->count() ) {
			$current_answer = $this->data->roster->current_history_step->answers->filter(function($ans) use($question_id) {
					return $ans->question_id == $question_id && $ans->active == 1;
				})->first();
		}

		if($current_answer)
		{
			\OnlineHistoryAnswer::where('online_history_step_id', '=', $current_answer->online_history_step_id)
				->where('question_id', '=', $current_answer->question_id)->delete();
		}
		
		if($this->data->question_id && $this->data->answer)
		{
			$result = 2;
			$description = $this->data->answer;
			$q_id = $this->data->question_id;
			$question = $this->data->step->questions->filter(function($question) use($q_id) {return $question->id == $q_id;})->first();
			//$question = \OnlineQuestion::find($this->data->question_id);
			if (is_numeric($this->data->answer))
			{
				$answer = $this->data->answer;
				$description = $question->answers->filter(function($ans) use($answer) { return $ans->id == $answer; })->first()->description;
				if($answer == $question->correct_answers->first()->id)
				$result = 1;
				else
					$result = 0;
			}
			
			if (!$this->data->question_already_answered)
			{
				$input = array(
					'online_history_step_id' => $this->data->current_step->id,
					'question_id' => $this->data->question_id,
					'answer' => $description,
					'result' => $result,
					'active' => 1
					);
				\OnlineHistoryAnswer::create($input);
			}
		}

	}

	public function deleteTimeoutHistory()
	{
		$active_rosters = \OnlineRoster::whereNull('certificate_id')->get();
		foreach($active_rosters as $roster)
		{
			Log::debug("roster id: " .  $roster->id . " | customer id : " . $roster->customer_id);
			if ($roster->history) 
			{
				foreach($roster->history->modules as $module_id)
				{
					$steps_on_module = \DB::table('online_steps')->where('module_id',$module_id)->where('active', 1)->count();
					$steps = $roster->history->steps->filter(function($h_step) use($module_id) {return $h_step->step->module_id == $module_id;	});	
					Log::debug( "steps_on_module ($module_id): $steps_on_module | steps completed: " . $steps->count());			
					if($steps->count() != $steps_on_module) {
						Log::debug( "not same deleting structure");
						$roster->history->steps->each(function($h_step) use($module_id) { if ($h_step->step->module_id == $module_id) {$h_step->delete();}	});
						//$steps->delete();
					}	
					
				}
			}
		}
	}
	
}