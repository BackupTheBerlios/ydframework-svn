<?php
//////////////////////////////////////////////////////////////
///  phpThumb() by James Heinrich <info@silisoftware.com>   //
//        available at http://phpthumb.sourceforge.net     ///
//////////////////////////////////////////////////////////////
///                                                         //
// See: phpthumb.readme.txt for usage instructions          //
//                                                         ///
//////////////////////////////////////////////////////////////

if (!include_once('phpthumb.functions.php')) {
	die('failed to include_once("'.realpath('phpthumb.functions.php').'")');
}

class phpthumb {

	// public:
	// START CONFIGURATION OPTIONS
	// See phpThumb.config.php for descriptions of what each of these settings do

	// * Directory Configuration
	var $config_cache_directory              = null;
	var $config_temp_directory               = null;
	var $config_document_root                = null;

	// * Default output configuration:
	var $config_output_format                = 'jpeg';
	var $config_output_maxwidth              = 0;
	var $config_output_maxheight             = 0;
	var $config_output_interlace             = true;

	// * Error message configuration
	var $config_error_message_image_default  = '';
	var $config_error_bgcolor                = 'CCCCFF';
	var $config_error_textcolor              = 'FF0000';
	var $config_error_fontsize               = 1;
	var $config_error_die_on_error           = true;

	// * Anti-Hotlink Configuration:
	var $config_nohotlink_enabled            = true;
	var $config_nohotlink_valid_domains      = array();
	var $config_nohotlink_erase_image        = true;
	var $config_nohotlink_fill_hexcolor      = 'CCCCCC';
	var $config_nohotlink_text_hexcolor      = 'FF0000';
	var $config_nohotlink_text_message       = 'Hotlinking is not allowed!';
	var $config_nohotlink_text_fontsize      = 3;

	// * Border & Background default colors
	var $config_border_hexcolor              = '000000';
	var $config_background_hexcolor          = 'FFFFFF';

	var $config_max_source_pixels            = 0;
	var $config_use_exif_thumbnail_for_speed = true;
	var $config_output_allow_enlarging       = false;

	var $config_imagemagick_path             = null;

	var $config_disable_debug                = false;
	// END CONFIGURATION OPTIONS


	// public: data source
	var $sourceFilename = null;
	var $rawImageData   = null;

	// public: error message(s)
	var $debugmessages = array();


	// public:
	// START PARAMETERS
	// See phpthumb.readme.txt for descriptions of what each of these values are
	var $src  = null;
	var $w    = null;
	var $h    = null;
	var $f    = 'jpeg';
	var $q    = 75;
	var $sx   = null;
	var $sy   = null;
	var $sw   = null;
	var $sh   = null;
	var $bw   = null;
	var $bg   = null;
	var $bgt  = null;
	var $bc   = null;
	var $usr  = null;
	var $usa  = null;
	var $ust  = null;
	var $wmf  = null;
	var $wmp  = null;
	var $wmm  = null;
	var $wma  = null;
	var $file = null;
	var $goto = null;
	var $err  = null;
	var $xto  = null;
	var $ra   = null;
	var $ar   = null;
	var $aoe  = null;
	var $iar  = null;
	var $brx  = null;
	var $bry  = null;
	var $maxb = null;
	var $down = null;
	// END PARAMETERS


	// private:
	var $phpThumbDebug    = null;
	var $thumbnailQuality = 75;
	var $thumbnailFormat  = null;

	var $gdimg_output     = null;
	var $gdimg_source     = null;

	var $getimagesizeinfo = null;

	var $source_width  = null;
	var $source_height = null;

	var $thumbnailCropX = null;
	var $thumbnailCropY = null;
	var $thumbnailCropW = null;
	var $thumbnailCropH = null;

	var $exif_thumbnail_width  = null;
	var $exif_thumbnail_height = null;
	var $exif_thumbnail_type   = null;
	var $exif_thumbnail_data   = null;

	var $thumbnail_width        = null;
	var $thumbnail_height       = null;
	var $thumbnail_image_width  = null;
	var $thumbnail_image_height = null;

	var $cache_filename         = null;
	var $RemoveFileOnCompletion = false;

	var $phpthumb_version = '1.4.10-200408221403';
	var $iswindows = null;
	var $osslash   = null;

	//////////////////////////////////////////////////////////////////////

	// public: constructor
	function phpThumb() {
		if (strtoupper(substr(PHP_OS, 0, 3)) == 'WIN') {
			$this->iswindows = true;
			$this->osslash   = '\\';
		} else {
			$this->iswindows = false;
			$this->osslash   = '/';
		}
		if (!empty($_SERVER['DOCUMENT_ROOT'])) {
			$this->config_document_root = $_SERVER['DOCUMENT_ROOT'];
		}
	}

	// public:
	function setSourceFilename($sourceFilename) {
		$this->rawImageData   = null;
		$this->sourceFilename = $sourceFilename;
		return true;
	}

	// public:
	function setSourceData($rawImageData) {
		$this->sourceFilename = null;
		$this->rawImageData   = $rawImageData;
		return true;
	}



	// public:
	function GenerateThumbnail() {

		if (empty($this->thumbnailFormat)) {
			$this->setOutputFormat();
		}
		$this->ResolveSource();
		if (empty($this->src) && !empty($this->rawImageData)) {
			$this->SetCacheFilename();
		}
		$this->ExtractEXIFgetImageSize();
		if (!$this->SourceImageToGD()) {
			return false;
		}
		$this->Rotate();
		$this->CreateGDoutput();
		$this->ImageBorder();

		// copy/resize image to appropriate dimensions (either nearest-neighbor or resample, depending on GD version)
		$this->ImageResizeFunction(
			$this->gdimg_output,
			$this->gdimg_source,
			round(($this->thumbnail_width  - $this->thumbnail_image_width)  / 2),
			round(($this->thumbnail_height - $this->thumbnail_image_height) / 2),
			$this->thumbnailCropX,
			$this->thumbnailCropY,
			$this->thumbnail_image_width,
			$this->thumbnail_image_height,
			$this->thumbnailCropW,
			$this->thumbnailCropH);


		$this->UnsharpMasking();
		$this->Watermarking();
		$this->AntiOffsiteLinking();
		$this->RoundedImageCorners();
		$this->MaxFileSize();

		if ($this->RemoveFileOnCompletion) {
			@unlink($this->sourceFilename);
		}
		return true;
	}


	// public:
	function RenderToFile($filename) {
		// render thumbnail to this file only, do not cache, do not output to browser
		$ImageOutFunction = 'image'.$this->thumbnailFormat;
		//$renderfilename = $this->ResolveFilenameToAbsolute(dirname($filename)).'/'.basename($filename);
		$renderfilename = $filename;
		if (($filename{0} != '/') && ($filename{0} != '\\') && ($filename{1} != ':')) {
			$renderfilename = $this->ResolveFilenameToAbsolute($filename);
		}
		$this->DebugMessage('RenderToFile('.$filename.') attempting to render to file "'.$renderfilename.'"', __FILE__, __LINE__);
		ob_start();
		switch ($this->thumbnailFormat) {
			case 'jpeg':
				$ImageOutFunction($this->gdimg_output, $renderfilename, $this->thumbnailQuality);
				break;

			case 'png':
			case 'gif':
				$ImageOutFunction($this->gdimg_output, $renderfilename);
				break;
		}
		$errormessage = strip_tags(ob_get_contents());
		ob_end_clean();
		if (!empty($errormessage)) {
			$this->DebugMessage('RenderToFile ['.$ImageOutFunction.'('.$renderfilename.')] failed with message "'.$errormessage.'"', __FILE__, __LINE__);
			return false;
		}
		return true;
	}


	// public:
	function OutputThumbnail() {
		if ($this->RemoveFileOnCompletion) {
			@unlink($this->sourceFilename);
		}

		if (headers_sent()) {
			return $this->ErrorImage('OutputThumbnail() failed - headers already sent');
			exit;
		}

		if (!empty($this->down)) {
			$downloadfilename = ereg_replace('[/\\:\*\?"<>|]', '_', $this->down);
			if (phpthumb_functions::version_compare_replacement(phpversion(), '4.1.0', '>=')) {
				$downloadfilename = trim($downloadfilename, '.');
			}
			if ($downloadfilename != $this->down) {
				$this->DebugMessage('renaming output file for "down" from "'.$this->down.'" to "'.$downloadfilename.'"', __FILE__, __LINE__);
			}
			if (!empty($downloadfilename)) {
				header('Content-Disposition: attachment; filename="'.$downloadfilename.'"');
			} else {
				$this->DebugMessage('failed to send Content-Disposition header because $downloadfilename is empty', __FILE__, __LINE__);
			}
		}

		ImageInterlace($this->gdimg_output, intval($this->config_output_interlace));
		$ImageOutFunction = 'image'.$this->thumbnailFormat;
		switch ($this->thumbnailFormat) {
			case 'jpeg':
				header('Content-type: image/'.$this->thumbnailFormat);
				@$ImageOutFunction($this->gdimg_output, '', $this->thumbnailQuality);
				break;

			case 'png':
			case 'gif':
				header('Content-type: image/'.$this->thumbnailFormat);
				@$ImageOutFunction($this->gdimg_output);
				break;
		}
		ImageDestroy($this->gdimg_output);
		return true;
	}


	// public:
	function CleanUpCacheDirectory() {
		if (($this->config_cache_maxage > 0) || ($this->config_cache_maxsize > 0) || ($this->config_cache_maxfiles > 0)) {
			$CacheDirOldFilesAge  = array();
			$CacheDirOldFilesSize = array();
			if ($dirhandle = opendir($this->config_cache_directory)) {
				while ($oldcachefile = readdir($dirhandle)) {
					if (eregi('^phpThumb_cache_', $oldcachefile)) {
						$CacheDirOldFilesAge[$oldcachefile] = fileatime($this->config_cache_directory.'/'.$oldcachefile);
						if ($CacheDirOldFilesAge[$oldcachefile] == 0) {
							$CacheDirOldFilesAge[$oldcachefile] = filemtime($this->config_cache_directory.'/'.$oldcachefile);
						}

						$CacheDirOldFilesSize[$oldcachefile] = filesize($this->config_cache_directory.'/'.$oldcachefile);
					}
				}
			}
			asort($CacheDirOldFilesAge);

			if ($this->config_cache_maxfiles > 0) {
				$TotalCachedFiles = count($CacheDirOldFilesAge);
				$DeletedKeys = array();
				foreach ($CacheDirOldFilesAge as $oldcachefile => $filedate) {
					if ($TotalCachedFiles > $this->config_cache_maxfiles) {
						$TotalCachedFiles--;
						unlink($this->config_cache_directory.'/'.$oldcachefile);
						$DeletedKeys[] = $oldcachefile;
					} else {
						// there are few enough files to keep the rest
						break;
					}
				}
				foreach ($DeletedKeys as $oldcachefile) {
					unset($CacheDirOldFilesAge[$oldcachefile]);
					unset($CacheDirOldFilesSize[$oldcachefile]);
				}
			}

			if ($this->config_cache_maxage > 0) {
				$mindate = time() - $this->config_cache_maxage;
				$DeletedKeys = array();
				foreach ($CacheDirOldFilesAge as $oldcachefile => $filedate) {
					if ($filedate > 0) {
						if ($filedate < $mindate) {
							unlink($this->config_cache_directory.'/'.$oldcachefile);
							$DeletedKeys[] = $oldcachefile;
						} else {
							// the rest of the files are new enough to keep
							break;
						}
					}
				}
				foreach ($DeletedKeys as $oldcachefile) {
					unset($CacheDirOldFilesAge[$oldcachefile]);
					unset($CacheDirOldFilesSize[$oldcachefile]);
				}
			}

			if ($this->config_cache_maxsize > 0) {
				$TotalCachedFileSize = array_sum($CacheDirOldFilesSize);
				$DeletedKeys = array();
				foreach ($CacheDirOldFilesAge as $oldcachefile => $filedate) {
					if ($TotalCachedFileSize > $this->config_cache_maxsize) {
						$TotalCachedFileSize -= $CacheDirOldFilesSize[$oldcachefile];
						unlink($this->config_cache_directory.'/'.$oldcachefile);
						$DeletedKeys[] = $oldcachefile;
					} else {
						// the total filesizes are small enough to keep the rest of the files
						break;
					}
				}
				foreach ($DeletedKeys as $oldcachefile) {
					unset($CacheDirOldFilesAge[$oldcachefile]);
					unset($CacheDirOldFilesSize[$oldcachefile]);
				}
			}

		}
		return true;
	}

