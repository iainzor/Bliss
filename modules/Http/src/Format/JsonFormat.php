<?php
namespace Http\Format;

class JsonFormat implements FormatInterface
{
	public function matches(string $path) : bool 
	{ 
		return preg_match("/\.(json)$/i", $path);
	}
	
	public function mimeType() : string { return "application/json"; }
	
	public function parse($data) : string
	{
		return json_encode($data);
	}
}
