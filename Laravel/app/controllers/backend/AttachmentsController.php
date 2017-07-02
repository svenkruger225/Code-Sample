<?php namespace Controllers\Backend;

use AdminController;
use Cartalyst\Sentry\Users\LoginRequiredException;
use Cartalyst\Sentry\Users\PasswordRequiredException;
use Cartalyst\Sentry\Users\UserExistsException;
use Cartalyst\Sentry\Users\UserNotFoundException;
use Config,Input,Lang,Redirect,Sentry,Validator,View;
use Attachment, UploadService;

class AttachmentsController extends AdminController {

	/**
	 * Attachment Repository
	 *
	 * @var Attachment
	 */
	protected $attachment;

	public function __construct(Attachment $attachment)
	{
		parent::__construct();
		$this->attachment = $attachment;
	}

	/**
	 * Display a listing of the resource.
	 *
	 * @return Response
	 */
	public function index()
	{
		$attachments = $this->attachment->orderBy('type')->orderBy('name')->get();

		return View::make('backend.attachments.index', compact('attachments'));
	}

	/**
	 * Show the form for creating a new resource.
	 *
	 * @return Response
	 */
	public function create()
	{
		$types = array('message'=>'Message', 'marketing'=>'Marketing');
		return View::make('backend.attachments.create', compact('types'));
	}

	/**
	 * Store a newly created resource in storage.
	 *
	 * @return Response
	 */
	public function store()
	{
		
		$input = Input::except('attachment');
		$file_attachment = Input::file('attachment');

		if ($input['type'] == 'marketing')
			$attachment_validation = Validator::make(array('attachment' => $file_attachment), Attachment::$attachment_marketing_rules);
		else
			$attachment_validation = Validator::make(array('attachment' => $file_attachment), Attachment::$attachment_rules);

		if ($attachment_validation->passes())
		{
			$attachment_path = UploadService::upload(Input::file('attachment'), storage_path() . '/attachments');
			if (empty($attachment_path) or $attachment_path == 'error')
				throw new Exception('Error uploading attachment');

			$input['path'] = $attachment_path;
			unset($input['attachment']);

			$validation = Validator::make($input, Attachment::$rules);

			if ($validation->passes())
			{
				$this->attachment->create($input);

				return Redirect::route('backend.attachments.index');
			}
		}

		return Redirect::route('backend.attachments.create')
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
		$attachment = $this->attachment->findOrFail($id);

		return View::make('backend.attachments.show', compact('attachment'));
	}

	/**
	 * Show the form for editing the specified resource.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function edit($id)
	{
		$types = array('message'=>'Message', 'marketing'=>'Marketing');
		$attachment = $this->attachment->find($id);

		if (is_null($attachment))
		{
			return Redirect::route('backend.attachments.index');
		}

		return View::make('backend.attachments.edit', compact('attachment', 'types'));
	}

	/**
	 * Update the specified resource in storage.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function update($id)
	{
		$input = array_except(Input::all(), '_method');
		$file_attachment = Input::file('attachment');
		$attachment = $this->attachment->find($id);
		
		if ($file_attachment)
		{
			if ($input['type'] == 'marketing')
				$attachment_validation = Validator::make(array('attachment' => $file_attachment), Attachment::$attachment_marketing_rules);
			else
				$attachment_validation = Validator::make(array('attachment' => $file_attachment), Attachment::$attachment_rules);

			if ($attachment_validation->passes())
			{
				$attachment_path = UploadService::upload(Input::file('attachment'), storage_path() . '/attachments');
				if (empty($attachment_path) or $attachment_path == 'error')
					throw new Exception('Error uploading attachment');
				
				$input['path'] = $attachment_path;
				unset($input['attachment']);
			}
		}
		else
		{
			$input['path'] = ($attachment) ? $attachment->path : '';
			unset($input['attachment']);
		}
		
		$validation = Validator::make($input, Attachment::$rules);

		if ($validation->passes())
		{
			$attachment->update($input);

			return Redirect::route('backend.attachments.index');
		}

		return Redirect::route('backend.attachments.edit', $id)
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
		$this->attachment->find($id)->delete();

		return Redirect::route('backend.attachments.index');
	}

}