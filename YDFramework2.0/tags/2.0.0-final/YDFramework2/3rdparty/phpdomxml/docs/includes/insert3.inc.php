<?php
	$item = $xml->firstChild->lastChild;
	while ($item) {
		echo $item->toString()."\n";
		$item = $item->previousSibling;
	}
?>
