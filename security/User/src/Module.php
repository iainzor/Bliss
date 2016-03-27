<?php
namespace User;

use Bliss\Module\AbstractModule,
	View\Partial\InjectorInterface,
	View\Partial\InjectableInterface,
	View\Partial\Partial,
	UnifiedUI\Module as UI,
	Config\PublicConfigInterface,
	Router\ProviderInterface as RouteProvider;

class Module extends AbstractModule implements InjectorInterface, PublicConfigInterface, RouteProvider
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
	 * @var RoleRegistry
	 */
	private $roleRegistry;

	/**
	 * Get the user instance
	 * 
	 * @return User
	 */
	public function user() {
		return $this->session()->user();
	}
	
	/**
	 * Get the user session
	 * 
	 * @return \User\Session\SessionInterface
	 */
	public function session()
	{
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
	 * 
	 * Once complete, each module in the system that implements UserSessionProcessorInterface will be executed
	 */
	public function initSession()
	{
		$this->session = new Session\Session();
		$this->session->load();
		
		if ($this->session->id()) {
			$manager = $this->sessionManager();
			$manager->attachUser($this->session);
		}
		
		foreach ($this->app->modules() as $module) {
			if ($module instanceof UserSessionProcessorInterface) {
				$module->processUserSession($this->session);
			}
		}
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
	
	public function initPartialInjector(InjectableInterface $injectable) 
	{
		$accountWidget = new Partial($this->resolvePath("layouts/partials/user-menu-widget.html.phtml"));
		$injectable->inject(UI::AREA_MENU, $accountWidget, -1);
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
			$registry->configure($roles);
		}
		return $registry;
	}
	
	/**
	 * Get or set the role registry
	 * @param \User\RoleRegistry $registry
	 * @return type
	 */
	public function roleRegistry(RoleRegistry $registry = null)
	{
		if ($registry !== null) {
			$this->roleRegistry = $registry;
		}
		if (!$this->roleRegistry) {
			$this->roleRegistry = new RoleRegistry();
		}
		
		Role::registry($this->roleRegistry);
		
		return $this->roleRegistry;
	}
}
