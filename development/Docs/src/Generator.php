<?php
namespace Docs;

use Bliss\App\Container as App,
	Bliss\String,
	Bliss\Module\ModuleInterface;

class Generator
{
	public static function generateModuleList(App $app)
	{
		$modules = [];
		
		foreach ($app->modules() as $module) {
			if (is_dir($module->resolvePath("docs"))) {
				$modules[] = self::generateModule($module);
			}
		}
		
		usort($modules, function($a, $b) {
			if ($a["id"] === "bliss") {
				return -1;
			}
			
			if ($b["id"] === "bliss") {
				return 1;
			}
			
			return strcasecmp($a["label"], $b["label"]);
		});
		
		return $modules;
	}
	
	public static function generateModule(ModuleInterface $module, $actionName = null)
	{
		$pages = self::_generatePages($module->resolvePath("docs"), $module->name(), $actionName);
		$module = [
			"id" => $module->name(),
			"label" => String::toCamelCase($module->name()),
			"path" => $pages[0]["path"],
			"pages" => $pages
		];
		
		return $module;
	}
	
	private static function _generatePages($dir, $path = "", $activeAction = null) 
	{
		$pages = [];
		
		if (is_dir($dir)) {
			$hasActive = false;
			
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
				
				$action = $path ."/". $fullname;
				$uri = "docs/modules/{$action}";
				
				
				if ($file->isFile() && $file->getExtension() === "html") {
					$active = $action === $activeAction;
					$pages[] = [
						"label" => $label,
						"path" => $uri,
						"action" => $fullname,
						"order" => $order,
						"active" => $active,
						"file" => $file->getBasename()
					];
					
					if ($active) {
						$hasActive = true;
					}
				}

				$dir = $file->getPath() ."/". $basename;
				if (is_dir($dir)) {
					$pages = array_merge($pages, self::_generatePages($file->getPathname(), $uri, $action));
				}
			}
			
			if (!$hasActive && count($pages)) {
				$pages[0]["active"] = true;
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