	//////////////////////////////////////////////////////////////////////

	function ResolveSource() {
		if (empty($this->sourceFilename)) {
			if (empty($this->rawImageData)) {

				$this->sourceFilename = $this->ResolveFilenameToAbsolute($this->src);

			} else {

				$this->RemoveFileOnCompletion = true;
				if ($tempfilename = $this->phpThumb_tempnam()) {
					$this->sourceFilename = $tempfilename;
					if ($fp_tempfile = @fopen($tempfilename, 'wb')) {
						fwrite($fp_tempfile, $this->rawImageData);
						unset($this->rawImageData);
						fclose($fp_tempfile);
					} else {
						return $this->ErrorImage('Failed to open temp file "'.$this->sourceFilename.'" for writing in '.__FILE__.' on line '.__LINE__."\n".'You may need to set $PHPTHUMB_CONFIG[temp_directory] in phpThumb.config.php');
					}
				} else {
					return $this->ErrorImage('Failed to generate temp filename in '.__FILE__.' on line '.__LINE__);
				}

			}
		}
		return true;
	}

	function setOutputFormat() {
		$AvailableImageOutputFormats = array();
		$AvailableImageOutputFormats[] = 'text';
		$this->thumbnailFormat         = 'text';

		// Set default output format based on what image types are available
		if (!function_exists('ImageTypes')) {
			return $this->ErrorImage('ImageTypes() does not exist - GD support might not be enabled?');
		}
		$imagetypes = ImageTypes();
		if ($imagetypes & IMG_WBMP) {
			$this->thumbnailFormat         = 'wbmp';
			$AvailableImageOutputFormats[] = 'wbmp';
		}
		if ($imagetypes & IMG_GIF) {
			$this->thumbnailFormat         = 'gif';
			$AvailableImageOutputFormats[] = 'gif';
		}
		if ($imagetypes & IMG_PNG) {
			$this->thumbnailFormat         = 'png';
			$AvailableImageOutputFormats[] = 'png';
		}
		if ($imagetypes & IMG_JPG) {
			$this->thumbnailFormat         = 'jpeg';
			$AvailableImageOutputFormats[] = 'jpeg';
		}
		if (in_array($this->config_output_format, $AvailableImageOutputFormats)) {
			// set output format to config default if that format is available
			$this->thumbnailFormat = $this->config_output_format;
		}
		if (!empty($this->f) && (in_array($this->f, $AvailableImageOutputFormats))) {
			// override output format if $this->f is set and that format is available
			$this->thumbnailFormat = $this->f;
		}

		// for JPEG images, quality 0 (worst) to 100 (best)
		// quality < 25 is nasty, with not much size savings - not recommended
		// problems with 100 - invalid JPEG?
		$this->thumbnailQuality = max(1, min(95, (!empty($this->q) ? $this->q : 75)));

		return true;
	}

	function setCacheDirectory() {
		// resolve cache directory to absolute pathname
		if ($this->iswindows) {
			$this->config_cache_directory = str_replace('/', $this->osslash, $this->config_cache_directory);
		}
		if ((substr($this->config_cache_directory, 0, 1) == '.') && (substr($this->src, 0, 1) == '/')) {
			// resolve relative cache directory to source image
			$this->config_cache_directory = realpath(dirname($this->config_document_root.$this->src).'/'.$this->config_cache_directory);
		}
		if (substr($this->config_cache_directory, -1) == '/') {
			$this->config_cache_directory = substr($this->config_cache_directory, 0, -1);
		}
		if (!is_dir($this->config_cache_directory)) {
			$this->DebugMessage('$this->config_cache_directory is not a directory ('.$this->config_cache_directory.')', __FILE__, __LINE__);
			$this->config_cache_directory = null;
		}

		return true;
	}


	function ResolveFilenameToAbsolute($filename) {
		$this->iswindows = (bool) (strtoupper(substr(PHP_OS, 0, 3)) == 'WIN');
		if (substr($filename, 0, 7) == 'http://') {

			// URL
			$AbsoluteFilename = $filename;

		} elseif ($this->iswindows && (substr($filename, 1, 1) == ':')) {

			// absolute pathname (Windows)
			$AbsoluteFilename = $filename;

		} elseif ($this->iswindows && ((substr($filename, 0, 2) == '//') || (substr($filename, 0, 2) == '\\\\'))) {

			// absolute pathname (Windows)
			$AbsoluteFilename = $filename;

		} elseif (substr($filename, 0, 1) == '/') {

			if (is_readable($filename) && !is_readable($this->config_document_root.$filename)) {
				// absolute filename (*nix)
				$AbsoluteFilename = $filename;
			} elseif (substr($filename, 1, 1) == '~') {
				// /~user/path
				if ($ApacheLookupURIarray = phpthumb_functions::ApacheLookupURIarray($filename)) {
					$AbsoluteFilename = $ApacheLookupURIarray['filename'];
				} else {
					$AbsoluteFilename = realpath($filename);
					if (is_readable($AbsoluteFilename)) {
						$this->DebugMessage('phpthumb_functions::ApacheLookupURIarray() failed for "'.$filename.'", but the correct filename ('.$AbsoluteFilename.') seems to have been resolved with realpath($filename)', __FILE__, __LINE__);
					} else {
						return $this->ErrorImage('phpthumb_functions::ApacheLookupURIarray() failed for "'.$filename.'". This has been known to fail on Apache2 - try using the absolute filename for the source image');
					}
				}
			} else {
				// relative filename (any OS)
				$AbsoluteFilename = $this->config_document_root.$filename;
			}

		} else {

			// relative to current directory (any OS)
			$AbsoluteFilename = $this->config_document_root.dirname(@$_SERVER['PHP_SELF']).'/'.$filename;
			if (substr(dirname(@$_SERVER['PHP_SELF']), 0, 2) == '/~') {
				if ($ApacheLookupURIarray = phpthumb_functions::ApacheLookupURIarray(dirname(@$_SERVER['PHP_SELF']))) {
					$AbsoluteFilename = $ApacheLookupURIarray['filename'].'/'.$filename;
				} else {
					$AbsoluteFilename = realpath('.').'/'.$filename;
					if (is_readable($AbsoluteFilename)) {
						$this->DebugMessage('phpthumb_functions::ApacheLookupURIarray() failed for "'.dirname(@$_SERVER['PHP_SELF']).'", but the correct filename ('.$AbsoluteFilename.') seems to have been resolved with realpath(.)/$filename', __FILE__, __LINE__);
					} else {
						return $this->ErrorImage('phpthumb_functions::ApacheLookupURIarray() failed for "'.dirname(@$_SERVER['PHP_SELF']).'". This has been known to fail on Apache2 - try using the absolute filename for the source image');
					}
				}
			}

		}
		return $AbsoluteFilename;
	}


	function ImageMagickVersion() {
		if (file_exists($this->config_imagemagick_path)) {
			if ($this->iswindows) {
				$commandline = substr($this->config_imagemagick_path, 0, 2).' && cd "'.substr(dirname($this->config_imagemagick_path), 2).'" && '.basename($this->config_imagemagick_path);
			} else {
				$commandline = '"'.$this->config_imagemagick_path.'"';
			}
		} elseif (phpthumb_functions::SafeBackTick('which convert')) {
			$commandline = 'convert';
		} else {
			$this->DebugMessage('Cannot find "convert'.(($this->iswindows) ? '.exe' : '').'" '.(($this->iswindows) ? '' : 'in path or ').'as specified in config_imagemagick_path ('.$this->config_imagemagick_path.')', __FILE__, __LINE__);
		}
		if (!empty($commandline)) {
			$commandline .= ' -version';
			$versionstring = phpthumb_functions::SafeBackTick($commandline);
			if (eregi('^Version: (.*) http', $versionstring, $matches)) {
				return $matches[1];
			}
			$this->DebugMessage('ImageMagick did not return recognized version string ('.$versionstring.')', __FILE__, __LINE__);
			return $versionstring;
		}
		return false;
	}

	function ImageMagickThumbnailToGD() {
		// http://freealter.org/doc_distrib/ImageMagick-5.1.1/www/convert.html
		if (ini_get('safe_mode')) {
			return false;
		}
		if (!function_exists('ImageCreateFromPNG')) {
			// ImageMagickThumbnailToGD() depends on ImageCreateFromPNG()
			return false;
		}
		if (file_exists($this->config_imagemagick_path)) {
			if ($this->iswindows) {
				$commandline = substr($this->config_imagemagick_path, 0, 2).' && cd "'.substr(dirname($this->config_imagemagick_path), 2).'" && '.basename($this->config_imagemagick_path);
			} else {
				$commandline = '"'.$this->config_imagemagick_path.'"';
			}
		} elseif (phpthumb_functions::SafeBackTick('which convert')) {
			$commandline = 'convert';
		}
		if (!empty($commandline)) {
			if ($IMtempfilename = $this->phpThumb_tempnam()) {
				$IMtempfilename = realpath($IMtempfilename);
				$IMwidth  = ((intval($this->w) > 0) ? intval($this->w) : 640);
				$IMheight = ((intval($this->h) > 0) ? intval($this->h) : 480);
				if ((empty($this->aoe) && empty($this->iar)) && ($getimagesize = @GetImageSize($this->sourceFilename))) {
					// limit output size to input size unless AllowOutputEnlargement is enabled
					$IMwidth  = min($IMwidth,  $getimagesize[0]);
					$IMheight = min($IMheight, $getimagesize[1]);
				}
				$commandline .= ' -resize '.$IMwidth.'x'.$IMheight;
				if (!empty($this->iar) && (intval($this->w) > 0) && (intval($this->h) > 0)) {
					$commandline .= '!';
				}
				$commandline .= ' "'.str_replace('/', $this->osslash, $this->sourceFilename).'"';
				$commandline .= ' png:'.$IMtempfilename;
				$commandline .= ' 2>&1';

				$IMresult = phpthumb_functions::SafeBackTick($commandline);
				if (!empty($IMresult)) {
					return $this->ErrorImage('ImageMagick was called as:'."\n".$commandline."\n\n".'but failed with message:'."\n".$IMresult);
				} else {
					if ($this->gdimg_source = ImageCreateFromPNG($IMtempfilename)) {
						unlink($IMtempfilename);
						$this->source_width  = ImageSX($this->gdimg_source);
						$this->source_height = ImageSY($this->gdimg_source);
						return true;
					}
				}
				unlink($IMtempfilename);
			}
		}
		return false;
	}


