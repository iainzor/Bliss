<?php
namespace User\Settings;

use Bliss\Module\AbstractModule,
	User\User,
	User\Db\UserSettingsTable,
	Database\Query\InsertQuery;

class Container
{
	/**
	 * @var User
	 */
	private $user;

	/**
	 * @var ModuleSettings
	 */
	private $modules = [];
	
	/**
	 * Constructor
	 * 
	 * @param User $user
	 */
	public function __construct(User $user)
	{
		$this->user($user);
	}
	
	/**
	 * Get or set the user the settings belong to
	 * 
	 * @param User $user
	 * @return User
	 */
	public function user(User $user = null)
	{
		if ($user !== null) {
			$this->user = $user;
		}
		return $this->user;
	}
	
	/**
	 * 
	 * @param AbstractModule|string $module
	 * @param ModuleSettings $settings
	 * @return ModuleSettings
	 */
	public function module($module, ModuleSettings $settings = null) 
	{
		if ($module instanceof AbstractModule) {
			$module = $module->name();
		}
		
		if ($settings !== null) {
			$this->modules[$module] = $settings;
		}
		if (!isset($this->modules[$module])) {
			$this->modules[$module] = new ModuleSettings($this, $module);
		}
		return $this->modules[$module];
	}
	
	/**
	 * Load an populate all settings for all modules
	 * 
	 * @throws \Exception
	 */
	public function load()
	{
		if (!$this->user->id()) {
			return;
		}
		
		$table = new UserSettingsTable();
		$query = $table->select();
		$query->where([
			"userId" => $this->user->id()
		]);
		$results = $query->fetchAll();
		
		foreach ($results as $setting) {
			$moduleName = $setting->moduleName();
			if (!isset($this->modules[$moduleName])) {
				continue;
				//throw new \Exception("Module '{$moduleName}' has no defined settings");
			}
			
			$moduleSettings = $this->modules[$moduleName];
			$moduleSettings->setValue($setting->key(), $setting->value(), true);
		}
	}
	
	/**
	 * Save settings to the database
	 */
	public function save()
	{
		$table = new UserSettingsTable();
		$query = new InsertQuery($table->db());
		$query->into($table);
		
		$rows = [];
		foreach ($this->modules as $module) {
			foreach ($module->settings() as $setting) {
				$setting->userId($this->user->id());
				
				$row = $setting->toArray();
				$row["value"] = $setting->encodedValue();
				$rows[] = $row;
			}
		}
		
		
		$query->rows($rows);
		$query->onDuplicateKeyUpdate(["value"]);
		$query->execute();
	}
	
	/**
	 * Get a single setting's value
	 * 
	 * @param AbstractModule|string $module
	 * @param string $key
	 * @return mixed
	 * @throws \Exception
	 */
	public function getValue($module, $key) 
	{
		if ($module instanceof AbstractModule) {
			$module = $module->name();
		}
		
		if (!isset($this->modules[$module])) {
			throw new \Exception("No settings for module: {$module}");
		}
		$modSettings = $this->modules[$module];
		return $modSettings->getValue($key);
	}
	
	/**
	 * Merge a settings definition into the container
	 * 
	 * @param Container|array $settings
	 * @throws \InvalidArgumentException
	 */
	public function merge($settings)
	{
		if ($settings instanceof self) {
			$settings = $settings->toArray();
		}
		if (!is_array($settings)) {
			throw new \InvalidArgumentException("\$settings must be an array");
		}
		foreach ($settings as $moduleName => $moduleSettings) {
			$this->module($moduleName)->merge($moduleSettings);
		}
	}
	
	/**
	 * Convert all settings to an array
	 * 
	 * @return array
	 */
	public function toArray()
	{
		$data = [];
		foreach ($this->modules as $name => $module) {
			$data[$name] = $module->toArray();
		}
		return $data;
	}
}