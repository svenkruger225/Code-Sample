<?php namespace App\Facades;

use Illuminate\Support\Facades\Facade;

class UploadFacade extends Facade {
	
	protected static function getFacadeAccessor()
	{
		return new \App\Services\UploadService;
	}
	
}

