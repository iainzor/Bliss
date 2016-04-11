<?php
namespace User\Db;

use Database\Table\AbstractTable,
	Database\Model,
	User\Settings\Setting;

class UserSettingsTable extends AbstractTable implements Model\ModelGeneratorInterface
{
	use Model\ModelGeneratorTrait;
	
	const NAME = "user_settings";
	
	public function defaultName() { return self::NAME; }
	
	public function createModelInstance() { return new Setting(); }
}