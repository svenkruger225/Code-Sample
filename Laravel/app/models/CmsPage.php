<?php

class CmsPage extends Eloquent {
	
	protected $table = 'cms_pages';
	
	protected $guarded = array();

	public static $rules = array(
	);

	public function children() 
	{
		return $this->hasMany('CmsPage', 'parent_id'); 
	}
	
	public function parent()
	{
		return $this->belongsTo('CmsPage','parent_id');
	}
	
	public function isParent()
	{
		if($this->parent_id > 0)
			return false;
		else 
			return true;
	}
	
	public function courseModel()
	{
		return $this->belongsTo('Course', 'course_id');
	}
	
	public function location()
	{
		return $this->belongsTo('Location');
	}
	
	public function getChildAttribute()
	{
		$location_name = $this->location_name;
		$child = null;
		if ($this->location_name)
			$child = $this->children->filter(function($c) use($location_name) {if($c->name == $location_name)	{return $c;	} });

		if(is_object($child))
		{
			return $child->first();
		}
		return new CmsPage();
	}
		
	public function getCourseAttribute()
	{
		return !empty($this->child->course_id) ?  $this->child->course_id : $this->course_id;
	}
	
	public function getKeywordsAttribute()
	{
		$keywords = !empty($this->child->meta_keywords) ? ', ' . $this->child->meta_keywords : '' ;
		return $this->meta_keywords . $keywords;
	}

	public function getDescriptionAttribute()
	{
		$description = !empty($this->child->meta_description) ? ', ' . $this->child->meta_description : '' ;
		return $this->meta_description . $description;
	}

	public function contents()
	{
		return $this->hasMany('CmsContent');
	}
	
	public function getPageTitleAttribute()
	{
		$title = $this->title;
		$child_obj = null;
		$location_name = $this->location_name;
		if ($this->location_name)
		{
			$child_list = $this->children->filter(function($child) use($location_name)
				{
					if(strtoupper($child->name) == strtoupper($location_name))
					{
						return $child;
					}
				});

			if(is_object($child_list))
			{
				$child_obj = $child_list->first();
				$title = $child_obj->title;
			}
				
		}
				
		return $title;
	}
	
	public function getContentAttribute()
	{
		return $this->getPageContent();	
		//return $this->replaceContentPlaceHolders($content);
	}
	
	public function getPageContent()
	{
		$this->content_top = '';
		$this->content_bottom = '';
		$sql = "SELECT c.content FROM cms_contents c
				JOIN cms_pages p ON p.id = c.cms_page_id   
				WHERE " . (($this->location_name) ? "route = '{$this->location_name}' AND " : "") .
				"parent_id = '" . $this->id ."' AND c.active = 1";

		$page = DB::select( $sql );
		
		if(count($page) == 0 || count($page) > 1 || empty($page[0]->content))
		{
			$sql = "SELECT c.content FROM cms_contents c
				JOIN cms_pages p ON p.id = c.cms_page_id
				WHERE p.id = '" . $this->id ."' AND c.active = 1";
			$page = DB::select( $sql );
		}
		$parts = explode('<!-- split -->', $page[0]->content);		
		$this->content_top = !empty($parts[0]) ? $this->replaceContentPlaceHolders($parts[0]) : '';
		if (empty($parts[1]))
		{
			$sql = "SELECT c.content FROM cms_contents c
			JOIN cms_pages p ON p.id = c.cms_page_id 
			WHERE p.id = '" . $this->id ."' AND c.active = 1";
			$page = DB::select( $sql );
			$parts = explode('<!-- split -->', $page[0]->content);		
		}
		
		$this->content_bottom = !empty($parts[1]) ? $this->replaceContentPlaceHolders($parts[1]) : '';
		
		return $this->content_top . $this->content_bottom;

	}

	public function setContentTopAttribute($value)
	{
		$this->attributes['content_top'] = $value;
	}

	public function setContentBottomAttribute($value)
	{
		$this->attributes['content_bottom'] = $value;
	}

	public function setHasContentAttribute($value)
	{
		$this->attributes['has_content'] = $value;
	}

	public function setLocationNameAttribute($value)
	{
		$this->attributes['location_name'] = $value;
	}
	
