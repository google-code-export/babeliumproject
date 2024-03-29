<?php

/**
 * Babelium Project open source collaborative second language oral practice - http://www.babeliumproject.com
 *
 * Copyright (c) 2011 GHyM and by respective authors (see below).
 *
 * This file is part of Babelium Project.
 *
 * Babelium Project is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * Babelium Project is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 */

require_once 'utils/Config.php';
require_once 'utils/Datasource.php';
require_once 'utils/SessionHandler.php';
require_once 'utils/VideoProcessor.php';

/**
 * Class to perform exercise related operations
 *
 * @author Babelium Team
 *
 */
class Exercise {

	private $filePath;
	private $imagePath;
	private $red5Path;
	private $posterPath;

	private $evaluationFolder = '';
	private $exerciseFolder = '';
	private $responseFolder = '';

	private $exerciseGlobalAvgRating;
	private $exerciseMinRatingCount;

	private $conn;
	private $mediaHelper;

	public function __construct() {

		try {
			$verifySession = new SessionHandler();
			$settings = new Config ( );
			$this->filePath = $settings->filePath;
			$this->imagePath = $settings->imagePath;
			$this->posterPath = $settings->posterPath;
			$this->red5Path = $settings->red5Path;
			$this->mediaHelper = new VideoProcessor();
			$this->conn = new Datasource ( $settings->host, $settings->db_name, $settings->db_username, $settings->db_password );

		} catch (Exception $e) {
			throw new Exception($e->getMessage());
		}
	}

	public function addUnprocessedExercise($exercise = null) {

		try {
			$verifySession = new SessionHandler(true);

			if(!$exercise)
				return false;

			$exerciseLevel = new stdClass();
			$exerciseLevel->userId = $_SESSION['uid'];
			$exerciseLevel->suggestedLevel = $exercise->avgDifficulty;

			if(isset($exercise->status) && $exercise->status == 'evaluation-video'){
				$sql = "INSERT INTO exercise (name, title, description, tags, language, source, fk_user_id, adding_date, duration, license, reference, status) ";
				$sql .= "VALUES ('%s', '%s', '%s', '%s', '%s', 'Red5', '%d', now(), '%d', '%s', '%s', '%s') ";
				$lastExerciseId = $this->conn->_insert( $sql, $exercise->name, $exercise->title, $exercise->description, $exercise->tags,
				$exercise->language, $_SESSION['uid'], $exercise->duration, $exercise->license, $exercise->reference, 'UnprocessedNoPractice' );
			} else {
				$sql = "INSERT INTO exercise (name, title, description, tags, language, source, fk_user_id, adding_date, duration, license, reference) ";
				$sql .= "VALUES ('%s', '%s', '%s', '%s', '%s', 'Red5', '%d', now(), '%d', '%s', '%s') ";
				$lastExerciseId = $this->conn->_insert( $sql, $exercise->name, $exercise->title, $exercise->description, $exercise->tags,
				$exercise->language, $_SESSION['uid'], $exercise->duration, $exercise->license, $exercise->reference );
			}
			if($lastExerciseId){
				$exerciseLevel->exerciseId = $lastExerciseId;
				if($this->addExerciseLevel($exerciseLevel))
				return $lastExerciseId;
			}

		} catch (Exception $e) {
			throw new Exception($e->getMessage());
		}

	}

