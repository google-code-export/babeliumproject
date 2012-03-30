<?php

require_once(dirname(__FILE__) . "/../Widget.php");

class WAssessedByUser extends Widget
{	
	public static function load($args)
	{
		parent::load($args);
		
		// Prepare template
		self::assign("data", $args[1]);
		
		return self::fetch("evaluation/DetailsAssessedByUser.tpl");
	}
}

?>