<?php namespace Controllers\Online;

use AdminController;
use Config, Input,Lang,Redirect,Sentry,Validator,View;
use OnlineAnswer;

class AnswersController extends AdminController {

	/**
	 * OnlineAnswer Repository
	 *
	 * @var OnlineAnswer
	 */
	protected $answer;

	public function __construct(OnlineAnswer $answer)
	{
		parent::__construct();
		$this->answer = $answer;
	}

	/**
	 * Display a listing of the resource.
	 *
	 * @return Response
	 */
	public function index()
	{
		$questions = \OnlineQuestion::lists('title','id');

		$question_id = \Session::has('question_id') ? \Session::get('question_id') : Input::get('question_id');
		$query = $this->answer;
		if (!empty($question_id)){
			$query = $query->where('question_id', $question_id);
			\Session::flashInput(array('question_id'=>$question_id));
		}
		$query = $query->orderBy('active', 'desc')
			->orderBy('question_id')
			->orderBy('order');
		$answers = $query->get();



		return View::make('online.backend.answers.index', compact('answers','questions'));

	}

	/**
	 * Show the form for creating a new resource.
	 *
	 * @return Response
	 */
	public function create()
	{
		$question_id = Input::get('question_id');
		if (!empty($question_id)){
			$questions = \OnlineQuestion::where('id', $question_id)->lists('title','id');
			\Session::flashInput(array('question_id'=>$question_id));
		}
		else {
			$questions = \OnlineQuestion::lists('title','id');
		}
		$types = array('multiple'=>'Multiple Choices', 'single'=>'Single Choice', 'text'=>'Open Text', 'upload'=>'Upload Answer');
		$weights = array('0', '5', '10','15','20','25','30','35','40','45','50','55','60','65','70','75','80','85','90','95','100');
		return View::make('online.backend.answers.create', compact('questions', 'types','weights'));
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
			$question = \OnlineQuestion::find($input['question_id']);	
			$input['order'] = $question->last_answer_order + 10;	
		}
		
		$validation = Validator::make($input, OnlineAnswer::$rules);

		if ($validation->passes())
		{
			$answer = $this->answer->create($input);
			\Session::flashInput(array('question_id'=>$answer->question_id));
			return Redirect::route('online.answers.index')
							->with('question_id', $answer->question_id)
							->with('success', 'OnlineAnswer successfully created.');
		}

		return Redirect::route('online.answers.create')
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
		$answer = $this->answer->find($id);

		if (is_null($answer))
		{
			return Redirect::route('online.answers.index');
		}
		\Session::flashInput(array('question_id'=>$answer->question_id));
		
		$questions = \OnlineQuestion::where('id', $answer->question_id)->lists('title','id');
		$types = array('multiple'=>'Multiple Choices', 'single'=>'Single Choice', 'text'=>'Open Text', 'upload'=>'Upload Answer');
		$weights = array('0', '5', '10','15','20','25','30','35','40','45','50','55','60','65','70','75','80','85','90','95','100');
		return View::make('online.backend.answers.edit', compact('answer','questions','types','weights'));
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
		$validation = Validator::make($input, OnlineAnswer::$rules);
		
		if ($validation->passes())
		{
			$answer = $this->answer->find($id);
			if (empty($input['order']))
			{
				$input['order'] = $answer->question->last_answer_order + 10;	
			}

			$answer->update($input);
			\Session::flashInput(array('question_id'=>$answer->question_id));
			return Redirect::route('online.answers.index')
							->with('question_id', $answer->question_id)
							->with('success', 'OnlineAnswer successfully updated.');
		}

		return Redirect::route('online.answers.edit', $id)
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
		$answer = $this->answer->find($id);
		\Session::flashInput(array('question_id'=>$answer->question_id));
		$answer->delete();

		return Redirect::route('online.answers.index')
			->with('question_id', $answer->question_id)
			->with('success', 'OnlineAnswer successfully deleted.');
	}

}