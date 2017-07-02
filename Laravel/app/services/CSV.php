<?php namespace App\Services;

class CSV
{

	protected $source;
	protected $handle;
	protected $headerRowExists = true;
	protected $delimiter = ',';
	protected $enclosure = '"';
	public function setDelimiter($delimiter)
	{
		$this->delimiter = $delimiter;
		return $this;
	}
	public function setHeaderRowExists($headerFlag=true)
	{
		$this->headerRowExists=$headerFlag;
		return $this;
	}
	public function setEnclosure($enclosure)
	{
		$this->enclosure = $enclosure;
		return $this;
	}
	public function setFileHandle($stream='php://output', $mode='r+')
	{
		$this->handle = fopen('php://output', $mode);
		return $this;
	}
	
	public function with($source, $headerRowExists = true, $mode = 'r+')
	{
		if (is_array($source)) { // fromArray
			$this->source = $source;
		} else 
			if (is_string($source)) { // fromfile
				$this->fromFile($source, $headerRowExists, $mode);
			} else
				throw new \Exception('Source must be either an array or a file name');
		
		return $this;
	}

	public function fromArray($arr, $headerRowExists = true)
	{
		$this->headerRowExists = $headerRowExists;
		$this->source = $arr;
		return $this;
	}
	public function fromFile($filePath, $headerRowExists = true, $mode = 'r+')
	{
		$from = fopen($filePath, $mode);
		$arr = array();
		$this->headerRowExists = $headerRowExists;
		if ($headerRowExists) {
			// first header row
			$header = fgetcsv($from, 1000, $this->delimiter, $this->enclosure);
		}
		while (($data = fgetcsv($from, 1000, $this->delimiter, $this->enclosure)) !== FALSE) {
			$arr[] = $headerRowExists ? array_combine($header, $data) : $data;
		}
		fclose($from);
		$this->source = $arr;
		return $this;
	}
	
	public function loadView($view, $param, $data = array(), $mergeData = array())
	{
		$$param = $data;
		// Make the view
		$html = \View::make($view, compact($param))->render();

		$arr = \Utils::parseTableToArray($html);

		$this->source = $arr;
		$this->headerRowExists = false;

		return $this;
	}

	public function put($filePath, $mode = 'w+')
	{
		$this->handle = fopen($filePath, $mode);
		fwrite($this->handle, $this->toString());   
		fclose($this->handle);   
		return $this;  
	}

	public function render($filename = 'export.csv', $mode = 'r+')
	{        
		$headers = array(
			'Content-Type' => 'text/csv',
			'Content-Disposition' => 'attachment; filename="' . $filename . '"',
			'Cache-Control' => 'private',
			'pragma' => 'cache'
			);
		$this->handle = fopen('php://output', $mode);
		return \Response::make($this->toString(), 200, $headers);
	} 
	  
	public function download($filename = 'document.csv' ){
		$output = $this->toString();
		return \Response::make($output, 200, array(
			'Content-Type' => 'text/csv',
			'Content-Disposition' => 'attachment; filename="' . $filename . '"',
			'Cache-Control' => 'private',
			'pragma' => 'cache'
			));
	
	}
	public function stream($filename = 'document.csv' ){
		$that = $this;
		return \Response::stream(function() use($that){
				echo $that->toString();
			}, 200, array(
				'Content-Type' => 'text/csv',
				'Content-Disposition' =>  'inline; filename="'.$filename.'"',
				));
	}

	
	public function getCSV()
	{
		foreach ($this->source as $key => $row) {
			if ($this->headerRowExists) {
				if (empty($header)) { // output header once!
					$header = array_keys(array_dot($row));
					fputcsv($this->handle, $header, $this->delimiter, $this->enclosure); // put them in csv
				}
			}
			fputcsv($this->handle, array_dot($row), $this->delimiter, $this->enclosure);
		}               
	}
	public function toString()
	{
		ob_start(); // buffer the output ...
		$this->handle = fopen('php://output', 'r+');    
		$this->getCSV();    
		return ob_get_clean(); //then return it as a string
	}
}