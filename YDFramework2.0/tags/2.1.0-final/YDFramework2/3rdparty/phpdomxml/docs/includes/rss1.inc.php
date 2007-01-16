<script type="text/javascript">
	function dspSwitch(id) {
		var d = document.getElementById(id).style;
		d.display = (d.display == 'block')?'none':'block';
	}
</script>
<?php
	// include xml lib
	include_once('../lib.xml.inc.php');

	// convert an rdf item to an associative array
	function asArray($item) {
		$a = array();
		while ($item) {
			$a[$item->nodeName] = $item->firstChild->nodeValue;
			$item = $item->nextSibling;
		}
		return $a;
	}

	// get the feed
	$rdf = new XML('http://slashdot.org/index.rss');

	// get article items
	$items = $rdf->getElementsByTagName('item');

	// loop and gather info
	$out = '';
	foreach ($items as $k => $item) {
		$a = asArray($item->firstChild);
		$out .= '<a href="javascript:;" '.
			'onclick="dspSwitch(\'item'.$k.'\')">'.
			$a['title'].'</a> ('.$a['dc:date'].')<br />'.
			'<div id="item'.$k.'" style="display:none">'.
			$a['description'].
			'&nbsp;[<a href="'.$a['link'].'">more</a>]'.
			'<br /><br /></div>';
	}

	// display info
	echo $out;

?>
