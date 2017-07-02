<?php 

use Cartalyst\Sentry\Users\Eloquent\User as SentryUserModel;

class User extends SentryUserModel {

	/**
	 * Indicates if the model should soft delete.
	 *
	 * @var bool
	 */
	protected $softDelete = true;

	/**
	 * Returns the user full name, it simply concatenates
	 * the user first and last name.
	 *
	 * @return string
	 */
	public function fullName()
	{
		return "{$this->first_name} {$this->last_name}";
	}
	
	public function getNameAttribute()
	{
		return $this->first_name . ' ' . $this->last_name;
	}

	public function scopeFindAllInGroup( $query, $group)
	{
		return $query = $query->join('users_groups', 'users.id', '=', 'users_groups.user_id')
			->where('users_groups.group_id',$group);
	}

	public static function instructors()
	{
		return static::join('users_groups', 'users.id', '=', 'users_groups.user_id')
		->where('users_groups.group_id',3)
		->get();
	}
	
	public function customer()
	{
		return $this->hasOne('Customer');
	}

	
	/**
	 * Returns the user Gravatar image url.
	 *
	 * @return string
	 */
	public function gravatar()
	{
		// Generate the Gravatar hash
		$gravatar = md5(strtolower(trim($this->gravatar)));

		// Return the Gravatar url
		return "//gravatar.org/avatar/{$gravatar}";
	}

}
