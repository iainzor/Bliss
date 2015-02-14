<?php
namespace View;

use Response\Format\FormatInterface;

class Module extends \Bliss\Module\AbstractModule
{
	/**
	 * @var \View\Decorator\Registry
	 */
	private $decorators;
	
	/**
	 * @var boolean
	 */
	private $initInjectables = false;
	
	/**
	 * Attempt to render data for a set of paramters and return the resulting string
	 * 
	 * @param \Request\Module $request
	 * @param array $params Additional parameters to pass to the view
	 * @return string
	 */
	public function render(\Request\Module $request, array $params = [])
	{	
		if (!$this->initInjectables) {
			$this->initInjectables = true;
			$this->_initInjectables();
		}
		
		$moduleName = $request->getModule();
		$controllerName = $request->getController();
		$actionName = $request->getAction();
		$formatName = $request->getFormat();
		$module = $this->app->module($moduleName);
		$extension = $formatName === null ? "html.phtml" : "{$formatName}.phtml";
		$viewpath = $module->resolvePath(sprintf("views/%s/%s.%s",
			$controllerName,
			$actionName,
			$extension
		));
		$partial = new Partial\Partial($viewpath, $this->app);
		$contents = $partial->render($params);
		
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
	 * Run any decorators for a format on the contents provided
	 *  
	 * @param string $contents
	 * @param array $params
	 * @param \Response\Format\FormatInterface $format
	 * @return string
	 */
	public function decorate($contents, array $params, FormatInterface $format)
	{
		$decorators = $this->decorators()->belongingTo($format);
		
		foreach ($decorators as $decorator) {
			$this->app->log("Running decorator: ". get_class($decorator));
			$contents = $decorator->decorate($contents, $params);
		}
		
		return $contents;
	}
	
	/**
	 * Compile decorators from all available modules
	 */
	private function _compileDecorators()
	{
		$this->app->log("Compiling view decorators");
		$this->decorators = new Decorator\Registry();
		
		foreach ($this->app->modules() as $module) {
			if ($module instanceof Decorator\ProviderInterface) {
				$this->app->log("----Initializing decorators from module '{$module->name()}'");
				$module->initViewDecorator($this->decorators);
			}
		}
	}
	
	/**
	 * Initialize all injectable modules
	 */
	private function _initInjectables()
	{
		foreach ($this->app->modules() as $module) {
			if ($module instanceof Partial\InjectableInterface) {
				$module->compileInjectables();
			}
		}
	}
}