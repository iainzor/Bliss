<?php
namespace User\Settings;

use Bliss\Module\AbstractModule;

class ModuleSettings
{
	/**
	 * @var Container
	 */
	private $container;
	
	/**
	 * @var AbstractModule
	 */
	private $module;
	
	/**
	 * @var Definitions
	 */
	private $definitions;
	
	/**
	 * @var Setting[]
	 */
	private $settings;
	
	/**
	 * Constructor
	 * 
	 * @param AbstractModule $module
	 */
	public function __construct(Container $container, AbstractModule $module)
	{
		$this->container = $container;
		$this->module = $module;
	}
	
	/**
	 * Get or set the setting definitions for the module
	 * 
	 * @param Definitions $definitions
	 * @return Definitions
	 */
	public function definitions(Definitions $definitions = null)
	{
		if ($definitions !== null) {
			$this->definitions = $definitions;
		}
		if (!$this->definitions) {
			$this->definitions = new Definitions();
		}
		return $this->definitions;
	}
	
	/**
	 * Get or set the settings for the module.
	 * 
	 * @param Setting[] $settings
	 * @return Setting[]
	 */
	public function settings(array $settings = null)
	{
		if ($settings !== null) {
			$this->settings = [];
			foreach ($settings as $setting) {
				$this->addSetting($setting);
			}
		}
		if (!$this->settings) {
			$this->settings = $this->definitions()->getDefaults();
		}
		return $this->settings;
	}
	
	/**
	 * Add a setting to the module
	 * 
	 * @param array|Setting $setting
	 * @throws \Exception
	 */
	public function addSetting($setting)
	{
		if (is_array($setting)) {
			$setting = Setting::factory($setting);
		}
		if (!($setting instanceof Setting)) {
			throw new \Exception("\$setting must be an array or properties or an instance of \\User\\Settings\\Setting");
		}
		$this->settings[$setting->key()] = $setting;
	}
	
	/**
	 * Get a setting's parsed value
	 * 
	 * @param string $key
	 * @param mixed $defaultValue The value returned if the setting does not exist
	 * @return mixed
	 */
	public function getValue($key, $defaultValue = null)
	{
		if (isset($this->settings[$key])) {
			$def = $this->definitions()->get($key);
			$setting = $this->settings[$key];
			
			return $def->parse($setting->value());
		}
		return null;
	}
	
	/**
	 * Set a single setting's value
	 * 
	 * @param string $key
	 * @param mixed $value
	 */
	public function setValue($key, $value)
	{
		if (isset($this->settings[$key])) {
			$this->settings[$key]->value($value);
		}
	}
	
	/**
	 * Save all settings within the module
	 */
	public function save()
	{
		$this->container->save($this);
	}
	
	/**
	 * Convert the module settings to an array
	 * 
	 * @return array
	 */
	public function toArray()
	{
		$data = [];
		foreach ($this->settings() as $setting) {
			$def = $this->definitions()->get($setting->key());
			$data[$setting->key()] = $def->parse($setting->value());
		}
		return $data;
	}
}