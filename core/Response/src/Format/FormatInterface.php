<?php
namespace Response\Format;

interface FormatInterface 
{
	public function transform(\Response\Module $response);
	
	public function mime();
	
	public function requiresView();
}