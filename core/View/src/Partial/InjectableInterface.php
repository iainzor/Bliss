<?php
namespace View\Partial;

interface InjectableInterface
{
	public function inject($area, PartialInterface $partial, $order = 0);
	
	public function compileInjectables();
	
	public function renderInjectables($area);
}