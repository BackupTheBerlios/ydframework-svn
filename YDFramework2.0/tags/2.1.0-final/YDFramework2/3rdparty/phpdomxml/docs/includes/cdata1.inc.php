<?php
	// include xml lib
	include_once('../lib.xml.inc.php');

	// create xml dom object
	$xml = new XML();

	// create comment tag and show it
	$comment = $xml->createComment('Hello World!');
	echo $comment->toString();
?>
