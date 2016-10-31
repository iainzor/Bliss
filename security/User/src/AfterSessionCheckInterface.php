<?php
namespace User;

interface AfterSessionCheckInterface
{
	public function afterSessionCheck(Session\Session $session);
}