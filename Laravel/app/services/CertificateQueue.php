<?php namespace App\Services;

use Log, Config, Input, Lang, Redirect, Sentry, Validator, View, Response, Exception, Mail, Message;

class CertificateQueue {

	public function __construct()
	{
	}
	
	public function fire($job, $data) 
	{

		$certificate = \Certificate::find($data['certificate_id']);
		$certificate->email_sent += 1;
		$certificate->save();
		
		\EmailService::sendCertificateToStudent($certificate);
		
		$job->delete();

	}
	
}
