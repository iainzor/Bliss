<?php
namespace Http;

class Module implements Format\FormatProviderInterface
{
	public function registerResponseFormats(Format\FormatRegistry $formatRegistry) 
	{
		$formatRegistry->registerAll([
			new Format\JsonFormat(),
			new Format\ImageFormat()
		]);
		
	}
}