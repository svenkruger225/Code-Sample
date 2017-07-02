<?php

class AuthController extends BaseController {

	public function getSignin()
	{
		// Is the user logged in?
		if (Sentry::check())
		{
			if (Sentry::getUser()->hasAccess('admin') )
			{
				return Redirect::route('backend');
			}
                        if (Sentry::getUser()->hasAccess('agent') )
			{
				return Redirect::route('backend.booking.newBooking');
			}
			if (Sentry::getUser()->hasAccess('student') )
			{
				return Redirect::route('online');
			}
			return Redirect::route('account');
		}

		// Show the page
		return View::make('backend.auth.signin');
	}

	public function postSignin()
	{
		// Declare the rules for the form validation
	//	$rules = array(
	//		'username'    => 'required',
	//		'password' => 'required|between:3,32',
	//	);

////		// Create a new validator instance from our validation rules
	//	$validator = Validator::make(Input::all(), $rules);

////		// If validation fails, we'll exit the operation now.
	//	if ($validator->fails())
	//	{
	//		// Ooops.. something went wrong
	//		return Redirect::back()->withInput()->withErrors($validator);
	//	}

		try
		{
			$login = Input::get('login');
			
			$online = Input::get('online', null);
			if ($online) {
				Session::put('loginRedirect', '/online');
			}
			
			//$loginAttribute = filter_var($login, FILTER_VALIDATE_EMAIL) ? 'email' : 'username';
			$loginAttribute = 'username';
			
			Sentry::getUserProvider()->getEmptyUser()->setLoginAttributeName($loginAttribute);
			
			$credentials = array($loginAttribute => $login,	'password' => Input::get('password'));
		
			Sentry::authenticate($credentials, Input::get('remember-me', 0));
			// Try to log the user in
                        
			// Get the page we were before
                        $redirect = Session::get('loginRedirect', 'backend');
			//if (Sentry::getUser()->hasAccess('student') )
			//{
			//	$redirect = 'online';
			//}

			// Unset the page we were before from the session
			Session::forget('loginRedirect');

			// Redirect to the users page
			return Redirect::to($redirect)->with('success', Lang::get('auth/message.signin.success'));
		}
		catch (Cartalyst\Sentry\Users\UserNotFoundException $e)
		{
			$this->messageBag->add($loginAttribute, Lang::get('auth/message.account_not_found'));
		}
		catch (Cartalyst\Sentry\Users\UserNotActivatedException $e)
		{
			$this->messageBag->add($loginAttribute, Lang::get('auth/message.account_not_activated'));
		}
		catch (Cartalyst\Sentry\Throttling\UserSuspendedException $e)
		{
			$this->messageBag->add($loginAttribute, Lang::get('auth/message.account_suspended'));
		}
		catch (Cartalyst\Sentry\Throttling\UserBannedException $e)
		{
			$this->messageBag->add($loginAttribute, Lang::get('auth/message.account_banned'));
		}

		// Ooops.. something went wrong
		return Redirect::back()->withInput()->withErrors($this->messageBag);
	}
	
