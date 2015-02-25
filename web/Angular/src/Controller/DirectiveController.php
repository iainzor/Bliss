<?php
namespace Angular\Controller;

use Bliss\Controller\AbstractController;

class DirectiveController extends AbstractController 
{
	public function renderAction()
	{
		$moduleName = $this->param("moduleName");
		$module = $this->app->module($moduleName);
		$filename = $this->param("path") .".". $this->param("format");
		$filepath = $module->resolvePath("views/directives/{$filename}.phtml");
		
		if (!is_file($filepath)) {
			throw new \Exception("Could not find directive: {$filename}");
		}
		
		$this->app->debug(false);
		
		return file_get_contents($filepath);
	}
}