<?php

	// Yellow Duck Framework version 2.0
	// (c) copyright 2004 Pieter Claerhout, pieter@yellowduck.be

	if ( ! defined( 'YD_FW_NAME' ) ) {
		die( 'Yellow Duck Framework is not loaded.' );
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
			return basename( $this->getAbsolutePath() );
		}

		/**
		 *	Function to get the extension of the file.
		 *
		 *	@returns	String containing the extension of the file.
		 */
		function getExtension() {
			ereg( ".*\.([a-zA-z0-9]{0,5})$", $this->getAbsolutePath(), $regs );
			return( $regs[1] );
		}

		/**
		 *	Function to get the full path of the object.
		 *
		 *	@returns	String containing the full path of the object.
		 */
		function getPath() {
			return realpath( dirname( $this->_path ) );
		}

		/**
		 *	Function to get the full absolute path of the object.
		 *
		 *	@returns	String containing the full absolute path of the object.
		 */
		function getAbsolutePath() {
			return realpath( $this->_path );
		}

		/**
		 *	Function to get the last modification date of the object.
		 *
		 *	@returns	String containing the last modification date of the object.
		 */
		function getLastModified() {
			clearstatcache();
			return filemtime( $this->getAbsolutePath() );
		}

		/**
		 *	Function to get the size of the file.
		 *
		 *	@returns	Double containing the length of the file.
		 */
		function getSize() {
			clearstatcache();
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

			// Get the parameters
			$params = getimagesize( $this->getAbsolutePath() );

			// Return false if not an image
			if ( $params == false ) {
				return false;
			}

			// Check if it's a supported image
			return in_array( $params[2], array( 1, 2, 3 ) );

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

			// Get the parameters
			$params = getimagesize( $this->getAbsolutePath() );

			// Return the right image type
			switch( $params[2] ) {
				case 1:
					return 'gif';
				case 2:
					return 'jpeg';
				case 3:
					return 'png';
			}

			// Raise error about unsupported image type
			trigger_error(
				'The getImageType function does not support the file format of the file "'
				. $this->getAbsolutePath() . '".', YD_ERROR
			);

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
			require_once( 'phpThumb/phpthumb.class.php' );

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
			$cacheFName = $thumb->GenerateCachedFilename();
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
		 *	This function will get a file list using a pattern. You can compare this function with the dir command from 
		 *	DOS or the ls command from Unix. The pattern syntax is the same as well.
		 *
		 *	@remarks
		 *		This will not work recursively on the subdirectories.
		 *
		 *	@param $pattern	(optional) Pattern to which the files should match. If you want multiple items, you can also
		 *					pass them as an array.
		 *	@param $class	(optional) If you specify a not null value for this option, this function will return the 
		 *					items in the directory as the indicated class.
		 *
		 *	@returns	Array of YDFile objects for the files that match the pattern.
		 */
		function getContents( $pattern='', $class=null ) {

			// Start with an empty list
			$fileList = array();

			// Get a handle to the directory
			$dirHandle = opendir( $this->getPath() );

			// Loop over the directory contents
			while ( false !== ( $file = readdir( $dirHandle ) ) ) {
				if ( $file != '.' && $file != '..' ) {
					if ( is_array( $pattern ) ) {
						foreach ( $pattern as $patternitem ) {
							if ( ! empty( $patternitem ) ) {
								if ( YDFSDirectory::_match( $patternitem, $file ) ) {
									$fileList[ strtolower( $file ) ] = $file;
									//array_push( $fileList, $file );
								}
							} else {
								$fileList[ strtolower( $file ) ] = $file;
								//array_push( $fileList, $file );
							}
						}
					} else {
						if ( ! empty( $pattern ) ) {
							if ( YDFSDirectory::_match( $pattern, $file ) ) {
								$fileList[ strtolower( $file ) ] = $file;
								//array_push( $fileList, $file );
							}
						} else {
							$fileList[ strtolower( $file ) ] = $file;
							//array_push( $fileList, $file );
						}
					}
				}
			}

			// Sort the list of files
			ksort( $fileList );
			$fileList = array_values( $fileList );

			// Convert the list of a list of YDFile objects
			$fileList2 = array();
			foreach ( $fileList as $file ) {
				$file = $this->getPath() . '/' . $file;
				if ( ! is_null( $class ) ) {
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
				array_push( $fileList2, $fileObj );
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
			return realpath( $this->_path );
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

			}

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

	}

?>
