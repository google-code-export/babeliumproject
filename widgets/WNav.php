<?php

require_once(dirname(__FILE__) . "/../util/interfaces/iWidget.php");
require_once(dirname(__FILE__) . "/../config/Config.php");

require_once(dirname(__FILE__) . "/../core/SessionManager.php");

class WNav implements IWidget
{	
	public static function load($args)
	{
		$cfg = Config::getInstance();
		
		$cfg->smarty->assign("user", $_SESSION['user-data']);
		$cfg->smarty->assign("isLoggedIn", $_SESSION['logged']);
		$cfg->smarty->assign("moduleTitle", $args[1]);
		$cfg->smarty->assign("sectionTitle", $args[2]);
		return $cfg->smarty->fetch("main/Navigation.tpl");
	}
}

?>