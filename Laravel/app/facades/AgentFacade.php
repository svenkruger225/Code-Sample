<?php namespace App\Facades;

use Illuminate\Support\Facades\Facade;

class AgentFacade extends Facade {
	
	protected static function getFacadeAccessor()
	{
		return new \App\Services\AgentService;
	}
	
}

