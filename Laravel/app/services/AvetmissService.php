<?php namespace App\Services;

use Config, Input, Lang, Redirect, Sentry, Validator, View, Response, Exception, DateTime, Log, File;
use Avetmiss, Voucher, Purchase, Roster, Order, Item, Payment, Invoice, DB, Location, CourseInstance, GroupBooking;

class AvetmissService {

	protected $input;
	protected $items = array();
	protected $transactions = array();
	protected $locations = array();
	protected $rosters = array();

	public function __construct()
	{
		$this->input = Input::all();
		if( isset($this->input['location_id']) && $this->input['location_id'] != '')
			$this->locations = DB::table('locations')
				->where('id', '=',  $this->input['location_id'])
				->orWhere('parent_id', '=',  $this->input['location_id'])
				->lists('id');
		
	}
	
	public function Export()
	{
		$result = array();
		$transactions = array();
		$totals = array();
		
		if(count($this->input) == 0)
			return $result;
		
		if (!isset($this->input['from_date']) && isset($this->input['to_date']))
			$this->input['from_date'] = date("Y-m-d", strtotime ($this->input['to_date']));
		
		if (!isset($this->input['from_date']))
			$this->input['from_date'] = '';
		else
			$this->input['from_date'] = date("Y-m-d", strtotime ($this->input['from_date']));

		if (!isset($this->input['to_date']))
			$this->input['to_date'] = '';
		else
			$this->input['to_date'] = date("Y-m-d", strtotime ($this->input['to_date']));

		if (isset($this->input['single_date']) && $this->input['single_date'] != '')
		{
			$this->input['from_date'] = date("Y-m-d", strtotime ($this->input['single_date']));
			$this->input['to_date'] = date("Y-m-d", strtotime ($this->input['single_date']));
		}

		if (empty($this->input['report_type'])) $this->input['report_type'] = 'All';
		
		$dateStart = $this->input['from_date'];
		$dateEnd = $this->input['to_date'];
		
		$path = storage_path() . '/avetmiss/';

		if ($this->input['report_type'] == 'All' || $this->input['report_type'] == 'NAT00010')
		{
			$content =  $this->CreateNAT00010();
			$filename = $path . '/' . date('Y-m-d') . '/nat00010.txt';
			if (File::exists($path . '/' . date('Y-m-d')) || File::makeDirectory($path . '/' . date('Y-m-d'), 0777, true))
				File::put($filename , $content);
		}
		
		if ($this->input['report_type'] == 'All' || $this->input['report_type'] == 'NAT00020')
		{
			$content = $this->CreateNAT00020();
			$filename = $path . '/' . date('Y-m-d') . '/nat00020.txt';
			if (File::exists($path . '/' . date('Y-m-d')) || File::makeDirectory($path . '/' . date('Y-m-d'), 0777, true))
				File::put($filename , $content);
		}
		
		if ($this->input['report_type'] == 'All' || $this->input['report_type'] == 'NAT00060')
		{
			$content = $this->CreateNAT00060();
			$filename = $path . '/' . date('Y-m-d') . '/nat00060.txt';
			if (File::exists($path . '/' . date('Y-m-d')) || File::makeDirectory($path . '/' . date('Y-m-d'), 0777, true))
				File::put($filename , $content);
		}
		
		if ($this->input['report_type'] == 'All' || $this->input['report_type'] == 'NAT00080')
		{
			$content = $this->CreateNAT00080();
			$filename = $path . '/' . date('Y-m-d') . '/nat00080.txt';
			if (File::exists($path . '/' . date('Y-m-d')) || File::makeDirectory($path . '/' . date('Y-m-d'), 0777, true))
				File::put($filename , $content);
		}
		
		if ($this->input['report_type'] == 'All' || $this->input['report_type'] == 'NAT00090')
		{
			$content = $this->CreateNAT00090();
			$filename = $path . '/' . date('Y-m-d') . '/nat00090.txt';
			if (File::exists($path . '/' . date('Y-m-d')) || File::makeDirectory($path . '/' . date('Y-m-d'), 0777, true))
				File::put($filename , $content);
		}
		
		if ($this->input['report_type'] == 'All' || $this->input['report_type'] == 'NAT00100')
		{
			$content = $this->CreateNAT00100();
			$filename = $path . '/' . date('Y-m-d') . '/nat00100.txt';
			if (File::exists($path . '/' . date('Y-m-d')) || File::makeDirectory($path . '/' . date('Y-m-d'), 0777, true))
				File::put($filename , $content);
		}
		
		if ($this->input['report_type'] == 'All' || $this->input['report_type'] == 'NAT00120')
		{
			$content = $this->CreateNAT00120();
			$filename = $path . '/' . date('Y-m-d') . '/nat00120.txt';
			if (File::exists($path . '/' . date('Y-m-d')) || File::makeDirectory($path . '/' . date('Y-m-d'), 0777, true))
				File::put($filename , $content);
		}

	}
	
