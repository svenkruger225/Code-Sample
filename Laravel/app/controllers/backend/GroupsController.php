<?php namespace Controllers\Backend;

use AdminController;
use Cartalyst\Sentry\Groups\GroupExistsException;
use Cartalyst\Sentry\Groups\GroupNotFoundException;
use Cartalyst\Sentry\Groups\NameRequiredException;
use Config;
use Input;
use Lang;
use Redirect;
use Sentry;
use Validator;
use View;

class GroupsController extends AdminController {

	/**
	 * Display a listing of the resource.
	 *
	 * @return Response
	 */
	public function index()
	{
		// Grab all the groups
		$groups = Sentry::getGroupProvider()->createModel()->paginate();

		// Show the page
		return View::make('backend/groups/index', compact('groups'));
	}

	/**
	 * Show the form for creating a new resource.
	 *
	 * @return Response
	 */
	public function create()
	{
		// Get all the available permissions
		$permissions = Config::get('permissions');
		$this->encodeAllPermissions($permissions, true);

		// Selected permissions
		$selectedPermissions = Input::old('permissions', array());

		// Show the page
		return View::make('backend/groups/create', compact('permissions', 'selectedPermissions'));
	}

	/**
	 * Store a newly created resource in storage.
	 *
	 * @return Response
	 */
	public function store()
	{
		// Declare the rules for the form validation
		$rules = array(
			'name' => 'required',
			);

		// Create a new validator instance from our validation rules
		$validator = Validator::make(Input::all(), $rules);

		// If validation fails, we'll exit the operation now.
		if ($validator->fails())
		{
			// Ooops.. something went wrong
			return Redirect::back()->withInput()->withErrors($validator);
		}

		try
		{
			// We need to reverse the UI specific logic for our
			// permissions here before we create the user.
			$permissions = Input::get('permissions', array());
			$this->decodePermissions($permissions);
			app('request')->request->set('permissions', $permissions);

			// Get the inputs, with some exceptions
			$inputs = Input::except('_token');

			// Was the group created?
			if ($group = Sentry::getGroupProvider()->create($inputs))
			{
				// Redirect to the new group page
				return Redirect::route('backend.groups.edit', $group->id)->with('success', Lang::get('backend/groups/message.success.create'));
			}

			// Redirect to the new group page
			return Redirect::route('backend.groups.create')->with('error', Lang::get('backend/groups/message.error.create'));
		}
		catch (NameRequiredException $e)
		{
			$error = 'group_name_required';
		}
		catch (GroupExistsException $e)
		{
			$error = 'group_exists';
		}

		// Redirect to the group create page
		return Redirect::route('backend.groups.create')->withInput()->with('error', Lang::get('backend/groups/message.'.$error));
	}

	/**
	 * Display the specified resource.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function show($id)
	{
		//
	}

	/**
	 * Show the form for editing the specified resource.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function edit($id)
	{
		try
		{
			// Get the group information
			$group = Sentry::getGroupProvider()->findById($id);

			// Get all the available permissions
			$permissions = Config::get('permissions');
			$this->encodeAllPermissions($permissions, true);

			// Get this group permissions
			$groupPermissions = $group->getPermissions();
			$this->encodePermissions($groupPermissions);
			$groupPermissions = array_merge($groupPermissions, Input::old('permissions', array()));
		}
		catch (GroupNotFoundException $e)
		{
			// Redirect to the groups management page
			return Redirect::route('groups')->with('error', Lang::get('backend/groups/message.group_not_found', compact('id')));
		}

		// Show the page
		return View::make('backend/groups/edit', compact('group', 'permissions', 'groupPermissions'));
	}

	/**
	 * Update the specified resource in storage.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function update($id)
	{
		// We need to reverse the UI specific logic for our
		// permissions here before we update the group.
		$permissions = Input::get('permissions', array());
		$this->decodePermissions($permissions);
		app('request')->request->set('permissions', $permissions);

		try
		{
			// Get the group information
			$group = Sentry::getGroupProvider()->findById($id);
		}
		catch (GroupNotFoundException $e)
		{
			// Redirect to the groups management page
			return Rediret::route('groups')->with('error', Lang::get('backend/groups/message.group_not_found', compact('id')));
		}

		// Declare the rules for the form validation
		$rules = array(
			'name' => 'required',
			);

		// Create a new validator instance from our validation rules
		$validator = Validator::make(Input::all(), $rules);

		// If validation fails, we'll exit the operation now.
		if ($validator->fails())
		{
			// Ooops.. something went wrong
			return Redirect::back()->withInput()->withErrors($validator);
		}

		try
		{
			// Update the group data
			$group->name        = Input::get('name');
			$group->permissions = Input::get('permissions');

			// Was the group updated?
			if ($group->save())
			{
				// Redirect to the group page
				return Redirect::route('backend.groups.edit', $id)->with('success', Lang::get('backend/groups/message.success.update'));
			}
			else
			{
				// Redirect to the group page
				return Redirect::route('backend.groups.edit', $id)->with('error', Lang::get('backend/groups/message.error.update'));
			}
		}
		catch (NameRequiredException $e)
		{
			$error = Lang::get('backend/group/message.group_name_required');
		}

		// Redirect to the group page
		return Redirect::route('backend.groups.edit', $id)->withInput()->with('error', $error);
	}

	/**
	 * Remove the specified resource from storage.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function destroy($id)
	{
		try
		{
			// Get group information
			$group = Sentry::getGroupProvider()->findById($id);

			// Delete the group
			$group->delete();

			// Redirect to the group management page
			return Redirect::route('groups')->with('success', Lang::get('backend/groups/message.success.delete'));
		}
		catch (GroupNotFoundException $e)
		{
			// Redirect to the group management page
			return Redirect::route('groups')->with('error', Lang::get('backend/groups/message.group_not_found', compact('id')));
		}
	}

}