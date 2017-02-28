<?php
namespace Core;

interface ConfigurableModuleInterface
{
	public function configure(AbstractApplication $app, ModuleConfig $config);
}