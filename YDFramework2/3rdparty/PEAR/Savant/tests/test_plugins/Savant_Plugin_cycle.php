<?php

/**
* 
* Example plugin for unit testing.
*
* @version $Id: Savant_Plugin_cycle.php,v 1.2 2003/09/24 16:04:55 pmjones Exp $
*
*/

require_once 'Savant/Plugin.php';

class Savant_Plugin_cycle extends Savant_Plugin {
	function cycle(&$savant)
	{
		return "REPLACES DEFAULT CYCLE";
	}
}
?>