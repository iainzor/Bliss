<?php
namespace Http;

$coreDir = dirname(dirname(__DIR__)) . DIRECTORY_SEPARATOR . "Core";
require_once $coreDir . DIRECTORY_SEPARATOR . "src" . DIRECTORY_SEPARATOR . "AbstractApplication.php";

class Application extends \Core\AbstractApplication
{
	/**
	 * @var Router
	 */
	private $router;
	
	/**
	 *
	 * @var Request
	 */
	private $request;
	
	/**
	 * Bootstrap the HTTP application
	 */
	protected function bootstrap() 
	{
		$this->moduleRegistry()->registerDirectory(dirname(__DIR__));
		
		$this->router = new Router();
		$this->request = new Request();
		
		$this->di()->register($this);
		$this->di()->register($this->router);
		$this->di()->register($this->request);		
	}
	
	/**
	 * Get the application's router instance
	 * 
	 * @return \Http\Router
	 */
	public function router() : Router
	{
		return $this->router;
	}
	
	public function routeCaller(Route $route) : RouteCaller
	{
		return new RouteCaller($this, $route);
	}

	public function run() 
	{
		$route = $this->router->find($this->request->uri());
		$caller = $this->routeCaller($route);
		$body = $caller->execute();
		
		header("Content-type: application/json");
		echo json_encode($body);
	}
}