	function ImageResizeFunction(&$dst_im, &$src_im, $dstX, $dstY, $srcX, $srcY, $dstW, $dstH, $srcW, $srcH) {
		if ($this->gd_version() >= 2.0) {
			return ImageCopyResampled($dst_im, $src_im, $dstX, $dstY, $srcX, $srcY, $dstW, $dstH, $srcW, $srcH);
		}
		return ImageCopyResized($dst_im, $src_im, $dstX, $dstY, $srcX, $srcY, $dstW, $dstH, $srcW, $srcH);
	}

	function ImageCreateFunction($x_size, $y_size) {
		$ImageCreateFunction = 'ImageCreate';
		if ($this->gd_version() >= 2.0) {
			$ImageCreateFunction = 'ImageCreateTrueColor';
		}
		if (!function_exists($ImageCreateFunction)) {
			return $this->ErrorImage($ImageCreateFunction.'() does not exist - no GD support?');
		}
		return $ImageCreateFunction($x_size, $y_size);
	}


	function Rotate() {
		if (!function_exists('ImageRotate')) {
			return false;
		}
		if (!empty($this->ra) || !empty($this->ar)) {
			$this->config_background_hexcolor = (!empty($this->bg) ? $this->bg : $this->config_background_hexcolor);
			if (!eregi('^[0-9A-F]{6}$', $this->config_background_hexcolor)) {
				return $this->ErrorImage('Invalid hex color string "'.$this->config_background_hexcolor.'" for parameter "bg"');
			}

			$rotate_angle = 0;
			if (!empty($this->ra)) {

				$rotate_angle = floatval($this->ra);

			} else {

				if (($this->ar == 'l') && ($this->source_height > $this->source_width)) {
					$rotate_angle = 270;
				} elseif (($this->ar == 'L') && ($this->source_height > $this->source_width)) {
					$rotate_angle = 90;
				} elseif (($this->ar == 'p') && ($this->source_width > $this->source_height)) {
					$rotate_angle = 90;
				} elseif (($this->ar == 'P') && ($this->source_width > $this->source_height)) {
					$rotate_angle = 270;
				}

			}
			while ($rotate_angle < 0) {
				$rotate_angle += 360;
			}
			$rotate_angle = $rotate_angle % 360;
			if ($rotate_angle != 0) {

				if (ImageColorTransparent($this->gdimg_source) >= 0) {
					// ImageRotate() forgets all about an image's transparency and sets the transparent color to black
					// To compensate, flood-fill the transparent color of the source image with the specified background color first
					// then rotate and the colors should match

					if (!function_exists('ImageIsTrueColor') || !ImageIsTrueColor($this->gdimg_source)) {
						// convert paletted image to true-color before rotating to prevent nasty aliasing artifacts

						$this->source_width  = ImageSX($this->gdimg_source);
						$this->source_height = ImageSY($this->gdimg_source);
						$gdimg_newsrc = $this->ImageCreateFunction($this->source_width, $this->source_height);
						$background_color = phpthumb_functions::ImageHexColorAllocate($gdimg_newsrc, $this->config_background_hexcolor);
						ImageFilledRectangle($gdimg_newsrc, 0, 0, $this->source_width, $this->source_height, phpthumb_functions::ImageHexColorAllocate($gdimg_newsrc, $this->config_background_hexcolor));
						ImageCopy($gdimg_newsrc, $this->gdimg_source, 0, 0, 0, 0, $this->source_width, $this->source_height);
						ImageDestroy($this->gdimg_source);
						unset($this->gdimg_source);
						$this->gdimg_source = $gdimg_newsrc;
						unset($gdimg_newsrc);

					} else {

						ImageColorSet(
							$this->gdimg_source,
							ImageColorTransparent($this->gdimg_source),
							hexdec(substr($this->config_background_hexcolor, 0, 2)),
							hexdec(substr($this->config_background_hexcolor, 2, 2)),
							hexdec(substr($this->config_background_hexcolor, 4, 2)));

						ImageColorTransparent($this->gdimg_source, -1);

					}
				}

				$background_color = phpthumb_functions::ImageHexColorAllocate($this->gdimg_source, $this->config_background_hexcolor);
				$this->gdimg_source = ImageRotate($this->gdimg_source, $rotate_angle, $background_color);
				$this->source_width  = ImageSX($this->gdimg_source);
				$this->source_height = ImageSY($this->gdimg_source);
			}
		}
		return true;
	}



	function FixedAspectRatio() {
		// optional image border and fixed-dimension images (regardless of aspect ratio)
		if (isset($this->bw)) {
			if ($this->thumbnail_image_width >= $this->thumbnail_width) {
				if (isset($this->w)) {
					$aspectratio = $this->thumbnail_image_height / $this->thumbnail_image_width;
					$this->thumbnail_image_width -= ($this->bw * 2);
					$this->thumbnail_image_height = round($this->thumbnail_image_width * $aspectratio);
					if (!isset($this->h)) {
						$this->thumbnail_height = $this->thumbnail_image_height + ($this->bw * 2);
					}
				} elseif (($this->thumbnail_image_height + ($this->bw * 2)) < $this->thumbnail_height) {
					$this->thumbnail_image_height = $this->thumbnail_height - ($this->bw * 2);
					$this->thumbnail_image_width  = round($this->thumbnail_image_height / $aspectratio);
				}
			} else {
				if (isset($this->h)) {
					$aspectratio = $this->thumbnail_image_width / $this->thumbnail_image_height;
					$this->thumbnail_image_height -= ($this->bw * 2);
					$this->thumbnail_image_width = round($this->thumbnail_image_height * $aspectratio);
				} elseif (($this->thumbnail_image_width + ($this->bw * 2)) < $this->thumbnail_width) {
					$this->thumbnail_image_width = $this->thumbnail_width - ($this->bw * 2);
					$this->thumbnail_image_height  = round($this->thumbnail_image_width / $aspectratio);
				}
			}
			if (!empty($this->brx) && !empty($this->bry) && ($this->bw > 0)) {
				$this->thumbnail_width  = max($this->thumbnail_width,  $this->thumbnail_image_width  + $this->brx);
				$this->thumbnail_height = max($this->thumbnail_height, $this->thumbnail_image_height + $this->bry);
			} else {
				$this->thumbnail_width  = max($this->thumbnail_width,  $this->thumbnail_image_width  + (2 * $this->bw));
				$this->thumbnail_height = max($this->thumbnail_height, $this->thumbnail_image_height + (2 * $this->bw));
			}
		}
		return true;
	}


	function ImageBorder() {
		if (isset($this->bw)) {
			// optional image border and fixed-dimension images (regardless of aspect ratio)
			$this->config_background_hexcolor = (!empty($this->bg) ? $this->bg : $this->config_background_hexcolor);
			$this->config_border_hexcolor     = (!empty($this->bc) ? $this->bc : $this->config_border_hexcolor);
			if (!eregi('^[0-9A-F]{6}$', $this->config_background_hexcolor)) {
				return $this->ErrorImage('Invalid hex color string "'.$this->config_background_hexcolor.'" for parameter "bg"');
			}
			if (!eregi('^[0-9A-F]{6}$', $this->config_border_hexcolor)) {
				return $this->ErrorImage('Invalid hex color string "'.$this->config_border_hexcolor.'" for parameter "bc"');
			}
			$background_color = phpthumb_functions::ImageHexColorAllocate($this->gdimg_output, $this->config_background_hexcolor);
			$border_color     = phpthumb_functions::ImageHexColorAllocate($this->gdimg_output, $this->config_border_hexcolor);
			ImageFilledRectangle($this->gdimg_output, 0, 0, $this->thumbnail_width, $this->thumbnail_height, $background_color);
			if ($this->bw > 0) {
				// ImageRectangle() draws a rectangle centred on the coordinates given,
				// so coordinates must be offset by half the line thickness
				if (($this->bw > 1) && @ImageSetThickness($this->gdimg_output, $this->bw)) {
					// better way (for lines > 1px thick), but requires GD v2.0.1+
					ImageRectangle($this->gdimg_output, floor($this->bw / 2), floor($this->bw / 2), $this->thumbnail_width - ceil($this->bw / 2), $this->thumbnail_height - ceil($this->bw / 2), $border_color);
				} else {
					for ($i = 0; $i < $this->bw; $i++) {
						ImageRectangle($this->gdimg_output, $i, $i, $this->thumbnail_width - $i - 1, $this->thumbnail_height - $i - 1, $border_color);
					}
				}
			}
			if (!empty($this->brx) && !empty($this->bry) && ($this->bw > 0)) {

				// if 'bw' > 0 then leave the image rectangular and have a rounded border line around the edges of the image
				// if 'bw' == 0 then round off the corners of the image itself (see RoundedImageCorners())

				ImageFilledRectangle($this->gdimg_output,                                   0,                                    0,             $this->brx,              $this->bry, $background_color);
				ImageFilledRectangle($this->gdimg_output, $this->thumbnail_width - $this->brx,                                    0, $this->thumbnail_width,              $this->bry, $background_color);
				ImageFilledRectangle($this->gdimg_output, $this->thumbnail_width - $this->brx, $this->thumbnail_height - $this->bry, $this->thumbnail_width, $this->thumbnail_height, $background_color);
				ImageFilledRectangle($this->gdimg_output,                                   0, $this->thumbnail_height - $this->bry,             $this->brx, $this->thumbnail_height, $background_color);

				// PHP bug: ImageArc() with thicknesses > 1 give bad/undesirable/unpredicatable results
				// Solution: Draw multiple 1px arcs side-by-side.
				if ($this->gd_version(false) >= 2) {
					// imagesetthickness(): requires GD 2.0 or later
					// GD1 always has line thickness of 1
					ImageSetThickness($this->gdimg_output, 1);
				}
				for ($thickness_offset = 0; $thickness_offset < $this->bw; $thickness_offset++) {
					// Problem: parallel arcs give strange/ugly antialiasing problems
					// Solution: draw non-parallel arcs, from one side of the line thickness at the start angle
					//   to the opposite edge of the line thickness at the terminating angle
					if ($this->bw > 1) {
						ImageArc($this->gdimg_output,                                              $this->brx,                       $thickness_offset - 1 + $this->bry, $this->brx * 2, $this->bry * 2, 180, 270, $border_color); // top-left
						ImageArc($this->gdimg_output,                      $thickness_offset - 1 + $this->brx,                                               $this->bry, $this->brx * 2, $this->bry * 2, 180, 270, $border_color); // top-left

						ImageArc($this->gdimg_output,                     $this->thumbnail_width - $this->brx,                       $thickness_offset - 1 + $this->bry, $this->brx * 2, $this->bry * 2, 270, 360, $border_color); // top-right
						ImageArc($this->gdimg_output, $this->thumbnail_width - $thickness_offset - $this->brx,                                               $this->bry, $this->brx * 2, $this->bry * 2, 270, 360, $border_color); // top-right

						ImageArc($this->gdimg_output,                     $this->thumbnail_width - $this->brx, $this->thumbnail_height - $thickness_offset - $this->bry, $this->brx * 2, $this->bry * 2,   0,  90, $border_color); // bottom-right
						ImageArc($this->gdimg_output, $this->thumbnail_width - $thickness_offset - $this->brx,                     $this->thumbnail_height - $this->bry, $this->brx * 2, $this->bry * 2,   0,  90, $border_color); // bottom-right

						ImageArc($this->gdimg_output,                                              $this->brx, $this->thumbnail_height - $thickness_offset - $this->bry, $this->brx * 2, $this->bry * 2,  90, 180, $border_color); // bottom-left
						ImageArc($this->gdimg_output,                     $thickness_offset - 1 +  $this->brx,                     $this->thumbnail_height - $this->bry, $this->brx * 2, $this->bry * 2,  90, 180, $border_color); // bottom-left
					} else {
						ImageArc($this->gdimg_output,                          $this->brx,       $thickness_offset + $this->bry, $this->brx * 2, $this->bry * 2, 180, 270, $border_color); // top-left
						ImageArc($this->gdimg_output, $this->thumbnail_width - $this->brx,                           $this->bry, $this->brx * 2, $this->bry * 2, 270, 360, $border_color); // top-right
						ImageArc($this->gdimg_output, $this->thumbnail_width - $this->brx, $this->thumbnail_height - $this->bry, $this->brx * 2, $this->bry * 2,   0,  90, $border_color); // bottom-right
						ImageArc($this->gdimg_output,                          $this->brx, $this->thumbnail_height - $this->bry, $this->brx * 2, $this->bry * 2,  90, 180, $border_color); // bottom-left
					}
				}


				ImageArc($this->gdimg_output,                          $this->brx,                           $this->bry, $this->brx * 2, $this->bry * 2, 180, 270, $border_color);
				ImageArc($this->gdimg_output, $this->thumbnail_width - $this->brx,                           $this->bry, $this->brx * 2, $this->bry * 2, 270, 360, $border_color);
				ImageArc($this->gdimg_output, $this->thumbnail_width - $this->brx, $this->thumbnail_height - $this->bry, $this->brx * 2, $this->bry * 2,   0,  90, $border_color);
				ImageArc($this->gdimg_output,                          $this->brx, $this->thumbnail_height - $this->bry, $this->brx * 2, $this->bry * 2,  90, 180, $border_color);

			}
		}
		return true;
	}


