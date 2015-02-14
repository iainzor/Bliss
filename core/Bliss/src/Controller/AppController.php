<?php
namespace Bliss\Controller;

class AppController extends AbstractController 
{
	public function indexAction()
	{
		return $this->app->toArray();
	}
}