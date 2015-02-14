<?php
namespace Docs\Controller;

use Bliss\String,
	Docs\Generator;

class ModuleController extends \Bliss\Controller\AbstractController
{
	public function indexAction()
	{
		return [];
	}
	
	public function renderAction()
	{
		$request = $this->app->request();
		$moduleName = $request->param("moduleName");
		$actionName = $request->param("actionName");
		$module = $this->app->module($moduleName);
		
		$moduleData = Generator::generateModule($module, "{$moduleName}/{$actionName}");
		$moduleData["page"] = call_user_func(function($pages) {
			foreach ($pages as $page) {
				if ($page["active"]) {
					return $page;
				}
			}
		}, $moduleData["pages"]);
		
		
		if (!isset($actionName)) {
			$actionName = $moduleData["page"]["action"];
		}
		
		$filename = $module->resolvePath("docs/{$actionName}.html");
		$contents = $this->_generateContents($filename);
		$moduleData["contents"] = $contents;
		
		return $moduleData;
	}
	
	/**
	 * Attempt to load the contents from the path provided
	 * 
	 * @param string $path
	 * @return string
	 */
	private function _generateContents($path)
	{
		if (!is_file($path)) {
			$path = $this->module->resolvePath("views/module/no-docs.html");
		}
		
		return file_get_contents($path);
	}
	
	private function _generatePages($dir, $path = "") 
	{
		$pages = [];
		$request = $this->app->request();
		$currentAction = $request->param("actionName");
		
		if (is_dir($dir)) {
			foreach (new \DirectoryIterator($dir) as $file) {
				if ($file->isDot()) {
					continue;
				}

				$fullname = $file->getBasename(".html");
				$basename = $fullname;
				$order = 100;
				
				if (preg_match("/^([0-9]+)\-(.*)$/i", $fullname, $matches)) {
					$order = (int) $matches[1];
					$basename = $matches[2];
				}
				
				if ($basename === "index") {
					$label = "Overview";
					$order = 0;
				} else {
					$label = String::unhyphenate($basename);
				}
				
				$uri = "docs/". $path ."/". $fullname;
				$foundActive = false;

				if ($file->isFile() && $file->getExtension() === "html") {
					$active = $fullname === $currentAction;
					$pages[] = [
						"label" => $label,
						"path" => $uri,
						"active" => $active,
						"order" => $order
					];
					
					if ($active) {
						$foundActive = true;
					}
				}
				
				if (!$foundActive && count($pages)) {
					$pages[0]["active"] = true;
				}

				$dir = $file->getPath() ."/". $basename;
				if (is_dir($dir)) {
					$pages = array_merge($pages, $this->_generatePages($file->getPathname(), $uri));
				}
			}

			usort($pages, function($a, $b) {
				if ($a["order"] === $b["order"]) {
					return strcasecmp($a["label"], $b["label"]);
				}
				
				return $a["order"] < $b["order"] ? -1 : 1;
			});
		}
		
		return $pages;
	}
}