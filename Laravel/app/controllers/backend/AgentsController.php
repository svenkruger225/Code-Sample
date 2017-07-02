<?php namespace Controllers\Backend;

use AdminController;
use Cartalyst\Sentry\Users\LoginRequiredException;
use Cartalyst\Sentry\Users\PasswordRequiredException;
use Cartalyst\Sentry\Users\UserExistsException;
use Cartalyst\Sentry\Users\UserNotFoundException;
use Config;
use Input;
use Lang;
use Redirect;
use Sentry;
use Validator;
use View;
use User, Agent, AgentCourse;
use CourseBundle, Course, Location;

class AgentsController extends AdminController {
    
	/**
	 * Agent Repository
	 *
	 * @var Agent
	 */
	protected $agent;
        /**
	 * Declare the rules for the form validation
	 *
	 * @var array
	 */
	protected $validationRules = array(
		'name'       => 'required',
                'username' => 'required|min:3|unique:users',
		'email'            => 'required|email|unique:users,email',
		'password'         => 'required|between:3,32',
                'email' => 'required|email|unique:users,email',
		'phone' => 'required',
                'code' => 'required|unique:agents,code'
	);

	public function __construct(Agent $agent)
	{
		parent::__construct();
		$this->agent = $agent;
	}
        
        public function updateRule($userId=0,$agentId=0){
            return array(
                    'name'       => 'required',
                    'username' => 'required|min:3|unique:users,username,'.$userId,
                    'email' => 'required|email|unique:users,email,'.$userId.'|unique:agents,email,'.$agentId,
                    'phone' => 'required',
                    'code' => 'required|unique:agents,code,'.$agentId
            );
        }
	/**
	 * Display a listing of the resource.
	 *
	 * @return Response
	 */
	public function dashboard()
	{
		return View::make('backend.agents.dashboard');
	}
	
	public function index()
	{

		$search = Input::get('search');
		
		$query = $this->agent;
		
		if(	$search && $search != '')
			$query = $query
				->where('name', 'like', '%' . $search . '%')
				->orWhere('contact_name', 'like', '%' . $search . '%')
				->orWhere('contact_position', 'like', '%' . $search . '%');

		// Paginate the users
		$agents = $query->orderBy('name')
			->paginate(20)
			->appends(array('search' => $search,));

		return View::make('backend.agents.index', compact('agents'));
	}

	/**
	 * Show the form for creating a new resource.
	 *
	 * @return Response
	 */
	public function create()
	{
            $courses = array('' => 'Select Course Type:') + \Course::where('active',1)->where('id','!=',9)->where('type','FaceToFace')->orderBy('name')->lists('name', 'id');
		
            $locations = array('' => 'Select Location:') + Location::where('parent_id',0)->lists('name', 'id');
            //Session::reflash();
            return View::make('backend.agents.create', compact('courses', 'locations'));
	}

	/**
	 * Store a newly created resource in storage.
	 *
	 * @return Response
	 */
	public function store()
	{
                $input = Input::except('location_id','course_id','price_online','price_offline');
			
		$course_data = Input::only('location_id','course_id','price_online','price_offline');
		$bundle_courses= array();
		foreach($course_data['course_id'] as $index => $val)
		{
			if ($index == 0)
				continue;
			$obj = array();
			$obj['course_id'] = $course_data['course_id'][$index];
			$obj['location_id'] = $course_data['location_id'][$index];
                        $obj['price_online'] = $course_data['price_online'][$index];
			$obj['price_offline'] = $course_data['price_offline'][$index];
			$obj['active'] = 1;
			array_push($bundle_courses, $obj);
		}

		$validation = Validator::make($input, $this->validationRules);
               
                if ($validation->passes())
		{
                    if(empty($input['active'])){
                        $active=0;
                    }
                    else{
                        $active=$input['active'];
                    }
                    $userInput=array(
                        'username'=>$input['username'],
                        'first_name'=>$input['name'],
                        'email'=>$input['email'],
                        'password'=>$input['password'],
                        'activated'=>$active
                    );
                        // Was the user created?
			if ($user = Sentry::getUserProvider()->create($userInput))
			{
				// Assign the selected groups to this user
				$group = Sentry::getGroupProvider()->findById(6);
                                $user->addGroup($group);
                                // Get the inputs, with some exceptions
                                $agentInput = Input::except('password','username','location_id','course_id','price_online','price_offline');
                                $agentInput['user_id']=$user->id;
                                $agentInput['contact_name']=$user->username;
                                
				
                                //Agent pricing added
                                $agentbundle=$this->agent->create($agentInput);
                                foreach ($bundle_courses as $data)
                                {
                                        $course_id = $data['course_id'];
                                        unset($data['course_id']);
                                        $agentbundle->bundles()->attach($course_id, $data );
                                        
                               }
			}
                        
			return Redirect::route('backend.agents.index');
		}

		return Redirect::route('backend.agents.create')
			->withInput()
			->withErrors($validation)
			->with('message', 'There were validation errors.');
	}

