<?php
namespace View\Decorator;

interface ProviderInterface 
{
	public function initViewDecorator(Registry $registry);
}