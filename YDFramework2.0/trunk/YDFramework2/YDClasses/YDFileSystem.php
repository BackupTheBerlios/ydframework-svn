<?php

	/*
	
		Yellow Duck Framework version 2.0
		Copyright (C) (c) copyright 2004 Pieter Claerhout
	
		This library is free software; you can redistribute it and/or
		modify it under the terms of the GNU Lesser General Public
		License as published by the Free Software Foundation; either
		version 2.1 of the License, or (at your option) any later version.
	
		This library is distributed in the hope that it will be useful,
		but WITHOUT ANY WARRANTY; without even the implied warranty of
		MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU
		Lesser General Public License for more details.
	
		You should have received a copy of the GNU Lesser General Public
		License along with this library; if not, write to the Free Software
		Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA 02111-1307 USA
	
	*/

	if ( ! defined( 'YD_FW_NAME' ) ) {
		die( 'Yellow Duck Framework is not loaded.' );
	}

	/**
	 *	This class houses all different path related functions.
	 */
	class YDPath extends YDBase {

		/**
		 *	Provides a platform-specific character used to separate directory levels in a path string that reflects a
		 *	hierarchical file system organization.
		 *
		 *	@returns	String containing the directory separator
		 *
		 *	@static
		 */
		function getDirectorySeparator() {
			return ( YD_PATHDELIM == ':' ) ? '/' : '\\';
		}

		/**
		 *	A platform-specific separator character used to separate path strings in environment variables.
		 *
		 *	@returns	String containing the path separator
		 *
		 *	@static
		 */
		function getPathSeparator() {
			return YD_PATHDELIM;
		}

		/**
		 *	Provides a platform-specific volume separator character.
		 *
		 *	@returns	String containing the volume separator
		 *
		 *	@static
		 */
		function getVolumeSeparator() {
			if ( strtoupper( substr( PHP_OS, 0, 3 ) ) == 'WIN' || strtoupper( PHP_OS ) == 'DARWIN' ) {
				return ':';
			} else {
				return '/';
			}
		}

		/**
		 *	Changes the extension of a path string.
		 *
		 *	@param $path	Path of the file or directory.
		 *	@param $ext	The new extension.
		 *
		 *	@returns	String with the changed extension
		 *
		 *	@static
		 */
		function changeExtension( $path, $ext ) {
			if ( ! empty( $ext ) && substr( $ext, 0, 1 ) != '.' ) {
				$ext = '.' . $ext;
			}
			return YDPath::getFilePathWithoutExtension( $path ) . $ext;
		}

		/**
		 *	@param $path	Path of the file or directory.
		 *
		 *	@returns	The directory information for the specified path string
		 *
		 *	@static
		 */
		function getDirectoryName( $path ) {
			return dirname( $path );
		}

		/**
		 *	@param $path	Path of the file or directory.
		 *
		 *	@returns	The extension of the specified path string
		 *
		 *	@static
		 */
		function getExtension( $path ) {
			ereg( ".*\.([a-zA-Z0-9]{0,5})$", $path, $regs );
			return( $regs[1] );
		}

		/**
		 *	@param $path	Path of the file or directory.
		 *
		 *	@returns	The file name and extension of the specified path string
		 *
		 *	@static
		 */
		function getFileName( $path ) {
			return basename( $path );
		}

		/**
		 *	@param $path	Path of the file or directory.
		 *
		 *	@returns	The file name of the specified path string without the extension
		 *
		 *	@static
		 */
		function getFileNameWithoutExtension( $path ) {
			return basename( $path, '.' . YDPath::getExtension( $path ) );
		}

		/**
		 *	@param $path	Path of the file or directory.
		 *
		 *	@returns	The file path and extension of the specified path string
		 *
		 *	@static
		 */
		function getFilePath( $path ) {
			return realpath( $path );
		}

		/**
		 *	@param $path	Path of the file or directory.
		 *
		 *	@returns	The file path of the specified path string without the extension
		 *
		 *	@static
		 */
		function getFilePathWithoutExtension( $path ) {
			return YDPath::getDirectoryName( $path ) . YDPath::getDirectorySeparator() . YDPath::getFileNameWithoutExtension( $path );
		}

		/**
		 *	@param $path	Path of the file or directory.
		 *
		 *	@returns	The absolute path for the specified path string
		 *
		 *	@static
		 */
		function getFullPath( $path ) {
			return realpath( $path );
		}

		/**
		 *	@returns	A uniquely named temporary file on disk and returns the full path to that file
		 *
		 *	@static
		 */
		function getTempFileName() {
			$tmpName = '';
			for ($i=0;$i<rand(0,50);$i++) {
				$tmpName .= "&#" . rand(33,255) . ";";
			}
			return YDPath::getTempPath() . YDPath::getDirectorySeparator() . md5( $tmpName ) . '.temp';
		}

		/**
		 *	@returns	The path of the current system's temporary folder.
		 *
		 *	@static
		 */
		function getTempPath() {
			return YD_DIR_TEMP;
		}

		/**
		 *	@param $path	Path of the file or directory.
		 *
		 *	@returns	Determines whether a path includes a file name extension
		 *
		 *	@static
		 */
		function hasExtension( $path ) {
			$ext = YDPath::getExtension( $path );
			return empty( $ext ) ? false : true;
		}

		/**
		 *	This function will check if the path is an absolute path or not.
		 *
		 *	@param	$path	The path to check
		 *
		 *	@returns	Boolean indicating if the path is absolute or not.
		 *
		 *	@static
		 */
		function isAbsolute( $path ) {
			if ( strtoupper( substr( PHP_OS, 0, 3 ) ) == 'WIN' ) {
				if ( strlen( $path ) > 3 && substr( $path, 1, 2 ) == ':\\' ) {
					return true;
				} else {
					return false;
				}
			} else {
				return ( substr( $path, 0, 1 ) == '/' );
			}
		}

		/**
		 *	This function combines different file path elements to each other.
		 *
		 *	@code
		 *	join( 'C:\temp', 'subdir', 'file.html' )
		 *	@endcode
		 *
		 *	results in the following path:
		 *
		 *	@code
		 *	C:\temp\subdir\file.html
		 *	@endcode
		 *
		 *	@returns	The joined path.
		 *
		 *	@static
		 */
		function join() {

			// Get the arguments for this function
			$args = func_get_args();

			// Start with an empty path
			$path = '';

			// Loop over the different elements
			foreach ( $args as $arg ) {

				// Check for an absolute path
				if ( YDPath::isAbsolute( $arg ) ) {
					$path = $arg;
				} else {

					// Normalize the path elements
					$path = str_replace( '/', YDPath::getDirectorySeparator(), $path );
					$path = str_replace( '\\', YDPath::getDirectorySeparator(), $path );

					// Remove the trailing directory separator
					if ( substr( $arg, -1, 1 ) == YDPath::getDirectorySeparator() ) {
						$arg = substr( $arg, 0, -1 );
					}

					// Add it to the path
					if ( strlen( $path ) > 0 ) {
						if ( substr( $arg, 0, 1 ) != YDPath::getDirectorySeparator() ) {
							$path .= YDPath::getDirectorySeparator() . $arg;
						} else {
							$path .= $arg;
						}
					} else {
						$path .= $arg;
					}

				}

			}

			// Return the joined path
			return $path;

		}

	}

	/**
	 *  This class defines a filesystem file.
	 */
	class YDFSFile extends YDBase {

		/**
		 *	The class constructor of the YDFSFile class takes the path to the file as it's only argument. It will then
		 *	provide you with a number of functions to get the properties of the file.
		 *
		 *	@param $path	Path of the file.
		 */
		function YDFSFile( $path ) {

			// Initialize YDBase
			$this->YDBase();

			// Fail if directory
			if ( ! is_file( $path ) ) {
				trigger_error( 'The file with path "' . $path . '" does not exist.', YD_ERROR );
			}

			// Save the path
			$this->_path = realpath( $path );

		}

		/**
		 *	Function to get the filename of the object. This does not include the path information.
		 *
		 *	@returns	String containing the name of the object.
		 */
		function getBasename() {
			return YDPath::getFileName( $this->getAbsolutePath() );
		}

		/**
		 *	Function to get the extension of the file.
		 *
		 *	@returns	String containing the extension of the file.
		 */
		function getExtension() {
			return YDPath::getExtension( $this->getAbsolutePath() );
		}

		/**
		 *	Function to get the full path of the object.
		 *
		 *	@returns	String containing the full path of the object.
		 */
		function getPath() {
			return YDPath::getDirectoryName( $this->getAbsolutePath() );
		}

		/**
		 *	Function to get the full absolute path of the object.
		 *
		 *	@returns	String containing the full absolute path of the object.
		 */
		function getAbsolutePath() {
			return YDPath::getFullPath( $this->_path );
		}

		/**
		 *	Function to get the last modification date of the object.
		 *
		 *	@returns	String containing the last modification date of the object.
		 */
		function getLastModified() {
			//clearstatcache();
			return filemtime( $this->getAbsolutePath() );
		}

		/**
		 *	Function to get the size of the file.
		 *
		 *	@returns	Double containing the length of the file.
		 */
		function getSize() {
			//clearstatcache();
			return filesize( $this->getAbsolutePath() );
		}

		/**
		 *	Function to get the contents of the file. Depending on the file contents, this will be returned as binary or
		 *	textual data.
		 *
		 *	@param $start	(optional) Byte to start reading from.
		 *	@param $length	(optional) Number of bytes to read.
		 *
		 *	@returns	String containing the contents of the file.
		 */
		function getContents( $start=null, $length=null ) {

			// Check the start byte
			if ( $start == null ) {
				$start = 0;
			}

			// No length given
			if ( $length == null ) {
				$length = filesize( $this->getAbsolutePath() ) - $start;
			}

			// Check that length is a positive integer
			if ( $length < 1 ) {
				trigger_error( 'getContents: Length should be a positive integer.', YD_ERROR );
			}

			// Variable to hold the return data
			$result = '';

			// Open the file in read binary mode
			$file = fopen( $this->getAbsolutePath(), 'rb' );

			// Check if we were able to open the file
			if ( $file == false ) {
				trigger_error( 'The file with path "' . $path . '" could not be read.', YD_ERROR );
			}

			// Find the start position
			fseek( $file, $start );

			// Get the contents of the file
			$result = fread( $file, $length );

			// Close the file handle
			fclose( $file );

			// Return the result
			return $result;

		}

		/**
		 *	Function to determine if the file is an image or not. This function will read the header of the file to
		 *	find out if it's an image or not.
		 *
		 *	@remarks
		 *		You need to have the GD library enabled in PHP in order to use this function.
		 *
		 *	@returns	Boolean indicating if the file is an image or not.
		 */
		function isImage() {

			// Check for the getimagesize function
			if ( ! function_exists( 'getimagesize' ) ) {
				trigger_error(
					'The "getimagesize" function does not exists. Make sure that the GD libraries are loaded before '
					. 'using the YDFSImage::getImageSize function.', YD_ERROR
				);
			}

			// Get the image type
			$type = $this->_getImageType();

			// Return false if not an image
			if ( $type == false ) {
				return false;
			} else {
				return true;
			}

		}

		/**
		 *	This function will return true if the directory is writeable, otherwise, it will return false.
		 *
		 *	@remarks
		 *		This only works correctly on Unix based systems.
		 *
		 *	@returns	Boolean indicating if the directory is writeable or not.
		 */
		function isWriteable() {
			return is_writable( $this->getAbsolutePath() );
		}
		
		/**
		 *	This function will force the browser to download the file.
		 *
		 *	@param $name	(optional) The name of the file download should show in the browser. By default, this is the
		 *					same as the filename.
		 */
		function download( $name=null ) {
		
			// Get the name of the file
			if ( is_null( $name ) ) {
				$name = $this->getBasename();
			}
			header( 'Content-Type: application/force-download; name="' . $name . '"');
			header( 'Content-Disposition: attachment; filename="' . $name . ' "');

			// Add the rest of the headers
			header( 'Cache-Control: public' );
			header( 'Content-Transfer-Encoding: binary' );
			header( 'Content-length: ' . $this->getSize() );

			// Send the file contents
			readfile( $this->getAbsolutePath() );
			
			// Stop the execution
			die();
		
		}

		/**
		 *	This function will return true if the filesystem object is a directory. In all other cases, it will return
		 *	false.
		 *
		 *	@returns	Boolean indicating if the object is a directory or not.
		 */
		function isDirectory() {
			return false;
		}

		/**
		 *	This function returns the file properties as an associative array.
		 *
		 *	@returns	Associative array with the file properties
		 */
		function toArray() {
			return array(
				'basename' => $this->getBaseName(),
				'extension' => $this->getExtension(),
				'path' => $this->getPath(),
				'absolutepath' => $this->getAbsolutePath(),
				'lastmodified' => $this->getLastModified(),
				'size' => $this->getSize(),
				'isimage' => $this->isImage(),
				'isdirectory' => $this->isDirectory(),
				'iswriteable' => $this->isWriteable(),
			);
		}

		/**
		 *	@internal
		 */
		function _getImageType() {
			$fp = fopen( $this->getAbsolutePath(), 'rb' );
			$header = fread( $fp, 32 );
			fclose( $fp );
			if ( substr( $header, 0, 6 ) == 'GIF87a' || substr( $header, 0, 6 ) == 'GIF89a' ) {
				return 'gif';
			}
			if ( substr( $header, 6, 4 ) == 'JFIF' ) {
				return 'jpeg';
			}
			if ( substr( $header, 0, 8 ) == "\211PNG\r\n\032\n" ) {
				return 'png';
			}
			return false;
		}

	}

	/**
	 *	This class defines an image file.
	 */
	class YDFSImage extends YDFSFile {

		/**
		 *	The class constructor of the YDImage class takes the path to the image as it's only argument. It will then 
		 *	provide you with a number of functions to get the properties of the image and also provides some actions 
		 *	like generating thumbnails.
		 *
		 *	@param $path	Path of the image.
		 */
		function YDImage( $path ) {
			$this->YDFSFile( $path );
		}

		/**
		 *	Function to output the thumbnail of an image. The function directly outputs the thumbnail to the client 
		 *	including the right headers needed to display the image.
		 *
		 *	This function can cache the thumbnails and regenerate them on the fly if needed. The cached thumbnails are
		 *	stored in the temp directory of the Yellow Duck framework and have the extension "tmn". You can delete these
		 *	automatically as they will be recreated on the fly if needed.
		 *
		 *	@param $width	The maximum width of the thumbnail.
		 *	@param $height	The maximum height of the thumbnail.
		 *	@param $cache	(optional) Indicate if the thumbnails should be cached. By default, caching is turned on.
		 */
		function outputThumbnail( $width, $height, $cache=true ) {

			// Create the thumbnail
			$thumb = & $this->_createThumbnail( $width, $height, $cache );

			// Output the thumbnail
			$thumb->OutputThumbnail();

		}

		/**
		 *	This function will create a thumbnail and save the thumbnail to disk.
		 *
		 *	@param $width	The maximum width of the thumbnail.
		 *	@param $height	The maximum height of the thumbnail.
		 *	@param $file	The filename to save the thumbnail to.
		 */
		function saveThumbnail( $width, $height, $file ) {

			// Create the thumbnail
			$thumb = & $this->_createThumbnail( $width, $height, false );

			// Save the thumbnail
			$thumb->RenderToFile( $file );

		}

		/**
		 *	This function will return the size of the image in pixels.
		 *
		 *	@returns	The imagesize in pixels. This is returned as an array of which the first element is the width,
		 *				the second element is the height in pixels.
		 */
		function getImageSize() {

			// Check for the getimagesize function
			if ( ! function_exists( 'getimagesize' ) ) {
				trigger_error(
					'The "getimagesize" function does not exists. Make sure that the GD libraries are loaded before '
					. 'using the YDFSImage::getImageSize function.', YD_ERROR
				);
			}

			// Get the parameters
			$params = getimagesize( $this->getAbsolutePath() );

			// Get the first two elements
			$imgSize = array_slice( $params, 0, 2 );

			// Return the image size
			return $imgSize;

		}

		/**
		 *	This function will return the width of the image in pixels.
		 *
		 *	@returns	The width in pixels.
		 */
		function getWidth() {
			$imgSize = $this->getImageSize();
			return $imgSize[0];
		}

		/**
		 *	This function will return the height of the image in pixels.
		 *
		 *	@returns	The height in pixels.
		 */
		function getHeight() {
			$imgSize = $this->getImageSize();
			return $imgSize[1];
		}

		/**
		 *	Function that returns the type of the image. Currently, it supports GIF, JPG and PNG.
		 *
		 *	@returns	The type of the image, which is either jpg, png or gif.
		 */
		function getImageType() {

			// Get the image type
			$type = $this->_getImageType();

			// Raise error about unsupported image type
			if ( $type === false ) {
				trigger_error(
					'The getImageType function does not support the file format of the file "'
					. $this->getAbsolutePath() . '".', YD_ERROR
				);
			} else {
				return $type;
			}

		 }

		/**
		 *	Function that returns the MIME type of the image. Currently, it supports GIF, JPG and PNG.
		 *
		 *	@returns	The type of the image, which is either jpg, png or gif.
		 */
		function getMimeType() {
			$type = $this->getImageType();
			return 'image/' . strtolower( $type );
		 }

		/**
		 *	This function returns the file properties as an associative array.
		 *
		 *	@returns	Associative array with the file properties
		 */
		function toArray() {
			return array(
				'basename' => $this->getBaseName(),
				'extension' => $this->getExtension(),
				'path' => $this->getPath(),
				'absolutepath' => $this->getAbsolutePath(),
				'lastmodified' => $this->getLastModified(),
				'size' => $this->getSize(),
				'isimage' => $this->isImage(),
				'isdirectory' => $this->isDirectory(),
				'iswriteable' => $this->isWriteable(),
				'imagesize' => $this->getImageSize(),
				'width' => $this->getWidth(),
				'height' => $this->getHeight(),
				'imagetype' => $this->getImageType(),
				'mimetype' => $this->getMimeType(),
			);
		}

		/**
		 *	This function is used to output an error image.
		 *
		 *	@param $name	(optional) Name of the error image. Default image that is shown is the generic 
		 *					"YD_ydfsimage_fatal_error".
		 *
		 *	@internal
		 */
		function _error( $name='YD_ydfsimage_fatal_error' ) {
			$img = new YDFSImage( YD_DIR_HOME . '/images/' . $name . '.gif' );
			header( 'Content-type: ' . $img->getMimeType() );
			echo( $img->getContents() );
			die();
		}

		/**
		 *	This function will do the actual work of creating a thumbnail image.
		 *
		 *	@param $width	The maximum width of the thumbnail.
		 *	@param $height	The maximum height of the thumbnail.
		 *	@param $cache	(optional) Indicate if the thumbnails should be cached. By default, caching is turned off.
		 *
		 *	@internal
		 */
		function & _createThumbnail( $width, $height, $cache=true ) {

			// Check if the GD library is loaded.
			if ( ! extension_loaded( 'gd' ) ) {
				  $this->_error( 'YD_gd_not_installed' );
			}

			// Include phpThumb
			YDInclude( 'phpThumb/phpthumb.class.php' );

			// Create a new thumbnail object
			$thumb = new phpThumb();
			$thumb->src = $this->getAbsolutePath();

			// Set the options for the creation of thumbnails
			$thumb->config_nohotlink_enabled = false;
			$thumb->config_cache_directory = YD_DIR_TEMP;

			// Set the width and the height
			$thumb->w = $width;
			$thumb->h = $height;

			// Create the cached thumbnail
			//$cacheFName = $thumb->GenerateCachedFilename();
			$cacheFName = $thumb->SetCacheFilename();
			$cacheFName .= $this->getLastModified();
			$cacheFName .= $this->getAbsolutePath();
			$cacheFName = YD_TMP_PRE . 'N_' . md5( $cacheFName ) . '.tmn';
			$cacheFName = YD_DIR_TEMP . '/' . $cacheFName;

			// Check if caching is enabled
			if ( $cache == true ) {

				// Output the cached version if any
				if ( is_file( $cacheFName ) ) {
					$img = new YDFSImage( $cacheFName );
					header( 'Content-type: ' . $img->getMimeType() );
					echo( $img->getContents() );
					die();
				}

			}

			// Width should be positive integer
			if ( $width < 1 ) {
				  $this->_error();
			}

			// Height should be positive integer
			if ( $width < 1 ) {
				  $this->_error();
			}

			// Generate the thumbnail
			$thumb->GenerateThumbnail();

			// Check if caching is enabled
			if ( $cache == true ) {
				$thumb->RenderToFile( $cacheFName );
			}

			// Return the thumbnail object
			return $thumb;

		}

	}

	/**
	 *  This class defines a filesystem directory.
	 */
	class YDFSDirectory extends YDBase {

		/**
		 *	This is the class constructor of the YDDirectory class.
		 *
		 *	@param $path	(optional) Path of the directory. Default is the current directory.
		 */
		function YDFSDirectory( $path='.' ) {

			// Initialize YDBase
			$this->YDBase();

			// Fail if directory
			if ( ! is_dir( $path ) ) {
				trigger_error( 'The directory with path "' . $path . '" does not exist.', YD_ERROR );
			}

			// Save the path
			$this->_path = realpath( $path );

		}
		
		/**
		 *	Function to get the basename of the directory. This does not include the path information.
		 *
		 *	@returns	String containing the name of the object.
		 */
		function getBasename() {
			return YDPath::getFileName( $this->getAbsolutePath() );
		}

		/**
		 *	Function to get the full absolute path of the object.
		 *
		 *	@returns	String containing the full absolute path of the object.
		 */
		function getAbsolutePath() {
			return $this->getPath();
		}

		/**
		 *	This function will get a file list using a pattern. You can compare this function with the dir command from 
		 *	DOS or the ls command from Unix. The pattern syntax is the same as well.
		 *
		 *	@remarks
		 *		This will not work recursively on the subdirectories.
		 *
		 *	@param $pattern	(optional) Pattern to which the files should match. If you want multiple items, you can also
		 *					pass them as an array. If the pattern is prefixed with an exclamation mark, the files that
		 *					match this pattern will not be included in the result.
		 *	@param $class	(optional) If you specify a not null value for this option, this function will return the 
		 *					items in the directory as the indicated class. If an empty string is given, it will return
		 *					the list of filenames instead of objects.
		 *	@param $classes	(optional) An array with the classes to include. Standard, it includes YDFSImage, YDFSFile
		 *					and YDFSDirectory classes. If you only need a single class, you can also specify it as a
		 *					string. 
		 *
		 *	@returns	Array of YDFile objects for the files that match the pattern.
		 */
		function getContents( $pattern='', $class=null, $classes=array( 'YDFSFile', 'YDFSImage', 'YDFSDirectory' ) ) {

			// Start with an empty list
			$fileList = array();

			// Get a handle to the directory
			$dirHandle = opendir( $this->getPath() );

			// Get the list of files
			while ( false !== ( $file = readdir( $dirHandle ) ) ) {
				if ( $file != '.' && $file != '..' ) {
					$fileList[ strtolower( $file ) ] = $file;
				}
			}

			// Get the list of patterns
			if ( ! is_array( $pattern ) ) {
				$pattern = array( $pattern );
			}

			// Apply the pattern to match
			$fileListMatch = array();
			foreach ( $fileList as $file ) {
				foreach ( $pattern as $patternitem ) {
					if ( ! empty( $patternitem ) && substr( $patternitem, 0, 1 ) != '!' ) {
						if ( YDFSDirectory::_match( $patternitem, $file ) ) {
							$fileListMatch[ $file ] = $file;
						}
					} else {
						$fileListMatch[ $file ] = $file;
					}
				}
			}
			$fileList = & $fileListMatch;

			// Apply the patterns to not match
			foreach ( $fileList as $file ) {
				foreach ( $pattern as $patternitem ) {
					if ( ! empty( $patternitem ) && substr( $patternitem, 0, 1 ) == '!' ) {
						if ( YDFSDirectory::_match( substr( $patternitem, 1 ), $file ) ) {
							unset( $fileList[ $file ] );
						}
					}
				}
			}

			// Get the values
			$fileList = array_values( $fileList );

			// Convert the list of a list of YDFile objects
			$fileList2 = array();
			foreach ( $fileList as $file ) {
				$file = $this->getPath() . '/' . $file;
				if ( ! is_null( $class ) && $class != '' ) {
					$fileObj = new $class( $file );
				} else {
					if ( is_dir( $file ) ) {
						$fileObj = new YDFSDirectory( $file );
					} else {
						$fileObj = new YDFSFile( $file );
						if ( $fileObj->isImage() ) {
							$fileObj = new YDFSImage( $file );
						}
					}
				}
				$fileList2[ strtolower( $file ) ] = $fileObj;
			}

			// Sort the list of files
			ksort( $fileList2 );
			$fileList2 = array_values( $fileList2 );

			// Remove the unsupported classes
			if ( ! is_array( $classes ) ) {
				$classes = array( $classes );
			}
			if ( sizeof( $classes ) == 0 ) {
				return array();
			}
			foreach ( $classes as $key => $val ) {
				$classes[ $key ] = strtolower( $val );
			}
			foreach ( $fileList2 as $key=>$val ) {
				if ( ! in_array( strtolower( get_class( $val ) ), $classes ) ) {
					unset( $fileList2[ $key ] );
				}
			}

			// Return a simple list if needed
			if ( $class === '' ) {
			
				// Initialize a list for the files only
				$fileOnlyList = array();
				
				// Add the files
				foreach ( $fileList2 as $file ) {
					array_push( $fileOnlyList, basename( $file->_path ) );
				}
			
				// Return the fileOnlyList array
				return $fileOnlyList;

			}

			// Return the file list
			return $fileList2;

		}

		/**
		 *	Function to get the full path of the directory.
		 *
		 *	@returns	String containing the full path of the directory.
		 */
		function getPath() {
			return YDPath::getFullPath( $this->_path );
			//return realpath( $this->_path );
		}

		/**
		 *	This function will return true if the directory is writeable, otherwise, it will return false.
		 *
		 *	@remarks
		 *		This only works correctly on Unix based systems.
		 *
		 *	@returns	Boolean indicating if the directory is writeable or not.
		 */
		function isWriteable() {
			return is_writable( $this->getPath() );
		}

		/**
		 *	This function will create a new file in the current directory, and will write the specified contents to the
		 *	file. Once finished, it will return a new YDFSFile object pointing to the file. All directory paths are 
		 *	relative to the current directory.
		 *
		 *	@param $filename	The filename of the new file.
		 *	@param $contents	The contents of the new file.
		 *
		 *	@returns	YDFSFile or YDFSImage object pointing to the new file.
		 */
		function createFile( $filename, $contents ) {

			// Set the directory of this object as the working directory
			chdir( $this->getPath() );

			// Create the new file
			$fp = fopen( $filename, 'wb' );

			// Save the contents to the file
			$result = fwrite( $fp, $contents );

			// Check for errors
			if ( $result == false ) {
				trigger_error(
					'Failed writing to the file "' . $file . '" in the directory called "' . $this->getPath() . '".',
					YD_ERROR
				);
			}

			// Close the file
			fclose( $fp );

			// Create the YDFSFile object
			$obj = new YDFSFile( $filename );

			// Check if it's an image
			if ( $obj->isImage() ) {
				$obj = new YDFSImage( $filename );
			}

			// Return the file object
			return $obj;

		}

		/**
		 *	This function will delete a file from the current directory.
		 *
		 *	@param $filename	The file you want to delete.
		 *	@param $failOnError	(optional) Indicate if a fatal error needs to be raised if deleting the file failed.
		 *
		 *	@return	There are three possible return values for this function. True indicates that the file exists and
		 *			is deleted successfully. False indicates the file exists but could not be deleted. Null indicates
		 *			the file didn't exist and therefor could not be deleted.
		 */
		function deleteFile( $filename, $failOnError=false ) {

			// Set the directory of this object as the working directory
			chdir( $this->getPath() );

			// Check if the file exists
			if ( file_exists( $filename ) ) {

				// Try to delete the file
				$result = unlink( $filename );

				// Check for errors
				if ( $result == false ) {

					// Check if we need to raise an error
					if ( $failOnError == true ) {
						trigger_error(
							'Failed deleting the file "' . $file . '" from the directory "' . $this->getPath() . '".',
							YD_ERROR
						);
					}

				}

				
				// Return if the file was deleted or not
				return $result;
				
			}
			
			// Return null if the file doesn't exist
			return null;

		}

		/**
		 *	This function will create a new subdirectory in the given directory.
		 *
		 *	@param $directory	Directory to create.
		 *	@param $mode		(optional) The mode for the directory. By default, this is 0777.
		 *
		 *	@returns	False on failure, otherwise, it will return a YDFSDirectory object for the new directory.
		 */
		function createDirectory( $directory, $mode=0700 ) {
			$directory = YDPath::join( $this->getAbsolutePath(), $directory );
			if ( is_dir( $directory ) || mkdir( $directory, $mode ) ) {
				return new YDFSDirectory( $directory );
			} else {
				return false;
			}
		}
		
		/**
		 *	This function will recursively delete a directory. It will delete the directory and the complete
		 *	contents of that directory! Be careful I would say!
		 *
		 *	@param $directory	Directory to be removed.
		 *
		 *	@return	Boolean indicating if the directory could be deleted or not.
		 */
		function deleteDirectory( $directory ) {
			$directory = YDPath::join( $this->getAbsolutePath(), $directory );
			if ( ! is_dir( $directory ) ) {
				return false;
			}
			return YDFSDirectory::_delete( $directory );
		}		 

		/**
		 *	This function will return true if the filesystem object is a directory. In all other cases, it will return
		 *	false.
		 *
		 *	@returns	Boolean indicating if the object is a directory or not.
		 */
		function isDirectory() {
			return true;
		}

		/**
		 *	This function will return true if the filesystem object is an image. In all other cases, it will return
		 *	false.
		 *
		 *	@returns	Boolean indicating if the object is an image or not.
		 */
		function isImage() {
			return false;
		}

		/**
		 *	This function returns the file properties as an associative array.
		 *
		 *	@returns	Associative array with the file properties
		 */
		function toArray() {
			return array(
				'basename' => $this->getBaseName(),
				'path' => $this->getPath(),
				'absolutepath' => $this->getAbsolutePath(),
				'isimage' => $this->isImage(),
				'isdirectory' => $this->isDirectory(),
				'iswriteable' => $this->isWriteable(),
			);
		}

		/**
		 *	Function to emulate the fnmatch function from UNIX which is not available on all servers.
		 *
		 *	@remark
		 *		This function is a reformatted version of the function found on:
		 *		http://www.php.net/manual/en/function.fnmatch.php#31353
		 *
		 *	@param $pattern	Pattern to match the file against.
		 *	@param $file	File name that needs to be checked.
		 *
		 *	@return	Boolean indicating if the file matched the pattern or not.
		 *
		 *	@internal
		 */
		function _match( $pattern, $file ) {

			// Loop over the characters of the pattern
			for ( $i=0; $i < strlen( $pattern ); $i++ ) {

				// Character is a *
				if ( $pattern[$i] == '*' ) {
					for ( $c = $i; $c < max( strlen( $pattern ), strlen( $file ) ); $c++ ) {
						if ( YDFSDirectory::_match( substr( $pattern, $i+1 ), substr( $file, $c ) ) ) {
							return true;
						}
					}
					return false;
				}

				// Pattern is a [
				if ( $pattern[$i] == '[' ) {
					$letter_set = array();
					for( $c=$i+1; $c < strlen( $pattern ); $c++ ) {
						if ( $pattern[$c] != ']' ) {
							array_push( $letter_set, $pattern[$c] );
						} else {
							break;
						}
					}
					foreach ( $letter_set as $letter ) {
						if ( YDFSDirectory::_match( $letter . substr( $pattern, $c+1 ), substr( $file, $i ) ) ) {
							return true;
						}
					}
					return false;
				}

				// Pattern is a ?
				if ( $pattern[$i] == '?' ) {
					continue;
				}

				// Pattern not the same as the file character
				if ( $pattern[$i] != $file[$i] ) {
					return false;
				}

			}

			// All the rest returns positive
			return true;

		}
		
		/**
		 *	Function to recursively delete a directory.
		 *
		 *	@param $dirname	Directory to be removed.
		 *
		 *	@return	Boolean indicating if the directory could be deleted or not.
		 *
		 *	@internal
		 */
		function _delete( $dirname ) {
		
		    // Simple delete for a file 
    		if ( is_file( $dirname ) ) { 
        		return unlink( $dirname ); 
    		} 

    		// Loop through the folder 
    		$dir = dir( $dirname ); 
			while ( false !== $entry = $dir->read() ) {
			
        		// Skip pointers 
        		if ( $entry == '.' || $entry == '..' ) { 
            		continue; 
        		} 

        		// Deep delete directories      
        		if ( is_dir( "$dirname/$entry" ) ) { 
        		    YDFSDirectory::_delete( "$dirname/$entry" ); 
        		} else { 
        		    unlink( "$dirname/$entry" ); 
        		} 
    		} 

    		// Clean up 
    		$dir->close(); 
    		return rmdir( $dirname ); 
		
		}

	}

?>
