<?php
	$item = $xml->firstChild->firstChild;
	while ($item) {
		echo $item->toString()."\n";
		$item = $item->nextSibling;
	}
?>
