<?php
namespace Router;

interface ProviderInterface
{
	public function initRouter(Module $router);
}