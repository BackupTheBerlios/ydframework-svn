<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
	"http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
<head>
	<title>phpdomxml-0.9.0 test / example batches</title>
	<meta http-equiv="content-type" content="text/html;charset=utf-8" />
	<link type="text/css" rel="stylesheet" href="index.css" title="phpdomxml test/example stylesheet" />
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

	<a href="../index.php">Index</a>&nbsp;&nbsp;|&nbsp;&nbsp;<a href="index.html">Quick Docs</a>&nbsp;&nbsp;|&nbsp;&nbsp;<a href="examples.php">Usage Examples</a><br />
	<hr />

	<h1><span class="name">phpdomxml</span>-0.9.0 Usage Examples (and test-case)</h1>

	<p>
	This set of usage examples shows live output of the presented code snippets. And besides
	being a useful example base, it also serves as a unit test system for the functionality of
	<span class="name">phpdomxml</span>. Please pick a test batch to run from the list below.
	</p>

<?php

	// phpdomxml test/example suite

	// (ps: I realise it's kinda awkward to use xml dom-methods in a
	// test-suite for exactly that, but fortunately the methods I use
	// below have already been tested thorougly before I made this.)

	// get batch to run
	$getbatch = isset($_GET['batch'])?$_GET['batch']:null;

	// include lib and get test batches from xml file
	include_once('../lib.xml.inc.php');
	$xml = new XML('tests.xml');

	// get list of batches
	$batches = $xml->getElementsByTagName('batch');

	// loop through all batches and list some info
	$i = 1;
	$s = '<p>';
	foreach ($batches as $batch)	 {

		$id = $batch->getAttribute('id');
		$title = $batch->firstChild->firstChild->data;
		$nrtests = count($batch->getElementsByTagName('test'));
		$pl = ($nrtests > 1);
		if ($id == $getbatch) $s .= '<b>';
		$s .= ($i++).'. <a href="'.$_SERVER['PHP_SELF'].
			'?batch='.$id.'">'.$title.'</a>'.
			' ('.$nrtests.' test'.($pl?'s':'').')<br />';
		if ($id == $getbatch) $s .= '</b>';
		$s .= "\n";

	}
	$s .= '</p>';

	// now get batch to run
	if (!is_null($getbatch)) {

		$s .= '<hr />';
		$batch = $xml->getElementById($getbatch);
		$title = $batch->firstChild->firstChild->data;
		$s .= '<p>Running batch: <b>'.$title.'</b></p>';

		// get list of tests in this batch
		$tests = $batch->getElementsByTagName('test');

		// make highlight_file xhtml compatible, thanks to tom pike (see php's manual)
		$search[0] = '<font color="'.ini_get('highlight.html').'">';
		$search[1] = '<font color="'.ini_get('highlight.default').'">';
		$search[2] = '<font color="'.ini_get('highlight.keyword').'">';
		$search[3] = '<font color="'.ini_get('highlight.string').'">';
		$search[4] = '<font color="'.ini_get('highlight.comment').'">';
		$search[5] = '</font>';
		$search[6] = '<br />';
		$search[7] = '\r';
		$search[8] = '<code>';
		$search[9] = '</code>';
		$replace[0] = '<span class="html">';
		$replace[1] = '<span class="default">';
		$replace[2] = '<span class="keyword">';
		$replace[3] = '<span class="string">';
		$replace[4] = '<span class="comment">';
		$replace[5] = '</span>';
		$replace[6] = "<br />\n";
		$replace[7] = '';
		$replace[8] = '';
		$replace[9] = '';

		// loop through tests and run 'em sequentially
		$_i = 1;
		foreach ($tests as $_test) {

			// get test info
			$_xmlcomment = $_test->firstChild->firstChild->nodeValue;
			$_xmlcode = $_test->getAttribute('file');
			$_xmloutput = $_test->getAttribute('output');

			if (!empty($_xmlcomment) && !empty($_xmlcode)) {

				// highlight source code + line numbering
				$_source = @highlight_file('includes/'.$_xmlcode, 1);
				$_source = str_replace($search, $replace, $_source);
				$_lines = substr_count($_source, '<br />')+1;
				$_numbers = '';
				while ($_lines-- > 1) $_numbers = $_lines.'<br />'.$_numbers;

				// line number and code in seperate td's, so code can be selected
				$_source = '<table class="code" cellpadding="0" cellspacing="0"><tr>'.
					'<td class="lines">'.$_numbers.'</td><td class="code">'.$_source.'</td></tr></table>';

				// show info
				$s .= '<a name="example'.$_i.'"></a>';
				$s .= '<h2>Example #'.$_i.'</h2>';
				$s .= '<p><b>Comment:</b></p>';
				$s .= '<p><i>'.$_xmlcomment.'</i></p>';
				$s .= '<p><b>Code:</b></p>';
				$s .= $_source;
				$s .= '<p><b>Output:</b></p>';
				$_i++;

				// run test and get result
				ob_start();
				@include('includes/'.$_xmlcode);
				if (!isset($_xmloutput) || $_xmloutput != 'raw') {
					$s .= '<pre>'.htmlentities(preg_replace("/\t/", "    ", ob_get_contents())).'</pre>';
				} else {
					$s .= preg_replace("/\t/", "    ", ob_get_contents());
				}
				ob_end_clean();

			}

		}

	}

	// show output
	echo $s;
	flush();

?>

	<hr />

	<p class="copyright">
	&copy; 2005 <a href="http://webtweakers.com">webtweakers.com</a>,
	Bas van Gaalen, 14 April 2005. <a href="http://validator.w3.org/check/referer">valid xhtml</a>
	</p>

	</div>

</body>
</html>
