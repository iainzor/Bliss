<?php
use Database\Config as DbConfig;

return [
	"database" => [
		DbConfig::SECTION_DEFAULT_CONNECTION => [
			DbConfig::CONF_DSN => "mysql:host=127.0.0.1;dbname=bliss",
			DbConfig::CONF_USER => "root",
			DbConfig::CONF_PASSWORD => ""
		]
	]
];