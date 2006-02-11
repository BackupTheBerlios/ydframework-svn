<?php
	// include xml lib
	include_once('../lib.xml.inc.php');

	// get some xml
	$xml = new XML('includes/some.xml');

	// show it
	echo $xml->toString(1);
?>
