<?php namespace Controllers\Online;

use AdminController;
use Config, Input,Lang,Redirect,Sentry,Validator,View;
use OnlineQuestion;

class QuestionsController extends AdminController {

	/**
	 * OnlineQuestion Repository
	 *
	 * @var OnlineQuestion
	 */
	protected $question;

	public function __construct(OnlineQuestion $question)
	{
		parent::__construct();
		$this->question = $question;
	}

	/**
	 * Display a listing of the resource.
	 *
	 * @return Response
	 */
	public function index()
	{
		$steps = \OnlineStep::lists('name','id');

		$step_id = \Session::has('step_id') ? \Session::get('step_id') : Input::get('step_id');
		$query = $this->question;
		if (!empty($step_id)){
			$query = $query->where('step_id', $step_id);
			\Session::flashInput(array('step_id'=>$step_id));
		}
		$query = $query->orderBy('active', 'desc')
			->orderBy('step_id')
			->orderBy('order');
		$questions = $query->get();



		return View::make('online.backend.questions.index', compact('questions','steps'));

	}

	/**
	 * Show the form for creating a new resource.
	 *
	 * @return Response
	 */
	public function create()
	{
		$step_id = Input::get('step_id');
		if (!empty($step_id)){
			$steps = \OnlineStep::where('id', $step_id)->lists('name','id');
			\Session::flashInput(array('step_id'=>$step_id));
		}
		else {
			$steps = \OnlineStep::lists('name','id');
		}
		$types = array('single'=>'Single Choice', 'multiple'=>'Multiple Choices', 'text'=>'Open Text', 'upload'=>'Upload Answer');
		$weights = array('0', '5', '10','15','20','25','30','35','40','45','50','55','60','65','70','75','80','85','90','95','100');
		return View::make('online.backend.questions.create', compact('steps', 'types','weights'));
	}

	/**
	 * Store a newly created resource in storage.
	 *
	 * @return Response
	 */
	public function store()
	{
		$input = Input::except('_method');
		
		$input['active'] = isset($input['active']) && $input['active'] == '1' ? 1 : 0;		
		
		if (empty($input['order']))
		{
			$step = \OnlineStep::find($input['step_id']);	
			$input['order'] = $step->last_question_order + 10;	
		}
		
		$validation = Validator::make($input, OnlineQuestion::$rules);

		if ($validation->passes())
		{
			$question = $this->question->create($input);
			\Session::flashInput(array('step_id'=>$question->step_id));
			return Redirect::route('online.questions.index')
						->with('step_id', $question->step_id)
						->with('success', 'OnlineQuestion successfully created.');
		}

		return Redirect::route('online.questions.create')
		->withInput()
		->withErrors($validation)
		->with('message', 'There were validation errors.');
	}

	/**
	 * Show the form for editing the specified resource.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function edit($id)
	{
		$question = $this->question->find($id);

		if (is_null($question))
		{
			return Redirect::route('online.questions.index');
		}
		
		\Session::flashInput(array('step_id'=>$question->step_id));

		$steps = \OnlineStep::where('id', $question->step_id)->lists('name','id');
		$types = array('single'=>'Single Choice', 'multiple'=>'Multiple Choices', 'text'=>'Open Text', 'upload'=>'Upload Answer');
		$weights = array('0', '5', '10','15','20','25','30','35','40','45','50','55','60','65','70','75','80','85','90','95','100');
		return View::make('online.backend.questions.edit', compact('question','steps','types','weights'));
	}

	/**
	 * Update the specified resource in storage.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function update($id)
	{
		$input = Input::except('_method');
		$input['active'] = isset($input['active']) && $input['active'] == '1' ? 1 : 0;		
		$validation = Validator::make($input, OnlineQuestion::$rules);
		
		if ($validation->passes())
		{
			$question = $this->question->find($id);
			if (empty($input['order']))
			{
				$input['order'] = $question->step->last_question_order + 10;	
			}

			$question->update($input);
			\Session::flashInput(array('step_id'=>$question->step_id));
			return Redirect::route('online.questions.index')
						->with('step_id', $question->step_id)
						->with('success', 'OnlineQuestion successfully updated.');
		}

		return Redirect::route('online.questions.edit', $id)
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
		$question = $this->question->find($id);
		\Session::flashInput(array('step_id'=>$question->step_id));
		$question->delete();

		return Redirect::route('online.questions.index')
			->with('step_id', $question->step_id)
			->with('success', 'OnlineQuestion successfully deleted.');

	}

}