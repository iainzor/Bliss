<?php
spl_autoload_register(function($className) {
	static $xml = null;
	
	if ($xml === null) {
		$xml = new SimpleXMLElement(file_get_contents("./cache/tests/config.xml"));
	}
	$dirs = [];

	foreach ($xml->testsuite as $module) {
		$name = (string) $module->attributes()->name;
		$dirs[$name] = (string) $module->directory;
	}
	
	$parts = explode("\\", $className);
	$namespace = array_shift($parts);
	$name = call_user_func(function($namespace) {
		$chars = str_split($namespace);
		$name = "";
		
		foreach ($chars as $i => $char) {
			if ($char === ucwords($char) && $i > 0) {
				$name .= "-";
			}
			
			$name .= strtolower($char);
		}
		
		return $name;
	}, $namespace);
	
	if (isset($dirs[$name])) {
		$file = $dirs[$name] ."/". implode("/", $parts) .".php";
		
		if (is_file($file)) {
			require_once $file;
		}
	}
});