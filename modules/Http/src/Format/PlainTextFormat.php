<?php
namespace Http\Format;

class PlainTextFormat implements FormatInterface
{
	public function extension() : string { return "txt"; }
	
	public function mimeType() : string { return "text/plain"; }
	
	public function parse($data) : string
	{
		return $this->_parse($data);
	}
	
	private function _parse($data) : string 
	{
		$return = "";
		switch (gettype($data)) {
			case "array":
				$return = $this->_array($data);
				break;
			case "string":
			default:
				$return = $data;
				break;
		}
		
		return $return;
	}
	
	private function _array(array $data) : string
	{
		return var_export($data, true);
	}
}
