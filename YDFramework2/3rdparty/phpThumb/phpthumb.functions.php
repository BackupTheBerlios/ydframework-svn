<?php

class phpthumb_functions {

	function user_function_exists($functionname) {
		if (function_exists('get_defined_functions')) {
			static $get_defined_functions = array();
			if (empty($get_defined_functions)) {
				$get_defined_functions = get_defined_functions();
			}
			return in_array(strtolower($functionname), $get_defined_functions['user']);
		}
		return function_exists($functionname);
	}

	function builtin_function_exists($functionname) {
		if (function_exists('get_defined_functions')) {
			static $get_defined_functions = array();
			if (empty($get_defined_functions)) {
				$get_defined_functions = get_defined_functions();
			}
			return in_array(strtolower($functionname), $get_defined_functions['internal']);
		}
		return function_exists($functionname);
	}

	function version_compare_replacement_sub($version1, $version2, $operator='') {
		static $versiontype_lookup = array();
		if (empty($versiontype_lookup)) {
			$versiontype_lookup['dev']   = 10001;
			$versiontype_lookup['a']     = 10002;
			$versiontype_lookup['alpha'] = 10002;
			$versiontype_lookup['b']     = 10003;
			$versiontype_lookup['beta']  = 10003;
			$versiontype_lookup['RC']    = 10004;
			$versiontype_lookup['pl']    = 10005;
		}
		if (isset($versiontype_lookup[$version1])) {
			$version1 = $versiontype_lookup[$version1];
		}
		if (isset($versiontype_lookup[$version2])) {
			$version2 = $versiontype_lookup[$version2];
		}

		switch ($operator) {
			case '<':
			case 'lt':
				return intval($version1 < $version2);
				break;
			case '<=':
			case 'le':
				return intval($version1 <= $version2);
				break;
			case '>':
			case 'gt':
				return intval($version1 > $version2);
				break;
			case '>=':
			case 'ge':
				return intval($version1 >= $version2);
				break;
			case '==':
			case '=':
			case 'eq':
				return intval($version1 == $version2);
				break;
			case '!=':
			case '<>':
			case 'ne':
				return intval($version1 != $version2);
				break;
		}
		if ($version1 == $version2) {
			return 0;
		} elseif ($version1 < $version2) {
			return -1;
		}
		return 1;
	}

	function version_compare_replacement($version1, $version2, $operator='') {
		if (function_exists('version_compare')) {
			return version_compare($version1, $version2, $operator='');
		}

		$version1 = strtr($version1, '_-+', '...');
		$version2 = strtr($version2, '_-+', '...');

		$version1 = eregi_replace('([0-9]+)([A-Z]+)([0-9]+)', '\\1.\\2.\\3', $version1);
		$version2 = eregi_replace('([0-9]+)([A-Z]+)([0-9]+)', '\\1.\\2.\\3', $version2);

		$parts1 = explode('.', $version1);
		$parts2 = explode('.', $version1);
		$parts_count = max(count($parts1), count($parts2));
		for ($i = 0; $i < $parts_count; $i++) {
			$comparison = version_compare_replacement_sub($version1, $version2, $operator);
			if ($comparison != 0) {
				return $comparison;
			}
		}
		return 0;
	}

	function phpinfo_array() {
		static $phpinfo_array = array();
		if (empty($phpinfo_array)) {
			ob_start();
			phpinfo();
			$phpinfo = ob_get_contents();
			ob_end_clean();
			$phpinfo_array = explode("\n", $phpinfo);
		}
		return $phpinfo_array;
	}

	function gd_info() {
		if (function_exists('gd_info')) {
			return gd_info();
		}

		static $gd_info = array();
		if (empty($gd_info)) {
			$gd_info = array(
				'GD Version'         => '',
				'FreeType Support'   => false,
				'FreeType Linkage'   => '',
				'T1Lib Support'      => false,
				'GIF Read Support'   => false,
				'GIF Create Support' => false,
				'JPG Support'        => false,
				'PNG Support'        => false,
				'WBMP Support'       => false,
				'XBM Support'        => false
			);
			$phpinfo_array = phpthumb_functions::phpinfo_array();
			foreach ($phpinfo_array as $line) {
				$line = trim(strip_tags($line));
				foreach ($gd_info as $key => $value) {
					if (strpos($line, $key) === 0) {
						$newvalue = trim(str_replace($key, '', $line));
						$gd_info[$key] = $newvalue;
					}
				}
			}
			if (empty($gd_info['GD Version'])) {
				if (function_exists('ImageTypes')) {
					$imagetypes = ImageTypes();
					if ($imagetypes & IMG_PNG) {
						$gd_info['PNG Support'] = true;
					}
					if ($imagetypes & IMG_GIF) {
						$gd_info['GIF Create Support'] = true;
					}
					if ($imagetypes & IMG_JPG) {
						$gd_info['JPG Support'] = true;
					}
					if ($imagetypes & IMG_WBMP) {
						$gd_info['WBMP Support'] = true;
					}
				}
				if (function_exists('ImageCreateFromGIF')) {
					if ($tempfilename = tempnam(null, '')) {
						if ($fp_tempfile = @fopen($tempfilename, 'wb')) {
							fwrite($fp_tempfile, base64_decode('R0lGODlhAQABAIAAAH//AP///ywAAAAAAQABAAACAUQAOw=='));
							fclose($fp_tempfile);
							$gd_info['GIF Read Support'] = (bool) @ImageCreateFromGIF($tempfilename);
						}
						unlink($tempfilename);
					}
				}
				if (function_exists('ImageCreateTrueColor') && @ImageCreateTrueColor(1, 1)) {
					$gd_info['GD Version'] = '2.0.1 or higher (assumed)';
				} elseif (function_exists('ImageCreate') && @ImageCreate(1, 1)) {
					$gd_info['GD Version'] = '1.6.0 or higher (assumed)';
				}
			}
		}
		return $gd_info;
	}

