<?php
//////////////////////////////////////////////////////////////
///  phpThumb() by James Heinrich <info@silisoftware.com>   //
//        available at http://phpthumb.sourceforge.net     ///
//////////////////////////////////////////////////////////////
///                                                         //
// See: phpthumb.changelog.txt for recent changes           //
// See: phpthumb.readme.txt for usage instructions          //
//                                                         ///
//////////////////////////////////////////////////////////////

// this script relies on the superglobal arrays, fake it here for old PHP versions
if (phpversion() < '4.1.0') {
	$_SERVER  = $HTTP_SERVER_VARS;
	$_REQUEST = $HTTP_GET_VARS;
}

error_reporting(E_ALL);
ini_set('display_errors', '1');


// returned the fixed string if the evil "magic_quotes_gpc" setting is on
if (get_magic_quotes_gpc()) {
	$RequestVarsToStripSlashes = array('src', 'wmf', 'file', 'err', 'goto');
	foreach ($RequestVarsToStripSlashes as $key) {
		if (isset($_REQUEST[$key])) {
			$_REQUEST[$key] = stripslashes($_REQUEST[$key]);
		}
	}
}

// instantiate a new phpThumb() object
require_once('phpthumb.class.php');
$phpThumb = new phpThumb();

require_once('phpthumb.config.php');
foreach ($PHPTHUMB_CONFIG as $key => $value) {
	$keyname = 'config_'.$key;
	$phpThumb->$keyname = $value;
}
foreach ($_REQUEST as $key => $value) {
	$phpThumb->$key = $value;
}

////////////////////////////////////////////////////////////////
// Debug output, to try and help me diagnose problems
if (@$_REQUEST['phpThumbDebug'] == '1') {
	$phpThumb->phpThumbDebug();
}
////////////////////////////////////////////////////////////////


// check to see if file already exists in cache, and output it with no processing if it does
if (!empty($this->config['cache_directory']) && empty($_REQUEST['phpThumbDebug'])) {
	$cache_filename = $phpThumb->GenerateCachedFilename();
	if (is_file($cache_filename)) {
		header('Content-type: image/'.$thumbnailFormat);
		@readfile($cache_filename);
		exit;
	}
}


////////////////////////////////////////////////////////////////
// You may want to pull data from a database rather than a physical file
// If so, uncomment the following $SQLquery line (modified to suit your database)
// Note: this must be the actual binary data of the image, not a URL or filename
// see http://www.billy-corgan.com/blog/archive/000143.php for a brief tutorial on this section

//$SQLquery = 'SELECT `Picture` FROM `products` WHERE (ProductID = \''.mysql_escape_string($_REQUEST['id']).'\')';
if (!empty($SQLquery)) {

	// change this information to match your server
	$server   = 'localhost';
	$username = 'user';
	$password = 'password';
	$database = 'database';
	if ($cid = @mysql_connect($server, $username, $password)) {
		if (@mysql_select_db($database, $cid)) {
			if ($result = mysql_query($SQLquery, $cid)) {
				if ($row = @mysql_fetch_array($result)) {
					mysql_free_result($result);
					mysql_close($cid);
					$phpThumb->rawImageData = $row[0];
					unset($row);
				} else {
					$phpThumb->ErrorImage('no matching data in database.');
					//$phpThumb->ErrorImage('no matching data in database. MySQL said: "'.mysql_error($cid).'"');
				}
			} else {
				$phpThumb->ErrorImage('Error in MySQL query: "'.mysql_error($cid).'"');
			}
		} else {
			$phpThumb->ErrorImage('cannot select MySQL database');
		}
	} else {
		$phpThumb->ErrorImage('cannot connect to MySQL server');
	}

} elseif (empty($_REQUEST['src'])) {

	$phpThumb->ErrorImage('Usage: '.$_SERVER['PHP_SELF'].'?src=/path/and/filename.jpg'."\n".'read Usage comments for details');

} elseif (substr(@$this->src, 0, strlen(strtolower('http://'))) == 'http://') {

	ob_start();
	if ($fp = fopen($this->src, 'rb')) {

		$phpThumb->rawImageData = '';
		do {
			$buffer = fread($fp, 8192);
			if (strlen($buffer) == 0) {
				break;
			}
			$phpThumb->rawImageData .= $buffer;
		} while (true);
		fclose($fp);

	} else {

		$fopen_error = ob_get_contents();
		ob_end_clean();
		if (ini_get('allow_url_fopen')) {
			$phpThumb->ErrorImage('cannot open "'.$this->src.'" - fopen() said: "'.$fopen_error.'"');
		} else {
			$phpThumb->ErrorImage('"allow_url_fopen" disabled');
		}

	}
	ob_end_clean();

}


////////////////////////////////////////////////////////////////
// Debug output, to try and help me diagnose problems
if (@$_REQUEST['phpThumbDebug'] == '2') {
	$phpThumb->phpThumbDebug();
}
////////////////////////////////////////////////////////////////

$phpThumb->GenerateThumbnail();

////////////////////////////////////////////////////////////////
// Debug output, to try and help me diagnose problems
if (@$_REQUEST['phpThumbDebug'] == '3') {
	$phpThumb->phpThumbDebug();
}
////////////////////////////////////////////////////////////////

if (!empty($_REQUEST['file'])) {

	$phpThumb->RenderToFile(phpthumb_functions::ResolveFilenameToAbsolute($_REQUEST['file']));
	if (!empty($_REQUEST['goto']) && (substr(strtolower($_REQUEST['goto']), 0, strlen('http://')) == 'http://')) {
		// redirect to another URL after image has been rendered to file
		header('Location: '.$_REQUEST['goto']);
		exit;
	}

} elseif (!empty($this->config['cache_directory']) && empty($this->phpThumbDebug) && is_writable($this->config['cache_directory'])) {

	$phpThumb->RenderToFile($cache_filename);

}

////////////////////////////////////////////////////////////////
// Debug output, to try and help me diagnose problems
if (@$_REQUEST['phpThumbDebug'] == '4') {
	$phpThumb->phpThumbDebug();
}
////////////////////////////////////////////////////////////////

$phpThumb->OutputThumbnail();

?>