	function AntiOffsiteLinking() {
		////////////////////////////////////////////////////////////////
		// Optional anti-offsite hijacking of the thumbnail script
		if ($this->config_nohotlink_enabled && (substr(strtolower($this->src), 0, 7) == 'http://')) {
			$parsed_url = parse_url($this->src);
			if (!in_array(@$parsed_url['host'], $this->config_nohotlink_valid_domains)) {
				// This domain is not allowed
				if (!eregi('^[0-9A-F]{6}$', $this->config_nohotlink_fill_hexcolor)) {
					return $this->ErrorImage('Invalid hex color string "'.$this->config_nohotlink_fill_hexcolor.'" for $this->config_nohotlink_fill_hexcolor');
				}
				if (!eregi('^[0-9A-F]{6}$', $this->config_nohotlink_text_hexcolor)) {
					return $this->ErrorImage('Invalid hex color string "'.$this->config_nohotlink_text_hexcolor.'" for $this->config_nohotlink_text_hexcolor');
				}
				if ($this->config_nohotlink_erase_image) {
					return $this->ErrorImage($this->config_nohotlink_text_message, $this->thumbnail_width, $this->thumbnail_height, $this->config_nohotlink_fill_hexcolor, $this->config_nohotlink_text_hexcolor, $this->config_nohotlink_text_fontsize);
				} else {
					$nohotlink_text_array = explode("\n", wordwrap($this->config_nohotlink_text_message, floor($this->thumbnail_width / ImageFontWidth($this->config_nohotlink_text_fontsize)), "\n"));
					$rowcounter = 0;
					$nohotlink_text_color = phpthumb_functions::ImageHexColorAllocate($this->gdimg_output, $this->config_nohotlink_text_hexcolor);
					foreach ($nohotlink_text_array as $textline) {
						ImageString($this->gdimg_output, $this->config_nohotlink_text_fontsize, 2, $rowcounter++ * ImageFontHeight($this->config_nohotlink_text_fontsize), $textline, $nohotlink_text_color);
					}
				}
			}
		}
		return true;
	}


	function UnsharpMasking() {
		if (!empty($this->usa) || !empty($this->usr)) {
			$this->usa = (isset($this->usa) ? $this->usa : 80);
			$this->usr = (isset($this->usr) ? $this->usr : 0.5);
			$this->ust = (isset($this->ust) ? $this->ust : 3);
			if ($this->gd_version() >= 2.0) {
				if (@include_once('phpthumb.unsharp.php')) {
					phpUnsharpMask($this->gdimg_output, $this->usa, $this->usr, $this->ust);
				} else {
					return $this->ErrorImage('Error including "phpthumb.unsharp.php" which is required for unsharp masking');
				}
			}
		}
		return true;
	}


	function Watermarking() {
		if (!empty($this->wmf)) {
			$WatermarkFilename = $this->ResolveFilenameToAbsolute($this->wmf);
			if (is_readable($WatermarkFilename)) {
				if ($fp_watermark = @fopen($WatermarkFilename, 'rb')) {
					$WatermarkImageData = fread($fp_watermark, filesize($WatermarkFilename));
					fclose($fp_watermark);
					if ($img_watermark = $this->ImageCreateFromStringReplacement($WatermarkImageData)) {
						$watermark_source_x        = 0;
						$watermark_source_y        = 0;
						$watermark_source_width    = ImageSX($img_watermark);
						$watermark_source_height   = ImageSY($img_watermark);
						$watermark_opacity_percent = (!empty($this->wmp) ? $this->wmp : 50);
						$watermark_margin_percent  = (100 - (!empty($this->wmm) ? $this->wmm : 5)) / 100;
						switch (@$this->wma) {
							case '*':
								$gdimg_tiledwatermark = $this->ImageCreateFunction($this->thumbnail_width, $this->thumbnail_height);
								// set the tiled image transparent color to whatever the untiled image transparency index is
								ImageColorTransparent($gdimg_tiledwatermark, ImageColorTransparent($img_watermark));

								// tile the image as many times as can fit
								for ($x = round((1 - $watermark_margin_percent) * $this->thumbnail_width); $x < ($this->thumbnail_width + $watermark_source_width); $x += round($watermark_source_width + ((1 - $watermark_margin_percent) * $this->thumbnail_width))) {
									for ($y = round((1 - $watermark_margin_percent) * $this->thumbnail_height); $y < ($this->thumbnail_height + $watermark_source_height); $y += round($watermark_source_height + ((1 - $watermark_margin_percent) * $this->thumbnail_height))) {
										ImageCopy($gdimg_tiledwatermark, $img_watermark, $x, $y, 0, 0, $watermark_source_width, $watermark_source_height);
									}
								}
								$watermark_source_width  = ImageSX($gdimg_tiledwatermark);
								$watermark_source_height = ImageSY($gdimg_tiledwatermark);
								$watermark_destination_x = 0;
								$watermark_destination_y = 0;
								ImageDestroy($img_watermark);
								$img_watermark = $gdimg_tiledwatermark;
								break;

							case 'T':
								$watermark_destination_x = round((($this->thumbnail_width  / 2) - ($watermark_source_width / 2)) + $watermark_margin_percent);
								$watermark_destination_y = round((1 - $watermark_margin_percent) * $this->thumbnail_height);
								break;

							case 'B':
								$watermark_destination_x = round((($this->thumbnail_width  / 2) - ($watermark_source_width / 2)) + $watermark_margin_percent);
								$watermark_destination_y = round(($this->thumbnail_height - $watermark_source_height) * $watermark_margin_percent);
								break;

							case 'L':
								$watermark_destination_x = round((1 - $watermark_margin_percent) * $this->thumbnail_width);
								$watermark_destination_y = round((($this->thumbnail_height / 2) - ($watermark_source_height / 2)) + $watermark_margin_percent);
								break;

							case 'R':
								$watermark_destination_x = round(($this->thumbnail_width - $watermark_source_width)  * $watermark_margin_percent);
								$watermark_destination_y = round((($this->thumbnail_height / 2) - ($watermark_source_height / 2)) + $watermark_margin_percent);
								break;

							case 'C':
								$watermark_destination_x = round((($this->thumbnail_width  / 2) - ($watermark_source_width / 2)) + $watermark_margin_percent);
								$watermark_destination_y = round((($this->thumbnail_height / 2) - ($watermark_source_height / 2)) + $watermark_margin_percent);
								break;

							case 'TL':
								$watermark_destination_x = round((1 - $watermark_margin_percent) * $this->thumbnail_width);
								$watermark_destination_y = round((1 - $watermark_margin_percent) * $this->thumbnail_height);
								break;

							case 'TR':
								$watermark_destination_x = round(($this->thumbnail_width - $watermark_source_width)  * $watermark_margin_percent);
								$watermark_destination_y = round((1 - $watermark_margin_percent) * $this->thumbnail_height);
								break;

							case 'BL':
								$watermark_destination_x = round((1 - $watermark_margin_percent) * $this->thumbnail_width);
								$watermark_destination_y = round(($this->thumbnail_height - $watermark_source_height) * $watermark_margin_percent);
								break;

							case 'BR':
							default:
								$watermark_destination_x = round(($this->thumbnail_width - $watermark_source_width)  * $watermark_margin_percent);
								$watermark_destination_y = round(($this->thumbnail_height - $watermark_source_height) * $watermark_margin_percent);
								break;
						}
						ImageCopyMerge($this->gdimg_output, $img_watermark, $watermark_destination_x, $watermark_destination_y, $watermark_source_x, $watermark_source_y, $watermark_source_width, $watermark_source_height, $watermark_opacity_percent);
						return true;
					}
				}
			}
			return false;
		}
		return true;
	}


