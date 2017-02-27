<?php
namespace Http\Format;

interface FormatInterface
{
	public function extension() : string;
	
	public function mimeType() : string;
	
	public function parse($data) : string;
	
}
