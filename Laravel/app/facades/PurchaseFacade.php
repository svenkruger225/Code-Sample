<?php namespace App\Facades;

use Illuminate\Support\Facades\Facade;

class PurchaseFacade extends Facade {
	
	protected static function getFacadeAccessor()
	{
		return new \App\Services\PurchaseService;
	}
	
}

