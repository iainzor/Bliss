<?php
namespace Http;

interface RouteProviderInterface 
{
	public function registerRoutes(Router $router);
}
