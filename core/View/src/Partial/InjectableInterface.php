<?php
namespace View\Partial;

interface InjectableInterface
{
	public function inject($area, Partial $partial, $order = 0);
	
	public function compileInjectables();
	
	public function renderInjectables($area);
}