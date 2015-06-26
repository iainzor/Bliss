<?php
namespace Database\QueryBuilder;

class Factory
{
	/**
	 * Attempt to create a new query builder instance based on the DSN
	 * 
	 * @param string $dsn
	 * @return \Database\QueryBuilder\BuilderInterface
	 */
	public static function create($dsn)
	{
		$driver = strtolower(preg_replace("/^([a-z0-9]+):.*$/i", "$1", $dsn));
		
		switch ($driver) {
			case "mysql":
				return new MySQLBuilder();
			default:
				return null;
		}
	}
}