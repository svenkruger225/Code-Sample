<?php namespace App\Facades;

use Illuminate\Support\Facades\Facade;

class ReportsFacade extends Facade {
	
	protected static function getFacadeAccessor()
	{
		return new \App\Services\ReportsService;
	}
	
}

