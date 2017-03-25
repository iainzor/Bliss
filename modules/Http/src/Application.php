<?php
namespace Http;

$coreDir = dirname(dirname(__DIR__)) ."/Core";
require_once $coreDir ."/src/AbstractApplication.php";
require_once __DIR__ ."/Router.php";
require_once __DIR__ ."/Request.php";
require_once __DIR__ ."/Response.php";


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
	
	public function __construct() 
	{
		parent::__construct();
		
		$this->router = new Router();
		$this->request = new Request();
		$this->response = new Response();
	}
	
	protected function onStart() 
	{
		$this->moduleRegistry()->registerDirectory(dirname(__DIR__));
		
		$this->di()->register($this);
		$this->di()->register($this->router);
		$this->di()->register($this->request);		
	}
	
	protected function onStop()
	{}
	
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
	 * 
	 * @param string $uri
	 */
	public function run(string $uri) 
	{
		if (!$this->started) {
			parent::start();
		}
		
		$this->router->init($this);
		$this->request->init($uri, $this);
		
		$format = $this->request->format();
		try {
			$route = $this->router->find($this->request->uri());
		} catch (RouteNotFoundException $e) {
			$uri = preg_replace("/^(.*)(\.[a-z0-9]+)$/i", "\\1", $this->request->uri());
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