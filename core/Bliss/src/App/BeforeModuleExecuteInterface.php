<?php
namespace Bliss\App;

use Bliss\Module\AbstractModule;

interface BeforeModuleExecuteInterface
{
	public function beforeModuleExecute(AbstractModule $module);
}