<?php
namespace Response\Format;

class HtmlFormat implements FormatInterface
{
	public function mime() { return "text/html"; }
	
	public function requiresView() { return true; }
	
	public function transform(\Response\Module $response) 
	{
		$body = $response->body();
		
		if (!$body) {
			throw new \Exception("HTML format cannot render responses with empty bodies");
		}
		
		return $body;
	}
}