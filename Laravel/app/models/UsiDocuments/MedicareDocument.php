<?php namespace App\Models\UsiDocuments;


class MedicareDocument {
	public $DvsDocumentType;
	public $NameLine1;
	public $NameLine2;
	public $NameLine3;
	public $NameLine4;
	public $CardColour;
	public $ExpiryDate;
	public $IndividualRefNumber;
	public $MedicareCardNumber;         

	public function __construct($data)
	{
		$this->DvsDocumentType = $data['DvsDocumentType'];
		$this->NameLine1 = $data['NameLine1'];
		$this->NameLine2 = $data['NameLine2'];
		$this->NameLine3 = $data['NameLine3'];
		$this->NameLine4 = $data['NameLine4'];
		$this->CardColour = $data['CardColour'];
		$this->ExpiryYear = $data['ExpiryYear'];
		$this->ExpiryMonth = $data['ExpiryMonth'];
		$this->ExpiryDay = $data['ExpiryDay'];
		$this->IndividualRefNumber = $data['IndividualRefNumber'];
		$this->MedicareCardNumber = $data['MedicareCardNumber'];         
	}
	
	public function getDvsDocument()
	{
		$expiry = new \DateTime($this->ExpiryYear . '-' . $this->ExpiryMonth . '-' . $this->ExpiryDay);
	
		return array(
			'$type' => 'usi.coffeeschool.com.au.USIServiceReference.MedicareDocumentType, usi.coffeeschool.com.au',
			'nameLine1Field' => $this->NameLine1,
			'nameLine2Field' => $this->NameLine2,
			'nameLine3Field' => $this->NameLine3,
			'nameLine4Field' => $this->NameLine4,
			'cardColourField' => $this->CardColour,
			'expiryDateField' => $this->CardColour == 'Green' ? $expiry->format('Y-m') : $expiry->format('Y-m-d'),
			'individualRefNumberField' => $this->IndividualRefNumber,
			'medicareCardNumberField' => $this->MedicareCardNumber
		);
	}
}
