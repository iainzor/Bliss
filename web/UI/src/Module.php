<?php
namespace UI;

use Bliss\Module\AbstractModule,
	User\Settings;

class Module extends AbstractModule implements Settings\SettingsProviderInterface
{
	public function defineUserSettings(Settings\Definitions $definitions) 
	{}
}