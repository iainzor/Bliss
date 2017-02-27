<?php
namespace Http;

class Module implements Format\FormatProviderInterface
{
	public function registerResponseFormats(Format\FormatRegistry $formatRegistry) 
	{
		$formatRegistry->register(new Format\JsonFormat());
	}
}