	private function replaceContentPlaceHolders($content)
	{
		$location = null;
		if ($this->location_name)
		{
			$location = Location::where('name','LIKE', '%' .$this->location_name .'%')
			->where('parent_id',0)
			->remember(Config::get('cache.minutes', 1))
			->first();
		}
		$has_location = ($location) ? true : false;	
		if ($has_location)
		{
			$address = ucfirst(strtolower($this->location_name)) . ' location: ' . $location->address . ', ' . $location->city . ', ' . $location->state;
			$state = strtoupper($location->state);

			$content = str_replace("{{specials_link}}", "/specials/" . $this->location_name, $content);
			$content = str_replace("{{bookings_link}}", "/bookings/" . $this->location_name . "?course=" . $this->course, $content);
			$content = str_replace("{{location}}",  ucfirst(strtolower($this->location_name)), $content);
			$content = str_replace("{{location_name}}",  ucfirst(strtolower($this->location_name)), $content);
			$content = str_replace("{{location_name_upper}}",  strtoupper($this->location_name), $content);
			$content = str_replace("{{location_address}}", $address, $content);
			$content = str_replace("{{location_state}}", $state, $content);
			$content = str_replace("{{location_phone}}", $location->phone, $content);
		}
		
		if (strpos($content, '{{specials_content}}') !== false)
		{
			$this->location_id = $has_location ? $location->id : null;
			$specials = $this->parseSpecials(!$has_location, false);
			$content = str_replace("{{specials_content}}", $specials, $content);
		}
		if (strpos($content, '{{specials_content_long}}') !== false)
		{
			$this->location_id = $has_location ? $location->id : null;
			$specials = $this->parseSpecials(!$has_location, true);
			$content = str_replace("{{specials_content_long}}", $specials, $content);
		}	
		
		if (strpos($content, '{{course_name}}') !== false || strpos($content, '{{certificate_code}}') !== false)
		{
			if ($this->course_model)
			{
				$content = str_replace("{{course_name}}", $this->course_model->name, $content);
				$content = str_replace("{{certificate_code}}", $this->course_model->certificate_code, $content);
			}
		}
		
		if (strpos($content, '{{contact_form}}') !== false)
		{
			$form = $this->parseContactForm();
			$content = str_replace("{{contact_form}}", $form, $content);
		}
		
		if (strpos($content, '{{reopen_enrolment_form}}') !== false)
		{
			$form = $this->parseReopenEnrolmentForm();
			$content = str_replace("{{reopen_enrolment_form}}", $form, $content);
		}
		
		if (strpos($content, '{{vouchers_locations_links}}') !== false)
		{
			$links = $this->getVouchersLocationsLinks();
			$content = str_replace("{{vouchers_locations_links}}", $links, $content);
		}
		
		if (strpos($content, '{{facebook_locations_links}}') !== false)
		{
			$links = $this->getLocationsLinks();
			$content = str_replace("{{facebook_locations_links}}", $links, $content);
		}
		
		if (strpos($content, '{{course_list_group_links}}') !== false)
		{
			$links = $this->getCoursesLinks(false);
			$content = str_replace("{{course_list_group_links}}", $links, $content);
		}
		
		if (strpos($content, '{{course_list_group_booking_links}}') !== false)
		{
			$links = $this->getCoursesLinks(true);
			$content = str_replace("{{course_list_group_booking_links}}", $links, $content);
		}
                
        if(strpos($content, '{{course_dates}}') !== false)
        {
            $this->location_id = $location->id;
            $dates = $this->getCourseDates();
            $content = str_replace("{{course_dates}}", $dates, $content);
        }
        preg_match('/{{course_dates_[0-9]*}}/', $content, $matches, PREG_OFFSET_CAPTURE);
        if(!empty($matches)){
            $courseDate=$matches[0][0];
            $courseId=substr($courseDate,15,-2);
            if(strpos($content, $courseDate) !== false)
            {
                $this->location_id = $location->id;
                $dates = $this->getCourseDatesWithId($courseId);
                $content = str_replace($courseDate, $dates, $content);
            }
        }
        

		$content = str_replace("{{facebook_map_link}}", "/content/" . (($this->parent) ? $this->parent->route : $this->route), $content);

		//\Log::info($content);
		
		return $content;
	}

	public function parseSpecials($list_all, $long = false)
	{
		if ($list_all)
		{
			$location_name = '';
			$list_of_locations = DB::table('locations')->remember(Config::get('cache.minutes', 1))->lists('id');
		}
		else
		{
			$location_name = $this->location_name;
			$list_of_locations = DB::table('locations')
				->where('id', '=',  $this->location_id)
				->orWhere('parent_id', '=',  $this->location_id)
				->remember(Config::get('cache.minutes', 1))->lists('id');
		}
		
		$specials = \Utils::GetSpecials($this->location_name, $list_of_locations);
				
		$specials_link = "/specials/" . $this->location_name;
		
		$display_header =$list_all;
		$body_css = $long ? 'specials-body-long' : 'specials-body';

		return View::make('frontend/common/specials-small', compact('location_name', 'specials', 'specials_link', 'display_header', 'body_css'))->render();


		
	}
	
