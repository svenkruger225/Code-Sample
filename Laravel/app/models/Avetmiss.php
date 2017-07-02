<?php

class Avetmiss {
	public $type;
	public $record;
	//public $nat00010;
	//public $nat00020;
	//public $nat00060;
	//public $nat00080;
	//public $nat00090;
	//public $nat00100;
	//public $nat00120;
	
	function __construct($args) {
		$this->type = 'single';
		$method = 'Get' . $args[0];
		$this->record = $this->$method($args);
	}
	
	private function GetNAT00010($args) {
		return array(
			'id'			=> str_pad($args[1], 10),
			'name'			=> str_pad($args[2], 100),
			'type_id'		=> str_pad($args[3], 2),
			'address1'		=> str_pad($args[4], 50),
			'address2'		=> str_pad($args[5], 50),
			'location'		=> str_pad($args[6], 50),
			'post_code'		=> str_pad($args[7], 4), 
			'state_code'	=> str_pad($args[8], 2) 
		);
	}
	
	private function GetNAT00020($args) {
		return array(
			'Training organisation identifier'						=> str_pad($args[1], 10),
			'Training organisation delivery location identifier'	=> str_pad($args[2], 10),
			'Training organisation delivery location name'			=> str_pad($args[3], 100),
			'post_code'												=> str_pad($args[4], 4),
			'state identifier'										=> str_pad($args[5], 2),
			'Address location – suburb, locality or town'			=> str_pad($args[6], 50),
			'country identifier'									=> str_pad($args[7], 4) 
			);
	}	

	private function GetNAT00060($args) {
		return array(
			'subject_flag'					=> str_pad($args[1], 1),
			'subject_identifier'			=> str_pad($args[2], 12),
			'subject name'					=> str_pad($args[3], 100),
			'subject field_edu_identifier'	=> str_pad($args[4], 6),
			'vet_flag'						=> str_pad($args[5], 1),
			'nominal_hours'					=> str_pad($args[6], 4, "0", STR_PAD_LEFT) 
			);
	}


	private function GetNAT00080($args) {
		return array(
			'customer_id'									=> str_pad($args[1],10),
			'Name for encryption'							=> str_pad($args[2],60),
			'Highest school level completed'				=> str_pad($args[3],2),
			'Year highest school level completed'			=> str_pad($args[4],4),
			'Sex'											=> str_pad($args[5],1),
			'Date of birth'									=> str_pad($args[6],8),
			'Postcode'										=> str_pad($args[7],4),
			'Indigenous status identifier'					=> str_pad($args[8],1),
			'Language identifier'							=> str_pad($args[9],4),
			'Labour force status identifier'				=> str_pad($args[10],2),
			'Country identifier'							=> str_pad($args[11],4),
			'Disability flag'								=> str_pad($args[12], 1),
			'Prior educational),chievement flag'			=> str_pad($args[13],1),
			'At school flag'								=> str_pad($args[14],1),
			'Proficiency in spoken English identifier'		=> str_pad($args[15],1),
			'Address location – suburb, locality or town'	=> str_pad($args[16],50),
			'Unique student identifier'						=> str_pad($args[17],10),
			'State identifier'								=> str_pad($args[18],2),
			'Address building/property name'				=> str_pad($args[19],50),
			'Address flat/unit details'						=> str_pad($args[20],30),
			'Address street number'							=> str_pad($args[21],15),
			'Address street name'							=> str_pad($args[22],70)	
			);
	}

	private function GetNAT00090($args) {
		$disabilities = array();
		$arr = json_decode($args[2], true);
		if (count($arr) > 0)
		{
			foreach ($arr as $disability) 
			{   
				if (strpos( $disability, 'other__' ) === false ) 
				{
					$this->type = 'multiple';
					array_push($disabilities, array(
						'customer_id'					=> str_pad($args[1],10),
						'Disability type identifier'	=> str_pad($disability, 2),
						));
				}
			}
		}
		
		
		return $disabilities;
	}

	private function GetNAT00100($args) {
		$achievements = array();
		$arr = json_decode($args[2], true);
		if (count($arr) > 0)
		{
			$this->type = 'multiple';
			foreach ($arr as $achievement) 
			{   
				array_push($achievements, array(
					'customer_id'					=> str_pad($args[1],10),
					'Prior educational achievement identifier'	=> str_pad($achievement, 2),
					));
			}
		}
		
		
		return $achievements;
	}

	private function GetNAT00120($args) {
		return array(
			'Training organisation delivery location identifier'	=> str_pad($args[1],10),
			'customer_id'											=> str_pad($args[2],10),
			'Subject identifier'									=> str_pad($args[3],12),
			'Program identifier'									=> str_pad($args[4],10),
			'Activity start date'									=> str_pad($args[5],8),
			'Activity end date'										=> str_pad($args[6],8),
			'Delivery mode identifier'								=> str_pad($args[7],2),
			'Outcome identifier – national'							=> str_pad($args[8],2),
			'Scheduled hours'										=> str_pad($args[9],4, "0", STR_PAD_LEFT),
			'Funding source – national'								=> str_pad($args[10],2),
			'Commencing program identifier'							=> str_pad($args[11],1),
			'Training contract identifier'							=> str_pad($args[12],10),
			'Client identifier – apprenticeships'					=> str_pad($args[13],10),
			'Study reason identifier'								=> str_pad($args[14],2),
			'VET in schools flag'									=> str_pad($args[15],1),
			'Specific funding identifier'							=> str_pad($args[16],10)

			);
	}

}
