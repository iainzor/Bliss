<?php
namespace Docs\Controller;

use Docs\Generator;

class ModulesController extends \Bliss\Controller\AbstractController
{
	public function indexAction()
	{
		$modules = Generator::generateModuleList($this->app);
		
		return $modules;
	}
}