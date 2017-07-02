<?php namespace App\Facades;

use Illuminate\Support\Facades\Facade;

class BookingFacade extends Facade {
	
	protected static function getFacadeAccessor()
	{
		return new \App\Services\BookingService;
	}
	
}

