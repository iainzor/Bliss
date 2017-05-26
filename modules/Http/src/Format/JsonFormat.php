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
		return json_encode($this->_parse($data));
	}
	
	private function _parse($data) 
	{
		if (is_array($data)) {
			foreach ($data as $key => $value) {
				$data[$key] = $this->_parse($value);
			}
		} else if (is_object($data)) {
			if ($data instanceof \JsonSerializable) {
				$data = $data->jsonSerialize();
			}
		}
		
		return $data;
	}
}
