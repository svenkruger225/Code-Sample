<?php namespace App\Facades;

use Illuminate\Support\Facades\Facade;

class CalendarFacade extends Facade {
	
	protected static function getFacadeAccessor()
	{
		return new \App\Services\CalendarService;
	}
	
}