	function RoundedImageCorners() {
		if ($this->gd_version() < 2) {
			return false;
		}
		if (!empty($this->brx) && !empty($this->bry) && ($this->bw == 0)) {
			$gdimg_cornermask = $this->ImageCreateFunction($this->thumbnail_width, $this->thumbnail_height);
			$background_color_cornermask  = phpthumb_functions::ImageHexColorAllocate($gdimg_cornermask, $this->config_background_hexcolor);
			$border_color_cornermask      = phpthumb_functions::ImageHexColorAllocate($gdimg_cornermask, $this->config_border_hexcolor);

			// Top Left
			ImageArc($gdimg_cornermask, $this->brx - 1, $this->bry - 1, $this->brx * 2, $this->bry * 2, 180, 270, $background_color_cornermask);
			ImageFillToBorder($gdimg_cornermask, 0, 0, $background_color_cornermask, $background_color_cornermask);

			// Top Right
			ImageArc($gdimg_cornermask, $this->thumbnail_width - $this->brx, $this->bry - 1, $this->brx * 2, $this->bry * 2, 270, 360, $background_color_cornermask);
			ImageFillToBorder($gdimg_cornermask, $this->thumbnail_width, 0, $background_color_cornermask, $background_color_cornermask);

			// Bottom Right
			ImageArc($gdimg_cornermask, $this->thumbnail_width - $this->brx, $this->thumbnail_height - $this->bry, $this->brx * 2, $this->bry * 2, 0, 90, $background_color_cornermask);
			ImageFillToBorder($gdimg_cornermask, $this->thumbnail_width, $this->thumbnail_height, $background_color_cornermask, $background_color_cornermask);

			// Bottom Left
			ImageArc($gdimg_cornermask, $this->brx - 1, $this->thumbnail_height - $this->bry, $this->brx * 2, $this->bry * 2, 90, 180, $background_color_cornermask);
			ImageFillToBorder($gdimg_cornermask, 0, $this->thumbnail_height, $background_color_cornermask, $background_color_cornermask);

			$transparent_color_cornermask = ImageColorTransparent($gdimg_cornermask, ImageColorAt($gdimg_cornermask, round($this->thumbnail_width / 2), round($this->thumbnail_height / 2)));
			ImageArc($gdimg_cornermask, $this->brx, $this->bry, $this->brx * 2, $this->bry * 2, 180, 270, $transparent_color_cornermask);
			ImageArc($gdimg_cornermask, $this->thumbnail_width - $this->brx, $this->bry, $this->brx * 2, $this->bry * 2, 270, 360, $transparent_color_cornermask);
			ImageArc($gdimg_cornermask, $this->thumbnail_width - $this->brx, $this->thumbnail_height - $this->bry, $this->brx * 2, $this->bry * 2, 0, 90, $transparent_color_cornermask);
			ImageArc($gdimg_cornermask, $this->brx, $this->thumbnail_height - $this->bry, $this->brx * 2, $this->bry * 2, 90, 180, $transparent_color_cornermask);

			ImageCopyMerge($this->gdimg_output, $gdimg_cornermask, 0, 0, 0, 0, $this->thumbnail_width, $this->thumbnail_height, 100);

			// Make rounded corners transparent (optional)
			if (!empty($this->bct)) {
				if ($this->bct == 256) {
					ImageTrueColorToPalette($this->gdimg_output, true, 256);
				}
				ImageColorTransparent($this->gdimg_output, ImageColorAt($this->gdimg_output, 0, 0));
			}
		}
		return true;
	}


	function MaxFileSize() {
		if ($this->gd_version() < 2) {
			return false;
		}
		if (!empty($this->maxb) && ($this->maxb > 0)) {
			switch ($this->thumbnailFormat) {
				case 'png':
				case 'gif':
					$imgRenderFunction = 'image'.$this->thumbnailFormat;

					ob_start();
					$imgRenderFunction($this->gdimg_output);
					$imgdata = ob_get_contents();
					ob_end_clean();

					if (strlen($imgdata) > $this->maxb) {
						for ($i = 8; $i >= 1; $i--) {
							$tempIMG = ImageCreateTrueColor(ImageSX($this->gdimg_output), ImageSY($this->gdimg_output));
							ImageCopy($tempIMG, $this->gdimg_output, 0, 0, 0, 0, ImageSX($this->gdimg_output), ImageSY($this->gdimg_output));
							ImageTrueColorToPalette($tempIMG, true, pow(2, $i));
							ob_start();
							$imgRenderFunction($tempIMG);
							$imgdata = ob_get_contents();
							ob_end_clean();

							if (strlen($imgdata) <= $this->maxb) {
								ImageTrueColorToPalette($this->gdimg_output, true, pow(2, $i));
								break;
							}
						}
					}
					if (strlen($imgdata) > $this->maxb) {
						ImageTrueColorToPalette($this->gdimg_output, true, pow(2, $i));
						return false;
					}
					break;

				case 'jpeg':
					ob_start();
					ImageJPEG($this->gdimg_output);
					$imgdata = ob_get_contents();
					ob_end_clean();

					$OriginalJPEGquality = $this->thumbnailQuality;
					if (strlen($imgdata) > $this->maxb) {
						for ($i = 3; $i < 20; $i++) {
							$q = round(100 * (1 - log10($i / 2)));
							ob_start();
							ImageJPEG($this->gdimg_output, '', $q);
							$imgdata = ob_get_contents();
							ob_end_clean();

							$this->thumbnailQuality = $q;
							if (strlen($imgdata) <= $this->maxb) {
								break;
							}
						}
					}
					if (strlen($imgdata) > $this->maxb) {
						return false;
					}
					break;

				default:
					return false;
					break;
			}
		}
		return true;
	}


	function CalculateThumbnailDimensions() {

		$this->thumbnailCropX = (!empty($this->sx) ? $this->sx : 0);
		$this->thumbnailCropY = (!empty($this->sy) ? $this->sy : 0);
		$this->thumbnailCropW = (!empty($this->sw) ? $this->sw : $this->source_width);
		$this->thumbnailCropH = (!empty($this->sh) ? $this->sh : $this->source_height);

		// limit source area to original image area
		$this->thumbnailCropW = min($this->thumbnailCropW, $this->source_width  - $this->thumbnailCropX);
		$this->thumbnailCropH = min($this->thumbnailCropH, $this->source_height - $this->thumbnailCropY);

		if (!empty($this->iar) && !empty($this->w) && !empty($this->h)) {

			// Ignore Aspect Ratio
			// forget all the careful proportional resizing we did above, stretch image to fit 'w' && 'h'
			$this->thumbnail_width  = $this->w;
			$this->thumbnail_height = $this->h;
			$this->thumbnail_image_width  = $this->thumbnail_width  - (@$this->bw * 2);
			$this->thumbnail_image_height = $this->thumbnail_height - (@$this->bw * 2);

		} else {

			// default new width and height to source area
			$this->thumbnail_image_width  = $this->thumbnailCropW;
			$this->thumbnail_image_height = $this->thumbnailCropH;
			if (($this->config_output_maxwidth > 0) && ($this->thumbnail_image_width > $this->config_output_maxwidth)) {
				if (($this->config_output_maxwidth < $this->thumbnailCropW) || $this->config_output_allow_enlarging) {
					$maxwidth = $this->config_output_maxwidth;
					$this->thumbnail_image_width = $maxwidth;
					$this->thumbnail_image_height = round($this->thumbnailCropH * ($this->thumbnail_image_width / $this->thumbnailCropW));
				}
			}

			// if user sets width, save as max width
			// and compute new height based on source area aspect ratio
			if (!empty($this->w)) {
				if (($this->w < $this->thumbnailCropW) || $this->config_output_allow_enlarging) {
					$maxwidth = $this->w;
					$this->thumbnail_image_width = $this->w;
					$this->thumbnail_image_height = round($this->thumbnailCropH * $this->w / $this->thumbnailCropW);
				}
			}

			// if user sets height, save as max height
			// if the max width has already been set and the new image is too tall,
			// compute new width based on source area aspect ratio
			// otherwise, use max height and compute new width
			if (!empty($this->h) || ($this->config_output_maxheight > 0)) {
				$maxheight = (!empty($this->h) ? $this->h : $this->config_output_maxheight);
				if (($maxheight < $this->thumbnailCropH) || $this->config_output_allow_enlarging) {
					if (isset($maxwidth)) {
						if ($this->thumbnail_image_height > $maxheight) {
							$this->thumbnail_image_width  = round($this->thumbnailCropW * $maxheight / $this->thumbnailCropH);
							$this->thumbnail_image_height = $maxheight;
						}
					} else {
						$this->thumbnail_image_height = $maxheight;
						$this->thumbnail_image_width  = round($this->thumbnailCropW * $this->thumbnail_image_height / $this->thumbnailCropH);
					}
				}
			}

			$this->thumbnail_width  = $this->thumbnail_image_width;
			$this->thumbnail_height = $this->thumbnail_image_height;
			if (!empty($this->w) && !empty($this->h) && isset($this->bw)) {
				$this->thumbnail_width  = $this->w;
				$this->thumbnail_height = $this->h;
			}

			$this->FixedAspectRatio();

		}
		return true;
	}


	function CreateGDoutput() {

		$this->CalculateThumbnailDimensions();

		// Create the GD image (either true-color or 256-color, depending on GD version)
		$this->gdimg_output = $this->ImageCreateFunction($this->thumbnail_width, $this->thumbnail_height);


		// Images that have transparency must have the background filled with the configured 'bg' color
		// otherwise the transparent color will appear as black
		$current_transparent_color = ImageColorTransparent($this->gdimg_source);
		if ($current_transparent_color >= 0) {

			$this->config_background_hexcolor = (!empty($this->bg) ? $this->bg : $this->config_background_hexcolor);
			if (!eregi('^[0-9A-F]{6}$', $this->config_background_hexcolor)) {
				return $this->ErrorImage('Invalid hex color string "'.$this->config_background_hexcolor.'" for parameter "bg"');
			}
			$background_color = phpthumb_functions::ImageHexColorAllocate($this->gdimg_output, $this->config_background_hexcolor);
			ImageFilledRectangle($this->gdimg_output, 0, 0, $this->thumbnail_width, $this->thumbnail_height, $background_color);

		}
		return true;
	}



