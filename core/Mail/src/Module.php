<?php
namespace Mail;

require dirname(__DIR__) ."/lib/PHPMailer/class.phpmailer.php";
require dirname(__DIR__) ."/lib/PHPMailer/class.smtp.php";

use Bliss\Module\AbstractModule,
	PHPMailer;

class Module extends AbstractModule 
{
	/**
	 * @var PHPMailer 
	 */
	protected $mailer;
	
	/**
	 * Get or set the PHPMailer instance
	 * 
	 * @param PHPMailer $mailer
	 * @return PHPMailer
	 */
	public function mailer(PHPMailer $mailer = null)
	{
		if (!$this->getSet("mailer", $mailer)) {
			$this->mailer = new PHPMailer();
		}
		return $this->mailer;
	}
	
	/**
	 * Configure the mailer to use SMTP authentication
	 * 
	 * @param array $config
	 */
	public function smtpConfig(array $config)
	{
		$mailer = $this->mailer();
		$config = new Config($config);
		$config->configureSmtp($mailer);
	}
}