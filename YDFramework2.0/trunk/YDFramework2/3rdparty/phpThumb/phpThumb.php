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

error_reporting(E_ALL);
ini_set('display_errors', '1');

// this script relies on the superglobal arrays, fake it here for old PHP versions
if (phpversion() < '4.1.0') {
	$_SERVER  = $HTTP_SERVER_VARS;
	$_REQUEST = $HTTP_GET_VARS;
}

if (!function_exists('ImageJPEG') && !function_exists('ImagePNG') && !function_exists('ImageGIF')) {
	// base64-encoded error image in GIF format
	$ERROR_NOGD = 'R0lGODlhIAAgALMAAAAAABQUFCQkJDY2NkZGRldXV2ZmZnJycoaGhpSUlKWlpbe3t8XFxdXV1eTk5P7+/iwAAAAAIAAgAAAE/vDJSau9WILtTAACUinDNijZtAHfCojS4W5H+qxD8xibIDE9h0OwWaRWDIljJSkUJYsN4bihMB8th3IToAKs1VtYM75cyV8sZ8vygtOE5yMKmGbO4jRdICQCjHdlZzwzNW4qZSQmKDaNjhUMBX4BBAlmMywFSRWEmAI6b5gAlhNxokGhooAIK5o/pi9vEw4Lfj4OLTAUpj6IabMtCwlSFw0DCKBoFqwAB04AjI54PyZ+yY3TD0ss2YcVmN/gvpcu4TOyFivWqYJlbAHPpOntvxNAACcmGHjZzAZqzSzcq5fNjxFmAFw9iFRunD1epU6tsIPmFCAJnWYE0FURk7wJDA0MTKpEzoWAAskiAAA7';
	header('Content-type: image/gif');
	echo base64_decode($ERROR_NOGD);
	exit;
}

// returned the fixed string if the evil "magic_quotes_gpc" setting is on
if (get_magic_quotes_gpc()) {
	$RequestVarsToStripSlashes = array('src', 'wmf', 'file', 'err', 'goto', 'down');
	foreach ($RequestVarsToStripSlashes as $key) {
		if (isset($_REQUEST[$key])) {
			$_REQUEST[$key] = stripslashes($_REQUEST[$key]);
		}
	}
}

// instantiate a new phpThumb() object
if (!include_once('phpthumb.class.php')) {
	die('failed to include_once("'.realpath('phpthumb.class.php').'")');
}
$phpThumb = new phpThumb();

if (!file_exists('phpThumb.config.php') || !@include_once('phpThumb.config.php')) {
	die('failed to include_once(phpThumb.config.php) - realpath="'.realpath('phpThumb.config.php').'"');
}
foreach ($PHPTHUMB_CONFIG as $key => $value) {
	$keyname = 'config_'.$key;
	$phpThumb->$keyname = $value;
}
foreach ($_REQUEST as $key => $value) {
	$phpThumb->$key = $value;
}
if (!empty($PHPTHUMB_DEFAULTS)) {
	foreach ($PHPTHUMB_DEFAULTS as $key => $value) {
		if ($PHPTHUMB_DEFAULTS_GETSTRINGOVERRIDE || !isset($_REQUEST[$key])) {
			$phpThumb->$key = $value;
		}
	}
}

////////////////////////////////////////////////////////////////
// Debug output, to try and help me diagnose problems
if (@$_REQUEST['phpThumbDebug'] == '1') {
	$phpThumb->phpThumbDebug();
}
////////////////////////////////////////////////////////////////


// check to see if file can be output from source with no processing or caching
$CanPassThroughDirectly = true;
$FilenameParameters = array('h', 'w', 'sx', 'sy', 'sw', 'sh', 'bw', 'brx', 'bry', 'bg', 'bgt', 'bc', 'usa', 'usr', 'ust', 'wmf', 'wmp', 'wmm', 'wma', 'xto', 'ra', 'ar', 'iar', 'maxb');
foreach ($FilenameParameters as $key) {
	if (isset($_REQUEST[$key])) {
		$CanPassThroughDirectly = false;
		break;
	}
}
if ($CanPassThroughDirectly && !empty($_REQUEST['src'])) {
	// no parameters set, passthru
	$SourceFilename = $phpThumb->ResolveFilenameToAbsolute($_REQUEST['src']);
	if ($getimagesize = @GetImageSize($SourceFilename)) {
		header('Content-type: '.phpthumb_functions::ImageTypeToMIMEtype($getimagesize[2]));
		@readfile($SourceFilename);
		exit;
	}
}


// check to see if file already exists in cache, and output it with no processing if it does
if (!empty($phpThumb->config_cache_directory) && empty($_REQUEST['phpThumbDebug'])) {
	$cache_filename = $phpThumb->GenerateCachedFilename();
	if (is_file($cache_filename)) {
		header('Content-type: image/'.$phpThumb->thumbnailFormat);
		@readfile($cache_filename);
		exit;
	}
}


////////////////////////////////////////////////////////////////
// You may want to pull data from a database rather than a physical file
// If so, uncomment the following $SQLquery line (modified to suit your database)
// Note: this must be the actual binary data of the image, not a URL or filename
// see http://www.billy-corgan.com/blog/archive/000143.php for a brief tutorial on this section

//$SQLquery = 'SELECT `Picture` FROM `products` WHERE (`ProductID` = \''.mysql_escape_string($_REQUEST['id']).'\')';
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

} elseif (substr(strtolower(@$phpThumb->src), 0, 7) == 'http://') {

	ob_start();
	$HTTPurl = strtr($phpThumb->src, array(' '=>'%20'));
	if ($fp = fopen($HTTPurl, 'rb')) {

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

		$fopen_error = strip_tags(ob_get_contents());
		ob_end_clean();
		if (ini_get('allow_url_fopen')) {
			$phpThumb->ErrorImage('cannot open "'.$HTTPurl.'" - fopen() said: "'.$fopen_error.'"');
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

	$phpThumb->RenderToFile($phpThumb->ResolveFilenameToAbsolute($_REQUEST['file']));
	if (!empty($_REQUEST['goto']) && (substr(strtolower($_REQUEST['goto']), 0, strlen('http://')) == 'http://')) {
		// redirect to another URL after image has been rendered to file
		header('Location: '.$_REQUEST['goto']);
		exit;
	}

} elseif (empty($phpThumb->phpThumbDebug) && !empty($phpThumb->config_cache_directory) && is_writable($phpThumb->config_cache_directory)) {

	$phpThumb->CleanUpCacheDirectory();
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