	function ExtractEXIFgetImageSize() {

		if ($this->getimagesizeinfo = @GetImageSize($this->sourceFilename)) {

			$this->source_width  = $this->getimagesizeinfo[0];
			$this->source_height = $this->getimagesizeinfo[1];

			if (function_exists('exif_thumbnail') && ($this->getimagesizeinfo[2] == 2)) {
				// Extract EXIF info from JPEGs

				$this->exif_thumbnail_width  = '';
				$this->exif_thumbnail_height = '';
				$this->exif_thumbnail_type   = '';

				// The parameters width, height and imagetype are available since PHP v4.3.0
				if (phpthumb_functions::version_compare_replacement(phpversion(), '4.3.0', '>=')) {

					$this->exif_thumbnail_data = @exif_thumbnail($this->sourceFilename, $this->exif_thumbnail_width, $this->exif_thumbnail_height, $this->exif_thumbnail_type);

				} else {

					// older versions of exif_thumbnail output an error message but NOT return false on failure
					ob_start();
					$this->exif_thumbnail_data = exif_thumbnail($this->sourceFilename);
					$exit_thumbnail_error = ob_get_contents();
					ob_end_clean();
					if (empty($exit_thumbnail_error) && !empty($this->exif_thumbnail_data)) {

						if ($gdimg_exif_temp = $this->ImageCreateFromStringReplacement($this->exif_thumbnail_data, false)) {
							$this->exif_thumbnail_width  = ImageSX($gdimg_exif_temp);
							$this->exif_thumbnail_height = ImageSY($gdimg_exif_temp);
							$this->exif_thumbnail_type   = 2; // (2 == JPEG) before PHP v4.3.0 only JPEG format EXIF thumbnails are returned
							unset($gdimg_exif_temp);
						} else {
							return $this->ErrorImage('Failed - $this->ImageCreateFromStringReplacement($this->exif_thumbnail_data) in '.__FILE__.' on line '.__LINE__);
						}

					}

				}

			} elseif (!function_exists('exif_thumbnail')) {

				$this->DebugMessage('exif_thumbnail() does not exis, cannot extract EXIF thumbnail', __FILE__, __LINE__);

			} else {

				//$this->DebugMessage('"'.$this->sourceFilename.'" is not JPEG, therefore no EXIF is present', __FILE__, __LINE__);

			}

			// see if EXIF thumbnail can be used directly with no processing
			if (!empty($this->exif_thumbnail_data)) {
				while (true) {
					if (empty($this->xto)) {
						if (isset($this->w) && ($this->w != $this->exif_thumbnail_width)) {
							break;
						}
						if (isset($this->h) && ($this->h != $this->exif_thumbnail_height)) {
							break;
						}
						$CannotBeSetParameters = array('sx', 'sy', 'sh', 'sw', 'bw', 'bg', 'bc', 'usa', 'usr', 'ust', 'wmf', 'wmp', 'wmm', 'wma');
						foreach ($CannotBeSetParameters as $parameter) {
							if (!empty($this->$parameter)) {
								break 2;
							}
						}
					}

					// EXIF thumbnail can be used directly for these parameters - write cached file
					$ImageTypesLookup = array(2=>'jpeg'); // EXIF thumbnails are (currently?) only availble from JPEG source images
					if (is_dir($this->config_cache_directory) && is_writable($this->config_cache_directory) && isset($ImageTypesLookup[$this->exif_thumbnail_type])) {
						if (is_writable($this->cache_filename)) {
							if ($fp_cached = @fopen($this->cache_filename, 'wb')) {
								fwrite($fp_cached, $this->exif_thumbnail_data);
								fclose($fp_cached);
							}
						}
					}

					if ($mime_type = phpthumb_functions::ImageTypeToMIMEtype($this->exif_thumbnail_type)) {
						header('Content-type: '.$mime_type);
						echo $this->exif_thumbnail_data;
						exit;
					} else {
						return $this->ErrorImage('phpthumb_functions::ImageTypeToMIMEtype('.$this->exif_thumbnail_type.') failed in '.__FILE__.' on line '.__LINE__);
					}
					break;
				}
			}

			if (($this->config_max_source_pixels > 0) && (($this->source_width * $this->source_height) > $this->config_max_source_pixels)) {
				// Source image is larger than would fit in available PHP memory.
				// If ImageMagick is installed, use it to generate the thumbnail.
				// Else, if an EXIF thumbnail is available, use that as the source image.
				// Otherwise, no choice but to fail with an error message

				if ($this->ImageMagickThumbnailToGD()) {

					// excellent, we have a thumbnailed source image

				} elseif (!empty($this->exif_thumbnail_data)) {

					// EXIF thumbnail exists, and will be use as source image
					$this->gdimg_source  = $this->ImageCreateFromStringReplacement($this->exif_thumbnail_data);
					$this->source_width  = $this->exif_thumbnail_width;
					$this->source_height = $this->exif_thumbnail_height;

					// override allow-enlarging setting if EXIF thumbnail is the only source available
					// otherwise thumbnails larger than the EXIF thumbnail will be created at EXIF size
					$this->config_output_allow_enlarging = true;

				} else {

					return $this->ErrorImage('Source image is more than '.sprintf('%1.1f', ($this->config_max_source_pixels / 1000000)).' megapixels - insufficient memory.'."\n".'EXIF thumbnail unavailable.');

				}
			}

		} else {

			$this->DebugMessage('GetImageSize("'.$this->sourceFilename.'") failed', __FILE__, __LINE__);

		}
		return true;
	}


	function SetCacheFilename() {
		$this->setOutputFormat();
		$this->setCacheDirectory();
		if (empty($this->config_cache_directory)) {
			$this->DebugMessage('SetCacheFilename() failed because $this->config_cache_directory is empty', __FILE__, __LINE__);
			return false;
		}

		if (empty($this->sourceFilename) && empty($this->rawImageData) && !empty($this->src)) {
			$this->sourceFilename = $this->ResolveFilenameToAbsolute($this->src);
		}

		$this->cache_filename  = $this->config_cache_directory.'/phpThumb_cache';
		if (empty($this->src) && !empty($this->rawImageData)) {
			$this->cache_filename .= '_'.strtolower(md5($this->rawImageData));
		} else {
			$this->cache_filename .= '_'.urlencode($this->src);
		}
		$FilenameParameters = array('h', 'w', 'sx', 'sy', 'sw', 'sh', 'bw', 'brx', 'bry', 'bg', 'bgt', 'bc', 'usa', 'usr', 'ust', 'wmf', 'wmp', 'wmm', 'wma', 'xto', 'ra', 'ar', 'iar', 'maxb');
		foreach ($FilenameParameters as $key) {
			if (isset($this->$key)) {
				if ($this->$key === true) {
					$this->cache_filename .= '_'.$key.'1';
				} else {
					$this->cache_filename .= '_'.$key.$this->$key;
				}
			}
		}
		$this->cache_filename .= '_'.intval(@filemtime($this->sourceFilename));
		$this->cache_filename .= '_q'.$this->thumbnailQuality;
		$this->cache_filename .= '_'.$this->thumbnailFormat;

		return true;
	}


	function SourceImageToGD() {

		if ($this->config_use_exif_thumbnail_for_speed && !empty($this->exif_thumbnail_data)) {
			if (($this->exif_thumbnail_width >= $this->thumbnail_image_width) && ($this->exif_thumbnail_height >= $this->thumbnail_image_height) &&
				($this->thumbnailCropX == 0) && ($this->thumbnailCropY == 0) &&
				($this->source_width == $this->thumbnailCropW) && ($this->source_height == $this->thumbnailCropH)) {
					// EXIF thumbnail exists, and is equal to or larger than destination thumbnail, and will be use as source image
					// Only benefit here is greater speed, not lower memory usage
					$this->DebugMessage('Trying to use EXIF thumbnail as source image', __FILE__, __LINE__);

					if ($gdimg_exif_temp = $this->ImageCreateFromStringReplacement($this->exif_thumbnail_data, false)) {

						$this->DebugMessage('Successfully using EXIF thumbnail as source image', __FILE__, __LINE__);

						$this->gdimg_source = $gdimg_exif_temp;
						$this->source_width  = $this->exif_thumbnail_width;
						$this->source_height = $this->exif_thumbnail_height;
						$this->thumbnailCropW = $this->source_width;
						$this->thumbnailCropH = $this->source_height;

						return true;

					} else {
						$this->DebugMessage('$this->ImageCreateFromStringReplacement($this->exif_thumbnail_data, false) failed', __FILE__, __LINE__);
					}

			} else {
				$this->DebugMessage('Not using EXIF thumbnail data because EXIF thumbnail is too small', __FILE__, __LINE__);
			}

		} else {

			if (!$this->config_use_exif_thumbnail_for_speed) {
				$this->DebugMessage('Not using EXIF thumbnail data because $this->config_use_exif_thumbnail_for_speed is FALSE', __FILE__, __LINE__);
			} elseif ($this->getimagesizeinfo[2] == 2) {
				$this->DebugMessage('Not using EXIF thumbnail data because EXIF thumbnail is unavailable', __FILE__, __LINE__);
			} else {
				//$this->DebugMessage('Not using EXIF thumbnail data because source image is not JPEG, therefore no EXIF thumbnail available', __FILE__, __LINE__);
			}

		}

		if (empty($this->gdimg_source)) {
			// try to create GD image source directly via GD, if possible,
			// rather than buffering to memory and creating with ImageCreateFromString
			$ImageCreateWasAttempted = false;

			if ($this->iswindows && ((substr($this->sourceFilename, 0, 2) == '//') || (substr($this->sourceFilename, 0, 2) == '\\\\'))) {
				// Windows \\share\filename.ext
			} elseif (substr($this->sourceFilename, 0, 7) == 'http://') {
				// URL
			} else {
				if (!file_exists($this->sourceFilename)) {
					return $this->ErrorImage('"'.$this->sourceFilename.'" does not exist');
				} elseif (!is_file($this->sourceFilename)) {
					return $this->ErrorImage('"'.$this->sourceFilename.'" is not a file');
				}
			}

			if ((@$this->getimagesizeinfo[2] == 1) && function_exists('ImageCreateFromGIF')) {
				$ImageCreateWasAttempted = true;
				$this->gdimg_source = @ImageCreateFromGIF($this->sourceFilename);
			} elseif ((@$this->getimagesizeinfo[2] == 2) && function_exists('ImageCreateFromJPEG')) {
				$ImageCreateWasAttempted = true;
				$this->gdimg_source = @ImageCreateFromJPEG($this->sourceFilename);
			} elseif ((@$this->getimagesizeinfo[2] == 3) && function_exists('ImageCreateFromPNG')) {
				$ImageCreateWasAttempted = true;
				$this->gdimg_source = @ImageCreateFromPNG($this->sourceFilename);
			} elseif ((@$this->getimagesizeinfo[2] == 15) && function_exists('ImageCreateFromWBMP')) {
				$ImageCreateWasAttempted = true;
				$this->gdimg_source = @ImageCreateFromWBMP($this->sourceFilename);
			}

			if (empty($this->gdimg_source)) {
				if ($ImageCreateWasAttempted) {
					$this->DebugMessage('ImageCreateFrom***() was attempted (format = '.$this->getimagesizeinfo[2].' but FAILED', __FILE__, __LINE__);
				}
				// cannot create from filename, attempt to create source image with ImageCreateFromString, if possible
				if (empty($this->rawImageData)) {
					if ($fp = @fopen($this->sourceFilename, 'rb')) {

						$this->rawImageData = '';
						$filesize = filesize($this->sourceFilename);
						$blocksize = 16384;
						$blockreads = ceil($filesize / $blocksize);
						for ($i = 0; $i < $blockreads; $i++) {
						//while (feof($fp) !== false) {
							$this->rawImageData .= fread($fp, $blocksize);
						}
						fclose($fp);
						$this->gdimg_source = $this->ImageCreateFromStringReplacement($this->rawImageData, true);

					} else {
						return $this->ErrorImage('cannot fopen("'.$this->sourceFilename.'") on line '.__LINE__.' of '.__FILE__);
					}
				}
			}

			if (empty($this->gdimg_source)) {
				if ($this->ImageMagickThumbnailToGD()) {

					// excellent, we have a thumbnailed source image
					$this->DebugMessage('ImageMagickThumbnailToGD() succeeded', __FILE__, __LINE__);

				} else {

					// cannot create image for whatever reason (maybe ImageCreateFromJPEG et al are not available?)
					// and ImageMagick is not available either, no choice but to output original (not resized/modified) data and exit
					$imageHeader = '';
					switch (substr($this->rawImageData, 0, 3)) {
						case 'GIF':
							$imageHeader = 'Content-type: image/gif';
							break;
						case "\xFF\xD8\xFF":
							$imageHeader = 'Content-type: image/jpeg';
							break;
						case "\x89".'PN':
							$imageHeader = 'Content-type: image/png';
							break;
					}
					if (!empty($imageHeader)) {
						header($imageHeader);
						echo $this->rawImageData;
						exit;
					}

					switch (substr($this->rawImageData, 0, 2)) {
						case 'BM':
							if (@include_once('phpthumb.bmp.php')) {
								$phpthumb_bmp = new phpthumb_bmp;
								if ($this->gdimg_source = $phpthumb_bmp->phpthumb_bmp2gd($this->rawImageData, ($this->gd_version() >= 2.0))) {
									$this->DebugMessage('$phpthumb_bmp->phpthumb_bmp2gd() succeeded', __FILE__, __LINE__);
									break;
								} else {
									return $this->ErrorImage('phpthumb_bmp2db failed');
								}
							} else {
								return $this->ErrorImage('include_once(phpthumb.bmp.php) failed');
							}
							return $this->ErrorImage('ImageMagick is unavailable and phpThumb() does not support BMP source images without it');
							break;
					}


					switch (substr($this->rawImageData, 0, 4)) {
						case 'II'."\x2A\x00":
						case 'MM'."\x00\x2A":
							return $this->ErrorImage('ImageMagick is unavailable and phpThumb() does not support TIFF source images without it');
							break;
					}

					if (empty($this->gdimg_source)) {
						return $this->ErrorImage('Unknown image type identified by "'.substr($this->rawImageData, 0, 4).'" ('.phpthumb_functions::HexCharDisplay(substr($this->rawImageData, 0, 4)).') in SourceImageToGD()');
					}

				}
			}

		}
		$this->source_width  = ImageSX($this->gdimg_source);
		$this->source_height = ImageSY($this->gdimg_source);
		return true;
	}




