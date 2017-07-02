<?php namespace Controllers\Api;

use AdminController;
use Cartalyst\Sentry\Users\LoginRequiredException;
use Cartalyst\Sentry\Users\PasswordRequiredException;
use Cartalyst\Sentry\Users\UserExistsException;
use Cartalyst\Sentry\Users\UserNotFoundException;
use Config, Input, Lang, Redirect, Sentry, Validator, View, DB, Response, Exception, File;
use Voucher, CourseBundle, Location, CoursePrice, CourseInstance, Course, PdfService;

class VouchersController extends AdminController {

	
	public function getVoucher($id)
	{
		try 
		{	
			$voucher = Voucher::find($id);
			$voucher_data = $voucher->toArray();
			$voucher_data['message'] = $voucher->message;

			return Response::json($voucher_data);
		}
		catch (Exception $e)
		{
			\Log::error($e);
			return Response::json(array(
				'success' => false,
				'Message' => "Problem retrieving voucher, <br>please contact Coffee School Office"
				), 500);
		}


	}
	
	public function download($id)
	{
		
		//for vouchers , we first delete any existing voucher
		$filename = 'voucher-' . $id . '.pdf';
		$filepath = storage_path() . '/vouchers/' . $filename;
		
		if (file_exists($filepath))
			File::delete($filepath);	
		
		//$voucher = Voucher::find($id);
		//return View::make('backend.vouchers.voucher', compact('voucher'));
		return PdfService::download('Voucher', 'backend.vouchers.voucher', '/vouchers/', 'voucher-', $id);	
	}

}