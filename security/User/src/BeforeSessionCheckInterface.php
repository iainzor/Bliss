<?php
namespace User;

interface BeforeSessionCheckInterface
{
	public function beforeSessionCheck(Session\Session $session);
}