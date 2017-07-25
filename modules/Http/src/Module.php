<?php
namespace Http;

use Core\ConfigurableModuleInterface,
	Core\AbstractApplication,
	Core\ModuleConfig;

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
	}
	
	public function registerResponseFormats(Format\FormatRegistry $formatRegistry) 
	{
		$formatRegistry->registerAll([
			new Format\JsonFormat(),
			new Format\ImageFormat()
		]);
	}
}