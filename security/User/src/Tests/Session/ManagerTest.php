<?php
namespace User\Tests\Session;

use User\Session\Manager,
	User\Db\UsersTable as UserDbTable,
	User\Db\UserSessionsTable as SessionDbTable,
	User\User,
	Database\PDO;

class ManagerTest extends \PHPUnit_Framework_TestCase
{
	const EMAIL = "someone@something.com";
	const PASSWORD = "123abc";
	
	/**
	 * @var Manager
	 */
	private static $manager;
	
	public static function setUpBeforeClass()
	{
		//ob_start();
		//session_start();
		//ob_end_flush();
		
		$db = new PDO("sqlite::memory:", null, null, [
			\PDO::ATTR_ERRMODE => \PDO::ERRMODE_EXCEPTION
		]);
		$db->driver("mysql");
		$db->schemaName("");
		$db->exec("
			CREATE TABLE `users` (
				`id` INTEGER NOT NULL PRIMARY KEY AUTOINCREMENT,
				`email` VARCHAR(32) NOT NULL UNIQUE,
				`password` VARCHAR(64) NOT NULL,
				`displayName` VARCHAR(32) NOT NULL
			)
		");
		$userDbTable = new UserDbTable($db);
		$sessionDbTable = new SessionDbTable($db);
		$hasher = User::passwordHasher();
		
		$password = $hasher->hash(self::PASSWORD);
		
		$statement = $db->prepare("INSERT INTO `users` VALUES (null, :email, :password, :displayName)");
		$statement->execute([
			":email" => self::EMAIL,
			":password" => $password,
			":displayName" => "Johny Doughburg"
		]);
		
		self::$manager = new Manager($sessionDbTable, $userDbTable, $hasher);
	}
	
	public function testBadCredentials()
	{
		$valid = self::$manager->isValid("foo@bar.com", "abc123");
		$this->assertFalse($valid);
	}
	
	public function testGoodCredentials()
	{
		$valid = self::$manager->isValid(self::EMAIL, self::PASSWORD);
		
		$this->assertTrue($valid);
	}
	
	public function testCreateSession()
	{
		$session = self::$manager->createSession("foo@bar.com", "bazzlyboo");
		
		$this->assertInstanceOf("\\User\\Session\\Session", $session);
	}
}