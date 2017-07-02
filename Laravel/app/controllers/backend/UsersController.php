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

class UsersController extends AdminController {

	/**
	 * Declare the rules for the form validation
	 *
	 * @var array
	 */
	protected $validationRules = array(
		'username'       => 'required|min:3|unique:users',
		'first_name'       => 'required|min:3',
		'last_name'        => 'required|min:3',
		'email'            => 'required|email|unique:users,email',
		'password'         => 'required|between:3,32',
		'password_confirm' => 'required|between:3,32|same:password',
	);

	public function __construct()
	{
		parent::__construct(); 
		//$this->beforeFilter('super-auth');
	}

	public function index()
	{

		$search = Input::get('search');
		$g_id = Input::get('g_id');
		$del = Input::get('del');

		// Grab all the users
		$users = Sentry::getUserProvider()->createModel();
		
		if ($g_id && $g_id != '')
		{
			$users = \User::findAllInGroup($g_id);
		}
		
			if(	$search && $search != '')
			$users = $users
				->where('first_name', 'like', '%' . $search . '%')
				->orWhere('last_name', 'like', '%' . $search . '%');

		// Do we want to include the deleted users?
		if ($del == 'withTrashed')
		{
			$users = $users->withTrashed();
		}
		else if ($del == 'onlyTrashed')
		{
			$users = $users->onlyTrashed();
		}

		// Paginate the users
		$users = $users->paginate(25)
			->appends(array(
					'g_id' => $g_id,
					'del' => $del,
					));

		$groups = array(''=>'Filter by Group') + \Group::lists('name', 'id');
		$delete_types = array('all'=>'only Active','withTrashed'=>'Include Deleted Users','onlyTrashed'=>'Only Deleted Users');
		
		Input::flash();
		// Show the page
		return View::make('backend/users/index', compact('groups', 'delete_types', 'users'));
	}

	/**
	 * User create.
	 *
	 * @return View
	 */
	public function create()
	{
		// Get all the available groups
		$groups = Sentry::getGroupProvider()->findAll();

		// Selected groups
		$selectedGroups = Input::old('groups', array());

		// Get all the available permissions
		$permissions = Config::get('permissions');
		$this->encodeAllPermissions($permissions);

		// Selected permissions
		$selectedPermissions = Input::old('permissions', array('superuser' => -1));
		$this->encodePermissions($selectedPermissions);

		// Show the page
		return View::make('backend/users/create', compact('groups', 'selectedGroups', 'permissions', 'selectedPermissions'));
	}

	/**
	 * User create form processing.
	 *
	 * @return Redirect
	 */
	public function store()
	{
		// Create a new validator instance from our validation rules
		$validator = Validator::make(Input::all(), $this->validationRules);

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
			$inputs = Input::except('csrf_token', 'password_confirm', 'groups');

			// Was the user created?
			if ($user = Sentry::getUserProvider()->create($inputs))
			{
				// Assign the selected groups to this user
				foreach (Input::get('groups', array()) as $groupId)
				{
					$group = Sentry::getGroupProvider()->findById($groupId);

					$user->addGroup($group);
				}

				// Prepare the success message
				$success = Lang::get('backend/users/message.success.create');

				// Redirect to the new user page
				return Redirect::route('backend.users.edit', $user->id)->with('success', $success);
			}

			// Prepare the error message
			$error = Lang::get('backend/users/message.error.create');

			// Redirect to the user creation page
			return Redirect::route('backend.users.create')->with('error', $error);
		}
		catch (LoginRequiredException $e)
		{
			$error = Lang::get('backend/users/message.user_login_required');
		}
		catch (PasswordRequiredException $e)
		{
			$error = Lang::get('backend/users/message.user_password_required');
		}
		catch (UserExistsException $e)
		{
			$error = Lang::get('backend/users/message.user_exists');
		}

