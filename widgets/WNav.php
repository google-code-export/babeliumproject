<?php

include_once(__DIR__."/../util/interfaces/iWidget.php");

class WNav implements IWidget
{	
	public static function load($args)
	{
		$cfg = Config::getInstance();
		
		$cfg->smarty->assign("moduleTitle", $args[1]);
		$cfg->smarty->assign("sectionTitle", $args[2]);
		return $cfg->smarty->fetch($args[0] . ".tpl");
	}
}

?>