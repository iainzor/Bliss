<?php
namespace User;

interface UserSessionProcessorInterface
{
	public function processUserSession(Session\Session $session);
}