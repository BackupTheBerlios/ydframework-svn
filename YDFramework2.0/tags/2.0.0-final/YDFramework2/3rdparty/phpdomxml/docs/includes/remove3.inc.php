<?php
	// remove item item from its parent
	$xml->firstChild->removeChild($item);

	// show new tree
	echo $xml->toString(1);
?>
