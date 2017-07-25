<?php
namespace Http;

use Core\ConfigurableModuleInterface,
	Core\AbstractApplication,
	Core\ModuleConfig,
	Core\ModuleDefinition;

class Module implements Format\FormatProviderInterface, ConfigurableModuleInterface
{
	public function configure(AbstractApplication $app, ModuleConfig $config) 
	{
		$app->di()->register(ErrorHandler::class, function(Request $request, Response $response) use ($app, $config) {
			$handler = new ErrorHandler($request, $response);
			$handler->showTrace(
				$config->get(Config::ERROR_SHOW_TRACE, false)
			);
			
			return $handler;
		});
		
		$formats = new Format\FormatRegistry(new Format\HtmlFormat());
		$app->moduleRegistry()->each(function(ModuleDefinition $moduleDef) use ($app, $formats) {
			$module = $moduleDef->instance($app);
			if ($module instanceof Format\FormatProviderInterface) {
				$module->registerResponseFormats($formats);
			}
		});
		$app->di()->register($formats);
	}
	
	public function registerResponseFormats(Format\FormatRegistry $formatRegistry) 
	{
		$formatRegistry->registerAll([
			new Format\JsonFormat(),
			new Format\ImageFormat()
		]);
	}
}