<?php

class Marketing extends Eloquent {
	protected $table = 'marketing';
	protected $guarded = array();

	public static $rules = array(
		'subject' => 'required',
		'body' => 'required_if_any:send_via,Both,Email',
		'sms_body' => 'required_if_any:send_via,Both,Sms'
	);

	public static function boot()
	{
		parent::boot();

		//if we change the property name lets update all references
		static::saving( function($msg) 
		{
				$sql = "SELECT count(DISTINCT r.customer_id) as rosters 
					FROM rosters r
					JOIN customers cus on cus.id = r.customer_id
					JOIN courseinstances ci ON r.course_instance_id = ci.id 
					JOIN courses c ON c.id = ci.course_id 
					WHERE (cus.mail_out_email = 1 OR cus.mail_out_sms = 1) AND c.id != 9 AND ci.cancelled = 0 AND ci.active = 1 ";
			
			if ((!empty($msg->location_id)) || (!empty($msg->course_id)) || (!empty($msg->date_from)) || (!empty($msg->date_to)))
			{

				if (!empty($msg->location_id))
					$sql .= "AND ci.location_id IN (SELECT ID FROM locations WHERE id = " . $msg->location_id . " OR parent_id = " . $msg->location_id . ") ";
						
				if (!empty($msg->course_id))
					$sql .= " AND ci.course_id = " .$msg->course_id;
						
				if (!empty($msg->date_from) && $msg->date_from != '0000-00-00')
					$sql .= " AND ci.course_date >= '" .$msg->date_from . "'";
						
				if (!empty($msg->date_to) && $msg->date_to != '0000-00-00')
					$sql .= " AND ci.course_date <= '" .$msg->date_to . "'";
			}
					
			//\Log::info($sql);

			$result = \DB::select($sql);
			if(count($result) > 0)
			{
				$msg->email_count = $result[0]->rosters;
				$msg->sms_count = $result[0]->rosters;
			}
				
		});
	
	}
	
	public function location()
	{
		return $this->belongsTo('Location');
	}
	
	public function course()
	{
		return $this->belongsTo('Course');
	}
	
	public function attachments()
	{
		return $this->belongsToMany('Attachment');
	}
	
	public function sessions()
	{
		return $this->hasMany('MarketinsSession', 'message_id');
	}

	public function getFromToAttribute($value)
	{
		$date_from = '';
		$date_to = '';
		if(!empty($this->date_from) && $this->date_from != '0000-00-00')
		{
			$date_from_str =  new \DateTime($this->date_from);
			$date_from = $date_from_str->format('d/m/Y');
		}
		if(!empty($this->date_to) && $this->date_to != '0000-00-00')
		{
			$date_to_str =  new \DateTime($this->date_to);
			$date_to = $date_to_str->format('d/m/Y');
		}
		
		return $date_from . ' | ' . $date_to;
	}

	public function setEmailsAttribute($value)
	{
		$this->attributes['emails'] = $value;
	}

	public function setMobilesAttribute($value)
	{
		$this->attributes['mobiles'] = $value;
	}

}