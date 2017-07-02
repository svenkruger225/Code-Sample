<?php namespace Controllers\Backend;

use AdminController;
use Config, DB, Session, Log,Input,Lang,Redirect,Sentry,Validator,View,Customer;


class CustomersController extends AdminController {

	protected $customer;

	public function __construct(Customer $customer)
	{
		parent::__construct();
		$this->customer = $customer;
	}

	public function index()
	{
		
		$input = Session::get('_old_input');

		if(Input::get('search') !== null)
			$search = Input::get('search');  
		elseif( $input && array_key_exists('search', $input))
			$search = $input['search'];  
		else
			$search = '';  


		//$search = empty($input['search']) ? Input::get('search') : $input['search'];
		
		$query = $this->customer;
		
		if(	$search && $search != '') {
			$query = $query->where(DB::raw('CONCAT(first_name, " ", last_name)'), 'like', '%' . $search . '%');
			$query = $query->orWhere('email', 'like', '%' . $search . '%');
		}

		// Paginate the users
		$customers = $query
			->orderBy('first_name')
			->orderBy('last_name')
			->paginate(20)
			->appends(array('search' => $search,));
		
		Session::flashInput(array('search'=>$search));
		
		return View::make('backend.customers.index', compact('customers'));
	}

	public function create()
	{
		Session::reflash();
		$titles = array('' => 'Select Title') + \Config::get('utils.titles', array());;
		$languages = array('' => 'Select Language') + \AvetmissLanguage::orderBy('name')->lists('name','id');
		$countries = array('' => 'Select Country') + \AvetmissCountry::orderBy('name')->lists('name','id');
		$states = array('' => 'Select State') + \AvetmissState::orderBy('name')->lists('name','id');
		$achievements_list = \AvetmissAchievement::orderBy('name')->get(array('id','name'))->toArray();
		$disabilities_list = \AvetmissDisability::orderBy('id')->get(array('id','name'))->toArray();
		$study_reasons_list = \AvetmissStudyReason::orderBy('id')->get(array('id','name'))->toArray();
		$usi_visa_issue_countries = array('' => 'Select Country') + \UsiVisaIssueCountry::orderBy('name')->lists('name','id');
		return View::make('backend.customers.create', compact('languages', 'titles','countries', 'states','achievements_list','disabilities_list','study_reasons_list','usi_visa_issue_countries'));
	}

	public function store()
	{
		$keys = array('disabilities', 'achievements');
		$input = Input::except('_method','DvsDocumentType','CertificateNumber','DatePrinted','RegistrationDate',
			'RegistrationNumber','RegistrationState','RegistrationYear','DocumentNumber','LicenceNumber',
			'LicenceState','preferred_method','city_of_birth','country_of_birth','country_of_residence',
			'CountryOfIssue','PassportNumber','NameLine1','NameLine2','NameLine3','NameLine4','CardColour',
			'ExpiryDate','ExpiryDay','ExpiryMonth','ExpiryYear','IndividualRefNumber','MedicareCardNumber','ImmiCardNumber','AcquisitionDate',
			'StockNumber');
		
		if(!empty($input['disabilities_other']))
			array_push($input['disabilities'], 'other__' . $input['disabilities_other'] );
		
		foreach($keys as $key)
		{
			if(isset($input[$key]))
				$input[$key] = json_encode($input[$key]);
		}
		
		//$input['dob'] = empty($input['dob']) ? null : \DateTime::createFromFormat('d/m/Y', $input['dob'])->format('Y-m-d');
		
		if (empty($input['lang_other']))
			$input['lang_other'] = '@@@@';
		
		if (empty($input['disability']))
			$input['disability'] = '0';

		if(!empty($input['origin']))
			$input['islander_origin'] = null;

		unset($input['other_disabilities']);
		unset($input['disabilities_other']);
		
		$input['first_name'] = trim($input['first_name']);
		$input['last_name'] = trim($input['last_name']);


		$validation = Validator::make($input, Customer::$rules);

		if ($validation->passes())
		{
			$this->customer->create($input);

			return Redirect::route('backend.customers.index');
		}
		Session::reflash();

		return Redirect::route('backend.customers.create')
			->withInput()
			->withErrors($validation)
			->with('message', 'There were validation errors.');
	}

	public function show($id)
	{
		$customer = $this->customer->findOrFail($id);

		return View::make('backend.customers.show', compact('customer'));
	}

