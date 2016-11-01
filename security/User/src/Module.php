<?php
namespace User;

use Acl\RoleRegistry,
	Bliss\Module\AbstractModule,
	Bliss\BeforeModuleExecuteInterface,
	Config\PublicConfigInterface,
	Router\ProviderInterface as RouteProvider;

class Module extends AbstractModule implements 
	BeforeModuleExecuteInterface,
	PublicConfigInterface, 
	RouteProvider, 
	Settings\SettingsProviderInterface
{
	const RESOURCE_NAME = "user-module";
	
	/**
	 * @var \User\Session\Session
	 */
	private $session;
	
	/**
	 * @var \User\Session\Manager
	 */
	private $sessionManager;
	
	/**
	 * @var array
	 */
	private $cookieConfig = [];
	
	/**
	 * @var RoleRegistry
	 */
	private $roleRegistry;
	
	/**
	 * Initialize the session before executing active module
	 * 
	 * @param AbstractModule $module
	 */
	public function beforeModuleExecute(AbstractModule $module) 
	{
		if (!$this->session) {
			$this->initSession();
		}
	}
	
	/**
	 * Get the user instance
	 * 
	 * @return User
	 */
	public function user() 
	{
		return $this->session()->user();
	}
	
	/**
	 * Set the user session configuration
	 * 
	 * @param array $config
	 */
	public function sessionConfig(array $config)
	{
		$this->sessionManager()->config($config);
	}
	
	/**
	 * Get the user session
	 * 
	 * @return \User\Session\SessionInterface
	 */
	public function session(Session\SessionInterface $session = null)
	{
		if ($session !== null) {
			$this->session = $session;
		}
		if (!isset($this->session)) {
			$this->initSession();
		}
		return $this->session;
	}
	
	/**
	 * Get or set the user session manager
	 * 
	 * @param \User\Session\Manager $manager
	 * @return \User\Session\Manager
	 */
	public function sessionManager(Session\Manager $manager = null) 
	{
		if ($manager !== null) {
			$this->sessionManager = $manager;
			$this->session = null;
		}
		if (!isset($this->sessionManager)) {
			$this->sessionManager = new Session\Manager(
				User::passwordHasher()
			);
		}
		return $this->sessionManager;
	}
	
	/**
	 * Initializes the user's session and attempts to attach an authenticated user
	 */
	public function initSession()
	{	
		$modules = $this->app->modules();
		$manager = $this->sessionManager();
		$this->session = $manager->loadSession();
		
		foreach ($modules as $module) {
			if ($module instanceof BeforeSessionCheckInterface) {
				$module->beforeSessionCheck($this);
			}
		}
		
		if ($this->session->id()) {
			$manager = $this->sessionManager();
			$manager->attachUser($this->session);
		}
		
		foreach ($modules as $module) {
			if ($module instanceof AfterSessionCheckInterface) {
				$module->afterSessionCheck($this->session);
			}
		}
		
		$user = $this->session->user();
		
		// Load role permissions
		$role = $this->roleRegistry()->role($user->roleId());
		$user->role($role);
		
		// Define user settings
		$settings = $user->settings();
		foreach ($modules as $module) {
			if ($module instanceof Settings\SettingsProviderInterface) {
				$moduleSettings = $settings->module($module);
				$module->defineUserSettings(
					$moduleSettings->definitions()
				);
			}
		}
		
		$settings->load();
	}
	
	public function initRouter(\Router\Module $router) 
	{
		$router->when("/^users\.?(json)?$/i", [
			1 => "format"
		], [
			"module" => "user",
			"controller" => "users",
			"action" => "all",
			"element" => "user-list"
		])->when("/^users\/([a-z-_]+)\.json$/i", [
			1 => "action"
		], [
			"module" => "user",
			"controller" => "users",
			"format" => "json"
		])->when("/^users\/([0-9]+)\.?(json)?$/i", [
			1 => "userId",
			2 => "format"
		], [
			"module" => "user",
			"controller" => "user",
			"action" => "index",
			"element" => "user-profile"
		])->when("/^sign-in\.?([a-z]+)?$/", [
			1 => "format"
		], [
			"module" => "user",
			"controller" => "auth",
			"action" => "sign-in",
			"element" => "user-login"
		])->when("/^sign-up\.json$/i", [], [
			"module" => $this->name(),
			"controller" => "auth",
			"action" => "sign-up",
			"format" => "json"
		])->when("/^sign-out\.?([a-z]+)?$/i", [
			1 => "format"
		], [
			"module" => "user",
			"controller" => "auth",
			"action" => "sign-out"
		])->when("/^account\/?([a-z0-9-]+)?\.?([a-z]+)?$/i", [
			1 => "action",
			2 => "format"
		], [
			"module" => "user",
			"controller" => "account",
			"action" => "index"
		]);
	}
	
	public function populatePublicConfig(\Config\Config $config) 
	{
		$session = $this->session();
		$config->setData($session->user()->toArray());
	}
	
	/**
	 * Configure multiple user roles
	 * 
	 * @param array $roles
	 * @return RoleRegistry
	 */
	public function roles(array $roles = null)
	{
		$registry = $this->roleRegistry();
		
		if ($roles !== null) {
			$registry->registerAll($roles);
		}
		
		return $registry;
	}
	
	/**
	 * Get or set the role registry
	 * @param RoleRegistry $registry
	 * @return RoleRegistry
	 */
	public function roleRegistry(RoleRegistry $registry = null)
	{
		if ($registry !== null) {
			$this->roleRegistry = $registry;
		}
		if (!$this->roleRegistry) {
			$this->roleRegistry = new RoleRegistry(new GuestRole());
		}
		
		return $this->roleRegistry;
	}
	
	/**
	 * Define the user settings for the user module
	 * 
	 * @param \User\Settings\Definitions $definitions
	 */
	public function defineUserSettings(Settings\Definitions $definitions) 
	{
		$definitions->set([
			[
				"key" => "twoFactorAuth",
				"defaultValue" => false,
				"valueParser" => "boolval"
			]
		]);
	}
}
