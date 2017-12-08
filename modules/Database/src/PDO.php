<?php
namespace Database;

class PDO extends \PDO
{
	public $logs = [];
	
	public function prepare($statement, $options = null)
	{
		if ($options === null) {
			$options = [];
		}
		
		$this->logs[] = $statement;
		
		return parent::prepare($statement, $options);
	}
}