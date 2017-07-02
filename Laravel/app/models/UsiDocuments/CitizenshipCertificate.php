<?php namespace App\Models\UsiDocuments;


class CitizenshipCertificate {
	public $DvsDocumentType;
	public $AcquisitionDate;
	public $StockNumber;

	public function __construct($data)
	{
		$this->DvsDocumentType = $data['DvsDocumentType'];
		$this->AcquisitionDate = $data['AcquisitionDate'];
		$this->StockNumber = $data['StockNumber'];
	}
	
	public function getDvsDocument()
	{
		return array(
			'$type' => 'usi.coffeeschool.com.au.USIServiceReference.CitizenshipCertificateDocumentType, usi.coffeeschool.com.au',
			'acquisitionDateField' => $this->AcquisitionDate,
			'stockNumberField' => $this->StockNumber
			);
	}
}
