<?php
namespace Assets\Compiler\File;

class Collection
{
	/**
	 * @var array
	 */
	private $files = [];
	
	/**
	 * Get all file names in the collection
	 * 
	 * @return array
	 */
	public function files()
	{
		return $this->files;
	}
	
	/**
	 * Add a file path to the collection
	 * 
	 * @param string $filepath
	 */
	public function add($filepath) 
	{
		$this->files[] = $filepath;
	}
	
	/**
	 * Recursively add files from a directory
	 * 
	 * @param string $dirname
	 */
	public function collectFromDir($dirname)
	{
		$do = new \DirectoryIterator($dirname);
		$foundDirs = [];
		foreach ($do as $file) {
			$filepath = $file->getPathname();
			
			if ($file->isFile()) {
				$this->add($filepath);
				
				$dir = preg_replace("/^(.*)\.[a-z0-9]+$/i", "\\1", $filepath);
				if (is_dir($dir)) {
					$foundDirs[] = str_replace("\\", "/", $dir);
					$this->collectFromDir($dir);
				}
			}
		}
		
		foreach ($do as $dir) {
			$dirpath = str_replace("\\", "/", $dir->getPathname());
			
			if (!$dir->isDot() && $dir->isDir() && !in_array($dirpath, $foundDirs)) {
				$this->collectFromDir($dir->getPathname());
			}
		}
	}
}