<?php namespace App\Models\UsiDocuments;



class DriversLicence {
	public $DvsDocumentType;
	public $LicenceNumber;
	public $LicenceState;

	public function __construct($data)
	{
		$this->DvsDocumentType = $data['DvsDocumentType'];
		$this->LicenceNumber = $data['LicenceNumber'];
		$this->LicenceState = $data['LicenceState'];
	}
	
	public function getDvsDocument()
	{
		$state = \AvetmissState::find($this->LicenceState);
		return array(
			'$type' => 'usi.coffeeschool.com.au.USIServiceReference.DriversLicenceDocumentType, usi.coffeeschool.com.au',
			'licenceNumberField' => $this->LicenceNumber,
			'stateField' => $state ? $state->code : ''
			);
	}
}
