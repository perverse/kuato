<?php

namespace Kuato\Traits;

trait HasRolesTrait 
{
	/**
	 * Assign a role to a user
	 *
	 * @param string	$name
	 *
	 */
	public function assignRoleByName(string $name) 
	{
		$this->roles()->detach();
		
		return $this->roles()->save(
			\App\Modules\Role\Entities\Role::whereSlug($name)->firstOrFail()
		);
	}

	public function assignRoleById(string $id) 
	{
		$this->roles()->detach();

		return $this->roles()->save(
			\App\Modules\Role\Entities\Role::whereId($id)->firstOrFail()
		);
	}

	/**
	 * Check which roles the user has
	 *
	 * @param mixed		$role
	 *
	 */
	public function hasRole($role) 
	{
		//Check if it's a string or a collection
		if( is_string($role) ):
			return $this->roles->contains('slug', $role);
		endif;

		return !! $role->intersect($this->roles)->count();
	}
}
