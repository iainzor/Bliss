<?php
namespace Http\Format;

class PlainTextFormat implements FormatInterface
{
	public function extension() : string { return "txt"; }
	
	public function mimeType() : string { return "text/plain"; }
	
	public function parse($data) : string
	{
		return $data;
	}
}
