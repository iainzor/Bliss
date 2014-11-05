<?php
namespace Docs\Controller;

use Bliss\String;

class ModulesController extends \Bliss\Controller\AbstractController
{
	public function indexAction()
	{
		$modules = [];
		
		foreach ($this->app->modules() as $module) {
			$modules[] = [
				"id" => $module->name(),
				"label" => String::toCamelCase($module->name()),
				"path" => "docs/{$module->name()}"
			];
		}
		
		return $modules;
	}
}