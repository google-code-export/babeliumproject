<?php

include_once(dirname(__FILE__) . "/../util/interfaces/iWidget.php");
include_once(dirname(__FILE__) . "/../config/Config.php");

class WHead implements IWidget
{	
	public static function load($args)
	{
		$cfg = Config::getInstance();
		$cfg->smarty->assign("MODULE_GATEWAY", $cfg->module_bridge);
		
		return $cfg->smarty->fetch($args[0] . ".tpl");
	}
}

?>