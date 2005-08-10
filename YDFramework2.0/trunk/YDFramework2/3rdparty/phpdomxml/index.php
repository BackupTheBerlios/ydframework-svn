<?php

	// include xml library
	include_once('lib.xml.inc.php');

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
	$rss = new XML('http://sourceforge.net/export/rss2_projsummary.php?group_id=84936');

	// get article items
	$items = $rss->getElementsByTagName('item');

	// loop through items and gather download info
	$downloads = 1270;
	foreach ($items as $k => $item) {
		$a = asArray($item->firstChild);
		if (preg_match("/Downloadable\ files\: (\d+)\ total\ downloads/is", $a['title'], $matches)) {
			$downloads += $matches[1];
		}
	}

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
	"http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
<head>
	<title>phpdomxml-0.9.0 test / example batches</title>
	<meta http-equiv="content-type" content="text/html;charset=utf-8" />
	<link type="text/css" rel="stylesheet" href="docs/index.css" title="phpdomxml test/example stylesheet" />
	<style type="text/css">
		span.html { color: <?=ini_get('highlight.html')?>; }
		span.default { color: <?=ini_get('highlight.default')?>; }
		span.keyword { color: <?=ini_get('highlight.keyword')?>; }
		span.string { color: <?=ini_get('highlight.string')?>; }
		span.comment { color: <?=ini_get('highlight.comment')?> }
	</style>
</head>

<body>

	<div id="container">

	<a href="index.php">Index</a>&nbsp;&nbsp;|&nbsp;&nbsp;<a href="docs/index.html">Quick Docs</a>&nbsp;&nbsp;|&nbsp;&nbsp;<a href="docs/examples.php">Usage Examples</a><br />
	<hr />

	<h1><span class="name">phpdomxml</span>-0.9.0</h1>

	<b>What is phpdomxml?</b>

	<p>
	<span class="name">phpdomxml</span> is an <span class="title" title="Object Oriented Programming">OOP</span> implementation
	of the <span class="title" title="Extensible Markup Language">XML</span> <span class="title" title="Document Object Model">DOM</span> for
	<a href="http://www.php.net"><span class="title" title="PHP: Hypertext Preprocessor">PHP</span></a>. It tries not
	to be a 100% complete implementation of the nifty <a href="http://www.w3.org/TR/2000/REC-DOM-Level-2-Core-20001113/core.html">w3c standards</a>.
	In fact, its aim is to be a light-weight implementation for 'every-day' usage of <span class="title" title="Extensible Markup Language">XML</span>-documents in a <span class="title" title="PHP: Hypertext Preprocessor">PHP</span> 4.10+ environment.
	<span class="name">phpdomxml</span> does not rely on any external libraries, it does however require the <a href="http://www.php.net/xml">expat XML parser functions</a> for <span class="title" title="PHP: Hypertext Preprocessor">PHP</span>, which should be included by default.
	</p>

	<b>Where can I download phpdomxml?</b>

	<p>
	Right here:<br /><br />
	<b>0.9</b>: 14 April 2005<br />
	- <a href="http://prdownloads.sourceforge.net/phpdomxml/phpdomxml-0.9.tar.gz?download" title="Download from sourceforge.net">phpdomxml-0.9.tar.gz</a> (22.7 kb)<br /><br />
	<b>0.8.5</b>: 25 October 2003<br />
	- <a href="http://prdownloads.sourceforge.net/phpdomxml/phpdomxml-0.8.5.tar.gz?download" title="Download from sourceforge.net">phpdomxml-0.8.5.tar.gz</a> (14.7 kb)<br /><br />
	<b>0.8</b>: 1 July 2003<br />
	- <a href="http://prdownloads.sourceforge.net/phpdomxml/phpdomxml-0.8.tar.gz?download" title="Download from sourceforge.net">phpdomxml-0.8.tar.gz</a> (3.9 kb)<br /><br />
	Total downloads: <?=$downloads ?>.
	</p>

	<b>What's new in version 0.9?</b>

	<p>
	Everything: the whole core has been rewritten to fit the design of the <a href="http://www.w3.org/TR/2000/REC-DOM-Level-2-Core-20001113/core.html">w3c</a>.
	</p>

	<b>Where's the documentation?</b>

	<p>
	Right <a href="docs/index.html">here</a>.
	</p>

	<b>Where are the examples?</b>

	<p>
	Right <a href="docs/examples.php">here</a>.
	</p>

	<b>Where can I find this on sourceforge?</b>

	<p>
	Right <a href="http://sourceforge.net/projects/phpdomxml">here</a>.
	</p>

	<hr />

	<p class="copyright">
	&copy; 2005 <a href="http://webtweakers.com">webtweakers.com</a>,
	Bas van Gaalen, 14 April 2005. <a href="http://validator.w3.org/check/referer">valid xhtml</a>
	</p>

	</div>

</body>
</html>
