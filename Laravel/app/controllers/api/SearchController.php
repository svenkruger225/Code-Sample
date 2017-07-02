<?php namespace Controllers\Api;

use AdminController;
use Config, Input, Lang, Redirect, Sentry, Validator, View, Response, Exception, File, DB;
use Customer, Certificate, ExternalDocuments;
use CustomerService, PdfService, UploadService, Utils;

class SearchController extends AdminController {

	public function searchCustomers()
	{
		try 
		{
			$search = Input::get('search_text', '');
			$results = Customer::whereRaw("CONCAT(first_name, ' ', last_name) LIKE '%" . $search . "%'")->orderBy('first_name')->orderBy('last_name')->get();
			return Response::json($results);
		}
		catch (Exception $e)
		{
			return Response::json(array(
				'success' => false,
				'Message' => "Problem updating costumer" . $e->getMessage()
				), 500);
		}	
	}
}