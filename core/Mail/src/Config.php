<?php
namespace Mail;

class Config extends \Bliss\Config
{
	const SECTION_SMTP_CONFIG = "smtpConfig";
	
	const SMTP_HOST = "host";
	const SMTP_USERNAME = "username";
	const SMTP_PASSWORD = "password";
	const SMTP_ENCRYPTION = "encryption";
	const SMTP_PORT = "port";
	
	const DEFAULT_PORT = 587;
	const DEFAULT_ENCRYPTION = "tls";
	
	/**
	 * Configure a PHPMailer instance for SMTP authentication
	 * 
	 * @param \PHPMailer $mailer
	 */
	public function configureSmtp(\PHPMailer $mailer)
	{
		$mailer->isSMTP();
		$mailer->SMTPAuth = true;
		$mailer->Host = $this->get(self::SMTP_HOST);
		$mailer->Username = $this->get(self::SMTP_USERNAME);
		$mailer->Password = $this->get(self::SMTP_PASSWORD);
		$mailer->SMTPSecure = $this->get(self::SMTP_ENCRYPTION, self::DEFAULT_ENCRYPTION);
		$mailer->Port = $this->get(self::SMTP_PORT, self::DEFAULT_PORT);
	}
}