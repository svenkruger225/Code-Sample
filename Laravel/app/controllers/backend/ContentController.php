<?php namespace Controllers\Backend;

use AdminController;
use Cartalyst\Sentry\Users\LoginRequiredException;
use Cartalyst\Sentry\Users\PasswordRequiredException;
use Cartalyst\Sentry\Users\UserExistsException;
use Cartalyst\Sentry\Users\UserNotFoundException;
use Config, Input, Lang, Redirect, Sentry, Validator, View, Response;
use CmsPage, CmsContent, DB;

class ContentController extends AdminController {

	protected $cms;
	protected $sections;

	public function __construct(CmsPage $cms)
	{
		parent::__construct();
		$this->cms = $cms;
		$this->sections = array();
	}
	
	public function content($id) {
		$content = CmsContent::where('cms_page_id', $id)->first();
		if (is_null($content))
			return $this->create($id);
		else
			return $this->edit($content->id);
			
	}


	/**
	 * Show the form for creating a new resource.
	 *
	 * @return Response
	 */
	public function create($id = null)
	{
		$page = CmsPage::find($id);
		$page->view_name = trim($page->view_name);
		if( empty($page->view_name))
			$page->view_name = 'template one';
	
		$view = Config::get('cms.'. $page->view_name, array());
		
		if (is_null($page))
		{
			return Redirect::route('backend.cms.index');
		}

		return View::make($view['create'], compact('page'));
	}

	public function store()
	{
		$input = Input::all();
		$validation = Validator::make($input, CmsContent::$rules);

		$input['content'] =  !empty($input['content']) ? $input['content'] . '<!-- split -->' : '';
		//$input['content'] .=  isset($input['content_col1']) ? $input['content_col1'] . '<!-- split -->' : '';
		//$input['content'] .=  isset($input['content_col2']) ? $input['content_col2'] . '<!-- split -->' : '';
		//$input['content'] .=  isset($input['blocks']) ? $input['blocks'] . '<!-- split -->' : '';
		$input['content'] .=  !empty($input['blocks']) ? $input['blocks'] : '';
		
		unset($input['type']);
		//unset($input['content_col1']);
		//unset($input['content_col2']);
		unset($input['blocks']);

		if ($validation->passes())
		{
			$user = Sentry::getUser();
			$input['active'] = 1;
			$input['version'] = 1;
			$input['user_id'] = $user->id;
			
			CmsContent::create($input);

			return Redirect::route('backend.cms.index');
		}

		return Redirect::route('backend.cms.create')
		->withInput()
		->withErrors($validation)
		->with('message', 'There were validation errors.');
	}

	public function edit($id)
	{
		$content = CmsContent::find($id);
		$page = CmsPage::find($content->cms_page_id);
		$page->view_name = trim($page->view_name);
		if( empty($page->view_name))
			$page->view_name = 'template one';

		$view = Config::get('cms.'. $page->view_name , array());
		
		$this->splitContentToSections($page->view_name, $content->content);
		
		foreach ($this->sections as $key=>$value)
		{
			$content->$key = $value;
		}
		
		
		if (is_null($page))
		{
			return Redirect::route('backend.cms.index');
		}

		return View::make($view['edit'], compact('page', 'content'));
	}	
	
	private function splitContentToSections($template, $content)
	{
		$parts = explode('<!-- split -->', $content);
		//if ($template == 'template one' )
		//{
			$this->sections['content'] = isset($parts[0]) ? $parts[0] : '';
			$this->sections['blocks'] = isset($parts[1]) ? $parts[1] : '';	
		//}
	}

	public function update($id)
	{
		$input = array_except(Input::all(), '_method');
		$validation = Validator::make($input, CmsContent::$rules);
		$input['content'] =  isset($input['content']) ? $input['content'] . '<!-- split -->' : '';
		//$input['content'] .=  isset($input['content_col1']) ? $input['content_col1'] . '<!-- split -->' : '';
		//$input['content'] .=  isset($input['content_col2']) ? $input['content_col2'] . '<!-- split -->' : '';
		//$input['content'] .=  isset($input['blocks']) ? $input['blocks'] . '<!-- split -->' : '';
		$input['content'] .=  isset($input['blocks']) ? $input['blocks'] : '';
		
		unset($input['type']);
		//unset($input['content_col1']);
		//unset($input['content_col2']);
		unset($input['blocks']);


		if ($validation->passes())
		{
			$cms = CmsContent::find($id);
			$cms->update($input);

			return Redirect::route('backend.cms.index');
		}

		return Redirect::route('backend.cms.edit', $id)
		->withInput()
		->withErrors($validation)
		->with('message', 'There were validation errors.');
	}


}

//
//		<div class="span99">
//			<div class="bs-docs-example">
//				<!-- carousel -->			
//				{{$sections['top']}}
//			</div>
//			<div class="row-fluid"></div><!--/row-->
//		</div><!--/span-->
//		{{$sections['bottom']}}