	public function addWebcamExercise($exercise = null) {

		try {

			$verifySession = new SessionHandler(true);
			
			if(!$exercise)
				return false;

			$result = 0;

			set_time_limit(0);
			$this->_getResourceDirectories();


			$videoPath = $this->red5Path .'/'. $this->exerciseFolder .'/'. $exercise->name . '.flv';
			$destPath = $this->red5Path . '/' . $this->responseFolder . '/' . $exercise->name . '.flv';

			$mediaData = $this->mediaHelper->retrieveMediaInfo($videoPath);
			$duration = $mediaData->duration;
			$this->mediaHelper->takeFolderedRandomSnapshots($videoPath, $this->imagePath, $this->posterPath);

			$exerciseLevel = new stdClass();
			$exerciseLevel->userId = $_SESSION['uid'];
			$exerciseLevel->suggestedLevel = $exercise->avgDifficulty;

			$this->conn->_startTransaction();

			$sql = "INSERT INTO exercise (name, title, description, tags, language, source, fk_user_id, adding_date, status, thumbnail_uri, duration, license, reference) ";
			$sql .= "VALUES ('%s', '%s', '%s', '%s', '%s', 'Red5', '%d', now(), 'Available', '%s', '%d', '%s', '%s') ";

			$lastExerciseId = $this->conn->_insert( $sql, $exercise->name, $exercise->title, $exercise->description, $exercise->tags,
			$exercise->language, $_SESSION['uid'], 'default.jpg', $duration, $exercise->license, $exercise->reference );

			if(!$lastExerciseId){
				$this->conn->_failedTransaction();
				throw new Exception ("Exercise save failed.");
			}

			$exerciseLevel->exerciseId = $lastExerciseId;
			$insertLevel = $this->addExerciseLevel($exerciseLevel);
			if(!$insertLevel){
				$this->conn->_failedTransaction();
				throw new Exception ("Exercise level save failed.");
			}

			if(isset($exercise->status) && $exercise->status == 'evaluation-video'){

				$sql = "UPDATE exercise SET name = NULL, thumbnail_uri='nothumb.png' WHERE ( id=%d )";
				$update = $this->conn->_update($sql,$lastExerciseId);
				if(!$update){
					$this->conn->_failedTransaction();
					throw new Exception("Couldn't update no-practice exercise. Changes rollbacked.");
				}
				$sql = "INSERT INTO response (fk_user_id, fk_exercise_id, file_identifier, is_private, thumbnail_uri, source, duration, adding_date, rating_amount, character_name, fk_transcription_id, fk_subtitle_id)
					VALUES (%d, %d, '%s', false, 'default.jpg', 'Red5', %d, NOW(), 0, 'None', NULL, NULL)";
				$lastResponseId = $this->conn->_insert($sql,$_SESSION['uid'],$lastExerciseId,$exercise->name,$duration);
				if(!$lastResponseId){
					$this->conn->_failedTransaction();
					throw new Exception("Couldn't insert no-practice response. Changes rollbacked.");
				}
				
				//Move the file from exercises folder to the response folder
				$renameResult = @rename($videoPath, $destPath);
				if(!$renameResult){
					$this->conn->_failedTransaction();
					throw new Exception("Couldn't move transcoded file. Changes rollbacked.");
				}
			}else{

				//Update the user's credit count
				$creditUpdate = $this->_addCreditsForUploading();
				if(!$creditUpdate){
					$this->conn->_failedTransaction();
					throw new Exception("Credit addition failed");
				}
				//Update the credit history
				$creditHistoryInsert = $this->_addUploadingToCreditHistory($lastExerciseId);
				if(!$creditHistoryInsert){
					$this->conn->_failedTransaction();
					throw new Exception("Credit history update failed");
				}
			}
			
			$this->conn->_endTransaction();
			$result = $this->_getUserInfo();

			return $result;

		} catch (Exception $e) {
			throw new Exception($e->getMessage());
		}
	}

	private function addExerciseLevel($exerciseLevel){
		$sql = "INSERT INTO exercise_level (fk_exercise_id, fk_user_id, suggested_level, suggest_date)
						 VALUES ('%d', '%d', '%d', NOW()) ";
		return $this->conn->_insert($sql, $exerciseLevel->exerciseId, $_SESSION['uid'], $exerciseLevel->suggestedLevel);
	}

	private function _addCreditsForUploading() {
		$sql = "UPDATE (users u JOIN preferences p)
				SET u.creditCount=u.creditCount+p.prefValue
				WHERE (u.ID=%d AND p.prefName='uploadExerciseCredits') ";
		return $this->conn->_update ( $sql, $_SESSION['uid'] );
	}

	private function _addUploadingToCreditHistory($exerciseId){
		$sql = "SELECT prefValue FROM preferences WHERE ( prefName='uploadExerciseCredits' )";
		$result = $this->conn->_singleSelect ( $sql );
		if($result){
			$sql = "INSERT INTO credithistory (fk_user_id, fk_exercise_id, changeDate, changeType, changeAmount) ";
			$sql = $sql . "VALUES ('%d', '%d', NOW(), '%s', '%d') ";
			return $this->conn->_insert($sql, $_SESSION['uid'], $exerciseId, 'exercise_upload', $result->prefValue);
		} else {
			return false;
		}
	}

	private function _getUserInfo(){

		$sql = "SELECT name, creditCount, joiningDate, isAdmin FROM users WHERE (id = %d) ";

		return $this->conn->_singleSelect($sql, $_SESSION['uid']);
	}

