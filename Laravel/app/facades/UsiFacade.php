<?php namespace App\Facades;

use Illuminate\Support\Facades\Facade;

class UsiFacade extends Facade {
	
	protected static function getFacadeAccessor()
	{
		return new \App\Services\UsiService;
	}
	
}

