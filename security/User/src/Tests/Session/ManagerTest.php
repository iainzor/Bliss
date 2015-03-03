<?php
namespace User\Tests\Session;

use User\Session\Manager,
	User\DbTable as UserDbTable,
	User\Session\DbTable as SessionDbTable,
	User\User,
	Database\PDO;

class ManagerTest extends \PHPUnit_Framework_TestCase
{
	/**
	 * @var Manager
	 */
	private $manager;
	
	public function setUp()
	{
		//ob_start();
		//session_start();
		//ob_end_flush();
		
		$db = new PDO("sqlite::memory:", null, null, [
			\PDO::ATTR_ERRMODE => \PDO::ERRMODE_EXCEPTION
		]);
		$db->exec("
			CREATE TABLE `users` (
				`id` INTEGER NOT NULL PRIMARY KEY AUTOINCREMENT ,
				`email` VARCHAR(32) NOT NULL UNIQUE,
				`password` VARCHAR(64) NOT NULL,
				`displayName` VARCHAR(32) NOT NULL
			)
		");
		$userDbTable = new UserDbTable($db);
		$sessionDbTable = new SessionDbTable($db);
		
		$password = User::passwordHasher()->hash("123abc");
		$db->exec("INSERT INTO `users` VALUES (null, 'iainedminster@gmail.com', '{$password}', 'iain.zor')");
		
		$this->manager = new Manager($sessionDbTable, $userDbTable);
	}
	
	public function testBadCredentials()
	{
		$valid = $this->manager->isValid("foo@bar.com", "abc123");
		$this->assertFalse($valid);
	}
	
	public function testGoodCredentials()
	{
		$valid = $this->manager->isValid("iainedminster@gmail.com", "123abc");
		
		$this->assertTrue($valid);
	}
	
	public function testCreateSession()
	{
		$session = $this->manager->createSession("iainedminster@gmail.com", "123abc");
		
		$this->assertInstanceOf("\\User\\Session\\Session", $session);
	}
}