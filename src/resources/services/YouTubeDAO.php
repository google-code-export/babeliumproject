<?php

require_once 'Zend/Loader.php';
require_once 'utils/Config.php';
require_once 'utils/Datasource.php';
require_once 'utils/SessionHandler.php';

class YouTubeDAO {
	
	// Enter your Google account credentials
	private $email;
	private $passwd;
	private $devKey;
	
	// Video duration size
	private $maxDuration;
	
	private $filePath;
	private $imagePath;
	private $red5Path;
	private $exerciseFolder;
	private $conn;
	private $mediaHelper;
	
	function YouTubeDAO() {
		Zend_Loader::loadClass ( 'Zend_Gdata_YouTube' );
		Zend_Loader::loadClass ( 'Zend_Gdata_ClientLogin' );
		Zend_Loader::loadClass ( 'Zend_Gdata_App_Exception' );
		Zend_Loader::loadClass ( 'Zend_Gdata_App_Extension_Control' );
		Zend_Loader::loadClass ( 'Zend_Gdata_App_CaptchaRequiredException' );
		Zend_Loader::loadClass ( 'Zend_Gdata_App_HttpException' );
		Zend_Loader::loadClass ( 'Zend_Gdata_App_AuthException' );
		Zend_Loader::loadClass ( 'Zend_Gdata_YouTube_VideoEntry' );
		Zend_Loader::loadClass ( 'Zend_Gdata_App_Entry' );
		
		try {
			$verifySession = new SessionHandler();
		
			$settings = new Config();
		
			$this->filePath = $settings->filePath;
			$this->imagePath = $settings->imagePath;
			$this->red5Path = $settings->red5Path;
			$this->email = $settings->yt_user;
			$this->passwd = $settings->yt_password;
			$this->devKey = $settings->yt_developerKey;
		
			$this->maxDuration = $settings->maxDuration;
		
			$this->conn = new Datasource ( $settings->host, $settings->db_name, $settings->db_username, $settings->db_password );
		
		} catch (Exception $e) {
			throw new Exception($e->getMessage());
		}
	}
	
	private function authenticate() {
		try {
			$client = Zend_Gdata_ClientLogin::getHttpClient ( $this->email, $this->passwd, 'youtube' );
		} catch ( Zend_Gdata_App_CaptchaRequiredException $cre ) {
			//echo 'URL of CAPTCHA image: ' . $cre->getCaptchaUrl() . "\n";
			//echo 'Token ID: ' . $cre->getCaptchaToken() . "\n";
			//echo 'Captcha required: ' . $cre->getCaptchaToken () . "\n";
			throw new Exception ( "Captcha required: " . $cre->getCaptchaToken () . "\n" . "URL of CAPTCHA image: " . $cre->getCaptchaUrl () . "\n" );
		} catch ( Zend_Gdata_App_AuthException $ae ) {
			//return 'Problem authenticating: ' . $ae->exception () . "\n";
			throw new Exception ( "Problem authenticating: " . $ae->getMessage () . "\n" );
		}
		
		$client->setHeaders ( 'X-GData-Key', 'key=' . $this->devKey );
		return $client;
	}
	
	/*public function directClientLoginUpload(ExerciseVO $data) {
		
		set_time_limit(0);
		
		$httpClient = $this->authenticate ();
		
		//For time consuming operations such as uploading it is important to increase
		//the httpClient timeout, from the default 10 second value to your needs.
		$config = array('timeout' => 30);
		$httpClient->setConfig($config);
		
		$yt = new Zend_Gdata_YouTube ( $httpClient );
		$myVideoEntry = new Zend_Gdata_YouTube_VideoEntry ( );
		
		//We get the string representing the full path to our file
		$filePath = $this->filePath . "/" . $data->name;
		
		$filesource = $yt->newMediaFileSource ( $filePath );
		//$filesource->setContentType ( 'video/quicktime' );
		$filesource->setContentType ( 'video/x-flv' );
		
		$filesource->setSlug ( $filePath );
		
		$myVideoEntry->setMediaSource ( $filesource );
		
		$myVideoEntry->setVideoTitle ( $data->title );
		$myVideoEntry->setVideoDescription ( $data->description );
		// Note that category must be a valid YouTube category !
		$myVideoEntry->setVideoCategory ( 'Education' );
		
		// Set keywords, note that this must be a comma separated string
		// and that each keyword cannot contain whitespace
		$myVideoEntry->SetVideoTags ( $data->tags );
		
		// Optionally set some developer tags
		//$myVideoEntry->setVideoDeveloperTags ( array ('mydevelopertag', 'anotherdevelopertag' ) );
		//$myVideoEntry->setVideoPrivate ();
		

		// Upload URI for the currently authenticated user
		$uploadUrl = 'http://uploads.gdata.youtube.com/feeds/users/default/uploads';
		
		// Try to upload the video, catching a Zend_Gdata_App_HttpException
		// if availableor just a regular Zend_Gdata_App_Exception
		

		try {
			$newEntry = $yt->insertEntry ( $myVideoEntry, $uploadUrl, 'Zend_Gdata_YouTube_VideoEntry' );
		} catch ( Zend_Gdata_App_HttpException $httpException ) {
			throw new Exception ( "Upload Exception: " . $httpException->getRawResponseBody () ."\n" );
		} catch ( Zend_Gdata_App_Exception $e ) {
			throw new Exception ( "Application Exception: " . $e->getMessage () ."\n" );
		}
		//$test = array ($newEntry->mediaGroup->player[0]->url, $newEntry->getVideoId(), $filePath);
		

		$result = $newEntry->getVideoId ();
		return $result;
	
	}*/
	