	/**
	 * Display the specified resource.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function show($id)
	{
            die('in agent show action');
		$agent = $this->agent->findOrFail($id);

		return View::make('backend.agents.show', compact('agent'));
	}

	/**
	 * Show the form for editing the specified resource.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function edit($id)
	{
		$courses = array('' => 'Select Course Type:') + \Course::where('active',1)->where('id','!=',9)->where('type','FaceToFace')->orderBy('name')->lists('name', 'id');
		
                $locations = array('' => 'Select Location:') + Location::where('parent_id',0)->lists('name', 'id');
		
		$agent = $this->agent->find($id);

		$agent->courses = $agent->courses->sortBy(function($agent_course)
			{
				return $agent_course->location ? $agent_course->location->name : $agent_course->location_id;
			});

		if (is_null($agent))
		{
			return Redirect::route('backend.agents.index');
		}

		return View::make('backend.agents.edit', compact('agent', 'courses', 'locations'));
	}

	/**
	 * Update the specified resource in storage.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function update($id)
	{
		$input = Input::except('c_id','agent_id', 'course_id','location_id','price_online','price_offline','act', '_method');
		
                $agent = $this->agent->find($id);
                $validation = Validator::make($input,$this->updateRule($agent->user_id,$id));
                
		$course_data = Input::only('c_id','agent_id','location_id','course_id','price_online','price_offline');
		$agent_courses= array();
		foreach($course_data['course_id'] as $index => $val)
		{
			if (empty($val))
				continue;
			$obj = array();
			$obj['id'] = $course_data['c_id'][$index] == '' ? null : $course_data['c_id'][$index];
			$obj['agent_id'] = $course_data['agent_id'][$index];
			$obj['course_id'] = $course_data['course_id'][$index];
			$obj['location_id'] = $course_data['location_id'][$index];
                        $obj['price_online'] = $course_data['price_online'][$index];
			$obj['price_offline'] = $course_data['price_offline'][$index];
			$obj['active'] = 1;
			array_push($agent_courses, $obj);
		}
		if ($validation->passes())
		{
                    //update agent record
                        unset($input['username']);
                        unset($input['password']);
                        $input['contact_name']=Input::get('username');
			$agent->update($input);
                    //update user record    
                        if(empty($input['active'])){
                            $active=0;
                        }
                        else{
                            $active=$input['active'];
                        }
                        $userInput=array(
                                'username'=>Input::get('username'),
                                'first_name'=>$input['name'],
                                'email'=>$input['email'],
                                'activated'=>$active
                            );
                        if (Input::get('password')!= '') {
                            $userInput['password']=Input::get('password');
                        }
                        $user = User::find($agent->user_id);
                        $user->update($userInput);
                    //update agent pricing record    
			foreach ($agent->courses as $item)
			{
				$found = array_filter($agent_courses, function ($course) use($item) {return ($course['id'] == $item->id);});
				$current = current($found);
				
				if (!$current)
					$item->delete();
				else
					$item->update($current);
				
			}

			foreach ($agent_courses as $item)
			{
				$found = array_filter($agent->courses->toArray(), function ($course) use($item) {return ($item['id'] == $course['id']);});
				$current = current($found);
				
				if (!$current)
					AgentCourse::create($item);
                                    
			}
			return Redirect::route('backend.agents.edit', $id)->with('success', 'Agent Updated successfully');
		}

		return Redirect::route('backend.agents.edit', $id)
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
		$this->agent->find($id)->delete();

		return Redirect::route('backend.agents.index');
	}


	function arrayRecursiveDiff($aArray1, $aArray2) { 
		$aReturn = array(); 

		foreach ($aArray1 as $mKey => $mValue) { 
			if (array_key_exists($mKey, $aArray2)) { 
				if (is_array($mValue)) { 
					$aRecursiveDiff = $this->arrayRecursiveDiff($mValue, $aArray2[$mKey]); 
					if (count($aRecursiveDiff)) { $aReturn[$mKey] = $aRecursiveDiff; } 
				} else { 
					if ($mValue != $aArray2[$mKey]) { 
						$aReturn[$mKey] = $mValue; 
					} 
				} 
			} else { 
				$aReturn[$mKey] = $mValue; 
			} 
		} 

		return $aReturn; 
	} 


}