	private function parseContactForm()
	{
		$locations = array(''=>'','Sydney'=>'Sydney','Parramatta'=>'Parramatta','Penrith'=>'Penrith','Melbourne'=>'Melbourne','Brisbane'=>'Brisbane','Perth'=>'Perth','Other'=>'Other');
		$subjects = array(
			''=>'',
			'Course Enquiry'=>'Course Enquiry',
			'School/Group/Team Booking'=>'School/Group/Team Booking',
			'Catering/Machine Hire'=>'Catering/Machine Hire',
			'Certificate Reprint'=>'Certificate Reprint',
			'Invoice/Admin Enquiry'=>'Invoice/Admin Enquiry',
			'Other Enquiry'=>'Other Enquiry');
		$form = View::make('frontend.common.contact', compact('locations', 'subjects'))->render();
		
		return $form;
	}
	
	private function parseReopenEnrolmentForm()
	{
		$form = View::make('frontend.common.reopen-enrolment-form')->render();
		
		return $form;
	}
	
	private function getLocationsLinks()
	{
		//\Log::info("getLocationsLinks");	
		$loc = Location::where('name','LIKE', '%' .$this->location_name .'%')
		->where('parent_id',0)
		->remember(Config::get('cache.minutes', 1))
		->first();
		
		$sql = "select distinct 
				`route`, `courses`.`short_name`, `courses`.`name`, `cms_pages`.`course_id`, '" . $this->location_name . "' as location_name 
				from `cms_pages` 
				inner join `courses` on `courses`.`id` = `cms_pages`.`course_id` 
				inner join `courseinstances` on `courseinstances`.`course_id` = `cms_pages`.`course_id` 
				inner join `locations` on `locations`.`id` = `courseinstances`.`location_id` OR `locations`.`parent_id` = `courseinstances`.`location_id`
				where 
				`cms_pages`.`parent_id` = 0 and 
				(`locations`.`id` = " . $loc->id . " OR `locations`.`parent_id` = " . $loc->id . ") and 
				`cms_pages`.`active` = 1";
	
		$courses =  DB::select( $sql );
		return View::make('frontend.common.facebook-locations', compact('courses'))->render();
		
	}

    private function getCourseDates()
    {
        $course = new Course();
        $course->id = $this->course_id;
        $course = $course->getCourseDates($this->location_id);
        $course=$course[0];

        return View::make('frontend.common.courses-dates',compact('course'))->render();
        return $this->course_id;
    }
    private function getCourseDatesWithId($id)
    {
        $course = new Course();
        $course->id = $id;
        $course = $course->getCourseDates($this->location_id);
        $course=$course[0];
        return View::make('frontend.common.courses-dates',compact('course'))->render();
        return $id;
    }
	
	private function getCoursesLinks($booking)
	{
		$course_title = $this->name . ' Course Information';
		$sql = "SELECT distinct c.id, c.name, '" . $this->route . "'as route, 
				CASE WHEN lp.id IS NULL THEN l.name ELSE lp.name END as location_name, 
				cp.price_online as price
				FROM `courseinstances` ci
				INNER JOIN courses c ON ci.course_id = c.id
				INNER JOIN locations l ON ci.location_id = l.id
				LEFT JOIN locations lp ON lp.id = l.parent_id
				INNER JOIN course_prices cp ON cp.course_id = ci.course_id AND cp.location_id = 
				CASE WHEN lp.id IS NULL THEN l.id ELSE lp.id END
				WHERE ci.course_id = " . $this->course_id . " AND ci.course_date > NOW() AND ci.active = 1 ORDER BY CASE WHEN lp.id IS NULL THEN l.order ELSE lp.order END";
		
		$courses =  DB::select( $sql );
		return View::make('frontend.common.courses-links', compact('courses', 'booking', 'course_title'))->render();
		
	}
	
	private function getVouchersLocationsLinks()
	{
		$locations = Location::where('parent_id', 0)->where('active', 1)->orderBy('order')->remember(Config::get('cache.minutes', 1))->get(array('id','short_name', 'name'));
		return View::make('frontend.common.vouchers-locations', compact('locations'))->render();
	}


}