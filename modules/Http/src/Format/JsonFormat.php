<?php
namespace Http\Format;

class JsonFormat implements FormatInterface
{
	public function extension() : string { return "json"; }
	
	public function mimeType() : string { return "application/json"; }
	
	public function parse($data) : string
	{
		return json_encode($data);
	}
}