	public function edit($id)
	{
		$keys = array('disabilities', 'achievements');
		$customer = $this->customer->find($id);

		if (is_null($customer))
		{
			return Redirect::route('backend.customers.index');
		}
		
		foreach($keys as $key)
		{
			$customer->$key = json_decode($customer->$key, true);
		}
		
		$disabilities = array();
		if ($customer->disabilities)
			foreach ($customer->disabilities as $disability) 
			{   
				if (strpos( $disability, 'other__' ) !== false ) 
				{
					$customer->disabilities_other = str_replace('other__', '', $disability);
				}
				else 
				{
					array_push($disabilities, $disability);
				}
			}
		$customer->disabilities = $disabilities;

		if (!$customer->achievements)
			$customer->achievements = array();
		
		//if ($customer->dob)
		//	$customer->dob = \DateTime::createFromFormat('Y-m-d', $customer->dob)->format('d/m/Y');

		$courses = \Course::where('active', 1)->remember(Config::get('cache.minutes', 1))->orderBy('name')->lists('name', 'id');		
		$types = \Config::get('utils.document_types', array());

		Session::reflash();

		//print_r($customer);
		//exit();

		$titles = array('' => 'Select Title') + \Config::get('utils.titles', array());;
		$languages = array('' => 'Select Language') + \AvetmissLanguage::orderBy('name')->lists('name','id');
		$countries = array('' => 'Select Country') + \AvetmissCountry::orderBy('name')->lists('name','id');
		$states = array('' => 'Select State') + \AvetmissState::orderBy('name')->lists('name','id');
		$achievements_list = \AvetmissAchievement::orderBy('id')->get(array('id','name'))->toArray();
		$disabilities_list = \AvetmissDisability::orderBy('id')->get(array('id','name'))->toArray();
		$study_reasons_list = \AvetmissStudyReason::orderBy('id')->get(array('id','name'))->toArray();
		$usi_visa_issue_countries = array('' => 'Select Country') + \UsiVisaIssueCountry::orderBy('name')->lists('name','id');
		return View::make('backend.customers.edit', compact('customer', 'titles', 'courses', 'types', 'languages','countries','states','achievements_list','disabilities_list','study_reasons_list', 'usi_visa_issue_countries'));
	}

	public function update($id)
	{
		
		$keys = array('disabilities', 'achievements');
		$input = Input::except('_method','DvsDocumentType','CertificateNumber','DatePrinted','RegistrationDate',
		'RegistrationNumber','RegistrationState','RegistrationYear','DocumentNumber','LicenceNumber',
			'LicenceState','preferred_method','city_of_birth','country_of_birth','country_of_residence',"DvsDocument",
			'CountryOfIssue','PassportNumber','NameLine1','NameLine2','NameLine3','NameLine4','CardColour',
			'ExpiryDate','ExpiryDay','ExpiryMonth','ExpiryYear','IndividualRefNumber','MedicareCardNumber','ImmiCardNumber','AcquisitionDate',
			'StockNumber',"errors","IsValid",'customer_id');
		
		if(!empty($input['disabilities_other']))
			array_push($input['disabilities'], 'other__' . $input['disabilities_other'] );
		
		foreach($keys as $key)
		{
			if(isset($input[$key]))
				$input[$key] = count($input[$key]) > 0 ? json_encode($input[$key]) : '';
		}
		
		//$input['dob'] = empty($input['dob']) ? null : \DateTime::createFromFormat('d/m/Y', $input['dob'])->format('Y-m-d');
		
		unset($input['other_disabilities']);
		unset($input['disabilities_other']);
		
		
		if (empty($input['lang_other']))
			$input['lang_other'] = '@@@@';
		
		if (empty($input['disability']))
			$input['disability'] = '0';

		if(!empty($input['origin']))
			$input['islander_origin'] = null;

		$input['first_name'] = trim($input['first_name']);
		$input['last_name'] = trim($input['last_name']);

		
		$validation = Validator::make($input, Customer::$rules);
		
		Session::reflash();

		if ($validation->passes())
		{
			$customer = $this->customer->find($id);
			$customer->update($input);

			return Redirect::route('backend.customers.edit', $id)->with('success', 'Customer updated successfully');
		}

		return Redirect::route('backend.customers.edit', $id)
			->withInput()
			->withErrors($validation)
			->with('message', 'There were validation errors.');
	}

	public function destroy($id)
	{
		try
		{

			$this->customer->find($id)->delete();
			Session::reflash();

			return Redirect::route('backend.customers.index')->with('success', 'Customer deleted successfully');
		}
		catch (Exception $e)
		{
			return Redirect::route('backend.customers.index')->with('error', $e->getMessage());
		}

	}

	public function merge()
	{
		try
		{
			$input = Input::all();
			$master = Input::get('master', '');
			$merge = Input::get('merge', array());
			
			if (($key = array_search($master, $merge)) !== false) {
				unset($merge[$key]);
			}
			
			$list_of_ids = implode(',', $merge);
			
			// we get a list of tables that contains a column 'customer_id'	
			$db = Config::get('database.connections.mysql.database', 'cshool_prod');	
			$sql = "SELECT DISTINCT TABLE_NAME FROM INFORMATION_SCHEMA.COLUMNS WHERE COLUMN_NAME IN ('customer_id') AND TABLE_SCHEMA='$db'";				

	//		print('master: ' . $master ."<br>");
	//		print('merge: ' . $list_of_ids ."<br>");
			
			$tables = DB::select($sql);
			
			foreach($tables as $table)
			{
				$sql = "UPDATE $table->TABLE_NAME SET customer_id = $master WHERE customer_id in ($list_of_ids)";				
				$changes = DB::update($sql);
				Log::info("Merge customer_id from '$list_of_ids' to '$master', $changes change(s) made in $table->TABLE_NAME.");
			}
			
			$sql = "DELETE from customers WHERE id in ($list_of_ids)";				
			$deletes = DB::update($sql);
			Log::info("delete customers ids '$list_of_ids' from customers table, $deletes delete(s) made in customers.");
			Session::reflash();

			return Redirect::route('backend.customers.index')->with('success', 'Merge successfully');
		}
		catch (Exception $e)
		{
			return Redirect::route('backend.customers.index')->with('error', $e->getMessage());
		}

	}

}