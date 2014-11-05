<?php
namespace View;

class Module extends \Bliss\Module\AbstractModule
{
	/**
	 * @var \View\Decorator\Registry
	 */
	private $decorators;
	
	/**
	 * Attempt to render data for a set of paramters and return the resulting string
	 * 
	 * @param \Request\Module $request
	 * @param array $params Additional parameters to pass to the view
	 * @return string
	 * @throws \Exception
	 * @throws \Exception
	 */
	public function render(\Request\Module $request, array $params = [])
	{	
		$response = $this->app->response();
		$moduleName = $request->getModule();
		$controllerName = $request->getController();
		$actionName = $request->getAction();
		$formatName = $request->getFormat();
		$format = $response->format($formatName);
		$module = $this->app->module($moduleName);
		$extension = $formatName === null ? "phtml" : "{$formatName}.phtml";
		$viewpath = $module->resolvePath(sprintf("views/%s/%s.%s",
			$controllerName,
			$actionName,
			$extension
		));
		$partial = new Partial($viewpath, $this->app);
		$contents = $partial->render($params);
		$decorators = $this->decorators()->belongingTo($format);
		
		foreach ($decorators as $decorator) {
			$contents = $decorator->decorate($contents, $params);
		}
		
		return $contents;
	}
	
	/**
	 * Get the view decorator registry
	 * 
	 * @return \View\Decorator\Registry
	 */
	public function decorators()
	{
		if (!isset($this->decorators)) {
			$this->_compileDecorators();
		}
		
		return $this->decorators;
	}
	
	/**
	 * Compile decorators from all available modules
	 */
	private function _compileDecorators()
	{
		$this->decorators = new Decorator\Registry();
		
		foreach ($this->app->modules() as $module) {
			if ($module instanceof Decorator\ProviderInterface) {
				$module->initViewDecorator($this->decorators);
			}
		}
	}
}