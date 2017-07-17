<?php
namespace Cache\Driver;

use Cache\DriverConfig;

class FileSystem implements DriverInterface 
{
	const CONFIG_ROOTDIR = "fileSystem.config.rootDir";
	
	/**
	 * @var string
	 */
	private $rootDir;
	
	/**
	 * @param DriverConfig $config
	 * @throws \Exception
	 */
	public function configure(DriverConfig $config)
	{
		$this->rootDir = $config->get(self::CONFIG_ROOTDIR);
		
		if (!is_writable($this->rootDir)) {
			throw new \Exception("File cache root directory is not writable");
		}
	}
	
	/**
	 * @param string $key
	 * @return mixed
	 */
	public function get(string $key) 
	{
		$path = $this->filepath($key);
		$content = false;
		
		if (is_file($path)) {
			$raw = file_get_contents($path);
			$data = unserialize($raw);
			$expires = $data["expires"];
			
			if ($expires < time()) {
				unset($path);
			} else {
				$content = $data["content"];
			}
		}
		
		return $content;
	}

	/**
	 * @param string $key
	 * @param mixed $data
	 * @param int $expires
	 */
	public function put(string $key, $content, int $expires) 
	{
		$path = $this->filepath($key);
		$data = [
			"expires" => $expires,
			"content" => $content
		];
		$raw = serialize($data);
		
		file_put_contents($path, $raw);
	}
	
	/**
	 * @param string $key
	 * @return string
	 */
	private function filepath(string $key) : string
	{
		return $this->rootDir . DIRECTORY_SEPARATOR .  md5($key);
	}
}