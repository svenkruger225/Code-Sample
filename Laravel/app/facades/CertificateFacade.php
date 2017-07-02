<?php namespace App\Facades;

use Illuminate\Support\Facades\Facade;

class CertificateFacade extends Facade {
	
	protected static function getFacadeAccessor()
	{
		return new \App\Services\CertificateService;
	}
	
}

