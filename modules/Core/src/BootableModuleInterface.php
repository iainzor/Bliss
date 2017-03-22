<?php
namespace Core;

interface BootableModuleInterface
{
	public function bootstrap(AbstractApplication $app);
}