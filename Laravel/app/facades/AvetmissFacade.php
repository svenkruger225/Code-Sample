<?php namespace App\Facades;

use Illuminate\Support\Facades\Facade;

class AvetmissFacade extends Facade {
	
	protected static function getFacadeAccessor()
	{
		return new \App\Services\AvetmissService;
	}
	
}

