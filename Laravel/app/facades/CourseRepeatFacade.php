<?php namespace App\Facades;

use Illuminate\Support\Facades\Facade;

class CourseRepeatFacade extends Facade {
	
	protected static function getFacadeAccessor()
	{
		return new \App\Services\CourseRepeatService;
	}
	
}

