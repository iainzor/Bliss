<?php
namespace User\Settings;

class Definitions
{
	/**
	 * @var Definition[]
	 */
	private $definitions = [];
	
	/**
	 * Add multiple setting definitions.  This will clear all existing definitions.
	 * 
	 * @param array $definitions
	 */
	public function set(array $definitions)
	{
		$this->definitions = [];
		
		foreach ($definitions as $definition) {
			$this->add($definition);
		}
	}
	
	/**
	 * Add a setting definition to the collection
	 * 
	 * @param Definition $definition
	 * @throws \Exception
	 */
	public function add($definition)
	{
		if (is_array($definition)) {
			$definition = Definition::factory($definition);
		}
		if (!($definition instanceof Definition)) {
			throw new \Exception("\$definition must be an array of properties or an instance of \\User\\Settings\\Definition");
		}
		
		$this->definitions[$definition->key()] = $definition;
	}
	
	/**
	 * Get a setting's definition by its key name
	 * 
	 * @param string $key
	 * @return \User\Settings\Definition
	 */
	public function get($key)
	{
		if (isset($this->definitions[$key])) {
			return $this->definitions[$key];
		} else {
			return new GenericDefinition();
		}
	}
	
	/**
	 * Generate a collection of all settings with their default values
	 * 
	 * @return \User\Settings\Setting[]
	 */
	public function getDefaults()
	{
		$settings = [];
		foreach ($this->definitions as $def) {
			$setting = new Setting($def);
			$setting->key($def->key());
			$setting->value($def->defaultValue());
			$settings[] = $setting;
		}
		return $settings;
	}
}