	public function postStudentSignIn()
	{
		try 
		{
			$login = Input::get('login');
			
			//$loginAttribute = filter_var($login, FILTER_VALIDATE_EMAIL) ? 'email' : 'username';
			$loginAttribute = 'username';
			
			Sentry::getUserProvider()->getEmptyUser()->setLoginAttributeName($loginAttribute);
			
			$credentials = array($loginAttribute => $login,	'password' => Input::get('password'));
			
			Sentry::authenticate($credentials, Input::get('remember-me', 0));
			// Try to log the user in

			// Unset the page we were before from the session
			Session::forget('loginRedirect');

			$user = Sentry::getUser();
			$has_roster = $user && $user->customer && $user->customer->onlinerosters->count() > 0 ? true : false;
                        
                        if ($user && $user->hasAnyAccess(array('agent')))
			{
				//\Log::info( 'User not student');
				$data = array('type' => 'SignIn', 'Message' => 'User signed in as agent');
				return Response::json($data, 403);
			}
			if ($user && !$user->hasAnyAccess(array('student')))
			{
				\Log::info( 'User not student');
				$data = array('type' => 'SignIn', 'Message' => 'User signed in but has no roster');
				return Response::json($data, 403);
			}

			if ( !$has_roster )
			{
				\Log::info( 'User does not have roster online');
				$data = array('type' => 'SignIn', 'Message' => 'User signed in but has no roster');
				return Response::json($data, 403);
			}

			// Redirect to the users page
			return Response::json(array(
				'success' => true, 
				'login' => array(
							'id' => $user->id,
							'name' => $user->customer->name,
							'login' => $login,
							'password' => '',
							'_token' => csrf_token(),
							'online' => 1,
							'lifetime' => \Config::get('session.lifetime') * 60,
							'last_used' => \Config::get('session.lifetime') * 60
					),
				'Message' => Lang::get('auth/message.signin.success')	));
		}
		catch(Exception $e)
		{
			Log::error($e);
			return Response::json(array(
				'success' => false,
				'Message' => $e->getMessage()
				), 500);
		}
	}

	public function getSignup()
	{
		// Is the user logged in?
		if (Sentry::check())
		{
			return Redirect::route('account');
		}

		// Show the page
		return View::make('backend.auth.signup');
	}

	public function postSignup()
	{
		// Declare the rules for the form validation
		$rules = array(
			'username'       => 'required|min:3|unique:users',
			'first_name'       => 'required|min:3',
			'last_name'        => 'required|min:3',
			'email'            => 'required|email|unique:users',
			'email_confirm'    => 'required|email|same:email',
			'password'         => 'required|between:3,32',
			'password_confirm' => 'required|same:password',
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
			// Register the user
			$user = Sentry::register(array(
				'username' => Input::get('username'),
				'first_name' => Input::get('first_name'),
				'last_name'  => Input::get('last_name'),
				'email'      => Input::get('email'),
				'password'   => Input::get('password'),
			));

			// Data to be used on the email view
			$data = array(
				'user'          => $user,
				'activationUrl' => URL::route('activate', $user->getActivationCode()),
			);

			// Send the activation code through email
			Mail::send('emails.register-activate', $data, function($m) use ($user)
			{
				$m->to($user->email, $user->first_name . ' ' . $user->last_name);
				$m->subject('Welcome ' . $user->first_name);
			});

			// Redirect to the register page
			return Redirect::back()->with('success', Lang::get('auth/message.signup.success'));
		}
		catch (Cartalyst\Sentry\Users\UserExistsException $e)
		{
			$this->messageBag->add('email', Lang::get('auth/message.account_already_exists'));
		}

		// Ooops.. something went wrong
		return Redirect::back()->withInput()->withErrors($this->messageBag);
	}

	public function getActivate($activationCode = null)
	{
		// Is the user logged in?
		if (Sentry::check())
		{
			return Redirect::route('account');
		}

		try
		{
			// Get the user we are trying to activate
			$user = Sentry::getUserProvider()->findByActivationCode($activationCode);

			// Try to activate this user account
			if ($user->attemptActivation($activationCode))
			{
				// Redirect to the login page
				return Redirect::route('signin')->with('success', Lang::get('auth/message.activate.success'));
			}

			// The activation failed.
			$error = Lang::get('auth/message.activate.error');
		}
		catch (Cartalyst\Sentry\Users\UserNotFoundException $e)
		{
			$error = Lang::get('auth/message.activate.error');
		}

		// Ooops.. something went wrong
		return Redirect::route('signin')->with('error', $error);
	}

	public function getForgotPassword()
	{
		// Show the page
		return View::make('backend.auth.forgot-password');
	}

