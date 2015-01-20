<?php
namespace Sample;

use Bliss\Module\AbstractModule,
	Database\ServerProviderInterface,
	Database\Registry;

class Module extends AbstractModule implements ServerProviderInterface
{
	public function initDatabaseServer(Registry $registry) 
	{
		$registry->addServer("mysql:host=127.0.0.1;dbname=bliss", "root", "1575027");
	}
}