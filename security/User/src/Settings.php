<?php
namespace User;

use Bliss\Component,
	Bliss\Module\AbstractModule;

class Settings extends Component
{
	/**
	 * @var AbstractModule
	 */
	private $module;
	
	/**
	 * @var User
	 */
	private $user;
	
	/**
	 * @var array
	 */
	private $data;
	
	/**
	 * Constructor
	 * 
	 * @param AbstractModule $module
	 * @param \User\User $user
	 */
	public function __construct(AbstractModule $module, User $user)
	{
		$this->module = $module;
		$this->user = $user;
	}
	
	/**
	 * Attempt to get a setting value
	 * 
	 * @param string $key
	 * @param mixed $defaultValue
	 * @return mixed
	 */
	public function get($key, $defaultValue = null)
	{
		$data = $this->data();
		
		return isset($data[$key]) ? $data[$key] : $defaultValue;
	}
	
	/**
	 * Get the settings for the current module and user
	 * 
	 * @return array
	 */
	public function data()
	{
		if (!isset($this->data)) {
			$table = new Db\UserSettingsTable();
			$query = $table->select();
			$query->where([
				"moduleName" => $this->module->name(),
				"userId" => $this->user->id()
			]);
			
			foreach ($query->fetchAll() as $setting) {
				$this->data[$setting->key()] = $setting->value();
			}
		}
		
		return $this->data;
	}
}