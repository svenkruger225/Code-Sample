<?php namespace Controllers\Api;

use AdminController;
use Cartalyst\Sentry\Users\LoginRequiredException;
use Cartalyst\Sentry\Users\PasswordRequiredException;
use Cartalyst\Sentry\Users\UserExistsException;
use Cartalyst\Sentry\Users\UserNotFoundException;
use Config,Input,Lang,Redirect,Sentry,Validator,View;
use Attachment, UploadService, Response;

class AttachmentsController extends AdminController {

	protected $attachment;

	public function __construct(Attachment $attachment)
	{
		parent::__construct();
		$this->attachment = $attachment;
	}

	public function upload()
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
				return Response::json(array(
							'success' => false,
							'Message' => 'Error uploading file'
							), 500);

			$input['path'] = $attachment_path;
			unset($input['attachment']);

			$validation = Validator::make($input, Attachment::$rules);

			if ($validation->passes())
			{
				$attachment = $this->attachment->create($input);

				return Response::json(array('Message'=>'File uploaded', 'list' => array('key' =>$attachment->id, 'value'=>$attachment->name)));
			}
		}

		return Response::json(array(
			'success' => false,
			'Message' => "Problem uploading file"
			), 500);
	}

}