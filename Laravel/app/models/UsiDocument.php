<?php

class UsiDocument extends Eloquent {
	
	protected $table = 'usi_documents';
	
	protected $guarded = array();

	public static $rules = array(
		);

	
	public function getDvsDocument()
	{
		$document = json_decode($this->document, true);
		$class = 'App\\Models\\UsiDocuments\\' .$document['DvsDocumentType'];

		if (!class_exists($class)) {
			throw new \Exception("Class '$class' not found");
		}

		$document = new $class($document);
		
		return $document->getDvsDocument();
	}


}
