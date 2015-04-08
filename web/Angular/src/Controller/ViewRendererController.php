<?php
namespace Angular\Controller;

use Bliss\Controller\AbstractController;

class ViewRendererController extends AbstractController 
{
	public function renderAction(\Response\Module $response)
	{
		$moduleName = $this->param("moduleName");
		$module = $this->app->module($moduleName);
		$type = $this->param("type") === "views" ? "" : "directive/";
		$filename = $type . $this->param("path") .".". $this->param("format");
		$filepath = $module->resolvePath("views/{$filename}.phtml");
		
		if (!is_file($filepath)) {
			throw new \Exception("Could not find directive: {$filename}");
		}
		
		$response->lastModified(new \DateTime(date("Y-m-d H:i:s", filectime($filepath))));
		$response->cache(true);
		
		$this->app->debugMode(false);
		
		return file_get_contents($filepath);
	}
}