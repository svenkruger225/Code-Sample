<?php namespace App\Facades;

use Illuminate\Support\Facades\Facade;

class SmsFacade extends Facade {
	
	protected static function getFacadeAccessor()
	{
		return new \App\Services\SmsService;
	}
	
}

