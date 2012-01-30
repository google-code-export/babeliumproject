<?php

require_once(dirname(__FILE__) . "/Module.php");

// Require service classes
require_once(dirname(__FILE__) . "/../api/services/Register.php");

class MRegister extends Module
{	
	public static function load($args)
	{
		parent::load($args);
		
		// HTML content that will be returned
		$content = "";
		
		$loggedIn = self::$sessionManager->isLoggedIn();
		
		//Is the user is already logged in don't let him register
		if(!$loggedIn){
			if ( self::$action == "newUser" && isset(self::$params) )
			{
				$response = self::registerUser(self::$params);
				if(is_object($response)){
					//Successful registration, returns the user data
				} else {
					//There was a problem while registering, for now echo this content
					$content = $response;
				}
			}
			elseif ( self::$action == "activate" && isset(self::$params))
			{
				//
			}
			else
			{
				$content = WidgetLoader::loadWidget("Register");
			}
		} else {
			// Return false or something to tell the client that it's already loggedIn and shouldn't be here
		}
	
		
		// We have an initial set of languages, one for the mother tongue and another for the language the user wants to practice.
		// Afterwards, we should be able to add and remove items dynamically.
		//$content = $content . WidgetLoader::loadWidget("RegisterLanguages");
		
		return $content;
	}
	
	private function registerUser($data){
		$reg = new Register();
		$response = $reg->newUser(Convert::array_to_object(Zend_Json::decode(base64_decode($data))));
		
		return $response;
	}
	
}

?>