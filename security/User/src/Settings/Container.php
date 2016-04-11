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
		$this->user = $user;
	}
	
	/**
	 * 
	 * @param AbstractModule $module
	 * @param ModuleSettings $settings
	 * @return ModuleSettings
	 */
	public function module(AbstractModule $module, ModuleSettings $settings = null) 
	{
		if ($settings !== null) {
			$this->modules[$module->name()] = $settings;
		}
		if (!isset($this->modules[$module->name()])) {
			$this->modules[$module->name()] = new ModuleSettings($this, $module);
		}
		return $this->modules[$module->name()];
	}
	
	/**
	 * Load an populate all settings for all modules
	 * 
	 * @throws \Exception
	 */
	public function load()
	{
		$table = new UserSettingsTable();
		$query = $table->select();
		$query->where([
			"userId" => $this->user->id()
		]);
		$results = $query->fetchAll();
		
		foreach ($results as $setting) {
			$moduleName = $setting->moduleName();
			if (!isset($this->modules[$moduleName])) {
				throw new \Exception("Module '{$moduleName}' has no defined settings");
			}
			
			$moduleSettings = $this->modules[$moduleName];
			$moduleSettings->addSetting($setting);
		}
	}
	
	/**
	 * Save settings to the database.  If no ModuleSettings instance is provided, 
	 * all settings for all modules will be saved.
	 * 
	 * @param \User\Settings\ModuleSettings $moduleSettings
	 */
	public function save(ModuleSettings $moduleSettings = null)
	{
		$table = new UserSettingsTable();
		$query = new InsertQuery($table->db());
		$query->into($table);
		
		if ($moduleSettings !== null) {
			$settings = $moduleSettings->settings();
		} else {
			$settings = [];
			foreach ($this->modules as $module) {
				$settings = array_merge($settings, $module->settings());
			}
		}
		
		$query->rows($settings);
		$query->onDuplicateKeyUpdate(["value"]);
		$query->execute();
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