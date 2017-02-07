<?php
namespace Core;

interface BootableModuleInterface
{
	public static function bootstrap(AbstractApplication $app);
}