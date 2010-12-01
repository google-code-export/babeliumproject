<?php


require_once 'utils/Config.php';
require_once 'utils/Datasource.php';
require_once 'utils/Mailer.php';
require_once 'utils/SessionHandler.php';

require_once 'vo/EvaluationVO.php';
require_once 'vo/UserVO.php';

class EvaluationDAO {

	private $conn;

	private $imagePath;
	private $red5Path;

	private $evaluationFolder = '';
	private $exerciseFolder = '';
	private $responseFolder = '';

	public function EvaluationDAO(){
		try {
			$verifySession = new SessionHandler(true);
			$settings = new Config ( );
			$this->imagePath = $settings->imagePath;
			$this->red5Path = $settings->red5Path;
			$this->conn = new Datasource ( $settings->host, $settings->db_name, $settings->db_username, $settings->db_password );

		} catch (Exception $e) {
			throw new Exception($e->getMessage());
		}
	}

	public function getResponsesWaitingAssessment() {
		$sql = "SELECT prefValue FROM preferences WHERE (prefName='trial.threshold') ";

		$result = $this->conn->_execute ( $sql );
		$row = $this->conn->_nextRow ( $result );
		if($row)
		$evaluationThreshold = $row [0];

		$sql = "SELECT DISTINCT A.file_identifier, A.id, A.rating_amount, A.character_name, A.fk_subtitle_id,
		                        A.adding_date, A.source, A.thumbnail_uri, A.duration, F.name, 
		                        B.id, B.name, B.duration, B.language, B.thumbnail_uri, B.title, B.source
				FROM (response AS A INNER JOIN exercise AS B on A.fk_exercise_id = B.id) 
				     INNER JOIN users AS F on A.fk_user_id = F.ID 
		     		 LEFT OUTER JOIN evaluation AS C on C.fk_response_id = A.id
				WHERE B.status = 'Available' AND A.rating_amount < %d AND A.fk_user_id <> %d AND A.is_private = 0
				AND NOT EXISTS (SELECT *
                                FROM evaluation AS D INNER JOIN response AS E on D.fk_response_id = E.id
                                WHERE E.id = A.id AND D.fk_user_id = %d)";

		$searchResults = $this->_listWaitingAssessmentQuery($sql, $evaluationThreshold, $_SESSION['uid'], $_SESSION['uid']);

		return $searchResults;
	}

	private function _listWaitingAssessmentQuery(){
		$searchResults = array();
		$result = $this->conn->_execute ( func_get_args() );

		while ( $row = $this->conn->_nextRow($result)){
			$temp = new EvaluationVO();

			$temp->responseFileIdentifier = $row[0];
			$temp->responseId = $row[1];
			$temp->responseRatingAmount = $row[2];
			$temp->responseCharacterName = $row[3];
			$temp->responseSubtitleId = $row[4];
			$temp->responseAddingDate = $row[5];
			$temp->responseSource = $row[6];
			$temp->responseThumbnailUri = $row[7];
			$temp->responseDuration = $row[8];

			$temp->responseUserName = $row[9];

			$temp->exerciseId = $row[10];
			$temp->exerciseName = $row[11];
			$temp->exerciseDuration = $row[12];
			$temp->exerciseLanguage = $row[13];
			$temp->exerciseThumbnailUri = $row[14];
			$temp->exerciseTitle = $row[15];
			$temp->exerciseSource = $row[16];
			array_push ( $searchResults, $temp );
		}

		return $searchResults;
	}

	public function getResponsesAssessedToCurrentUser(){

		$sql = "SELECT A.file_identifier, A.id, A.rating_amount, A.character_name, A.fk_subtitle_id,
		               A.adding_date, A.source, A.thumbnail_uri, A.duration,
		               B.id, B.name, B.duration, B.language, B.thumbnail_uri, B.title, B.source,
		               AVG(C.score_overall) AS avg_rating, AVG(C.score_intonation) AS avg_intonation, 
		               AVG(score_fluency) AS avg_fluency, AVG(score_rhythm) avg_rhythm, AVG(score_spontaneity) AS avg_spontaneity,
		               AVG(suggested_level) as avg_exerciselevel
		        FROM response AS A INNER JOIN exercise AS B ON B.id = A.fk_exercise_id
					 INNER JOIN evaluation AS C ON C.fk_response_id = A.id 
					 LEFT OUTER JOIN exercise_level E ON B.id=E.fk_exercise_id
				WHERE ( A.fk_user_id = '%d' ) 
				GROUP BY A.id,B.id";

		$searchResults = $this->_listAssessedToCurrentUserQuery ( $sql, $_SESSION['uid'] );

		return $searchResults;
	}

