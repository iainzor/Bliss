<?php
namespace Pages;

use Bliss\Module\AbstractModule,
	Config\Config,
	Config\PublicConfigInterface,
	Router\ProviderInterface as RouteProvider;

class Module extends AbstractModule implements PublicConfigInterface, RouteProvider
{
	/**
	 * @var \Pages\Container
	 */
	private $root;
	
	/**
	 * @var boolean
	 */
	private $compiled = false;
	
	public function initRouter(\Router\Module $router) 
	{
		$router->when("/^sitemap\.xml$/i", [], [
			"module" => "pages",
			"controller" => "sitemap",
			"action" => "render",
			"format" => "xml"
		]);
	}
	
	/**
	 * Get the root page container
	 * 
	 * @return \Pages\Container
	 */
	public function pages()
	{
		if (!$this->compiled) {
			$this->compiled = true;
			$this->root = $this->compile();
		}
		
		return $this->root;
	}
	
	/**
	 * Generate a public configuration object for all
	 * registered pages
	 * 
	 * @param \Config\Config
	 */
	public function populatePublicConfig(Config $config) 
	{
		$config->setData($this->pages()->toArray());
	}
	
	/**
	 * Compile pages from all registered modules
	 * 
	 * @return \Pages\Container
	 */
	private function compile()
	{
		$container = new Container();
		foreach ($this->app->modules() as $module) {
			if ($module instanceof ProviderInterface) {
				$module->initPages($container);
			}
		}
		return $container;
	}
}