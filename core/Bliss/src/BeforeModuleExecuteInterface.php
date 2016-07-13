<?php
namespace Bliss;

interface BeforeModuleExecuteInterface
{
	public function beforeModuleExecute(Module\AbstractModule $module);
}