	private function _listAssessedToCurrentUserQuery() {
		$searchResults = array ();
		$result = $this->conn->_execute ( func_get_args() );

		while ( $row = $this->conn->_nextRow ( $result ) ) {
			$temp = new EvaluationVO();

			$temp->responseFileIdentifier = $row[0];
			$temp->responseId = $row[1];
			$temp->responseRatingAmount = $row[2];
			$temp->responseCharacterName = $row[3];
			$temp->responseSubtitleId = $row[4];
			$temp->responseAddingDate = $row[5];
			$temp->responseSource = $row[6];
			$temp->responseThumbnailUri = $row[7];
			$temp->responseDuration = $row[8];

			$temp->exerciseId = $row[9];
			$temp->exerciseName = $row[10];
			$temp->exerciseDuration = $row[11];
			$temp->exerciseLanguage = $row[12];
			$temp->exerciseThumbnailUri = $row[13];
			$temp->exerciseTitle = $row[14];
			$temp->exerciseSource = $row[15];

			$temp->overallScoreAverage = $row[16];
			$temp->intonationScoreAverage = $row[17];
			$temp->fluencyScoreAverage = $row[18];
			$temp->rhythmScoreAverage = $row[19];
			$temp->spontaneityScoreAverage = $row[20];

			$temp->exerciseAvgDifficulty = $row[21];

			array_push ( $searchResults, $temp );
		}

		return $searchResults;
	}

	public function getResponsesAssessedByCurrentUser(){
		$sql = "SELECT DISTINCT A.file_identifier, A.id, A.rating_amount, A.character_name, A.fk_subtitle_id,
		               			A.adding_date, A.source, A.thumbnail_uri, A.duration,
		               			U.name, C.score_overall, C.score_intonation, C.score_fluency, C.score_rhythm,
		               			C.score_spontaneity, C.comment, C.adding_date,
		               			B.id, B.name, B.duration, B.language, B.thumbnail_uri, B.title, B.source 
			    FROM response AS A INNER JOIN exercise AS B ON B.id = A.fk_exercise_id  
			         INNER JOIN evaluation AS C ON C.fk_response_id = A.id
			         INNER JOIN users AS U ON U.ID = A.fk_user_id
			    WHERE (C.fk_user_id = '%d')";

		$searchResults = $this->_listAssessedByCurrentUserQuery ( $sql, $_SESSION['uid'] );

		return $searchResults;
	}

	private function _listAssessedByCurrentUserQuery(){
		$searchResults = array();
		$result = $this->conn->_execute(func_get_args());

		while ($row = $this->conn->_nextRow($result)){
			$temp = new EvaluationVO();

			$temp->responseFileIdentifier = $row[0];
			$temp->responseId = $row[1];
			$temp->responseRatingAmount = $row[2];
			$temp->responseCharacterName = $row[3];
			$temp->responseSubtitleId = $row[4];
			$temp->responseAddingDate = $row[5];
			$temp->responseSource = $row[6];
			$temp->responseThumbnailUri = $row[7];
			$temp->responseDuration = $row[8];
			$temp->responseUserName = $row[9];

			$temp->overallScore = $row[10];
			$temp->intonationScore = $row[11];
			$temp->fluencyScore = $row[12];
			$temp->rhythmScore = $row[13];
			$temp->spontaneityScore = $row[14];
			$temp->comment = $row[15];
			$temp->addingDate = $row[16];

			$temp->exerciseId = $row[17];
			$temp->exerciseName = $row[18];
			$temp->exerciseDuration = $row[19];
			$temp->exerciseLanguage = $row[20];
			$temp->exerciseThumbnailUri = $row[21];
			$temp->exerciseTitle = $row[22];
			$temp->exerciseSource = $row[23];

			array_push ( $searchResults, $temp );
		}

		return $searchResults;
	}

	public function detailsOfAssessedResponse($responseId){
		$sql = "SELECT C.name, A.score_overall, A.score_intonation, A.score_fluency, A.score_rhythm, A.score_spontaneity,
					   A.adding_date, A.comment, B.video_identifier 
			    FROM (evaluation AS A INNER JOIN users AS C ON A.fk_user_id = C.id) 
			    	 LEFT OUTER JOIN evaluation_video AS B on A.id = B.fk_evaluation_id 
				WHERE (A.fk_response_id = '%d') ";

		$searchResults = $this->_listDetailsOfAssessedResponseQuery ( $sql, $responseId );

		return $searchResults;
	}

