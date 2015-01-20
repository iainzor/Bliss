<?php
namespace Sample;

use Bliss\Module\AbstractModule,
	Database\ServerProviderInterface,
	Database\Registry;

class Module extends AbstractModule implements ServerProviderInterface
{
	public function initDatabaseServer(Registry $registry) 
	{}
}