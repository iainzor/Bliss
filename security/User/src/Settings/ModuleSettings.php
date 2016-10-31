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
	 * @var string
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
	 * @param string $module
	 */
	public function __construct(Container $container, $module)
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
			$this->populate($definitions);
		}
		if (!$this->definitions) {
			$this->definitions = new Definitions();
		}
		return $this->definitions;
	}
	
	/**
	 * Populate the settings container with a set of default settings.
	 * This will only set default settings if they are not already set.
	 * 
	 * @param \User\Settings\Definitions $definitions
	 */
	public function populate(Definitions $definitions)
	{
		foreach ($definitions->getDefaults() as $default) {
			$key = $default->key();
			if (!isset($this->settings[$key])) {
				$this->settings[$key] = $default;
			}
		}
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
		foreach ($this->settings as $setting) {
			$setting->userId($this->container->user()->id());
			$setting->moduleName($this->module);
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
			$setting = $this->settings[$key];
			
			return $setting->value();
		}
		return $defaultValue;
	}
	
	/**
	 * Set a single setting's value
	 * 
	 * @param string $key
	 * @param mixed $value
	 * @param boolean $encoded Wether the value being set is encoded
	 */
	public function setValue($key, $value, $encoded = false)
	{
		if (!isset($this->settings[$key])) {
			$this->settings[$key] = $this->definitions()->get($key)->toSetting();
		}
		
		$this->settings[$key]->value($value, $encoded);
	}
	
	/**
	 * Save all settings within the module
	 */
	public function save()
	{
		$this->container->save($this);
	}
	
	/**
	 * Merge a setting definition into this one
	 * 
	 * @param ModuleSettings|array $settings
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
		foreach ($settings as $name => $value) {
			$this->setValue($name, $value);
		}
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
			$data[$setting->key()] = $setting->value();
		}
		return $data;
	}
}