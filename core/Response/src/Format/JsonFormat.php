<?php
namespace Response\Format;

class JsonFormat implements FormatInterface
{
	public function mime() { return "application/json"; }
	
	public function requiresView() { return false; }
	
	public function transform(\Response\Module $response) 
	{
		$params = [];
		foreach ($response->params() as $name => $value) {
			$params[$name] = $this->_transform($value);
		}
		
		if (!count($params)) { 
			$params = null;
		}
		
		return json_encode($params);
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
			}
			return $value;
		}
	}
}