	private function CreateNAT00010() {
		$content = new Avetmiss(array('NAT00010', '1','Coffee School','1234','CoffeeSchool Address','','Sydney','2000','10'));
		return implode($content->record);
	}
	
	private function CreateNAT00020() {
		
		$sql = "SELECT DISTINCT
				'1' as organisation_id, 
				CAST(l.id as Char(10)) as id, 
				l.name, 
				l.post_code, 
				CASE 
					WHEN l.state = 'NSW' THEN '01'
					WHEN l.state = 'VIC' THEN '02'
					WHEN l.state = 'QLD' THEN '03'
					WHEN l.state = 'SA' THEN '04'
					WHEN l.state = 'WA' THEN '05'
					WHEN l.state = 'TAS' THEN '06'
					WHEN l.state = 'NT' THEN '07'
					WHEN l.state = 'ACT' THEN '08'
					ELSE '01'
				END as state_identifier, 
				l.city, '1100' as country_code
				from rosters r
				LEFT JOIN courseinstances ci ON r.course_instance_id = ci.id
				LEFT JOIN groupbookings gb ON r.group_booking_id = gb.id
				JOIN courses c ON c.id = CASE WHEN ci.id IS NOT NULL THEN ci.course_id ELSE gb.course_id END
				JOIN locations ll ON ll.id = CASE WHEN ci.id IS NOT NULL THEN ci.location_id ELSE gb.location_id END
				JOIN locations l ON l.id = CASE WHEN ll.parent_id = 0 THEN ll.id ELSE ll.parent_id END  
				WHERE c.id != 9 AND 
				CASE WHEN ci.id IS NOT NULL THEN ci.active = 1 ELSE gb.active = 1 END ";
				
		if (!empty($this->input['from_date']))
		{
			$sql .= "AND CASE 
				WHEN ci.id IS NOT NULL 
				THEN ci.course_date BETWEEN '" . $this->input['from_date'] . "' AND '" . $this->input['to_date'] . "' 
				ELSE gb.course_date BETWEEN '" . $this->input['from_date'] . "' AND '" . $this->input['to_date'] . "'
			END";
		}
				
		return $this->ProcessQuery($sql, 'NAT00020');
				
	}
	
	private function CreateNAT00060() {
		
		$sql = "SELECT DISTINCT
				'C' as subject_flag, c.avetmiss_subject_identifier, c.name, c.avetmiss_field_identifier, 
				'Y' as vet_flag, c.avetmiss_course_hours 
				from rosters r
				LEFT JOIN courseinstances ci ON r.course_instance_id = ci.id
				LEFT JOIN groupbookings gb ON r.group_booking_id = gb.id
				JOIN courses c ON c.id = CASE WHEN ci.id IS NOT NULL THEN ci.course_id ELSE gb.course_id END
				WHERE c.id != 9 AND 
				CASE WHEN ci.id IS NOT NULL THEN ci.active = 1 ELSE gb.active = 1 END ";
		
		if (!empty($this->input['from_date']))
		{
			$sql .= "AND CASE 
				WHEN ci.id IS NOT NULL 
				THEN ci.course_date BETWEEN '" . $this->input['from_date'] . "' AND '" . $this->input['to_date'] . "' 
				ELSE gb.course_date BETWEEN '" . $this->input['from_date'] . "' AND '" . $this->input['to_date'] . "'
			END";
		}
		
		return $this->ProcessQuery($sql, 'NAT00060');
		
	}

