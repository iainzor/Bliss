<?php
namespace Response\Format;

class JsonFormat implements FormatInterface
{
	public function mime() { return "application/json"; }
	
	public function requiresView() { return false; }
	
	public function transform(\Response\Module $response) 
	{
		return json_encode($response->params());
	}
}