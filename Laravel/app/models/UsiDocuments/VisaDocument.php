<?php namespace App\Models\UsiDocuments;


class VisaDocument {
	public $DvsDocumentType;
	public $CountryOfIssue;
	public $PassportNumber;
	
	public function __construct($data)
	{
		$this->DvsDocumentType = $data['DvsDocumentType'];
		$this->CountryOfIssue = $data['CountryOfIssue'];
		$this->PassportNumber = $data['PassportNumber'];
	}
	
	public function getDvsDocument()
	{
		$country = \UsiVisaIssueCountry::find($this->CountryOfIssue);
		return array(
			'$type' => 'usi.coffeeschool.com.au.USIServiceReference.VisaDocumentType, usi.coffeeschool.com.au',
			'countryOfIssueField' =>  $country->name,
			'passportNumberField' =>  $this->PassportNumber
		);
	}
}
