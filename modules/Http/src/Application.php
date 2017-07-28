<?php
namespace Http;

$coreDir = dirname(dirname(__DIR__)) ."/Core";
require_once $coreDir ."/src/AbstractApplication.php";
require_once __DIR__ ."/ErrorHandler.php";
require_once __DIR__ ."/Request.php";
require_once __DIR__ ."/Response.php";
require_once __DIR__ ."/Router.php";
require_once __DIR__ ."/RouteProviderInterface.php";


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
	 * @var ErrorHandler
	 */
	private $errorHandler;
	
	/**
	 * Constructor
	 * 
	 * @param string $uri
	 */
	public function __construct(string $uri) 
	{
		parent::__construct();
		
		$this->moduleRegistry()->registerDirectory(dirname(__DIR__));
		
		$this->router = new Router($this);
		$this->request = new Request($uri);
		$this->response = new Response($this);
	}
	
	protected function onStart() 
	{
		$this->moduleRegistry()->registerDirectory(dirname(__DIR__));
		
		$this->di()->register($this);
		$this->di()->register($this->router);
		$this->di()->register($this->request);
		
		$this->errorHandler = $this->di()->get(ErrorHandler::class);
		$this->errorHandler->attach();
		
		$this->request->setFormatRegistry(
			$this->di()->get(Format\FormatRegistry::class)
		);
	}
	
	protected function onStop()
	{
		$this->errorHandler->detach();
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
	 * 
	 * @param string $uri
	 */
	public function run() 
	{
		if (!$this->started) {
			parent::start();
		}
		
		$this->router->init($this);
		
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