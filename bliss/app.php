<?php
require_once "core/Bliss/src/App/Container.php";

/**
 * # Core modules
 * @method \Assets\Module assets() Assets module
 * @method \Bliss\Module bliss() Bliss module
 * @method \Config\Module config() Config module
 * @method \Error\Module error() Error module
 * @method \Request\Module request() Request module
 * @method \Response\Module response() Response module
 * @method \Router\Module router() Router module
 * @method \View\Module view() View module
 * 
 * # Development Modules
 * @method \Docs\Module docs() Docs module
 * @method \Tests\Module tests() Tests module
 */
class BlissApp extends \Bliss\App\Container
{
	/**
	 * @return \BlissApp
	 */
	public static function create()
	{
		$instance = new self();
		$instance->autoloader()->registerNamespace("Bliss", __DIR__ ."/core/Bliss/src");
		$instance->modules()->registerModulesDirectory(__DIR__ ."/core");
		
		return $instance;
	}
}