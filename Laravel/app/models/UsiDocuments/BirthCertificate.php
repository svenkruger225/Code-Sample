<?php namespace App\Models\UsiDocuments;

class BirthCertificate{
	
	public $DvsDocumentType;
	public $CertificateNumber;
	public $DatePrinted;
	public $DatePrintedSpecified;
	public $RegistrationDate;
	public $RegistrationDateSpecified;
	public $RegistrationNumber;
	public $RegistrationState;
	public $RegistrationYear;

	public function __construct($data)
	{
		$this->DvsDocumentType = $data['DvsDocumentType'];
		$this->CertificateNumber = $data['CertificateNumber'];
		$this->DatePrinted = $data['DatePrinted'];
		$this->DatePrintedSpecified = empty($data['DatePrinted']) ? false : true;
		//$this->DatePrintedSpecified = $data['DatePrintedSpecified'];
		$this->RegistrationDate = $data['RegistrationDate'];
		$this->RegistrationDateSpecified = empty($data['RegistrationDate']) ? false : true;
		//$this->RegistrationDateSpecified = $data['RegistrationDateSpecified'];
		$this->RegistrationNumber = $data['RegistrationNumber'];
		$this->RegistrationState = $data['RegistrationState'];
		$this->RegistrationYear = $data['RegistrationYear'];
	}
	
	public function getDvsDocument()
	{
		$state = \AvetmissState::find($this->RegistrationState);
		return array(
			'$type' => 'usi.coffeeschool.com.au.USIServiceReference.BirthCertificateDocumentType, usi.coffeeschool.com.au',
			'certificateNumberField' => $this->CertificateNumber,
			'datePrintedField' => $this->DatePrinted,
			'datePrintedFieldSpecified' => empty($this->DatePrinted) ? false : true,
			'registrationDateField' => $this->RegistrationDate,
			'registrationDateFieldSpecified' => empty($this->RegistrationDate) ? false : true,
			'registrationNumberField' => $this->RegistrationNumber,
			'registrationStateField' => $state ? $state->code : '',
			'registrationYearField' => $this->RegistrationYear
			);
	}
	
}
