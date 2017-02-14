<?php
namespace Database\Table;

class NamedTable implements TableInterface
{
	/**
	 * @var string
	 */
	private $name;
	
	/**
	 * Constructor
	 * 
	 * @param string $name
	 */
	public function __construct(string $name)
	{
		$this->name = $name;
	}
	
	/**
	 * @return string
	 */
	public function getName() : string 
	{
		return $this->name;
	}
}
