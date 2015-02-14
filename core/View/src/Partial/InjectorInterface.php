<?php
namespace View\Partial;

interface InjectorInterface
{
	public function initPartialInjector(InjectableInterface $injectable);
}