	private function CreateNAT00080() {
		
		$sql = "SELECT DISTINCT c.id, CONCAT(c.first_name,' ',c.last_name) as name, 
				CASE 
					WHEN c.school_level = _utf8 'Year 12 or equivalent' THEN '12'
					WHEN c.school_level = _utf8 'Year 11 or equivalent' THEN '11'
					WHEN c.school_level = _utf8 'Year 10 or equivalent' THEN '10'
					WHEN c.school_level = _utf8 'Year 9 or equivalent' THEN '09'
					WHEN c.school_level = _utf8 'Year 8 or below' THEN '08'
					WHEN c.school_level = _utf8 'Never attended school' THEN '02'
					ELSE '02'
				END as school_level,
				CASE 
					WHEN c.school_level = _utf8 'Never attended school' THEN '@@@@'
					WHEN c.school_year IS NULL OR c.school_year = '' THEN '@@@@'
					ELSE c.school_year
				END as school_year,
				c.gender, 
				CASE WHEN IFNULL(c.gender, '0') = '0' THEN '@' ELSE c.gender END as gender, 
				CASE WHEN IFNULL(c.dob, '') = '' THEN '@@@@@@@@' ELSE DATE_FORMAT(c.dob, '%d%m%Y') END as dob, c.post_code, 
				CASE 
					WHEN (c.origin = _utf8 'Aboriginal' AND c.islander_origin = 1) THEN 'C'
					WHEN (c.origin = _utf8 '' AND c.islander_origin = 0) THEN 'E'
					WHEN (c.origin IS NULL AND c.islander_origin IS NULL) THEN 'F'
					WHEN (c.origin = _utf8 '' OR c.islander_origin = 0) AND (c.origin = _utf8 'Aboriginal' OR c.islander_origin = 1) THEN 'B'
					WHEN (c.origin = _utf8 'Aboriginal' OR c.islander_origin = 1) THEN 'A'
					ELSE 'F'
				END as indigenous, 
				CASE 
					WHEN c.lang_eng = 1 THEN '1201'
					WHEN l.id IS NOT NULL THEN l.id
					ELSE '@@@@'
				END as lang,
				CASE 
					WHEN lfs.id IS NOT NULL THEN lfs.id
					ELSE '@@'
				END as employment,
				CASE 
					WHEN aco.id IS NOT NULL THEN aco.code
					ELSE '@@@@'
				END as country,
				CASE 
					WHEN c.disability = 1 THEN 'Y'
					ELSE 'N'
				END as disability,
				CASE 
					WHEN c.achievements IS NOT NULL AND c.achievements != '' THEN 'Y'
					ELSE 'N'
				END as achievements,
				CASE 
					WHEN c.school_attending = 1 THEN 'Y'
					ELSE 'N'
				END as school_attending,
				CASE 
					WHEN c.lang_eng = '1' OR c.lang_other = '9700' OR c.lang_other = '9701' OR c.lang_other = '9702' OR c.lang_other = '9799' OR c.lang_other = '@@@@'  THEN ' '
					WHEN c.lang_eng_level IS NULL OR c.lang_eng_level = '' THEN '@'
					WHEN c.lang_eng_level = 'Very well' THEN '1'
					WHEN c.lang_eng_level = 'Well' THEN '2'
					WHEN c.lang_eng_level = 'Not Well' THEN '3'
					WHEN c.lang_eng_level = 'Not at all' THEN '4'
					ELSE '@'
				END as english_level, 
				c.city,
				c.unique_student_identifier,
				CASE 
					WHEN c.state = 'NSW' THEN '01'
					WHEN c.state = 'VIC' THEN '02'
					WHEN c.state = 'QLD' THEN '03'
					WHEN c.state = 'SA' THEN '04'
					WHEN c.state = 'WA' THEN '05'
					WHEN c.state = 'TAS' THEN '06'
					WHEN c.state = 'NT' THEN '07'
					WHEN c.state = 'ACT' THEN '08'
					ELSE '99'
				END as state_identifier, 
				c.address_building_name,
				c.address_unit_details,
				c.address_street_number,
				c.address_street_name
				from rosters r
				LEFT JOIN courseinstances ci ON r.course_instance_id = ci.id
				LEFT JOIN groupbookings gb ON r.group_booking_id = gb.id
				JOIN courses co ON co.id = CASE WHEN ci.id IS NOT NULL THEN ci.course_id ELSE gb.course_id END
				JOIN customers c ON c.id = r.customer_id 
				LEFT JOIN avetmiss_language_codes l ON l.id = c.lang_other 
				LEFT JOIN avetmiss_country_codes aco ON aco.id = c.country_of_birth 
				LEFT JOIN avetmiss_labor_force_status lfs ON lfs.name = c.employment
				WHERE co.id != 9 AND 
				CASE WHEN ci.id IS NOT NULL THEN ci.active = 1 ELSE gb.active = 1 END ";
	
		if (!empty($this->input['from_date']))
		{
			$sql .= "AND CASE 
				WHEN ci.id IS NOT NULL 
				THEN ci.course_date BETWEEN '" . $this->input['from_date'] . "' AND '" . $this->input['to_date'] . "' 
				ELSE gb.course_date BETWEEN '" . $this->input['from_date'] . "' AND '" . $this->input['to_date'] . "'
			END";
		}
		
		//$sql .= " LIMIT 100";

		return $this->ProcessQuery($sql, 'NAT00080');
		
	}	
	
