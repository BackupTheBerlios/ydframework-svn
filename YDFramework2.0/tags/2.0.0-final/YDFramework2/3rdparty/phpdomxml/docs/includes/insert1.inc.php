<?php
	// include xml lib 
	include_once('../lib.xml.inc.php');

	// define a list
	$list = array(
		'first in line',
		'second in line',
		'third in line',
		'fourth in line',
		'fifth in line'
	);

	// create empty xml dom object 
	$xml = new XML();

	// create a tree
	$refChild = null;
	$doc = $xml->createElement('document');
	for ($i = 0; $i < count($list); $i++) {

		// create new child
		$child = 'node'.($i+1);
		$$child = $xml->createElement('text-node');
		$$child->setAttribute('id', $child);
		$$child->appendChild($xml->createTextNode($list[$i]));

		// insert in tree & update refChild
		$refChild = $doc->insertBefore($$child, $refChild);
	}

	// append the created 'doc' element to the root
	$xml->appendChild($doc);

	// dump dom using toString
	echo $xml->toString(1);
?>
