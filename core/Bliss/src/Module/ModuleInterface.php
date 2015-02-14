<?php
namespace Bliss\Module;

interface ModuleInterface
{
	public function init();
	
	/**
	 * @return string
	 */
	public function name();
	
	/**
	 * @param \Request\Module $request
	 */
	public function execute(\Request\Module $request);
	
	/**
	 * @return \Bliss\Application\Container
	 */
	public function app();
	
	/**
	 * @param string $controllerName
	 * @return \Bliss\Controller\AbstractController
	 */
	public function controller($controllerName);
	
	/**
	 * @param string $segment
	 * @return string
	 */
	public function resolvePath($segment);
}