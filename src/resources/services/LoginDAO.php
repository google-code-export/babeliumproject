<?php

require_once 'utils/Datasource.php';
require_once 'utils/Config.php';
require_once 'vo/UserVO.php';
require_once 'vo/UserLanguageVO.php';
require_once 'vo/LoginVO.php';
require_once 'Mailer.php';

class LoginDAO{

	private $conn;

	public function LoginDAO(){
		$settings = new Config();
		$this->conn = new DataSource($settings->host, $settings->db_name, $settings->db_username, $settings->db_password);
	}

	public function getUserInfo($username){
		if (!$username)
		{
			return false;
		}

		$sql = "SELECT id, name, email, creditCount FROM users WHERE (name = '%s') ";

		return $this->_singleQuery($sql, $username);
	}

	public function processLogin(LoginVO $user){
		if($this->getUserInfo($user->name)==false){
			return "wrong_user";
		} else {
			$sql = "SELECT id FROM users WHERE (name = '%s' AND active = 0)";
			$result = $this->_singleQuery($sql, $user->name);
			if ( $result )
			return "inactive_user";
			$sql = "SELECT id, name, email, creditCount, realName, realSurname, active, joiningDate, isAdmin FROM users WHERE (name='%s' AND password='%s') ";
			$result = $this->_singleQuery($sql, $user->name, $user->pass);
			if($result){
				$userId = $result->id;
				$result->userLanguages = $this->_getUserLanguages($userId);
				$this->_startUserSession($userId);
				return $result;
			} else {
				return "wrong_password";
			}
		}
	}

	public function resendActivationEmail($user){
		if($this->getUserInfo($user->name)==false){
			return "wrong_user";
		} else {
			$sql = "SELECT id, activation_hash FROM users WHERE (name= '%s' AND email= '%s' AND active = 0 AND activation_hash <> '')";
			$inactiveUserExists = $this->_singleQueryInactiveUser($sql, $user->name, $user->email);
			if ($inactiveUserExists){
				$userId = $inactiveUserExists->id;
				$activationHash = $inactiveUserExists->hash;
				$userLanguages = $this->_getUserLanguages($userId);
				foreach($userLanguages as $lang){
					if($lang->level == 7){
						$usersFirstMotherTongue = $lang->language;
						break;
					}
				}
				// Submit activation email
				$mail = new Mailer($user->name);

				$subject = 'Babelium Project: Account Activation';

				$args = array(
						'PROJECT_NAME' => 'Babelium Project',
						'USERNAME' => $user->name,
						'PROJECT_SITE' => 'http://'.$_SERVER['HTTP_HOST'].'/Main.html#',
						'ACTIVATION_LINK' => 'http://'.$_SERVER['HTTP_HOST'].'/Main.html#/activation/activate/hash='.$activationHash.'&user='.$user->name,
						'SIGNATURE' => 'The Babelium Project Team');

				if ( !$mail->makeTemplate("mail_activation", $args, $usersFirstMotherTongue) ) return null;

				return $mail->send($mail->txtContent, $subject, $mail->htmlContent);
			} else {
				return "user_active_wrong_email";
			}
		}

	}

	private function _startUserSession($userId){

		//Initialize session
		session_start();
		$sessionId = session_id();

		//Check that there's not another active session for this user
		$sql = "SELECT * FROM user_session WHERE ( session_id = '%s' AND fk_user_id = '%d' AND closed = 0 )";
		$result = $this->conn->_execute ( $sql, $sessionId, $userId );
		$row = $this->conn->_nextRow($result);
		if(!$row){
			//Generate a new session id and remove previous data (if any)
			session_regenerate_id(true);
			$sessionId = session_id();

			$sql = "INSERT INTO user_session (fk_user_id, session_id, session_date, duration, keep_alive)
					VALUES ('%d', '%s', now(), 0, 1)";
			$result = $this->_create($sql, $userId, $sessionId);
		}
	}

	private function _getUserLanguages($userId){
		$sql = "SELECT language, level, positives_to_next_level
				FROM user_languages WHERE (fk_user_id='%d')";
		return $this->_listUserLanguagesQuery($sql, $userId);
	}

	//Returns a single User object
	private function _singleQuery(){
		$valueObject = new UserVO();
		$result = $this->conn->_execute(func_get_args());

		$row = $this->conn->_nextRow($result);
		if ($row)
		{
			$valueObject->id = $row[0];
			$valueObject->name = $row[1];
			$valueObject->email = $row[2];
			$valueObject->creditCount = $row[3];
			$valueObject->realName = $row[4];
			$valueObject->realSurname = $row[5];
			$valueObject->active = $row[6];
			$valueObject->joiningDate = $row[7];
			$valueObject->isAdmin = $row[8] == 1;
		}
		else
		{
			return false;
		}
		return $valueObject;
	}

	private function _singleQueryInactiveUser(){
		$valueObject = new UserVO();
		$result = $this->conn->_execute(func_get_args());

		$row = $this->conn->_nextRow($result);
		if ($row)
		{
			$valueObject->id = $row[0];
			$valueObject->hash = $row[1];
		} else {
			return false;
		}
		return $valueObject;
	}

	private function _listUserLanguagesQuery(){
		$searchResults = array();

		$result = $this->conn->_execute(func_get_args());
		while($row = $this->conn->_nextRow($result)){
			$temp = new UserLanguageVO();
			$temp->language = $row[0];
			$temp->level = $row[1];
			$temp->positivesToNextLevel = $row[2];

			array_push($searchResults, $temp);
		}
		return $searchResults;
	}

	private function _create() {

		$this->conn->_execute ( func_get_args() );

		$sql = "SELECT last_insert_id()";
		$result = $this->conn->_execute ( $sql );

		$row = $this->conn->_nextRow ( $result );
		if ($row) {
			return $row [0];
		} else {
			return false;
		}
	}

}
?>