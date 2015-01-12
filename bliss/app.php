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
 * 
 * # Authentication Modules
 * @method \Acl\Module acl() ACL module
 */
class BlissApp extends \Bliss\App\Container
{
	/**
	 * @param string $name
	 * @param string $rootPath
	 * @return \BlissApp
	 */
	public static function create($name, $rootPath)
	{
		if (session_id() === "") {
			session_start();
		}
		
		$instance = new self($name, $rootPath);
		$instance->autoloader()->registerNamespace("Bliss", __DIR__ ."/core/Bliss/src");
		$instance->modules()->registerModulesDirectory(__DIR__ ."/core");
		$instance->modules()->registerModulesDirectory(__DIR__ ."/web");
		$instance->modules()->registerModulesDirectory(__DIR__ ."/auth");
		
		return $instance;
	}
}