	private function _getResourceDirectories(){
		$sql = "SELECT prefValue 
				FROM preferences
				WHERE (prefName='exerciseFolder' OR prefName='responseFolder' OR prefName='evaluationFolder') 
				ORDER BY prefName";
		$result = $this->conn->_multipleSelect($sql);
		if($result){
			$this->evaluationFolder = $result[0] ? $result[0]->prefValue : '';
			$this->exerciseFolder = $result[1] ? $result[1]->prefValue : '';
			$this->responseFolder = $result[2] ? $result[2]->prefValue : '';
		}
	}

	public function getExercises(){
		$sql = "SELECT e.id, 
			       e.title, 
			       e.description, 
			       e.language, 
			       e.tags, 
			       e.source, 
			       e.name, 
			       e.thumbnail_uri as thumbnailUri,
       			       e.adding_date as addingDate, 
			       e.duration, 
			       u.name as userName, 
			       avg (suggested_level) as avgDifficulty, 
			       e.status, 
			       e.license, 
			       e.reference
			FROM   exercise e INNER JOIN users u ON e.fk_user_id= u.ID
       				   LEFT OUTER JOIN exercise_score s ON e.id=s.fk_exercise_id
       				   LEFT OUTER JOIN exercise_level l ON e.id=l.fk_exercise_id
       			WHERE (e.status = 'Available')
				GROUP BY e.id
				ORDER BY e.adding_date DESC";

		$searchResults = $this->conn->_multipleSelect($sql);
		foreach($searchResults as $searchResult){
			$searchResult->avgRating = $this->getExerciseAvgBayesianScore($searchResult->id)->avgRating;
		}

		return $searchResults;
	}

	/**
	 * Return exercise by id
	 */
	public function getExerciseById($id = 0){
		if(!$id)
			return;
			
		$sql = "SELECT e.id, 
			       e.title, 
			       e.description, 
			       e.language, 
			       e.tags, 
			       e.source, 
			       e.name, 
			       e.thumbnail_uri as thumbnailUri,
			       e.adding_date as addingDate, 
			       e.duration, 
			       u.name as userName, 
			       avg (suggested_level) as avgDifficulty,
			       e.status, 
			       e.license, 
			       e.reference
			FROM   exercise e INNER JOIN users u ON e.fk_user_id= u.ID
			LEFT OUTER JOIN exercise_score s ON e.id=s.fk_exercise_id
			LEFT OUTER JOIN exercise_level l ON e.id=l.fk_exercise_id
			WHERE (e.id = %d AND e.status='Available')
			GROUP BY e.id";
		$result = $this->conn->_singleSelect($sql,$id);
		if($result)
			$result->avgRating = $this->getExerciseAvgBayesianScore($result->id)->avgRating;

		return $result;
	}

	/**
	 * Return exercise by name
	 */
	public function getExerciseByName($name = null){
		if(!$name)
			return;
			
		$sql = "SELECT e.id, 
					   e.title, 
					   e.description, 
					   e.language, 
					   e.tags, 
					   e.source, 
					   e.name, 
					   e.thumbnail_uri as thumbnailUri,
       				   e.adding_date, 
       				   e.duration, 
       				   u.name as userName, 
       				   avg (suggested_level) as avgDifficulty, 
       				   e.status, 
       				   e.license, 
       				   e.reference
				FROM   exercise e INNER JOIN users u ON e.fk_user_id= u.ID
       				   LEFT OUTER JOIN exercise_score s ON e.id=s.fk_exercise_id
       				   LEFT OUTER JOIN exercise_level l ON e.id=l.fk_exercise_id
       			WHERE (e.name = '%s')
				GROUP BY e.id
				LIMIT 1";

		$result = $this->conn->_singleSelect($sql,$name);
		if($result)
			$result->avgRating = $this->getExerciseAvgBayesianScore($result->id)->avgRating;

		return $result;
	}

