<?php

require_once(dirname(__FILE__) . "/Widget.php");

class W404 extends Widget
{	
	public static function load($args)
	{
		parent::load($args);
		
		return self::fetch("404.tpl");
	}
}

?>