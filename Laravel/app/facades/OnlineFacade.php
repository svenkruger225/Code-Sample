<?php namespace App\Facades;

use Illuminate\Support\Facades\Facade;

class OnlineFacade extends Facade {
	
	protected static function getFacadeAccessor()
	{
		return new \App\Services\OnlineService;
	}
	
}