	public function getExercisesUnfinishedSubtitling(){
		try {
			$verifySession = new SessionHandler(true);

			$sql = "SELECT e.id, 
				       e.title, 
				       e.description, 
				       e.language, 
				       e.tags, 
				       e.source, 
				       e.name, 
				       e.thumbnail_uri as thumbnailUri,
				       e.adding_date as addingDate, 
				       e.duration, 
				       u.name as userName, 
				       avg (suggested_level) as avgDifficulty, 
				       e.status, 
				       e.license, 
				       e.reference
				FROM exercise e 
					 	 INNER JOIN users u ON e.fk_user_id= u.ID
	 				 	 LEFT OUTER JOIN exercise_score s ON e.id=s.fk_exercise_id
       				 	 LEFT OUTER JOIN exercise_level l ON e.id=l.fk_exercise_id
       				 	 LEFT OUTER JOIN subtitle a ON e.id=a.fk_exercise_id
       			 	 	 WHERE (e.status = 'Available' AND a.complete IS NULL OR a.complete=0)
				 	GROUP BY e.id
				 	ORDER BY e.adding_date DESC";

			$searchResults = $this->conn->_multipleSelect($sql);
			foreach($searchResults as $searchResult){
				$searchResult->avgRating = $this->getExerciseAvgBayesianScore($searchResult->id)->avgRating;
			}

			//Filter searchResults to include only the "evaluate" languages of the user
			$filteredResults = $this->filterByLanguage($searchResults, 'evaluate');
			return $filteredResults;
		} catch (Exception $e){
			throw new Exception($e->getMessage());
		}
	}

	public function getRecordableExercises(){
		$sql = "SELECT e.id, 
			       e.title, 
			       e.description, 
			       e.language, 
			       e.tags, 
			       e.source, 
			       e.name, 
			       e.thumbnail_uri as thumbnailUri,
       			       e.adding_date as addingDate, 
			       e.duration, 
			       u.name as userName, 
       			       avg (suggested_level) as avgDifficulty,
			       e.status, 
			       e.license, 
			       e.reference
			       FROM   exercise e 
				 		INNER JOIN users u ON e.fk_user_id= u.ID
				 		INNER JOIN subtitle t ON e.id=t.fk_exercise_id
       				    LEFT OUTER JOIN exercise_score s ON e.id=s.fk_exercise_id
       				    LEFT OUTER JOIN exercise_level l ON e.id=l.fk_exercise_id
       			 WHERE e.status = 'Available' AND t.complete = 1
				 GROUP BY e.id
				 ORDER BY e.adding_date DESC, e.language DESC";
		
		$searchResults = $this->conn->_multipleSelect($sql);
		foreach($searchResults as $searchResult){
			$searchResult->avgRating = $this->getExerciseAvgBayesianScore($searchResult->id)->avgRating;
		}

		try {
			$verifySession = new SessionHandler(true);
			$filteredResults = $this->filterByLanguage($searchResults, 'practice');
			return $filteredResults;
		} catch (Exception $e) {
			return $searchResults;
		}

	}

	public function filterByLanguage($searchList, $languagePurpose){
		if(!isset($_SESSION['user-languages']) || !is_array($_SESSION['user-languages']) || count($_SESSION['user-languages']) < 1)
			return $searchList;
		if($languagePurpose != 'evaluate' && $languagePurpose != 'practice')
			return $searchList;

		$filteredList = array();
		foreach ($searchList as $listItem){
			foreach ($_SESSION['user-languages'] as $userLanguage) {
				if ($userLanguage->purpose == $languagePurpose){
					if($listItem->language == $userLanguage->language && $listItem->avgDifficulty <= $userLanguage->level){
						array_push($filteredList, $listItem);
						break;
					}
				}
			}
		}
		return $filteredList;

	}

	public function getExerciseLocales($exerciseId=0) {
		if(!$exerciseId)
			return false;

		$sql = "SELECT DISTINCT language as locale FROM subtitle
				WHERE fk_exercise_id = %d";

		$results = $this->conn->_multipleSelect ( $sql, $exerciseId );

		return $results; // return languages
	}

	public function addInappropriateExerciseReport($report = null){
		try {
			$verifySession = new SessionHandler(true);

			if(!$report)
				return false;

			$result = $this->userReportedExercise($report);

			if (!$result){
				// The user is reporting an innapropriate exercise
				$sql = "INSERT INTO exercise_report (fk_exercise_id, fk_user_id, reason, report_date)
				    VALUES ('%d', '%d', '%s', NOW() )";

				$result = $this->conn->_insert($sql, $report->exerciseId, $_SESSION['uid'], $report->reason);
				//$this->notifyExerciseReported($report);
				return $result;
			} else {
				return 0;
			}
		} catch (Exception $e) {
			throw new Exception($e->getMessage());
		}
	}

	private function notifyExerciseReported($report){
		$mail = new Mailer();
		$subject = 'Babelium Project: Exercise reported';
		$text = sprintf("Exercise (id=%d) has been reported to be %s by the user (id=%d)", $report->exerciseId, $report->reason, $_SESSION['uid']);
		return ($mail->send($text, $subject, null));
	}

