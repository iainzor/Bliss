<?php
namespace Assets\Compiler;

use Config\Config;

class Container 
{
	/**
	 * @var \Config\Config
	 */
	private $config;
	
	/**
	 * Constructor
	 * 
	 * @param \Assets\Compiler\Config $config
	 */
	public function __construct(Config $config)
	{
		$this->config = $config;
	}
	
	/**
	 * Check if the compiler is enabled
	 * 
	 * @return boolean
	 */
	public function isEnabled()
	{
		return $this->config->get("enabled", false);
	}
	
	/**
	 * Compile a file or collection of files
	 * 
	 * If $filepath is a path to a file, that file and all files under the directory
	 * with the same name will be compiled.  For example, if the path is my-asset.css
	 * the compiler will first look for that file and then recursively compile all files
	 * within the /my-asset directory
	 * 
	 * @param string $filepath
	 * @return \Assets\Compiler\File\File
	 */
	public function compile($filepath)
	{
		$collection = new File\Collection();
		$dir = preg_replace("/^(.*)\.[a-z0-9]+$/i", "\\1", $filepath);
		$contents = "";
		
		if (is_file($filepath)) {
			$collection->add($filepath);
		}
		
		if (is_dir($dir)) {
			$collection->collectFromDir($dir);
		}
		
		foreach ($collection->files() as $filepath) {
			$contents .= "/* {$filepath} */\n";
			$contents .= file_get_contents($filepath) ."\n\n";
		}
		
		return new File\File($contents);
	}
}