<?php

/**
*
* Example plugin for unit testing.
* 
* @version $Id: Savant_Plugin_fester.php,v 1.3 2003/10/02 19:03:39 pmjones Exp $
*
*/

require_once 'Savant/Plugin.php';

class Savant_Plugin_fester extends Savant_Plugin {
	
	var $message = "Fester";
	var $count = 0;
	
	function Savant_Plugin_fester(&$savant)
	{
		// initialize the parent constructor
		$this->Savant_Plugin(&$savant);
		
		// do some other constructor stuff
		$this->message .= " is printing this: ";
	}
	
	function fester(&$savant, &$text)
	{
		$output = $this->message . $text . " ({$this->count})";
		$this->count++;
		return $output;
	}
}
?>