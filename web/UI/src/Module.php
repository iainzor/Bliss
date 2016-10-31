<?php
namespace UI;

use Bliss\Module\AbstractModule,
	User\Settings;

class Module extends AbstractModule implements Settings\SettingsProviderInterface
{
	public function defineUserSettings(Settings\Definitions $definitions) 
	{
		$definitions->set([
			[
				"key" => "themes",
				"encoder" => "json_encode",
				"decoder" => function($value) { return json_decode($value, true); },
				"defaultValue" => [
					[
						"name" => "primary",
						"label" => "Primary",
						"background" => "#ffffff",
						"color" => "#212121"
					], [
						"name" => "secondary",
						"label" => "Secondary",
						"background" => "#3b4751",
						"color" => "#ffffff"
					], [
						"name" => "bad",
						"locked" => true,
						"background" => "#F44336",
						"color" => "#ffffff"
					], [
						"name" => "good",
						"locked" => true,
						"background" => "#4CAF50",
						"color" => "#ffffff"
					], [
						"name" => "warning",
						"locked" => true,
						"background" => "#FF9800",
						"color" => "#ffffff"
					]
				]
			]
		]);
	}
}