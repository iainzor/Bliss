<?php
namespace Assets\Controller;

class AssetController extends \Bliss\Controller\AbstractController
{
	public function renderAction()
	{
		$request = $this->app->request();
		$response = $this->app->response();
		$path = $request->param("path");
		$moduleName = $request->param("moduleName");
		$module = $this->app->module($moduleName);
		
		if (preg_match("/^(.*)-compiled\.([a-z0-9]+)$/i", $path, $matches)) {
			$compiler = $this->module->compiler();
			$sourceName = sprintf("%s.%s",
				$matches[1],
				$matches[2]
			);
			$sourcePath = $module->resolvePath("assets/". $sourceName);
			$filename = $this->module->resolvePath(
				sprintf("files/compiled/%s/%s",
					$moduleName,
					$sourceName
				)
			);
			
			if ($compiler->isEnabled()) {
				$compiler->compile($sourcePath)->save($filename);
			}
		} else {
			$filename = $module->resolvePath("assets/{$path}");
		}
		
		if (!is_file($filename)) {
			throw new \Exception("File could not be found: {$path}", 404);
		}
		
		$modifiedTime = filemtime($filename);
		$response->setLastModified($modifiedTime);
		
		if ($response->isExpired()) {
			$contents = file_get_contents($filename);
		
			return $contents;
		} else {
			$response->notModified();
		}
	}
	
	/**
	 * Action used to compile and render all assets from all registered modules 
	 * of a single format
	 */
	public function renderAllAction()
	{
		$request = $this->app->request();
		$response = $this->app->response();
		$formatName = $request->getFormat();
		$filename = $this->app->resolvePath("cache/assets/compiled/all.{$formatName}");
		$compiler = $this->module->compiler();
		
		if ($compiler->isEnabled()) {
			$contents = "";
			foreach ($this->app->modules() as $module) {
				$compiled = $compiler->compile(
					$module->resolvePath("assets/{$formatName}")
				);
				
				if (!$compiled->isEmpty()) {
					$contents .= "/* Module: {$module->name()} */\n";
					$contents .= $compiled->contents() ."\n\n";
				}
			}
			
			$file = new \Assets\Compiler\File\File($contents);
			$file->save($filename);
		}
		
		$modifiedTime = filemtime($filename);
		$response->setLastModified($modifiedTime);
		
		if ($response->isExpired()) {
			return file_get_contents($filename);
		} else {
			$response->notModified();
		}
	}
}