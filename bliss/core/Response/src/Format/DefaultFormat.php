<?php
namespace Response\Format;

class DefaultFormat implements FormatInterface 
{
	public function mime() { return "text/html"; }

	public function requiresView() { return true; }

	public function transform(\Response\Module $response) {}
}