	private function _listDetailsOfAssessedResponseQuery(){
		$searchResults = array();
		$result = $this->conn->_execute(func_get_args());

		while ($row = $this->conn->_nextRow($result)){
			$temp = new EvaluationVO();

			$temp->userName = $row[0];
			$temp->overallScore = $row[1];
			$temp->intonationScore = $row[2];
			$temp->fluencyScore = $row[3];
			$temp->rhythmScore = $row[4];
			$temp->spontaneityScore = $row[5];
			$temp->addingDate = $row[6];
			$temp->comment = $row[7];
			$temp->evaluationVideoFileIdentifier = $row[8];

			array_push ( $searchResults, $temp );
		}

		return $searchResults;
	}

	public function getEvaluationChartData($responseId){
		$sql = "SELECT U.name, E.score_overall, E.comment
				FROM evaluation AS E INNER JOIN users AS U ON E.fk_user_id = U.ID 
				     INNER JOIN response AS R ON E.fk_response_id = R.id 
				WHERE (R.id = '%d') ";

		$searchResults = $this->_listEvaluationChartDataQuery($sql, $responseId);

		return $searchResults;
	}

	private function _listEvaluationChartDataQuery(){
		$searchResults = array();
		$result = $this->conn->_execute(func_get_args());

		while ($row = $this->conn->_nextRow($result)){
			$temp = new EvaluationVO();

			$temp->userName = $row[0];
			$temp->overallScore = $row[1];
			$temp->comment = $row[2];

			array_push ( $searchResults, $temp );
		}
		return $searchResults;
	}
	
	private function _responseNotEvaluatedByUser($responseId){
		$sql = "SELECT * 
				FROM evaluation e INNER JOIN response r ON e.fk_response_id = r.id
				WHERE (r.id = '%d' AND e.fk_user_id = '%d')";
		$result = $this->conn->_execute($sql, $responseId, $_SESSION['uid']);
		$row = $this->conn->_nextRow($result);
		if($row)
			return false;
		else
			return true;
	}
	
	private function _responseRatingCountBelowThreshold($responseId){
		$sql = "SELECT *
				FROM response
				WHERE id = '%d' AND rating_amount < (SELECT prefValue FROM preferences WHERE prefName='trial.threshold')";
		$result = $this->conn->_execute($sql, $responseId);
		$row = $this->conn->_nextRow($result);
		if($row)
			return true;
		else
			return false;	
	}
	

	public function addAssessment($evalData){
		
		$result = 0;
		$responseId = $evalData->responseId;
		
		//Ensure that this user can evaluate this response
		if(!$this->_responseNotEvaluatedByUser($responseId) || !$this->_responseRatingCountBelowThreshold($responseId))
			return $result;
		
		$this->conn->_startTransaction();
		
		$sql = "INSERT INTO evaluation (fk_response_id, fk_user_id, score_overall, score_intonation, score_fluency, score_rhythm, score_spontaneity, comment, adding_date) VALUES (";
		$sql = $sql . "'%d', ";
		$sql = $sql . "'%d', ";
		$sql = $sql . "'%d', ";
		$sql = $sql . "'%d', ";
		$sql = $sql . "'%d', ";
		$sql = $sql . "'%d', ";
		$sql = $sql . "'%d', ";
		$sql = $sql . "'%s', NOW() )";

		$evaluationId = $this->_create ( $sql, $evalData->responseId, $_SESSION['uid'], $evalData->overallScore,
										 $evalData->intonationScore, $evalData->fluencyScore, $evalData->rhythmScore,
										 $evalData->spontaneityScore, $evalData->comment );
		if(!$evaluationId){
			$this->conn->_failedTransaction();
			throw new Exception("Evaluation save failed");
		}

		$update = $this->_updateResponseRatingAmount($responseId);
		if(!$update){
			$this->conn->_failedTransaction();
			throw new Exception("Evaluation save failed");
		}
		
		//Update the user's credit count
		$creditUpdate = $this->_addCreditsForEvaluating();
		if(!$creditUpdate){
			$this->conn->_failedTransaction();
			throw new Exception("Credit addition failed");
		}

		//Update the credit history
		$creditHistoryInsert = $this->_addEvaluatingToCreditHistory($responseId, $evaluationId);
		if(!$creditHistoryInsert){
			$this->conn->_failedTransaction();
			throw new Exception("Credit history update failed");
		}
		
		if($evaluationId && $update && $creditUpdate && $creditHistoryInsert){
			$this->conn->_endTransaction();
			$result = $this->_getUserInfo();
			$this->_notifyUserAboutResponseBeingAssessed($evalData);
		}
		
		return $result;	
	}

