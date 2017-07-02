<?php namespace App\Services;

class UploadService {
	
	public function __construct()
	{
		
	}
	
	//
	//public function upload($file, $dir)
	//{
	//	if ($file)
	//	{
	//		$destination = storage_path() . '/' . $dir;
	//		$filename    = $file->getClientOriginalName();
	//		$path        = storage_path() . '/' . $dir . '/' . $filename;
	//		
	//		$uploaded    = $file->move($destination, $filename);
	//		
	//		if ($uploaded)
	//		{
	//			return $path;
	//		}
	//	}
	//}

	
	public function upload($file, $dir, $newname=null)
	{
		if ($file)
		{
			$destination = $dir;
			$filename    = !empty($newname) ? $newname : $file->getClientOriginalName();
			$path        = $dir . '/' . $filename;
			
			$uploaded    = $file->move($destination, $filename);
			
			if ($uploaded)
			{
				return $path;
			}
		}
		return 'error';
	}
	
	
}

