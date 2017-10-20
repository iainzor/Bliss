<?php
namespace Database;

class PDO extends \PDO
{
	public function prepare($statement, $options = null)
	{
		if ($options === null) {
			$options = [];
		}
		
		return parent::prepare($statement, $options);
	}
}