		// Redirect to the user creation page
		return Redirect::route('backend.users.create')->withInput()->with('error', $error);
	}

	/**
	 * User update.
	 *
	 * @param  int  $id
	 * @return View
	 */
	public function edit($id = null)
	{
		try
		{
			// Get the user information
			$user = Sentry::getUserProvider()->findById($id);

			// Get this user groups
			$userGroups = $user->groups()->lists('name', 'group_id');

			// Get this user permissions
			$userPermissions = array_merge(Input::old('permissions', array('superuser' => -1)), $user->getPermissions());
			$this->encodePermissions($userPermissions);

			// Get a list of all the available groups
			$groups = Sentry::getGroupProvider()->findAll();

			// Get all the available permissions
			$permissions = Config::get('permissions');
			$this->encodeAllPermissions($permissions);
		}
		catch (UserNotFoundException $e)
		{
			// Prepare the error message
			$error = Lang::get('backend/users/message.user_not_found', compact('id'));

			// Redirect to the user management page
			return Redirect::route('users')->with('error', $error);
		}

		// Show the page
		return View::make('backend/users/edit', compact('user', 'groups', 'userGroups', 'permissions', 'userPermissions'));
	}

	/**
	 * User update form processing page.
	 *
	 * @param  int  $id
	 * @return Redirect
	 */
	public function update($id = null)
	{
		// We need to reverse the UI specific logic for our
		// permissions here before we update the user.
		$permissions = Input::get('permissions', array());
		$this->decodePermissions($permissions);
		app('request')->request->set('permissions', $permissions);

		try
		{
			// Get the user information
			$user = Sentry::getUserProvider()->findById($id);
		}
		catch (UserNotFoundException $e)
		{
			// Prepare the error message
			$error = Lang::get('backend/users/message.user_not_found', compact('id'));

			// Redirect to the user management page
			return Redirect::route('users')->with('error', $error);
		}

		//
		$this->validationRules['username'] = "required|min:3|unique:users,username,{$user->username},username";
		$this->validationRules['email'] = "required|email|unique:users,email,{$user->email},email";

		// Do we want to update the user password?
		if ( ! $password = Input::get('password'))
		{
			unset($this->validationRules['password']);
			unset($this->validationRules['password_confirm']);
			#$this->validationRules['password']         = 'required|between:3,32';
			#$this->validationRules['password_confirm'] = 'required|between:3,32|same:password';
		}

		// Create a new validator instance from our validation rules
		$validator = Validator::make(Input::all(), $this->validationRules);

		// If validation fails, we'll exit the operation now.
		if ($validator->fails())
		{
			// Ooops.. something went wrong
			return Redirect::back()->withInput()->withErrors($validator);
		}

		try
		{
			// Update the user
			$user->username  = Input::get('username');
			$user->first_name  = Input::get('first_name');
			$user->last_name   = Input::get('last_name');
			$user->email       = Input::get('email');
			$user->mobile       = Input::get('mobile');
			$user->activated   = Input::get('activated', $user->activated);
			$user->permissions = Input::get('permissions');

			// Do we want to update the user password?
			if ($password)
			{
				$user->password = $password;
			}

			// Get the current user groups
			$userGroups = $user->groups()->lists('group_id', 'group_id');

			// Get the selected groups
			$selectedGroups = Input::get('groups', array());

			// Groups comparison between the groups the user currently
			// have and the groups the user wish to have.
			$groupsToAdd    = array_diff($selectedGroups, $userGroups);
			$groupsToRemove = array_diff($userGroups, $selectedGroups);

			// Assign the user to groups
			foreach ($groupsToAdd as $groupId)
			{
				$group = Sentry::getGroupProvider()->findById($groupId);

				$user->addGroup($group);
			}

			// Remove the user from groups
			foreach ($groupsToRemove as $groupId)
			{
				$group = Sentry::getGroupProvider()->findById($groupId);

				$user->removeGroup($group);
			}

			// Was the user updated?
			if ($user->save())
			{
				// Prepare the success message
				$success = Lang::get('backend/users/message.success.update');

				// Redirect to the user page
				return Redirect::route('backend.users.edit', $id)->with('success', $success);
			}

			// Prepare the error message
			$error = Lang::get('backend/users/message.error.update');
		}
		catch (LoginRequiredException $e)
		{
			$error = Lang::get('backend/users/message.user_login_required');
		}

		// Redirect to the user page
		return Redirect::route('backend.users.edit', $id)->withInput()->with('error', $error);
	}

	/**
	 * Delete the given user.
	 *
	 * @param  int  $id
	 * @return Redirect
	 */
	public function destroy($id = null)
	{
		try
		{
			// Get user information
			$user = Sentry::getUserProvider()->findById($id);

			// Check if we are not trying to delete ourselves
			if ($user->id === Sentry::getId())
			{
				// Prepare the error message
				$error = Lang::get('backend/users/message.error.delete');

				// Redirect to the user management page
				return Redirect::route('users')->with('error', $error);
			}

			// Do we have permission to delete this user?
			if ($user->isSuperUser() and ! Sentry::getUser()->isSuperUser())
			{
				// Redirect to the user management page
				return Redirect::route('backend.users.index')->with('error', 'Insufficient permissions!');
			}

			// Delete the user
			$user->delete();

			// Prepare the success message
			$success = Lang::get('backend/users/message.success.delete');

			// Redirect to the user management page
			return Redirect::route('backend.users.index')->with('success', $success);
		}
		catch (UserNotFoundException $e)
		{
			// Prepare the error message
			$error = Lang::get('backend/users/message.user_not_found', compact('id' ));

			// Redirect to the user management page
			return Redirect::route('backend.users.index')->with('error', $error);
		}
	}

	/**
	 * Restore a deleted user.
	 *
	 * @param  int  $id
	 * @return Redirect
	 */
	public function restore($id = null)
	{
		try
		{
			// Get user information
			$user = Sentry::getUserProvider()->createModel()->withTrashed()->find($id);

			// Restore the user
			$user->restore();

			// Prepare the success message
			$success = Lang::get('backend/users/message.success.restored');

			// Redirect to the user management page
			return Redirect::route('backend.users.index')->with('success', $success);
		}
		catch (UserNotFoundException $e)
		{
			// Prepare the error message
			$error = Lang::get('backend/users/message.user_not_found', compact('id'));

			// Redirect to the user management page
			return Redirect::route('backend.users.index')->with('error', $error);
		}
	}

}
