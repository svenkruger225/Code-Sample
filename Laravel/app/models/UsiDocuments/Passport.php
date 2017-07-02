<?php namespace App\Models\UsiDocuments;


class Passport {
	public $DvsDocumentType;
	public $DocumentNumber;

	public function __construct($data)
	{
		$this->DvsDocumentType = $data['DvsDocumentType'];
		$this->DocumentNumber = $data['DocumentNumber'];
	}
	
	public function getDvsDocument()
	{
		return array(
			'$type' => 'usi.coffeeschool.com.au.USIServiceReference.PassportDocumentType, usi.coffeeschool.com.au',
			'documentNumberField' => $this->DocumentNumber
			);
	}
}