	public function addExerciseScore($score = null){
		try {
			$verifySession = new SessionHandler(true);
			
			if(!$score)
				return false;

			$result = $this->userRatedExercise($score);
			if (!$result){
				//The user can add a score

				$sql = "INSERT INTO exercise_score (fk_exercise_id, fk_user_id, suggested_score, suggestion_date)
			        VALUES ( '%d', '%d', '%d', NOW() )";

				$insert_result = $this->conn->_insert($sql, $score->exerciseId, $_SESSION['uid'], $score->suggestedScore);

				//return $this->getExerciseAvgScore($score->exerciseId);
				return $this->getExerciseAvgBayesianScore($score->exerciseId);

			} else {
				//The user has already given a score ignore the input.
				return 0;
			}
		} catch (Exception $e) {
			throw new Exception($e->getMessage());
		}
	}

	/**
	 * Check if the user has already rated this exercise today
	 * @param stdClass $score
	 * @throws Exception
	 */
	public function userRatedExercise($score = null){
		try {
			$verifySession = new SessionHandler(true);
	
			if(!$score)
				return false;
			
			$sql = "SELECT *
		        	FROM exercise_score 
		        	WHERE ( fk_exercise_id='%d' AND fk_user_id='%d' AND CURDATE() <= suggestion_date )";
			return $this->conn->_singleSelect ( $sql, $score->exerciseId, $_SESSION['uid']);
		} catch (Exception $e) {
			throw new Exception($e->getMessage());
		}
	}

	/**
	 * Check if the user has already reported about this exercise
	 * @param stdClass $report
	 */
	public function userReportedExercise($report = null){
		try {
			$verifySession = new SessionHandler(true);
			if(!$report)
				return false;
				
			$sql = "SELECT *
				FROM exercise_report 
				WHERE ( fk_exercise_id='%d' AND fk_user_id='%d' )";
			return $this->conn->_singleSelect ($sql, $report->exerciseId, $_SESSION['uid']);
		} catch (Exception $e) {
			throw new Exception($e->getMessage());
		}
	}

	private function getExerciseAvgScore($exerciseId){

		$sql = "SELECT e.id, 
			       avg (suggested_score) as avgRating, 
			       count(suggested_score) as ratingCount
				FROM exercise e LEFT OUTER JOIN exercise_score s ON e.id=s.fk_exercise_id    
				WHERE (e.id = '%d' ) GROUP BY e.id";

		return $this->conn->_singleSelect($sql, $exerciseId);
	}

	/**
	 * The average score is not accurate information in statistical terms, so we use a weighted value
	 * @param int $exerciseId
	 */
	public function getExerciseAvgBayesianScore($exerciseId = 0){
		if(!$exerciseId)
			return false;
		
		
		if(!isset($this->exerciseMinRatingCount)){
			$sql = "SELECT prefValue FROM preferences WHERE (prefName = 'minVideoRatingCount')";

			$result = $this->conn->_singleSelect($sql);

			if($result)
				$this->exerciseMinRatingCount = $result->prefValue;
			else
				$this->exerciseMinRatingCount = 0;
		}

		if(!isset($this->exerciseGlobalAvgRating)){
			$this->exerciseGlobalAvgRating = $this->getExercisesGlobalAvgScore();
		}

		$exerciseRatingData = $this->getExerciseAvgScore($exerciseId);

		$exerciseAvgRating = $exerciseRatingData->avgRating;
		$exerciseRatingCount = $exerciseRatingData->ratingCount;

		/* Avoid division by zero errors */
		if ($exerciseRatingCount == 0) $exerciseRatingCount = 1;

		$exerciseBayesianAvg = ($exerciseAvgRating*($exerciseRatingCount/($exerciseRatingCount + $this->exerciseMinRatingCount))) +
							   ($this->exerciseGlobalAvgRating*($this->exerciseMinRatingCount/($exerciseRatingCount + $this->exerciseMinRatingCount)));

		$exerciseRatingData->avgRating = $exerciseBayesianAvg;

		return $exerciseRatingData;

	}

	private function getExercisesGlobalAvgScore(){
		$sql = "SELECT avg(suggested_score) as globalAvgScore FROM exercise_score ";

		return ($result = $this->conn->_singleSelect($sql)) ? $result->globalAvgScore : 0;
	}


}

?>
