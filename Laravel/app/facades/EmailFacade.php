<?php namespace App\Facades;

use Illuminate\Support\Facades\Facade;

class EmailFacade extends Facade {
	
	protected static function getFacadeAccessor()
	{
		return new \App\Services\EmailService;
	}
	
}

