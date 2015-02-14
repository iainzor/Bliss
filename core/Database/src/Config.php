<?php
namespace Database;

class Config extends \Config\Config
{
	const SECTION_DEFAULT_CONNECTION = "defaultConnection";
	
	const CONF_DSN = "dsn";
	const CONF_USER = "user";
	const CONF_PASSWORD = "password";
	const CONF_OPTIONS = "options";
}