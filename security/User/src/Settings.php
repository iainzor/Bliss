<?php
namespace User;

use Bliss\Component,
	Bliss\Module\AbstractModule,
	Database\Query\InsertQuery;

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
	private $data = [];
	
	/**
	 * @var boolean
	 */
	private $isLoaded = false;
	
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
	 * Put a value into the settings instance
	 * 
	 * @param string $key
	 * @param mixed $value
	 */
	public function put($key, $value)
	{
		$this->data[$key] = $value;
	}
	
	/**
	 * Get the settings for the current module and user
	 * 
	 * @return array
	 */
	public function data()
	{
		if (!$this->isLoaded && $this->user->isActive()) {
			$table = new Db\UserSettingsTable();
			$query = $table->select();
			$query->where([
				"moduleName" => $this->module->name(),
				"userId" => $this->user->id()
			]);
			
			foreach ($query->fetchAll() as $setting) {
				$this->data[$setting->key()] = $setting->value();
			}
			
			$this->isLoaded = true;
		}
		
		return $this->data;
	}
	
	/**
	 * Commit the settings to the database
	 */
	public function commit()
	{
		$table = new Db\UserSettingsTable();
		$query = new InsertQuery($table->db());
		$query->into($table);
		$query->onDuplicateKeyUpdate(["value"]);
		
		foreach ($this->data as $key => $value) {
			$query->addRow([
				"userId" => $this->user->id(),
				"moduleName" => $this->module->name(),
				"key" => $key,
				"value" => $value
			]);
		}
		
		$query->execute();
	}
}