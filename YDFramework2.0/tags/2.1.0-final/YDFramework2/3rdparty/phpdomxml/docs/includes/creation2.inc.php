<?php
	// include xml lib
	include_once('../lib.xml.inc.php');

	// create new xml object
	$xml = new XML();

	// create xml element
	$root = $xml->createElement('xml');
	$xml->appendChild($root);

	// create 1st item
	$item1 = $xml->createElement('item');
	$item1->setAttribute('id', 'item1');
	$item1->appendChild(
		$xml->createTextNode('this is a text-node'));
	$root->appendChild($item1);

	// create 2nd item
	$item2 = $xml->createElement('item');
	$item2->setAttribute('id', 'item2');
	$item2->appendChild(
		$xml->createTextNode('and here is another one'));
	$root->appendChild($item2);

	// create 3rd item
	$item3 = $xml->createElement('item');
	$item3->setAttribute('id', 'item3');
	$item3->appendChild(
		$xml->createTextNode('this is the 3rd element'));
	$root->appendChild($item3);

	// create 4th item
	$item4 = $xml->createElement('item');
	$item4->setAttribute('id', 'item4');
	$item4->appendChild(
		$xml->createTextNode('and this is the last item'));
	$root->appendChild($item4);

	// show the dom
	echo $xml->toString(1);
?>