	function phpThumbDebugVarDump($var) {
		if (is_null($var)) {
			return 'null';
		} elseif (is_bool($var)) {
			return ($var ? 'TRUE' : 'FALSE');
		} elseif (is_string($var)) {
			return 'string('.strlen($var).')'.str_repeat(' ', max(0, 3 - strlen(strlen($var)))).' "'.$var.'"';
		} elseif (is_int($var)) {
			return 'integer     '.$var;
		} elseif (is_float($var)) {
			return 'float       '.$var;
		} elseif (is_array($var)) {
			ob_start();
			var_dump($var);
			$vardumpoutput = ob_get_contents();
			ob_end_clean();
			return strtr($vardumpoutput, "\n\r\t", '   ');
		}
		return gettype($var);
	}

	function phpThumbDebug() {
		if ($this->config_disable_debug) {
			return $this->ErrorImage('phpThumbDebug disabled');
		}

		$FunctionsExistance = array('exif_thumbnail', 'gd_info', 'image_type_to_mime_type', 'ImageCopyResampled', 'ImageCopyResized', 'ImageCreate', 'ImageCreateFromString', 'ImageCreateTrueColor', 'ImageIsTrueColor', 'ImageRotate', 'ImageTypes', 'version_compare', 'ImageCreateFromGIF', 'ImageCreateFromJPEG', 'ImageCreateFromPNG', 'ImageCreateFromWBMP', 'ImageCreateFromXBM', 'ImageCreateFromXPM', 'ImageCreateFromString', 'ImageCreateFromGD', 'ImageCreateFromGD2', 'ImageCreateFromGD2Part', 'ImageJPEG', 'ImageGIF', 'ImagePNG', 'ImageWBMP');
		$ParameterNames     = array('src', 'w', 'h', 'f', 'q', 'sx', 'sy', 'sw', 'sh', 'bw', 'bg', 'bgt', 'bc', 'usr', 'usa', 'ust', 'wmf', 'wmp', 'wmm', 'wma', 'file', 'goto', 'err', 'xto', 'ra', 'ar', 'aoe', 'iar', 'brx', 'bry', 'maxb');
		$OtherVariableNames = array('phpThumbDebug', 'thumbnailQuality', 'thumbnailFormat', 'gdimg_output', 'gdimg_source', 'sourceFilename', 'source_width', 'source_height', 'thumbnailCropX', 'thumbnailCropY', 'thumbnailCropW', 'thumbnailCropH', 'exif_thumbnail_width', 'exif_thumbnail_height', 'exif_thumbnail_type', 'thumbnail_width', 'thumbnail_height', 'thumbnail_image_width', 'thumbnail_image_height');

		$DebugOutput = array();
		$DebugOutput[] = 'phpThumb() version         = '.$this->phpthumb_version;
		$DebugOutput[] = 'phpversion()               = '.@phpversion();
		$DebugOutput[] = 'PHP_OS                     = '.PHP_OS;
		$DebugOutput[] = '$_SERVER[PHP_SELF]         = '.@$_SERVER['PHP_SELF'];
		$DebugOutput[] = '$_SERVER[DOCUMENT_ROOT]    = '.@$_SERVER['DOCUMENT_ROOT'];
		$DebugOutput[] = 'realpath(.)                = '.@realpath('.');
		$DebugOutput[] = 'get_magic_quotes_gpc()     = '.@get_magic_quotes_gpc();
		$DebugOutput[] = 'get_magic_quotes_runtime() = '.@get_magic_quotes_runtime();
		$DebugOutput[] = 'ini_get(error_reporting)   = '.@ini_get('error_reporting');
		$DebugOutput[] = 'ini_get(allow_url_fopen)   = '.@ini_get('allow_url_fopen');
		$DebugOutput[] = 'ini_get(disable_functions) = '.@ini_get('disable_functions');
		$DebugOutput[] = 'ini_get(safe_mode)         = '.@ini_get('safe_mode');
		$DebugOutput[] = 'ini_get(open_basedir)      = '.@ini_get('open_basedir');
		$DebugOutput[] = 'ini_get(memory_limit)      = '.@ini_get('memory_limit');
		$DebugOutput[] = 'get_cfg_var(memory_limit)  = '.@get_cfg_var('memory_limit');
		$DebugOutput[] = 'memory_get_usage()         = '.(function_exists('memory_get_usage') ? @memory_get_usage() : 'n/a');
		$DebugOutput[] = '';

		$DebugOutput[] = '$this->config_imagemagick_path              = '.$this->config_imagemagick_path;
		$DebugOutput[] = 'SafeBackTick(which convert)                 = '.trim(phpthumb_functions::SafeBackTick('which convert'));
		$IMpathUsed = ($this->config_imagemagick_path ? $this->config_imagemagick_path : trim(phpthumb_functions::SafeBackTick('which convert')));
		$DebugOutput[] = '[actual ImageMagick path used]              = '.$this->phpThumbDebugVarDump($IMpathUsed);
		$DebugOutput[] = 'file_exists([actual ImageMagick path used]) = '.$this->phpThumbDebugVarDump(file_exists($IMpathUsed));
		$DebugOutput[] = 'ImageMagickVersion()                        = '.$this->ImageMagickVersion();
		$DebugOutput[] = '';

		$DebugOutput[] = '$this->config_cache_directory               = '.$this->phpThumbDebugVarDump($this->config_cache_directory);
		$DebugOutput[] = '$this->cache_filename                       = '.$this->phpThumbDebugVarDump($this->cache_filename);
		$DebugOutput[] = 'is_writable($this->config_cache_directory)  = '.$this->phpThumbDebugVarDump(is_writable($this->config_cache_directory));
		$DebugOutput[] = 'is_writable($this->cache_filename)          = '.(file_exists($this->cache_filename) ? $this->phpThumbDebugVarDump(is_writable($this->cache_filename)) : 'n/a');
		$DebugOutput[] = '';

		$DebugOutput[] = '$this->config_document_root                 = '.$this->phpThumbDebugVarDump($this->config_document_root);
		$DebugOutput[] = '$this->config_output_format                 = '.$this->phpThumbDebugVarDump($this->config_output_format);
		$DebugOutput[] = '$this->config_output_maxwidth               = '.$this->phpThumbDebugVarDump($this->config_output_maxwidth);
		$DebugOutput[] = '$this->config_output_maxheight              = '.$this->phpThumbDebugVarDump($this->config_output_maxheight);
		$DebugOutput[] = '$this->config_error_message_image_default   = '.$this->phpThumbDebugVarDump($this->config_error_message_image_default);
		$DebugOutput[] = '$this->config_error_bgcolor                 = '.$this->phpThumbDebugVarDump($this->config_error_bgcolor);
		$DebugOutput[] = '$this->config_error_textcolor               = '.$this->phpThumbDebugVarDump($this->config_error_textcolor);
		$DebugOutput[] = '$this->config_error_fontsize                = '.$this->phpThumbDebugVarDump($this->config_error_fontsize);
		$DebugOutput[] = '$this->config_nohotlink_enabled             = '.$this->phpThumbDebugVarDump($this->config_nohotlink_enabled);
		$DebugOutput[] = '$this->config_nohotlink_valid_domains       = '.$this->phpThumbDebugVarDump($this->config_nohotlink_valid_domains);
		$DebugOutput[] = '$this->config_nohotlink_erase_image         = '.$this->phpThumbDebugVarDump($this->config_nohotlink_erase_image);
		$DebugOutput[] = '$this->config_nohotlink_fill_hexcolor       = '.$this->phpThumbDebugVarDump($this->config_nohotlink_fill_hexcolor);
		$DebugOutput[] = '$this->config_nohotlink_text_hexcolor       = '.$this->phpThumbDebugVarDump($this->config_nohotlink_text_hexcolor);
		$DebugOutput[] = '$this->config_nohotlink_text_message        = '.$this->phpThumbDebugVarDump($this->config_nohotlink_text_message);
		$DebugOutput[] = '$this->config_nohotlink_text_fontsize       = '.$this->phpThumbDebugVarDump($this->config_nohotlink_text_fontsize);
		$DebugOutput[] = '$this->config_border_hexcolor               = '.$this->phpThumbDebugVarDump($this->config_border_hexcolor);
		$DebugOutput[] = '$this->config_background_hexcolor           = '.$this->phpThumbDebugVarDump($this->config_background_hexcolor);
		$DebugOutput[] = '$this->config_max_source_pixels             = '.$this->phpThumbDebugVarDump($this->config_max_source_pixels);
		$DebugOutput[] = '$this->config_use_exif_thumbnail_for_speed  = '.$this->phpThumbDebugVarDump($this->config_use_exif_thumbnail_for_speed);
		$DebugOutput[] = '$this->config_output_allow_enlarging        = '.$this->phpThumbDebugVarDump($this->config_output_allow_enlarging);
		$DebugOutput[] = '';

		foreach ($OtherVariableNames as $varname) {
			$value = $this->$varname;
			$DebugOutput[] = '$this->'.str_pad($varname, 27, ' ', STR_PAD_RIGHT).' = '.$this->phpThumbDebugVarDump($value);
		}
		$DebugOutput[] = 'strlen($this->rawImageData)        = '.strlen(@$this->rawImageData);
		$DebugOutput[] = 'strlen($this->exif_thumbnail_data) = '.strlen(@$this->exif_thumbnail_data);
		$DebugOutput[] = '';

		foreach ($ParameterNames as $varname) {
			$value = $this->$varname;
			$DebugOutput[] = '$this->'.str_pad($varname, 4, ' ', STR_PAD_RIGHT).' = '.$this->phpThumbDebugVarDump($value);
		}
		$DebugOutput[] = '';

		foreach ($FunctionsExistance as $functionname) {
			$DebugOutput[] = 'builtin_function_exists('.$functionname.')'.str_repeat(' ', 23 - strlen($functionname)).' = '.$this->phpThumbDebugVarDump(phpthumb_functions::builtin_function_exists($functionname));
		}
		$DebugOutput[] = '';

		$gd_info = $this->gd_info();
		foreach ($gd_info as $key => $value) {
			$DebugOutput[] = 'gd_info.'.str_pad($key, 34, ' ', STR_PAD_RIGHT).' = '.$this->phpThumbDebugVarDump($value);
		}
		$DebugOutput[] = '';

		$exif_info = phpthumb_functions::exif_info();
		foreach ($exif_info as $key => $value) {
			$DebugOutput[] = 'exif_info.'.str_pad($key, 26, ' ', STR_PAD_RIGHT).' = '.$this->phpThumbDebugVarDump($value);
		}
		$DebugOutput[] = '';

		if ($ApacheLookupURIarray = phpthumb_functions::ApacheLookupURIarray(dirname(@$_SERVER['PHP_SELF']))) {
			foreach ($ApacheLookupURIarray as $key => $value) {
				$DebugOutput[] = 'ApacheLookupURIarray.'.str_pad($key, 15, ' ', STR_PAD_RIGHT).' = '.$this->phpThumbDebugVarDump($value);
			}
		} else {
				$DebugOutput[] = 'ApacheLookupURIarray() -- FAILED';
		}
		$DebugOutput[] = '';

		if (isset($_GET) && is_array($_GET)) {
			foreach ($_GET as $key => $value) {
				$DebugOutput[] = '$_GET['.$key.']'.str_repeat(' ', 30 - strlen($key)).'= '.$this->phpThumbDebugVarDump($value);
			}
		}
		if (isset($_POST) && is_array($_POST)) {
			foreach ($_POST as $key => $value) {
				$DebugOutput[] = '$_POST['.$key.']'.str_repeat(' ', 29 - strlen($key)).'= '.$this->phpThumbDebugVarDump($value);
			}
		}
		$DebugOutput[] = '';

		$DebugOutput[] = '$this->debugmessages:';
		foreach ($this->debugmessages as $errorstring) {
			$DebugOutput[] = '  * '.$errorstring;
		}

		return $this->ErrorImage(implode("\n", $DebugOutput), 700, 500);
	}

