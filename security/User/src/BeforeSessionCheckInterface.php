<?php
namespace User;

interface BeforeSessionCheckInterface
{
	public function beforeSessionCheck(Module $userModule);
}