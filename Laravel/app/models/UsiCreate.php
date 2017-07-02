<?php

class UsiCreate extends Eloquent {
	
	protected $table = 'usi_create';
	
	protected $guarded = array();

	public static $rules = array(
		);

	public function stateObj()
	{
		return $this->hasOne('AvetmissState', 'id', 'state');
	}

	public function countryBirth()
	{
		return $this->hasOne('AvetmissCountry', 'id', 'country_of_birth');
	}

	public function countryResidence()
	{
		return $this->hasOne('AvetmissCountry', 'id', 'country_of_residence');
	}

	public function document()
	{
		return $this->hasOne('UsiDocument', 'usi_create_id');
	}

	public function customer()
	{
		return $this->belongsTo('Customer', 'customer_id');
	}


}