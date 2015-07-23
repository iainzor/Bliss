<?php
namespace Tests\Controller;

use Tests\Result;

class RunnerController extends \Bliss\Controller\AbstractController
{
	private $configPath;
	
	public function init()
	{
		$this->configPath = $this->app->resolvePath("tests/config.xml");
		
		$dir = dirname($this->configPath);
		if (!is_dir($dir)) {
			mkdir($dir, 0777, true);
		}
	}
	
	public function runAction()
	{
		$request = $this->app->request();
		
		if ($request->param("format") === "json") {
			$this->_generateConfig();
			
			$response = shell_exec("cd ". $this->app->resolvePath() ." && phpunit -c {$this->configPath} --bootstrap ". __DIR__ ."/autoload.php");
			$result = new Result();
			$result->parseResponse($response);
			
			return $result->toArray();
		}
	}
	
	private function _generateConfig()
	{
		ob_start();
		$writer = new \XMLWriter("1.0", "UTF-8");
		$writer->openUri("php://output");
		$writer->setIndent(4);
		$writer->startDocument();
		$writer->startElement("testsuites");
		
		foreach ($this->app->modules() as $module) {
			$writer->startElement("testsuite");
				$writer->writeAttribute("name", $module->name());
				$writer->writeElement("directory", $module->resolvePath("src"));
			$writer->endElement();
		}
		
		$writer->endElement();
		$writer->endDocument();
		$writer->flush(true);
		$content = trim(ob_get_clean());
		
		file_put_contents($this->configPath, $content);
	}
}