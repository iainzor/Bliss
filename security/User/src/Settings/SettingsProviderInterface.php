<?php
namespace User\Settings;

interface SettingsProviderInterface
{
	public function defineUserSettings(Definitions $definitions);
}