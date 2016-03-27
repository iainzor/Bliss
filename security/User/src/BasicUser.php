<?php
namespace User;

class BasicUser extends User
{
	private $removeProperties = [
		"email",
		"password",
		"role",
		"roleId"
	];
	
	/**
	 * Remove sensitive data from the exported array
	 * 
	 * @return array
	 */
	public function toArray() 
	{
		$data = parent::toArray();
		foreach ($this->removeProperties as $name) {
			unset($data[$name]);
		}
		return $data;
	}
}