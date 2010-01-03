<?php

require_once ('Datasource.php');
require_once ('Config.php');
require_once ('Zend/Mail.php');
require_once ('Zend/Mail/Transport/Smtp.php');

class Mailer
{
	private $_conn;
	private $_settings;
	private $_userMail;
	private $_userRealName;
	private $_validUser;

	public function Mailer($username)
	{
		$this->_settings = new Config();
		$this->_conn = new DataSource($this->_settings->host, $this->_settings->db_name, $this->_settings->db_username, $this->_settings->db_password);

		$this->_validUser = $this->_getUserInfo($username);
	}

	private function _getUserInfo($username)
	{
		if (!$username)
			return false;

		$aux = "name";
		if ( Mailer::checkEmail($username) )
			$aux = "email";

		$sql = "SELECT name, email FROM users WHERE (".$aux." = '". $username ."') ";
		$result = $this->_conn->_execute($sql);
		$row = $this->_conn->_nextRow($result);

		if ($row)
		{
			$this->_userRealName = $row[0];
			$this->_userMail = $row[1];
		}
		else
			return false;

		return true;
	}

	public function send($body, $subject, $htmlBody = null)
	{
		if ( !$this->_validUser )
			return false;

		// SMTP Server config
		$config = array('auth' => 'login',
						'username' => $this->_settings->smtp_server_username,
						'password' => $this->_settings->smtp_server_password,
						'ssl' => $this->_settings->smtp_server_ssl,
						'port' => $this->_settings->smtp_server_port
		);
 
		$transport = new Zend_Mail_Transport_Smtp($this->_settings->smtp_server_host, $config);


		$mail = new Zend_Mail();
		$mail->setBodyText($body);
		if ( $htmlBody != null )
			$mail->setBodyHtml($htmlBody);
		$mail->setFrom($this->smtp_mail_setFromMail, $this->smtp_mail_setFromName);
		$mail->addTo($this->_userMail, $this->_userRealName);
		$mail->setSubject($subject);
		$mail->send($transport);

		return true;
	}

	public static function checkEmail($email)
	{
		$reg = "/^[^0-9][a-zA-Z0-9_]+([.][a-zA-Z0-9_]+)*[@][a-zA-Z0-9_]+([.][a-zA-Z0-9_]+)*[.][a-zA-Z]{2,4}$/";
		return preg_match($reg, $email);
	}

}

?>