	/*
	public function browserBasedClientLoginUploadToken(YouTubeMetadataVO $data) {
		
		$httpClient = $this->authenticate ();
		$yt = new Zend_Gdata_YouTube ( $httpClient );
		
		$myVideoEntry = new Zend_Gdata_YouTube_VideoEntry ( );
		$myVideoEntry->setVideoTitle ( $data->title );
		$myVideoEntry->setVideoDescription ( $data->description );
		
		// Note that category must be a valid YouTube category
		$myVideoEntry->setVideoCategory ( $data->category );
		$myVideoEntry->SetVideoTags ( $data->tags );
		
		$tokenHandlerUrl = 'http://gdata.youtube.com/action/GetUploadToken';
		$tokenArray = $yt->getFormUploadToken ( $myVideoEntry, $tokenHandlerUrl );
		$tokenValue = $tokenArray ['token'];
		$postUrl = $tokenArray ['url'];
		$result = array ($tokenValue, $postUrl );
		
		return $result;
	}*/
	
	/*public function checkUploadedVideoStatus($videoId) {
		
		$httpClient = $this->authenticate ();
		$youTubeService = new Zend_Gdata_YouTube ( $httpClient );
		
		$feed = $youTubeService->getuserUploads ( 'default' );
		$message = 'No further status information available yet.';
		
		foreach ( $feed as $videoEntry ) {
			if ($videoEntry->getVideoId () == $videoId) {
				// check if video is in draft status
				try {
					$control = $videoEntry->getControl ();
				} catch ( Zend_Gdata_App_Exception $e ) {
					return 'ERROR - not able to retrieve control element ' . $e->getMessage ();
				}
				
				if ($control instanceof Zend_Gdata_App_Extension_Control) {
					if (($control->getDraft () != null) && ($control->getDraft ()->getText () == 'yes')) {
						$state = $videoEntry->getVideoState ();
						if ($state instanceof Zend_Gdata_YouTube_Extension_State) {
							$message = 'Upload status: ' . $state->getName () . ' ' . $state->getText ();
						} else {
							return $message;
						}
					}
				}
			}
		}
		return $message;
	
	}*/
	
	public function retrieveVideo($data) {

		$url = escapeshellcmd($data);
		$pattern = '/v=([A-Za-z0-9._%-]*)[&\w;=\+_\-]*/';
		preg_match($pattern, $url, $matches);
		$result = $matches[1];
			
		return $result;
	}
	
	public function retrieveUserVideo($data) {

		$url = escapeshellcmd($data);
		$pattern = '/\/([^\/]*)$/'; //Captures each character starting from the last / of the Url 
		preg_match($pattern, $url, $matches);
		$result = $matches[1];
	
		return $result;
	}
	
	public function insertVideoSlice($data, $data2) {
	
		set_time_limit(0); // Bypass the execution time limit
		
		try {
			new SessionHandler(true);
			
		$watchUrl = $data->watchUrl;
		
		/*$sql = "SELECT prefValue FROM preferences WHERE (prefName = 'sliceDownCommandPath')";
		$pathComando = $this->_singleQuery($sql);*/	
		
		//$comandoDescarga = $pathComando." -e --get-thumbnail ".$watchUrl; //Linea solo para developers en Windows
		$comandoDescarga = "youtube-dl -e --get-thumbnail ".$watchUrl;
		$thumbnail = exec($comandoDescarga); //Get VideoSlice's Thumbnail Uri
				
		$sql = "INSERT INTO video_slice (name, watchUrl, start_time, duration) VALUES ('%s', '%s', %d, %d)";
		$result = $this->_create($sql, $data->name, $data->watchUrl, $data->start_time, $data->duration);
		
		$sql2 = "INSERT INTO exercise (name, description, source, language, fk_user_id, tags, title, thumbnail_uri, duration, status, license, reference, adding_date) VALUES ('%s', '%s', '%s', '%s', %d, '%s', '%s', '%s', %d, '%s', '%s', '%s', NOW())";
		$result = $this->_create($sql2, $data2->name, $data2->description, $data2->source, $data2->language, $_SESSION['uid'], $data2->tags, $data2->title, $thumbnail, $data->duration, $data2->status, $data2->license, $data2->reference);
		
		return $result;
		
		}catch (Exception $e){
				throw new Exception($e->getMessage());
		}	
	}
	
	private function _singleQuery() {
	
		$result = $this->conn->_execute ( func_get_args() );
		$row = $this->conn->_nextRow($result);
      	if ($row){
        	return $row[0];
      	}	
	}
	
	private function _create() {

		$this->conn->_execute ( func_get_args() );

		$sql = "SELECT last_insert_id()";
		$result = $this->conn->_execute ( $sql );

		$row = $this->conn->_nextRow ( $result );
		if ($row) {
			return true;
		} else {
			return false;
		}
	}
	
}
?>