	private function CreateNAT00090() {
		
		$sql = "SELECT DISTINCT c.id, c.disabilities
				from rosters r
				LEFT JOIN courseinstances ci ON r.course_instance_id = ci.id
				LEFT JOIN groupbookings gb ON r.group_booking_id = gb.id
				JOIN courses co ON co.id = CASE WHEN ci.id IS NOT NULL THEN ci.course_id ELSE gb.course_id END
				JOIN customers c ON c.id = r.customer_id 
				LEFT JOIN avetmiss_language_codes l ON l.id = c.lang_other 
				LEFT JOIN avetmiss_country_codes aco ON aco.id = c.country_of_birth 
				LEFT JOIN avetmiss_labor_force_status lfs ON lfs.name = c.employment
				WHERE co.id != 9 AND 
				CASE WHEN ci.id IS NOT NULL THEN ci.active = 1 ELSE gb.active = 1 END AND
				IFNULL(disabilities, '') != '' ";
		
		if (!empty($this->input['from_date']))
		{
			$sql .= "AND CASE 
				WHEN ci.id IS NOT NULL 
				THEN ci.course_date BETWEEN '" . $this->input['from_date'] . "' AND '" . $this->input['to_date'] . "' 
				ELSE gb.course_date BETWEEN '" . $this->input['from_date'] . "' AND '" . $this->input['to_date'] . "'
			END";
		}
		
		return $this->ProcessQuery($sql, 'NAT00090');
		
	}
	
