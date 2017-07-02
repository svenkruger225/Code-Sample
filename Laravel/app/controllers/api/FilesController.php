<?php namespace Controllers\Api;

use AdminController;
use Cartalyst\Sentry\Users\LoginRequiredException;
use Cartalyst\Sentry\Users\PasswordRequiredException;
use Cartalyst\Sentry\Users\UserExistsException;
use Cartalyst\Sentry\Users\UserNotFoundException;
use Config, Input, Lang, Redirect, Sentry, Validator, View, Response, Exception, File;
use Agent, Company, Location, Course, CourseInstance, Order, Item, Invoice, DB;
use Roster, Status, Voucher, Customer, Certificate, ExternalDocuments;
use CertificateService, PdfService, UploadService, Utils;

class FilesController extends AdminController {

	
	public function upload($type)
	{
		//var_dump(Input::all());
		//exit();
		$file = Input::file('upload');	
		$extension = $file->getClientOriginalExtension();
		$dir = storage_path() . '/others';		
		switch (strtolower($extension))
		{
			case 'pdf' :
				$dir = storage_path() . '/pdfs';
				break;
			case 'doc' :
			case 'docx' :
			case 'xls' :
			case 'xlsx' :
				$dir = storage_path() . '/docos';
				break;
			case 'jpg' :
			case 'jepg' :
			case 'png' :
			case 'gif' :
				$dir = storage_path() . '/images';
				break;
			case 'mp3' :
			case 'mp4' :
			case 'wav' :
			case 'wma' :
			case 'avi' :
			case 'flv' :
			case 'm4v' :
			case 'mpeg' :
			case 'mpg' :
				$dir = storage_path() . '/videos';
				break;
			default :
				$dir = storage_path() . '/others';
		}
		
				
		$path = UploadService::upload($file, $dir);

		if (empty($path) or $path == 'error')
			return Response::json(array(
				'success' => false,
				'Message' => "Problem uploading file [" .$path . "]"
				), 500);


		return Response::json(array(
				'success' => true,
				'Message' => "File uploaded [" .$path . "]"
				), 200);

	}
		
	public function downloadFile($filename)
	{
		$extension = pathinfo($filename, PATHINFO_EXTENSION);
		$dir = storage_path() . '/others';		
		switch (strtolower($extension))
		{
			case 'pdf' :
				$dir = storage_path() . '/pdfs/';
				break;
			case 'doc' :
			case 'docx' :
			case 'xls' :
			case 'xlsx' :
				$dir = storage_path() . '/docos/';
				break;
			case 'jpg' :
			case 'jepg' :
			case 'png' :
			case 'gif' :
				$dir = storage_path() . '/images/';
				break;
			case 'mp3' :
			case 'mp4' :
			case 'wav' :
			case 'wma' :
			case 'avi' :
			case 'flv' :
			case 'm4v' :
			case 'mpeg' :
			case 'mpg' :
				$dir = storage_path() . '/videos/';
				break;
			default :
				$dir = storage_path() . '/others/';
		}
		return Response::download($dir . $filename, $filename, array('content-type' => 'application/octet-stream'));
	}
	
	public function viewFile($filename)
	{
		$extension = pathinfo($filename, PATHINFO_EXTENSION);
		$dir = storage_path() . '/others';		
		switch (strtolower($extension))
		{
			case 'pdf' :
				$dir = storage_path() . '/pdfs/';
				break;
			case 'doc' :
			case 'docx' :
			case 'xls' :
			case 'xlsx' :
				$dir = storage_path() . '/docos/';
				break;
			case 'jpg' :
			case 'jepg' :
			case 'png' :
			case 'gif' :
				$dir = storage_path() . '/images/';
				break;
			case 'mp3' :
			case 'mp4' :
			case 'wav' :
			case 'wma' :
			case 'avi' :
			case 'flv' :
			case 'm4v' :
			case 'mpeg' :
			case 'mpg' :
				$dir = storage_path() . '/videos/';
				break;
			default :
				$dir = storage_path() . '/others/';
		}
		$path = $dir . $filename;
		return Utils::ViewFile($path, $filename, 10);
	}
	
	public function getFilesList($type)
	{
		try 	
		{
			$all_files = array();	
			$files = array();
			$folders = explode("|", $type);	
			foreach($folders as $folder)
			{			
				switch (strtolower($folder))
				{
					case 'pdfs' :
						$dir = storage_path() . '/pdfs';
						break;
					case 'docos' :
						$dir = storage_path() . '/docos';
						break;
					case 'images' :
					case 'photos' :
						$dir = storage_path() . '/images';
						break;
					case 'audios' :
					case 'videos' :
						$dir = storage_path() . '/videos';
						break;
					case 'others' :
						$dir = storage_path() . '/others';
						break;
					default :
						$dir = storage_path() . '/others';
				}
				
				
				$all_files = \File::glob($dir . "/*");
				foreach($all_files as $file)
				{
					$module = new \stdClass();
					$module->FileName = basename($file);
					$module->Folder = $folder;
					$module->FilePath = $file;
					$module->FileUrl = '//' .\Request::server('SERVER_NAME') . '/api/files/viewFile/' . basename($file);
					array_push($files, $module);
				}

			}
			return \Response::json($files);
			
		}
		catch (Exception $e)
		{
			return \Response::json(array(
				'success' => false,
				'message' => "Problem loading file list <br>" . $e->getMessage()
				), 500);
		}
	}

}