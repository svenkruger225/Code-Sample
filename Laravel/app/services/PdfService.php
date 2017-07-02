<?php namespace App\Services;

use Config, Input, Lang, Redirect, Sentry, Validator, View;
use Response, Exception, PEAR, Mail, Email, PDF, Order, Invoice, Location;

class PdfService {

	public function __construct()
	{
	}

	public function save($type, $view, $path, $name, $id) 
	{
		
		$param = strtolower($type);
		$filename = $name . $id . '.pdf';
		$filepath = storage_path() . $path . $filename;
		
		if (!file_exists($filepath))
		{		
			if($type == 'Order')
			{
				$invoice = Invoice::find($id);
				$id = $invoice->order_id;
				$$param = $type::with('agent','company')->find($id);
			}
			else
			{
				$$param = $type::find($id);
			}
		
			//var_dump($$param);
			//exit;
			$locations = Location::where('parent_id', 0)->where('active', 1)->orderBy('order')->remember(720)->get();
		
			PDF::loadView($view, compact($param, 'locations'))->setPaper('a4')->save($filepath);
		}
		

	}

	public function download($type, $view, $path, $name, $id) 
	{
		
		$filename = $name . $id .'.pdf';
		$filepath = storage_path() . $path . $filename;
		
		if (!file_exists($filepath))
		{	
			PdfService::save($type, $view, $path,$name, $id);	
		}
		
		return Response::download($filepath, $filename, array('content-type' => 'application/octet-stream'));

	}


}