	private function CreateNAT00100() {
		
		$sql = "SELECT DISTINCT c.id, c.achievements
				from rosters r
				LEFT JOIN courseinstances ci ON r.course_instance_id = ci.id
				LEFT JOIN groupbookings gb ON r.group_booking_id = gb.id
				JOIN courses co ON co.id = CASE WHEN ci.id IS NOT NULL THEN ci.course_id ELSE gb.course_id END
				JOIN customers c ON c.id = r.customer_id 
				LEFT JOIN avetmiss_language_codes l ON l.id = c.lang_other 
				LEFT JOIN avetmiss_country_codes aco ON aco.id = c.country_of_birth 
				LEFT JOIN avetmiss_labor_force_status lfs ON lfs.name = c.employment
				WHERE co.id != 9 AND 
				CASE WHEN ci.id IS NOT NULL THEN ci.active = 1 ELSE gb.active = 1 END AND
				IFNULL(achievements, '') != '' ";
		
		if (!empty($this->input['from_date']))
		{
			$sql .= "AND CASE 
				WHEN ci.id IS NOT NULL 
				THEN ci.course_date BETWEEN '" . $this->input['from_date'] . "' AND '" . $this->input['to_date'] . "' 
				ELSE gb.course_date BETWEEN '" . $this->input['from_date'] . "' AND '" . $this->input['to_date'] . "'
			END";
		}
		
		return $this->ProcessQuery($sql, 'NAT00100');
		
	}
	
	private function CreateNAT00120() {
		
		$sql = "SELECT 
				CAST(l.id as Char(10)) as location_id, 
				c.id as customer_id,
				co.avetmiss_subject_identifier,
				'' as program_identifier,
				CASE WHEN ci.id IS NOT NULL THEN DATE_FORMAT(ci.course_date, '%d%m%Y') ELSE DATE_FORMAT(gb.course_date, '%d%m%Y') END as start_date,
				CASE WHEN ci.id IS NOT NULL THEN DATE_FORMAT(ci.course_date, '%d%m%Y') ELSE DATE_FORMAT(gb.course_date, '%d%m%Y') END as end_date,
				CASE WHEN co.type = 'FaceToFace' THEN '10' ELSE '20' END as delivery_mode,
				CASE WHEN cer.id IS NULL THEN '30' ELSE '20' END as outcome_identifier,
				co.avetmiss_course_hours,
				'20' as funding_source,
				'8' as commencing_program,
				'' as training_contract,
				'' as apprenticeship,
				'' as study_reason,
				'N' as vet_in_schools,
				'' as funding_identifier
				from rosters r
				LEFT JOIN courseinstances ci ON r.course_instance_id = ci.id
				LEFT JOIN groupbookings gb ON r.group_booking_id = gb.id
				JOIN courses co ON co.id = CASE WHEN ci.id IS NOT NULL THEN ci.course_id ELSE gb.course_id END
				JOIN customers c ON c.id = r.customer_id 
				JOIN locations ll ON ll.id = CASE WHEN ci.id IS NOT NULL THEN ci.location_id ELSE gb.location_id END
				JOIN locations l ON l.id = CASE WHEN ll.parent_id = 0 THEN ll.id ELSE ll.parent_id END  
				LEFT JOIN certificates cer ON r.id = cer.roster_id AND c.id = cer.customer_id 
				WHERE co.id != 9 AND 
				CASE WHEN ci.id IS NOT NULL THEN ci.active = 1 ELSE gb.active = 1 END ";
		
		if (!empty($this->input['from_date']))
		{
			$sql .= "AND CASE 
				WHEN ci.id IS NOT NULL 
				THEN ci.course_date BETWEEN '" . $this->input['from_date'] . "' AND '" . $this->input['to_date'] . "' 
				ELSE gb.course_date BETWEEN '" . $this->input['from_date'] . "' AND '" . $this->input['to_date'] . "'
			END";
		}
		
		return $this->ProcessQuery($sql, 'NAT00120');
		
	}
	
	private function ProcessQuery($sql, $type) {
	
		//\Log::info($sql);
		$rows = DB::select( $sql );
		$records = array();
		foreach($rows as $row)
		{
			$data = array($type);
			foreach((array)$row as $key => $value)
			{ 
				$data[] = $value;
			}
			$content= new Avetmiss($data);
			if($content->type == 'multiple')
			{
				foreach($content->record as $key => $value)
				{
					$records[] = implode($value);
				}
			}
			else
			{
				$records[] = implode($content->record);
			}
		}
		return implode("\n", $records);
		
	}
	
}