	public function postForgotPassword()
	{
		// Declare the rules for the validator
		$rules = array(
			'email' => 'required|email',
		);

		// Create a new validator instance from our dynamic rules
		$validator = Validator::make(Input::all(), $rules);

		// If validation fails, we'll exit the operation now.
		if ($validator->fails())
		{
			// Ooops.. something went wrong
			return Redirect::route('forgot-password')->withInput()->withErrors($validator);
		}

		try
		{
			Sentry::getUserProvider()->getEmptyUser()->setLoginAttributeName('email');

			// Get the user password recovery code
			$user = Sentry::getUserProvider()->findByLogin(Input::get('email'));

			// Data to be used on the email view
			$data = array(
				'user'              => $user,
				'forgotPasswordUrl' => URL::route('forgot-password-confirm', $user->getResetPasswordCode()),
			);

			// Send the activation code through email
			Mail::send('emails.forgot-password', $data, function($m) use ($user)
			{
				$m->to($user->email, $user->first_name . ' ' . $user->last_name);
				$m->subject('Account Password Recovery');
			});
		}
		catch (Cartalyst\Sentry\Users\UserNotFoundException $e)
		{
			// Even though the email was not found, we will pretend
			// we have sent the password reset code through email,
			// this is a security measure against hackers.
		}

		//  Redirect to the forgot password
		return Redirect::route('forgot-password')->with('success', Lang::get('auth/message.forgot-password.success'));
	}

	public function getForgotPasswordConfirm($passwordResetCode = null)
	{
		try
		{
			// Find the user using the password reset code
			$user = Sentry::getUserProvider()->findByResetPasswordCode($passwordResetCode);
		}
		catch(Cartalyst\Sentry\Users\UserNotFoundException $e)
		{
			// Redirect to the forgot password page
			return Redirect::route('forgot-password')->with('error', Lang::get('auth/message.account_not_found'));
		}

		// Show the page
		return View::make('backend.auth.forgot-password-confirm');
	}

	public function postForgotPasswordConfirm($passwordResetCode = null)
	{
		// Declare the rules for the form validation
		$rules = array(
			'password'         => 'required',
			'password_confirm' => 'required|same:password'
		);

		// Create a new validator instance from our dynamic rules
		$validator = Validator::make(Input::all(), $rules);

		// If validation fails, we'll exit the operation now.
		if ($validator->fails())
		{
			// Ooops.. something went wrong
			return Redirect::route('forgot-password-confirm', $passwordResetCode)->withInput()->withErrors($validator);
		}

		try
		{
			// Find the user using the password reset code
			$user = Sentry::getUserProvider()->findByResetPasswordCode($passwordResetCode);

			// Attempt to reset the user password
			if ($user->attemptResetPassword($passwordResetCode, Input::get('password')))
			{
				// Password successfully reseted
				return Redirect::route('signin')->with('success', Lang::get('auth/message.forgot-password-confirm.success'));
			}
			else
			{
				// Ooops.. something went wrong
				return Redirect::route('signin')->with('error', Lang::get('auth/message.forgot-password-confirm.error'));
			}
		}
		catch (Cartalyst\Sentry\Users\UserNotFoundException $e)
		{
			// Redirect to the forgot password page
			return Redirect::route('forgot-password')->with('error', Lang::get('auth/message.account_not_found'));
		}
	}

	public function getLogout()
	{
		// Log the user out
		Sentry::logout();

		// Redirect to the users page
		return Redirect::route('home')->with('success', 'You have successfully logged out!');
	}
	
	public function getOnlineLogout()
	{
			// Log the user out
			Sentry::logout();
		// Redirect to the users page
			return Redirect::route('online')->with('success', 'You have successfully logged out!');
	}
		
	public function getStudentLogout()
	{
		try 
		{
			// Log the user out
			Sentry::logout();
			return Response::json(array('success' => true,'Message' => 'Successfuly logout'	));
		}
		catch(Exception $e)
		{
			Log::error($e);
			return Response::json(array(
				'success' => false,
				'Message' => $e->getMessage()
				), 500);
		}

	}

}
