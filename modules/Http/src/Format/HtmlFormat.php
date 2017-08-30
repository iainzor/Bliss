<?php
namespace Http\Format;

class HtmlFormat implements FormatInterface 
{
	public function matches(string $path): bool { return preg_match("/\.html?$/i", $path); }

	public function mimeType(): string { return "text/html"; }

	public function parse($data) : string
	{ 
		if (!is_string($data)) {
			$data = print_r($data, true);
		}
		
		return $data; 
	}
}