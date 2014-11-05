<?php
namespace Docs\Controller;

use Bliss\String;

class ModuleController extends \Bliss\Controller\AbstractController
{
	public function renderAction()
	{
		$request = $this->app->request();
		$moduleName = $request->param("moduleName");
		$actionName = $request->param("actionName");
		$module = $this->app->module($moduleName);
		$filename = $module->resolvePath("docs/{$actionName}.html");
		$contents = $this->_generateContents($filename);
		$pages = $this->_generatePages($module->resolvePath("docs"), $moduleName);
		$activePage = call_user_func(function($pages) {
			foreach ($pages as $page) {
				if ($page["active"]) {
					return $page;
				}
			}
		}, $pages);
		
		return [
			"id" => $module->name(),
			"label" => String::toCamelCase($module->name()),
			"contents" => $contents,
			"pages" => $pages,
			"page" => $activePage,
			"isEmpty" => !is_file($filename)
		];
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
		$currentAction = $request->param("actionName", "index");
		
		if (is_dir($dir)) {
			foreach (new \DirectoryIterator($dir) as $file) {
				if ($file->isDot()) {
					continue;
				}

				$basename = $file->getBasename(".html");
				$label = ($basename === "index") ? "Overview" : String::formatSentences($basename);

				if ($file->isFile() && $file->getExtension() === "html") {
					$pages[] = [
						"label" => $label,
						"path" => "docs/". $path ."/". $basename,
						"active" => $basename === $currentAction 
					];
				}

				$dir = $file->getPath() ."/". $basename;
				if (is_dir($dir)) {
					$pages = array_merge($pages, $this->_generatePages($file->getPathname(), $path ."/". $basename));
				}
			}

			usort($pages, function($a, $b) {
				if ($a["label"] === "Overview") {
					return -1;
				} else {
					return strcasecmp($a["label"], $b["label"]);
				}
			});
		}
		
		return $pages;
	}
}