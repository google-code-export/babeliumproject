<?php

class UserLanguageVO{
	
	public $id;
	public $userId;
	public $language; //Use the language's two digit code: ES, EU, FR, EN...
	public $level; //Level goes from 1 to 6. 7 used for mother tongue
	public $positives_to_next_level; //An indicator of how many assessments or steps are needed to advance to the next level

	public $_explicitType = "UserLanguageVO";
	
}
?>