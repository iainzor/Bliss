<?php
namespace Http\Format;

interface FormatInterface
{
	public function matches(string $path) : bool;
	
	public function mimeType() : string;
	
	public function parse($data) : string;
	
}
