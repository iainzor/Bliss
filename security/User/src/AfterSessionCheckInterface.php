<?php
namespace User;

interface AfterSessionCheckInterface
{
	public function afterSessionCheck(Module $userModule);
}