	function ErrorImage($text, $width=400, $height=100) {
		if ($this->RemoveFileOnCompletion) {
			@unlink($this->sourceFilename);
		}

		$this->DebugMessage($text);
		if (!$this->config_error_die_on_error) {
			return false;
		}
		if (!empty($this->err) || !empty($this->config_error_message_image_default)) {
			// Show generic custom error image instead of error message
			// for use on production sites where you don't want debug messages
			if (@$this->err == 'showerror') {
				// fall through and actually show error message even if default error image is set
			} else {
				header('Location: '.(!empty($this->err) ? $this->err : $this->config_error_message_image_default));
				exit;
			}
		}
		if (@$this->f == 'text') {
			// bypass all GD functions and output text error message
			die('<PRE>'.$text.'</PRE>');
		}

		$FontWidth  = ImageFontWidth($this->config_error_fontsize);
		$FontHeight = ImageFontHeight($this->config_error_fontsize);

		$LinesOfText = explode("\n", wordwrap($text, floor($width / $FontWidth), "\n", true));
		$height = max($height, count($LinesOfText) * $FontHeight);

		if (headers_sent()) {

			echo "\n".'**Headers already sent, dumping error message as text:**<br>'."\n\n".$text;

		} elseif ($gdimg_error = @ImageCreate($width, $height)) {

			$background_color = phpthumb_functions::ImageHexColorAllocate($gdimg_error, $this->config_error_bgcolor,   true);
			$text_color       = phpthumb_functions::ImageHexColorAllocate($gdimg_error, $this->config_error_textcolor, true);
			ImageFilledRectangle($gdimg_error, 0, 0, $width, $height, $background_color);
			$lineYoffset = 0;
			foreach ($LinesOfText as $line) {
				ImageString($gdimg_error, $this->config_error_fontsize, 2, $lineYoffset, $line, $text_color);
				$lineYoffset += $FontHeight;
			}
			if (function_exists('ImageTypes')) {
				$imagetypes = ImageTypes();
				if ($imagetypes & IMG_PNG) {
					header('Content-type: image/png');
					ImagePNG($gdimg_error);
				} elseif ($imagetypes & IMG_GIF) {
					header('Content-type: image/gif');
					ImageGIF($gdimg_error);
				} elseif ($imagetypes & IMG_JPG) {
					header('Content-type: image/jpeg');
					ImageJPEG($gdimg_error);
				} elseif ($imagetypes & IMG_WBMP) {
					header('Content-type: image/wbmp');
					ImageWBMP($gdimg_error);
				}
			}
			ImageDestroy($gdimg_error);

		}
		if (!headers_sent()) {
			echo "\n".'**Failed to send graphical error image, dumping error message as text:**<br>'."\n\n".$text;
		}
		exit;
		return true;
	}

	function ImageCreateFromStringReplacement(&$RawImageData, $DieOnErrors=false) {
		// there are serious bugs in the non-bundled versions of GD which may cause
		// PHP to segfault when calling ImageCreateFromString() - avoid if at all possible
		// when not using a bundled version of GD2
		$gd_info = $this->gd_info();
		if (strpos($gd_info['GD Version'], 'bundled') !== false) {
			return @ImageCreateFromString($RawImageData);
		}

		switch (substr($RawImageData, 0, 3)) {
			case 'GIF':
				$ICFSreplacementFunctionName = 'ImageCreateFromGIF';
				break;
			case "\xFF\xD8\xFF":
				$ICFSreplacementFunctionName = 'ImageCreateFromJPEG';
				break;
			case "\x89".'PN':
				$ICFSreplacementFunctionName = 'ImageCreateFromPNG';
				break;
			default:
				//if ($DieOnErrors) {
				//	return $this->ErrorImage('Unknown image type identified by "'.substr($RawImageData, 0, 3).'" ('.phpthumb_functions::HexCharDisplay(substr($this->rawImageData, 0, 3)).') in ImageCreateFromStringReplacement()');
				//}
				return false;
				break;
		}
		if ($tempnam = $this->phpThumb_tempnam()) {
			if ($fp_tempnam = @fopen($tempnam, 'wb')) {
				fwrite($fp_tempnam, $RawImageData);
				fclose($fp_tempnam);
				if (($ICFSreplacementFunctionName == 'ImageCreateFromGIF') && !function_exists($ICFSreplacementFunctionName)) {

					// Need to create from GIF file, but ImageCreateFromGIF does not exist
					if (@include_once('phpthumb.gif.php')) {
						// gif_loadFileToGDimageResource() cannot read from raw data, write to file first
						if ($tempfilename = $this->phpThumb_tempnam()) {
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

				} elseif (function_exists($ICFSreplacementFunctionName) && ($gdimg_source = $ICFSreplacementFunctionName($tempnam))) {

					// great
					unlink($tempnam);
					return $gdimg_source;

				} else { // GD functions not available

					// base64-encoded error image in GIF format
					$ERROR_NOGD = 'R0lGODlhIAAgALMAAAAAABQUFCQkJDY2NkZGRldXV2ZmZnJycoaGhpSUlKWlpbe3t8XFxdXV1eTk5P7+/iwAAAAAIAAgAAAE/vDJSau9WILtTAACUinDNijZtAHfCojS4W5H+qxD8xibIDE9h0OwWaRWDIljJSkUJYsN4bihMB8th3IToAKs1VtYM75cyV8sZ8vygtOE5yMKmGbO4jRdICQCjHdlZzwzNW4qZSQmKDaNjhUMBX4BBAlmMywFSRWEmAI6b5gAlhNxokGhooAIK5o/pi9vEw4Lfj4OLTAUpj6IabMtCwlSFw0DCKBoFqwAB04AjI54PyZ+yY3TD0ss2YcVmN/gvpcu4TOyFivWqYJlbAHPpOntvxNAACcmGHjZzAZqzSzcq5fNjxFmAFw9iFRunD1epU6tsIPmFCAJnWYE0FURk7wJDA0MTKpEzoWAAskiAAA7';
					header('Content-type: image/gif');
					echo base64_decode($ERROR_NOGD);
					exit;

				}
			} else {
				$ErrorMessage = 'Failed to fopen('.$tempnam.', "wb") in '.__FILE__.' on line '.__LINE__."\n".'You may need to set $PHPTHUMB_CONFIG[temp_directory] in phpThumb.config.php';
			}
			unlink($tempnam);
		} else {
			$ErrorMessage = 'Failed to generate phpThumb_tempnam() in '.__FILE__.' on line '.__LINE__."\n".'You may need to set $PHPTHUMB_CONFIG[temp_directory] in phpThumb.config.php';
		}
		if ($DieOnErrors && !empty($ErrorMessage)) {
			return $this->ErrorImage($ErrorMessage);
		}
		return false;
	}

	function phpThumb_tempnam() {
		return tempnam($this->config_temp_directory, 'pThumb');
	}

	function gd_version($fullstring=false) {
		static $cache_gd_version = array();
		if (empty($cache_gd_version)) {
			$gd_info = $this->gd_info();
			if (substr($gd_info['GD Version'], 0, strlen('bundled (')) == 'bundled (') {
				$cache_gd_version[1] = $gd_info['GD Version'];                                         // e.g. "bundled (2.0.15 compatible)"
				$cache_gd_version[0] = (float) substr($gd_info['GD Version'], strlen('bundled ('), 3); // e.g. "2.0" (not "bundled (2.0.15 compatible)")
			} else {
				$cache_gd_version[1] = $gd_info['GD Version'];                       // e.g. "1.6.2 or higher"
				$cache_gd_version[0] = (float) substr($gd_info['GD Version'], 0, 3); // e.g. "1.6" (not "1.6.2 or higher")
			}
		}
		return $cache_gd_version[intval($fullstring)];
	}

	function gd_info() {
		if (function_exists('gd_info')) {
			// built into PHP v4.3.0+ (with bundled GD2 library)
			return gd_info();
		}

		static $gd_info = array();
		if (empty($gd_info)) {
			// based on code by johnschaefer at gmx dot de
			// from PHP help on gd_info()
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
					//if (strpos($line, $key) !== false) {
					if (strpos($line, $key) === 0) {
						$newvalue = trim(str_replace($key, '', $line));
						$gd_info[$key] = $newvalue;
					}
				}
			}
			if (empty($gd_info['GD Version'])) {
				// probable cause: "phpinfo() disabled for security reasons"
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
				// to determine capability of GIF creation, try to use ImageCreateFromGIF on a 1px GIF
				if (function_exists('ImageCreateFromGIF')) {
					if ($tempfilename = $this->phpThumb_tempnam()) {
						if ($fp_tempfile = @fopen($tempfilename, 'wb')) {
							fwrite($fp_tempfile, base64_decode('R0lGODlhAQABAIAAAH//AP///ywAAAAAAQABAAACAUQAOw==')); // very simple 1px GIF file base64-encoded as string
							fclose($fp_tempfile);

							// if we can convert the GIF file to a GD image then GIF create support must be enabled, otherwise it's not
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

	function DebugMessage($message, $file='', $line='') {
		$this->debugmessages[] = $message.($file ? ' in file "'.(basename($file) ? basename($file) : $file).'"' : '').($line ? ' on line '.$line : '');
		return true;
	}

}

?>