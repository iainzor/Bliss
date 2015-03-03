<?php
namespace Database;

interface ServerProviderInterface
{
	public function initDatabaseServer(Registry $registry);
}