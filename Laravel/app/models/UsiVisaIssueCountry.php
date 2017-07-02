<?php

class UsiVisaIssueCountry extends Eloquent {
	
	protected $table = 'usi_visa_issue_countries';

	protected $guarded = array();

	public static $rules = array(
		'name' => 'required'
		);

	
}