	function exif_info() {
		static $exif_info = array();
		if (empty($exif_info)) {
			$exif_info = array(
				'EXIF Support'           => '',
				'EXIF Version'           => '',
				'Supported EXIF Version' => '',
				'Supported filetypes'    => ''
			);
			$phpinfo_array = phpthumb_functions::phpinfo_array();
			foreach ($phpinfo_array as $line) {
				$line = trim(strip_tags($line));
				foreach ($exif_info as $key => $value) {
					if (strpos($line, $key) === 0) {
						$newvalue = trim(str_replace($key, '', $line));
						$exif_info[$key] = $newvalue;
					}
				}
			}
		}
		return $exif_info;
	}

	function gd_version($fullstring=false) {
		static $cache_gd_version = array();
		if (empty($cache_gd_version)) {
			$gd_info = phpthumb_functions::gd_info();
			if (substr($gd_info['GD Version'], 0, strlen('bundled (')) == 'bundled (') {
				$cache_gd_version[1] = $gd_info['GD Version'];
				$cache_gd_version[0] = (float) substr($gd_info['GD Version'], strlen('bundled ('), 3);
			} else {
				$cache_gd_version[1] = $gd_info['GD Version'];
				$cache_gd_version[0] = (float) substr($gd_info['GD Version'], 0, 3);
			}
		}
		return $cache_gd_version[intval($fullstring)];
	}

	function ImageTypeToMIMEtype($imagetype) {
		if (!function_exists('image_type_to_mime_type')) {
			return image_type_to_mime_type($imagetype);
		}
		static $image_type_to_mime_type = array(
			1  => 'image/gif',
			2  => 'image/jpeg',
			3  => 'image/png',
			4  => 'application/x-shockwave-flash',
			5  => 'image/psd',
			6  => 'image/bmp',
			7  => 'image/tiff',
			8  => 'image/tiff',
			9  => 'application/octet-stream',
			10 => 'image/jp2',
			11 => 'application/octet-stream',
			12 => 'application/octet-stream',
			13 => 'application/x-shockwave-flash',
			14 => 'image/iff',
			15 => 'image/vnd.wap.wbmp',
			16 => 'image/xbm');

		return (isset($image_type_to_mime_type[$imagetype]) ? $image_type_to_mime_type[$imagetype] : false);
	}

	function HexCharDisplay($string) {
		$len = strlen($string);
		$output = '';
		for ($i = 0; $i < $len; $i++) {
			$output .= ' 0x'.str_pad(dechex(ord($string{$i})), 2, '0', STR_PAD_LEFT);
		}
		return $output;
	}

	function ImageHexColorAllocate(&$gdimg_hexcolorallocate, $HexColorString, $dieOnInvalid=false) {
		if (!is_resource($gdimg_hexcolorallocate)) {
			die('$gdimg_hexcolorallocate is not a GD resource in ImageHexColorAllocate()');
		}
		if (eregi('^[0-9A-F]{6}$', $HexColorString)) {
			$R = hexdec(substr($HexColorString, 0, 2));
			$G = hexdec(substr($HexColorString, 2, 2));
			$B = hexdec(substr($HexColorString, 4, 2));
			return ImageColorAllocate($gdimg_hexcolorallocate, $R, $G, $B);
		}
		if ($dieOnInvalid) {
			die('Invalid hex color string: "'.$HexColorString.'"');
		}
		return ImageColorAllocate($gdimg_hexcolorallocate, 0, 0, 0);
	}

