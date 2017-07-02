<?php namespace App\Facades;

use Illuminate\Support\Facades\Facade;

class SearchFacade extends Facade {
	
	protected static function getFacadeAccessor()
	{
		return new \App\Services\SearchService;
	}
	
}

