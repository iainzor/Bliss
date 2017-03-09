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
	 * @var Response
	 */
	private $response;
	
	/**
	 * Bootstrap the HTTP application
	 */
	protected function bootstrap() 
	{
		$this->moduleRegistry()->registerDirectory(dirname(__DIR__));
		
		$this->router = new Router();
		$this->request = new Request();
		$this->response = new Response();
		
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
	
	/**
	 * Run the application and output the result
	 */
	public function run() 
	{
		$this->request->init($this);
		
		$format = $this->request->format();
		try {
			$route = $this->router->find($this->request->uri());
		} catch (RouteNotFoundException $e) {
			$uri = preg_replace("/^(.*)\.". $format->extension() ."$/i", "\\1", $this->request->uri());
			$route = $this->router->find($uri);
		}
		
		$this->di()->register($route);
		
		$result = $this->execute(
			$route->module(), 
			$route->controller(),
			$route->action(), 
			$route->params()
		);
		$body = $format->parse($result);
		
		$this->response->header("Content-Type: ". $format->mimeType());
		$this->response->body($body);
		$this->response->output();
	}
}