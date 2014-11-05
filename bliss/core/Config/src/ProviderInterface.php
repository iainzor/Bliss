<?php
namespace Config;

interface ProviderInterface
{
	public function initConfig(Config $rootConfig);
}