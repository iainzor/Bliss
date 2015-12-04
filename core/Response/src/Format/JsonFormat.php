<?php
namespace Response\Format;

class JsonFormat implements FormatInterface
{
	public function mime() { return "application/json; charset=UTF-8"; }
	
	public function requiresView() { return false; }
	
	public function transform(\Response\Module $response) 
	{
		$params = [];
		foreach ($response->params() as $name => $value) {
			$params[$name] = $this->_transform($value);
		}
		
		$encoded = json_encode($params, JSON_PARTIAL_OUTPUT_ON_ERROR);
		if ($encoded === false) {
			$error = json_last_error_msg();
			throw new \Exception("JSON encoding failed: {$error}");
		}
		
		return $encoded;
	}
	
	private function _transform($value)
	{
		if (is_array($value)) {
			foreach ($value as $k => $v) {
				$value[$k] = $this->_transform($v);
			}
			return $value;
		} else {
			if (is_object($value) && method_exists($value, "toArray")) {
				return $value->toArray();
			} else if (is_string($value)) {
				return $value;
			}
			return $value;
		}
	}
}