	function ImageCreateFromStringReplacement(&$RawImageData, $DieOnErrors=false) {
		$gd_info = phpthumb_functions::gd_info();
		if (strpos($gd_info['GD Version'], 'bundled') !== false) {
			return @ImageCreateFromString($RawImageData);
		}

		switch (substr($RawImageData, 0, 3)) {
			case 'GIF':
				$ImageCreateFromStringReplacementFunction = 'ImageCreateFromGIF';
				break;
			case "\xFF\xD8\xFF":
				$ImageCreateFromStringReplacementFunction = 'ImageCreateFromJPEG';
				break;
			case "\x89".'PN':
				$ImageCreateFromStringReplacementFunction = 'ImageCreateFromPNG';
				break;
			default:
				die('Unknown image type identified by "'.substr($RawImageData, 0, 3).'" ('.HexCharDisplay(substr($RawImageData, 0, 3)).')');
				break;
		}
		if ($tempnam = tempnam(null, '')) {
			if ($fp_tempnam = @fopen($tempnam, 'wb')) {
				fwrite($fp_tempnam, $RawImageData);
				fclose($fp_tempnam);
				if (($ImageCreateFromStringReplacementFunction == 'ImageCreateFromGIF') && !function_exists($ImageCreateFromStringReplacementFunction)) {
					if (@include_once('phpthumb.gif.php')) {
						if ($tempfilename = tempnam(null, '')) {
							if ($fp_tempfile = @fopen($tempfilename, 'wb')) {
								fwrite($fp_tempfile, $RawImageData);
								fclose($fp_tempfile);
								$gdimg_source = gif_loadFileToGDimageResource($tempfilename);
								unlink($tempfilename);
								return $gdimg_source;
								break;
							} else {
								$ErrorMessage = 'Failed to open tempfile in '.__FILE__.' on line '.__LINE__;
							}
						} else {
							$ErrorMessage = 'Failed to open generate tempfile name in '.__FILE__.' on line '.__LINE__;
						}
					} else {
						$ErrorMessage = 'Failed to include required file "phpthumb.gif.php" in '.__FILE__.' on line '.__LINE__;
					}

				} elseif (function_exists($ImageCreateFromStringReplacementFunction) && ($gdimg_source = $ImageCreateFromStringReplacementFunction($tempnam))) {
					unlink($tempnam);
					return $gdimg_source;
				} else {
					$ERROR_NOGD = 'R0lGODlhIAAgALMAAAAAABQUFCQkJDY2NkZGRldXV2ZmZnJycoaGhpSUlKWlpbe3t8XFxdXV1eTk5P7+/iwAAAAAIAAgAAAE/vDJSau9WILtTAACUinDNijZtAHfCojS4W5H+qxD8xibIDE9h0OwWaRWDIljJSkUJYsN4bihMB8th3IToAKs1VtYM75cyV8sZ8vygtOE5yMKmGbO4jRdICQCjHdlZzwzNW4qZSQmKDaNjhUMBX4BBAlmMywFSRWEmAI6b5gAlhNxokGhooAIK5o/pi9vEw4Lfj4OLTAUpj6IabMtCwlSFw0DCKBoFqwAB04AjI54PyZ+yY3TD0ss2YcVmN/gvpcu4TOyFivWqYJlbAHPpOntvxNAACcmGHjZzAZqzSzcq5fNjxFmAFw9iFRunD1epU6tsIPmFCAJnWYE0FURk7wJDA0MTKpEzoWAAskiAAA7';
					header('Content-type: image/gif');
					echo base64_decode($ERROR_NOGD);
					exit;
				}
			} else {
				$ErrorMessage = 'Failed to fopen('.$tempnam.', "wb") in '.__FILE__.' on line '.__LINE__;
			}
			unlink($tempnam);
		} else {
			$ErrorMessage = 'Failed to generate tempnam() in '.__FILE__.' on line '.__LINE__;
		}
		if ($DieOnErrors && !empty($ErrorMessage)) {
			die($ErrorMessage);
		}
		return false;
	}

	function ResolveFilenameToAbsolute($filename) {
		if ((strtoupper(substr(PHP_OS, 0, 3)) == 'WIN') && (substr($filename, 1, 1) == ':')) {
			$AbsoluteFilename = $filename;

		} elseif (substr($filename, 0, 1) == '/') {

			if (!file_exists($_SERVER['DOCUMENT_ROOT'].$filename) && file_exists($filename)) {
				$AbsoluteFilename = $filename;
			} elseif (substr($filename, 1, 1) == '~') {
				$AbsoluteFilename = $_SERVER['DOCUMENT_ROOT'].$filename;
				if ($apache_lookup_uri_object = apache_lookup_uri($filename)) {
					$AbsoluteFilename = $apache_lookup_uri_object->filename;
				}
			} else {
				$AbsoluteFilename = $_SERVER['DOCUMENT_ROOT'].$filename;
			}

		} else {
			$AbsoluteFilename = $_SERVER['DOCUMENT_ROOT'].dirname($_SERVER['PHP_SELF']).'/'.$filename;
			if (substr(dirname($_SERVER['PHP_SELF']), 0, 2) == '/~') {
				if ($apache_lookup_uri_object = apache_lookup_uri(dirname($_SERVER['PHP_SELF']))) {
					$AbsoluteFilename = $apache_lookup_uri_object->filename.'/'.$filename;
				}
			}

		}
		return $AbsoluteFilename;
	}


}

?>