<?php
namespace Bliss\Controller;

interface ControllerInterface
{
	public function init();
	
	public function execute(\Request\Module $request);
}