	public function addVideoAssessment($evalData){
		
		$result = 0;
		$responseId = $evalData->responseId;
		
		//Ensure that this user can evaluate this response
		if(!$this->_responseNotEvaluatedByUser($responseId) || !$this->_responseRatingCountBelowThreshold($responseId))
			return $result;
		

		$this->conn->_startTransaction();
		
		//Insert the evaluation data
		$sql = "INSERT INTO evaluation (fk_response_id, fk_user_id, score_overall, score_intonation, score_fluency, score_rhythm, score_spontaneity, comment, adding_date) VALUES (";
		$sql = $sql . "'%d', ";
		$sql = $sql . "'%d', ";
		$sql = $sql . "'%d', ";
		$sql = $sql . "'%d', ";
		$sql = $sql . "'%d', ";
		$sql = $sql . "'%d', ";
		$sql = $sql . "'%d', ";
		$sql = $sql . "'%s', NOW() )";

		$evaluationId = $this->_create ( $sql, $evalData->responseId, $_SESSION['uid'], $evalData->overallScore,
										 $evalData->intonationScore, $evalData->fluencyScore, $evalData->rhythmScore,
										 $evalData->spontaneityScore, $evalData->comment );

		if(!$evaluationId){
			$this->conn->_failedTransaction();
			throw new Exception("Evaluation save failed");
		}
		
		//Insert video evaluation data
		$this->_getResourceDirectories();
		$duration = $this->calculateVideoDuration($evalData->evaluationVideoFileIdentifier);
		$this->takeRandomSnapshot($evalData->evaluationVideoFileIdentifier, $evalData->evaluationVideoFileIdentifier);

		$sql = "INSERT INTO evaluation_video (fk_evaluation_id, video_identifier, source, thumbnail_uri) VALUES (";
		$sql = $sql . "'%d', ";
		$sql = $sql . "'%s', ";
		$sql = $sql . "'Red5', ";
		$sql = $sql . "'%s')";
		
		$evaluationVideoId = $this->_create ( $sql, $evaluationId, $evalData->evaluationVideoFileIdentifier, $evalData->evaluationVideoFileIdentifier.'.jpg' );
		if(!$evaluationVideoId){
			$this->conn->_failedTransaction();
			throw new Exception("Evaluation save failed");
		}
		
		//Update the rating count for this response
	
		$update = $this->_updateResponseRatingAmount($responseId);
		if(!$update){
			$this->conn->_failedTransaction();
			throw new Exception("Evaluation save failed");
		}
		
		//Update the user's credit count
		$creditUpdate = $this->_addCreditsForEvaluating();
		if(!$creditUpdate){
			$this->conn->_failedTransaction();
			throw new Exception("Credit addition failed");
		}

		//Update the credit history
		$creditHistoryInsert = $this->_addEvaluatingToCreditHistory($responseId, $evaluationId);
		if(!$creditHistoryInsert){
			$this->conn->_failedTransaction();
			throw new Exception("Credit history update failed");
		}
		
		if($evaluationId && $update && $creditUpdate && $creditHistoryInsert){
			$this->conn->_endTransaction();
			$result = $this->_getUserInfo();
			$this->_notifyUserAboutResponseBeingAssessed($evalData);
		}
		
		return $result;	
		
	}

	private function _addCreditsForEvaluating() {
		$sql = "UPDATE (users u JOIN preferences p)
			SET u.creditCount=u.creditCount+p.prefValue
			WHERE (u.ID=%d AND p.prefName='evaluatedWithVideoCredits') ";
		return $this->_databaseUpdate ( $sql, $_SESSION['uid'] );
	}

	private function _addEvaluatingToCreditHistory($responseId, $evaluationId){
		$sql = "SELECT prefValue FROM preferences WHERE ( prefName= 'evaluatedWithVideoCredits' )";
		$result = $this->conn->_execute ( $sql );
		$row = $this->conn->_nextRow($result);
		if($row){
			$changeAmount = $row[0];
			$sql = "SELECT fk_exercise_id FROM response WHERE (id='%d')";
			$result = $this->conn->_execute($sql, $responseId);
			$row = $this->conn->_nextRow($result);
			if($row){
				$exerciseId = $row[0];
				$sql = "INSERT INTO credithistory (fk_user_id, fk_exercise_id, fk_response_id, fk_eval_id, changeDate, changeType, changeAmount) ";
				$sql = $sql . "VALUES ('%d', '%d', '%d', '%d', NOW(), '%s', '%d') ";
				return $this->_create($sql, $_SESSION['uid'], $exerciseId, $responseId, $evaluationId, 'evaluation', $changeAmount);
			} else {
				return false;
			}
		} else {
			return false;
		}
	}

