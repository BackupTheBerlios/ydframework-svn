<?php
	// include xml lib
	include_once('../lib.xml.inc.php');

	// define list
	$list = array(
		1 => 'this is a text-node',
		'and here is another one',
		'this is the 3rd element',
		'and this is the final item!'
	);

	// create new xml object
	$xml = new XML();

	// create xml element
	$root = $xml->createElement('xml');
	$xml->appendChild($root);

	// loop through list
	foreach ($list as $i => $text) {

		// set name of variable variable
		$item = 'item'.$i;

		// create 'item' element
		$$item = $xml->createElement('item');

		// set an attribute, called 'id'
		$$item->setAttribute('id', $item);

		// append a text-node to 'item'
		$$item->appendChild(
			$xml->createTextNode($text));

		// append the new item to the root element
		$root->appendChild($$item);

	}

	// show the dom
	echo $xml->toString(1);
?>
