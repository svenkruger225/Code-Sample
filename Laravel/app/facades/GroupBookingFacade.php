<?php namespace App\Facades;

use Illuminate\Support\Facades\Facade;

class GroupBookingFacade extends Facade {
	
	protected static function getFacadeAccessor()
	{
		return new \App\Services\GroupBookingService;
	}
	
}

