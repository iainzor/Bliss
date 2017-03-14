<?php
namespace Response\Format;

class CLIFormat implements FormatInterface
{
	public function mime() { return null; }

	public function requiresView() { return false; }

	public function transform(\Response\Module $response) 
	{
		$data = "";
		foreach ($response->params() as $name => $value) {
			if (is_string($value)) {
				$data .= $name .":". $value ."\r\n";
			}
		}
		return $data;
	}

}