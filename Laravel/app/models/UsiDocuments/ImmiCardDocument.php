<?php namespace App\Models\UsiDocuments;


class ImmiCardDocument {
	public $DvsDocumentType;
	public $ImmiCardNumberr;

	public function __construct($data)
	{
		$this->DvsDocumentType = $data['DvsDocumentType'];
		$this->ImmiCardNumber = $data['ImmiCardNumber'];
	}
	
	public function getDvsDocument()
	{
		return array(
			'$type' => 'usi.coffeeschool.com.au.USIServiceReference.ImmiCardDocumentType, usi.coffeeschool.com.au',
			'immiCardNumberField' => $this->ImmiCardNumber
			);
	}
}