	private function _getUserInfo(){

		$sql = "SELECT name, creditCount, joiningDate, isAdmin FROM users WHERE (id = %d) ";

		return $this->_singleQuery($sql, $_SESSION['uid']);
	}

	private function _singleQuery(){
		$valueObject = new UserVO();
		$result = $this->conn->_execute(func_get_args());

		$row = $this->conn->_nextRow($result);
		if ($row)
		{
			$valueObject->name = $row[0];
			$valueObject->creditCount = $row[1];
			$valueObject->joiningDate = $row[2];
			$valueObject->isAdmin = $row[3]==1;
		}
		else
		{
			return false;
		}
		return $valueObject;
	}

	private function takeRandomSnapshot($videoFileName,$outputImageName){
		$videoPath  = $this->red5Path .'/'. $this->evaluationFolder .'/'. $videoFileName . '.flv';
		// where you'll save the image
		$imagePath  = $this->imagePath .'/'. $outputImageName . '.jpg';
		// default time to get the image
		$second = 1;

		// get the duration and a random place within that
		$resultduration = (exec("ffmpeg -i $videoPath 2>&1",$cmd));
		if (preg_match('/Duration: ((\d+):(\d+):(\d+))/s', implode($cmd), $time)) {
			$total = ($time[2] * 3600) + ($time[3] * 60) + $time[4];
			$second = rand(1, ($total - 1));
		}
		$resultsnap = (exec("ffmpeg -y -i $videoPath -r 1 -ss $second -vframes 1 -r 1 -s 120x90 $imagePath 2>&1",$cmd));
		return $resultsnap;
	}

	private function calculateVideoDuration($videoFileName){
		$videoPath  = $this->red5Path .'/'. $this->evaluationFolder .'/'. $videoFileName .'.flv';
		$total = 0;

		$resultduration = (exec("ffmpeg -i $videoPath 2>&1",$cmd));
		if (preg_match('/Duration: ((\d+):(\d+):(\d+))/s', implode($cmd), $time)) {
			$total = ($time[2] * 3600) + ($time[3] * 60) + $time[4];
		}
		return $total;
	}

	private function _getResourceDirectories(){
		$sql = "SELECT prefValue FROM preferences
				WHERE (prefName='exerciseFolder' OR prefName='responseFolder' OR prefName='evaluationFolder') 
				ORDER BY prefName";
		$result = $this->conn->_execute($sql);

		$row = $this->conn->_nextRow($result);
		$this->evaluationFolder = $row ? $row[0] : '';
		$row = $this->conn->_nextRow($result);
		$this->exerciseFolder = $row ? $row[0] : '';
		$row = $this->conn->_nextRow($result);
		$this->responseFolder = $row ? $row[0] : '';
	}

	private function _notifyUserAboutResponseBeingAssessed($evaluation){

		$sql = "SELECT language
				FROM user_languages 
				WHERE ( level=7 AND fk_user_id = (SELECT fk_user_id FROM response WHERE id='%d') ) LIMIT 1";
		$result = $this->conn->_execute($sql, $evaluation->responseId);
		$row = $this->conn->_nextRow($result);
		if($row){
			$locale = $row[0];

			$mail = new Mailer($evaluation->responseUserName);

			$subject = 'Babelium Project: You have been assessed';

			$args = array(
						'DATE' => $evaluation->responseAddingDate,
						'EXERCISE_TITLE' => $evaluation->exerciseTitle,
						'EVALUATOR_NAME' => $evaluation->userName,
						'ASSESSMENT_LINK' => 'http://'.$_SERVER['HTTP_HOST'].'/Main.html#/evaluation/revise/'.$evaluation->responseFileIdentifier,
						'SIGNATURE' => 'The Babelium Project Team');

			if ( !$mail->makeTemplate("assessment_notify", $args, $locale) )
			return null;

			return ($mail->send($mail->txtContent, $subject, $mail->htmlContent));

		} else {
			return false;
		}
	}

	private function _updateResponseRatingAmount($responseId){
		$sql = "UPDATE response SET rating_amount = (rating_amount + 1)
		        WHERE (id = '%d')";

		return $result = $this->_databaseUpdate ( $sql, $responseId );
	}

	private function _databaseUpdate() {
		$result = $this->conn->_execute ( func_get_args() );

		return $result;
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