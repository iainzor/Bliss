<?php
namespace User;

use Bliss\Module\AbstractModule,
	View\Partial\InjectorInterface,
	View\Partial\InjectableInterface,
	View\Partial\Partial,
	UnifiedUI\Module as UI,
	Config\PublicConfigInterface,
	Config\Config,
	Router\ProviderInterface as RouteProvider,
	Pages\ProviderInterface as PageProvider;

class Module extends AbstractModule implements InjectorInterface, PublicConfigInterface, RouteProvider, PageProvider
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
			$db = $this->app->database()->connection();
			$this->sessionManager = new Session\Manager(
				new Db\UserSessionsTable($db),
				new Db\UsersTable(),
				User::passwordHasher()
			);
		}
		return $this->sessionManager;
	}
	
	public function initSession()
	{
		$this->session = new Session\Session();
		$this->session->load();
		
		if ($this->session->id()) {
			$manager = $this->sessionManager();
			$manager->attachUser($this->session);
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
			"action" => "sign-in"
		])->when("/^sign-out\.?([a-z]+)?$/i", [
			1 => "format"
		], [
			"module" => "user",
			"controller" => "auth",
			"action" => "sign-out"
		]);
	}
	
	public function initPartialInjector(InjectableInterface $injectable) 
	{
		$accountWidget = new Partial($this->resolvePath("layouts/partials/user-menu-widget.html.phtml"));
		$injectable->inject(UI::AREA_MENU, $accountWidget, -1);
	}
	
	public function initPages(\Pages\Container $root) 
	{
		$pages = [
			[
				"title" => "Sign In",
				"path" => "sign-in"
			], [
				"title" => "Signing Out",
				"path" => "sign-out"
			], [
				"title" => "Create an Account",
				"path" => "sign-up"
			], [
				"title" => "Account Recovery",
				"path" => "account/recover"
			]
		];
		
		$root->add([
			[
				"id" => self::RESOURCE_NAME,
				"visible" => false,
				"pages" => $pages
			]
		]);
	}
	
	public function populatePublicConfig(Config $config) 
	{
		$session = $this->session();
		$config->setData($session->user()->toArray());
		return;
		
		if ($session->isValid()) {
			if ($session->user()) {
				$config->setData(
					$session->user()->toArray()
				);
			}
		}
	}
}