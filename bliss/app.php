<?php
require_once "core/Bliss/src/App/Container.php";

class BlissApp
{
	/**
	 * @return \Bliss\App\Container
	 */
	public static function create()
	{
		$instance = new Bliss\App\Container();
		$instance->autoloader()->registerNamespace("Bliss", __DIR__ ."/core/Bliss/src");
		$instance->modules()->registerModulesDirectory(__DIR__ ."/core");
		
		return $instance;
	}
}