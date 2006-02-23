<?php

    /*

        Yellow Duck Framework version 2.0
        (c) Copyright 2002-2005 Pieter Claerhout

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

    // Check if the framework is loaded
    if ( ! defined( 'YD_FW_NAME' ) ) {
        die( 'Yellow Duck Framework is not loaded.' );
    }

    // Includes
    include_once( YD_DIR_HOME_CLS . '/YDUtil.php');
    
    // YDFSImage cropping specific constants
    define( 'YD_FS_CROP_UNCHANGED', 1 );
    define( 'YD_FS_CROP_ENLARGED',  2 );
    define( 'YD_FS_CROP_BORDERED',  3 );
    
    // Config when cropping smaller images
    YDConfig::set( 'YD_FS_CROP', YD_FS_CROP_ENLARGED, false );
    
    // The mime types mapping
    $GLOBALS['YD_FS_MIME_MAPPING'] = array(
        'ez' => 'application/andrew-inset',
        'hqx' => 'application/mac-binhex40',
        'cpt' => 'application/mac-compactpro',
        'mathml' => 'application/mathml+xml',
        'doc' => 'application/msword',
        'oda' => 'application/oda',
        'ogg' => 'application/ogg',
        'pdf' => 'application/pdf',
        'rdf' => 'application/rdf+xml',
        'gram' => 'application/srgs',
        'grxml' => 'application/srgs+xml',
        'mif' => 'application/vnd.mif',
        'xul' => 'application/vnd.mozilla.xul+xml',
        'xls' => 'application/vnd.ms-excel',
        'ppt' => 'application/vnd.ms-powerpoint',
        'wbxml' => 'application/vnd.wap.wbxml',
        'wmlc' => 'application/vnd.wap.wmlc',
        'wmlsc' => 'application/vnd.wap.wmlscriptc',
        'vxml' => 'application/voicexml+xml',
        'bcpio' => 'application/x-bcpio',
        'vcd' => 'application/x-cdlink',
        'pgn' => 'application/x-chess-pgn',
        'cpio' => 'application/x-cpio',
        'csh' => 'application/x-csh',
        'dvi' => 'application/x-dvi',
        'spl' => 'application/x-futuresplash',
        'gtar' => 'application/x-gtar',
        'hdf' => 'application/x-hdf',
        'js' => 'application/x-javascript',
        'latex' => 'application/x-latex',
        'sh' => 'application/x-sh',
        'shar' => 'application/x-shar',
        'swf' => 'application/x-shockwave-flash',
        'sit' => 'application/x-stuffit',
        'sv4cpio' => 'application/x-sv4cpio',
        'sv4crc' => 'application/x-sv4crc',
        'tar' => 'application/x-tar',
        'tcl' => 'application/x-tcl',
        'tex' => 'application/x-tex',
        'man' => 'application/x-troff-man',
        'me' => 'application/x-troff-me',
        'ms' => 'application/x-troff-ms',
        'ustar' => 'application/x-ustar',
        'src' => 'application/x-wais-source',
        'xslt' => 'application/xslt+xml',
        'dtd' => 'application/xml-dtd',
        'zip' => 'application/zip',
        'm3u' => 'audio/x-mpegurl',
        'rpm' => 'audio/x-pn-realaudio-plugin',
        'ra' => 'audio/x-realaudio',
        'wav' => 'audio/x-wav',
        'pdb' => 'chemical/x-pdb',
        'xyz' => 'chemical/x-xyz',
        'bmp' => 'image/bmp',
        'cgm' => 'image/cgm',
        'gif' => 'image/gif',
        'ief' => 'image/ief',
        'png' => 'image/png',
        'jpg' => 'image/jpeg',
        'svg' => 'image/svg+xml',
        'wbmp' => 'image/vnd.wap.wbmp',
        'ras' => 'image/x-cmu-raster',
        'ico' => 'image/x-icon',
        'pnm' => 'image/x-portable-anymap',
        'pbm' => 'image/x-portable-bitmap',
        'pgm' => 'image/x-portable-graymap',
        'ppm' => 'image/x-portable-pixmap',
        'rgb' => 'image/x-rgb',
        'xbm' => 'image/x-xbitmap',
        'xpm' => 'image/x-xpixmap',
        'xwd' => 'image/x-xwindowdump',
        'css' => 'text/css',
        'rtx' => 'text/richtext',
        'rtf' => 'text/rtf',
        'tsv' => 'text/tab-separated-values',
        'wml' => 'text/vnd.wap.wml',
        'wmls' => 'text/vnd.wap.wmlscript',
        'etx' => 'text/x-setext',
        'avi' => 'video/x-msvideo',
        'movie' => 'video/x-sgi-movie',
        'ice' => 'x-conference/x-cooltalk',
        'php' => 'text/plain',
    );

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
            if ( strrchr( $path, '.' ) ) {
                return substr( strrchr( $path, '.' ), 1 );
            } else {
                return '';
            }
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

                if ( ! strlen( $arg ) ) {
                    continue;
                }
                
                // Normalize the path elements
                $arg = str_replace( '/', YDPath::getDirectorySeparator(), $arg );
                $arg = str_replace( '\\', YDPath::getDirectorySeparator(), $arg );
                
                // Check for an absolute path
                if ( YDPath::isAbsolute( $arg ) ) {
                    $path = $arg;
                } else {

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

        /**
         *	Function to determine if the file is an image or not. This function will read the header of the file to
         *	find out if it's an image or not.
         *
         *	@returns	Boolean indicating if the file is an image or not.
         */
        function isImage( $path ) {

            // Return false if path doesn't exist
            if ( ! realpath( $path ) ) {
                return false;
            }

            // Check if we have an extension
            if ( YDPath::getExtension( $path ) && strtolower( YDPath::getExtension( $path ) ) != 'tmn' ) {
                if ( in_array( strtolower( YDPath::getExtension( $path ) ), array( 'jpg', 'png', 'gif', 'jpeg' ) ) ) {
                    return strtolower( YDPath::getExtension( $path ) );
                }
            }

            // No extension, read the file
            $fp = fopen( $path, 'rb' );
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

            // Not an image
            return false;

        }

    }

    /**
     *  This class defines a filesystem file.
     */
    class YDFSFile extends YDBase {

        /**
         *  The class constructor of the YDFSFile class takes the path to the file as it's first argument. 
         *  It will then provide you with a number of functions to get the properties of the file.
         *
         *  @param $path    Path of the file.
         *  @param $create  (optional) Force the creation of the file if it doesn't exist. Default: false.
         */
        function YDFSFile( $path, $create=false ) {

            // Initialize YDBase
            $this->YDBase();

            // Check if the path if the file exists
            if ( ! is_file( $path ) ) {
                
                // Check if the file should be created
                if ( $create ) {

                    // Create a new YDFSDirectory object
                    $dir = new YDFSDirectory( dirname( $path ) );
                    
                    // Create the file
                    $dir->createFile( basename( $path ), ' ' );

                } else {
                    trigger_error( 'The file with path "' . $path . '" does not exist.', YD_ERROR );
                }
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
         *	Function to get the filename of the object without the extension. This does not include the path information.
         *
         *	@returns	String containing the name of the object.
         */
        function getBasenameNoExt() {
            return substr( YDPath::getFilePathWithoutExtension( $this->getBasename() ), 2 );
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
         *  @param  $format (optional) The date format to use to format the date.
         *  @param  $locale (optional) The locale to use for formatting the date.
         *
         *	@returns	String containing the last modification date of the object.
         */
        function getLastModified( $format = 'timestamp', $locale=null ) {
            if ( $format == 'timestamp' ) {
                return filemtime( $this->getAbsolutePath() );
            }
            return YDStringUtil::formatDate( filemtime( $this->getAbsolutePath() ), $format, $locale ); 
        }

        /**
         *	Function to get the size of the file.
         *
         *  @param  $formatted  (optional) If set to true, the filesize will be returned in a human readable format.
         *  @param  $decimals   (optional) The number of decimals to use for formatting the filesize.
         *
         *	@returns	Double containing the length of the file.
         */
        function getSize( $formatted = false, $decimals = 1 ) {
            if ( ! $formatted ) {
                return filesize( $this->getAbsolutePath() );
            }
            return YDStringUtil::formatFilesize( filesize( $this->getAbsolutePath() ), $decimals );
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

            // Clear the stat cache
            clearstatcache();

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
         *	This function will create a new file in the current directory, and will write the specified contents to the
         *	file. Once finished, it will return a new YDFSFile object pointing to the file. All directory paths are
         *	relative to the current directory.
         *
         *	@param $contents	The contents of the new file.
         *	@param $append		Boolean indicating if the content should be appended to the file or if the file contents
         *						should be replaced.
         */
        function setContents( $contents, $append=false ) {

            // Set the mode
            $mode = ( $append === true ) ? 'ab' : 'wb';

            // Open the file
            $fp = fopen( $this->getAbsolutePath(), $mode );

            // Save the contents to the file
            $result = fwrite( $fp, $contents );

            // Check for errors
            if ( $result == false ) {
                trigger_error(
                    'Failed writing to the file "' . $this->getAbsolutePath() . '" in the directory called "' . $this->getPath() . '".',
                    YD_ERROR
                );
            }

            // Close the file
            fclose( $fp );

            // Clear the stat cache
            clearstatcache();

        }

        /**
         *	Function to determine if the file is an image or not. This function will read the header of the file to
         *	find out if it's an image or not.
         *
         *	@returns	Boolean indicating if the file is an image or not.
         */
        function isImage() {
            return YDPath::isImage( $this->getAbsolutePath() );
        }

        /**
         *  This function returns true if the file is readable.
         *
         *  @returns    Returns true if the file is readable.
         */
        function isReadable(){
            return is_readable( $this->getAbsolutePath() );
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
         *  @param $inline  (optional) If set to true, the download is inline, otherwise, a download will be forced by
         *                  the browser. Default is false.
         */
        function download( $name=null, $inline=false ) {

            // Get the name of the file
            if ( is_null( $name ) ) {
                $name = $this->getBasename();
            }

            // Force download or do inline
            if ( ! $inline ) {
                header( 'Content-Type: application/force-download; name="' . $name . '"');
                header( 'Content-Disposition: attachment; filename="' . $name . ' "');
            } else {
                header( 'Content-Type: ' . $this->getMimeType() );
                header( 'Content-Disposition: inline; filename="' . $name . ' "');
            }

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
         *	This function will delete a file from the current directory.
         *
         *	@param $failOnError	(optional) Indicate if a fatal error needs to be raised if deleting the file failed.
         *
         *	@return	There are three possible return values for this function. True indicates that the file exists and
         *			is deleted successfully. False indicates the file exists but could not be deleted. Null indicates
         *			the file didn't exist and therefor could not be deleted.
         */
        function delete( $failOnError=false ) {

            // Set the filename
            $filename = $this->getAbsolutePath();

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
         *  This function will rename the file to the specified file path.
         *
         *  @param  $new_path   The new path of the file.
         *
         *  @returns    True on success, false on failure.
         */
        function rename( $new_path ) {
            @unlink( $new_path );
            $result = rename( $this->getAbsolutePath(), $new_path );
            if ( $result === true ) {
                $this->_path = realpath( $new_path );
            }
            return $result;
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
         *  This function returns the mime type for the file object.
         *
         *  @returns    The mime type for the document. If the mime type is not known, it will use the
         *              application/octet-stream mime type.
         */
        function getMimeType() {
            $extension = strtolower( $this->getExtension() );
            if ( isset( $GLOBALS['YD_FS_MIME_MAPPING'][$extension] ) ) {
                return $GLOBALS['YD_FS_MIME_MAPPING'][$extension];
            } else {
                return 'application/octet-stream';
            }
        }

        /**
         *	@internal
         */
        function _getImageType() {
            return YDPath::isImage( $this->getAbsolutePath() );
        }

        /**
         *	This function will move the file to the specified path and update the object accordingly.
         *
         *	@param	$path	Target path.
         *
         *	@returns	False on a failure, true on success.
         */
        function move( $path ) {
            $result = rename( $this->getAbsolutePath(), $path );
            if ( $result ) {
                $this->_path = realpath( $path );
            }
            return $result;
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
         *  @param $create  (optional) Force the creation of the file if it doesn't exist. Default: false.
         */
        function YDFSImage( $path, $create=false ) {

            // Initialize the parent
            $this->YDFSFile( $path, $create );

            // Get the image size
            $this->image_size = null;

        }

        /**
         *  Function determine the image size. This is just a helper function for the other ones.
         *
         *  @internal
         */
        function _initImageSize() {
        
            // Check for the getimagesize function
            if ( ! function_exists( 'getimagesize' ) ) {
                trigger_error(
                    'The "getimagesize" function does not exists. Make sure that the GD libraries are loaded before '
                    . 'using the YDFSImage::getImageSize function.', YD_ERROR
                );
            }
        
            if ( is_null( $this->image_size ) ) {
                $this->image_size = getimagesize( $this->getAbsolutePath() );
            }
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
         *	@param $cache	(optional) Indicate if the thumbnail should be cached. By default, caching is turned on.
         *	@param $crop	(optional) Indicate if the thumbnail should be cropped to the exact size. By default, false.
         */
        function outputThumbnail( $width, $height, $cache=true, $crop=false ) {

            // Output right headers
            $content_type = $this->getMimeType();
            header( 'Content-type: ' . $content_type );

            // Output the thumbnail
            echo( $this->_createThumbnail( $width, $height, $cache, $crop ) );

            // Stop the execution
            die();

        }

        /**
         *	This function will create a thumbnail and save the thumbnail to disk.
         *
         *	@param $width	The maximum width of the thumbnail.
         *	@param $height	The maximum height of the thumbnail.
         *	@param $file	The filename to save the thumbnail to.
         *	@param $crop	(optional) Indicate if the thumbnail should be cropped to the exact size. By default, false.
         *
         *  @returns    A new YDFSImage object for the thumbnail.
         */
        function saveThumbnail( $width, $height, $file, $crop=false ) {

            // Create the thumbnail
            $thumb = $this->_createThumbnail( $width, $height, false, $crop );

            // Save it to a file
            $f = new YDFSImage( $file, true );
            $f->setContents( $thumb );

            // Return the thumbnail
            return $f;

        }

        /**
         *	This function will return the size of the image in pixels.
         *
         *	@returns	The imagesize in pixels. This is returned as an array of which the first element is the width,
         *				the second element is the height in pixels.
         */
        function getImageSize() {
        
            // Init image_size
            $this->_initImageSize();
            
            // Get the first two elements
            $imgSize = array_slice( $this->image_size, 0, 2 );

            // Return the image size
            return $imgSize;

        }

        /**
         *	This function will return the width of the image in pixels.
         *
         *	@returns	The width in pixels.
         */
        function getWidth() {
            $this->_initImageSize();
            return $this->image_size[0];
        }

        /**
         *	This function will return the height of the image in pixels.
         *
         *	@returns	The height in pixels.
         */
        function getHeight() {
            $this->_initImageSize();
            return $this->image_size[1];
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
         *	@param $crop	(optional) Indicate if the thumbnails should be cropped to the exact size. By default, false.
         *
         *	@internal
         */
        function & _createThumbnail( $width, $height, $cache=true, $crop=false ) {

            // Check if the GD library is loaded.
            if ( ! extension_loaded( 'gd' ) ) {
                $this->_error( 'YD_gd_not_installed' );
            }

            // Width and height should be positive integer
            if ( $width < 1 || $height < 1 ) {
                $this->_error();
            }

            // Get the cache filename
            $cacheFName = YD_DIR_TEMP . '/' . $this->_createThumbnailName( $width, $height );

            // Check if caching is enabled
            if ( $cache === true ) {

                // Output the cached version if any
                if ( is_file( $cacheFName ) ) {

                    // Create a new image for the cache file
                    $img = new YDFSImage( $cacheFName );

                    // Set the content type
                    header( 'Content-type: ' . $img->getMimeType() );

                    // Output the image
                    $f = fopen( $cacheFName, 'rb' );
                    $data = fread( $f, filesize( $cacheFName ) );
                    fclose( $f );
                    die( $data );

                }

            }

            // Check the extension
            $img_type = $this->isImage();

            // Open the source image
            if ( $img_type == 'gif' ) {
                if ( ! function_exists( 'imagecreatefromgif' ) ) {
                    $this->_error();
                }
                $src_img = imagecreatefromgif( $this->getAbsolutePath() );
            } elseif ( $img_type == 'png' ) {
                $src_img = imagecreatefrompng( $this->getAbsolutePath() );
            } else {
                $src_img = imagecreatefromjpeg( $this->getAbsolutePath() );
            }

            // Get the current image size
            $ori_width  = imageSX( $src_img );
            $ori_height = imageSY( $src_img );
            
            // Calculate the new image size
            if ( $crop ) {
            
                if ( $ori_width > $ori_height ) {
                    $thumb_w = ceil( $ori_width * ( $height / $ori_height ) );
                    $thumb_h = $height;
                } 
                if ( $ori_width < $ori_height ) {
                    $thumb_w = $width;
                    $thumb_h = ceil( $ori_height * ( $width / $ori_width ) );
                }
            
            } else {
            
                if ( $ori_width > $ori_height ) {
                    $thumb_w = $width;
                    $thumb_h = ceil( $ori_height * ( $width / $ori_width ) );
                }
                if ( $ori_width < $ori_height ) {
                    $thumb_w = ceil( $ori_width * ( $height / $ori_height ) );
                    $thumb_h = $height;
                }
            
            }
            
            if ( $ori_width == $ori_height ) {
                $thumb_w = $width;
                $thumb_h = $height;
            }
            
            if ( ( $width >= $ori_width || $height >= $ori_height ) && ( ! $crop || ( $crop && YDConfig::get( 'YD_FS_CROP' ) != YD_FS_CROP_ENLARGED ) ) ) {
                
                if ( $width >= $ori_width && $height < $ori_height ) {
                    $thumb_w = ceil( $ori_width * ( $height / $ori_height ) );
                    $thumb_h = $height;
                } else if ( $width < $ori_width && $height >= $ori_height ) {
                    $thumb_w = $width;
                    $thumb_h = ceil( $ori_height * ( $width / $ori_width ) );
                } else {
                    $thumb_w = $ori_width;
                    $thumb_h = $ori_height;
                }
                
            }

            // Resample the image
            $dst_img = imagecreatetruecolor( $thumb_w, $thumb_h ); 
            if ( $img_type == 'png' ) {
                imagecopyresized( $dst_img, $src_img, 0, 0, 0, 0, $thumb_w, $thumb_h, $ori_width, $ori_height );
            } else {
                imagecopyresampled( $dst_img, $src_img, 0, 0, 0, 0, $thumb_w, $thumb_h, $ori_width, $ori_height );
            }
            
            if ( $crop && ( $width != $thumb_w || $height != $thumb_h ) ) {
            
                $x = ceil( abs( $thumb_w-$width  ) / 2 );
                $y = ceil( abs( $thumb_h-$height ) / 2 );
                
                $default = true;
                
                if ( $ori_width < $width || $ori_height < $height ) {
                
                    switch ( YDConfig::get( 'YD_FS_CROP', YD_FS_CROP_ENLARGED ) ) {
                    
                        case YD_FS_CROP_UNCHANGED:
                        
                            if ( $ori_width < $width && $ori_height < $height ) {
                                $crp_img = $dst_img;
                                $default = false;
                            } else if ( $ori_width < $width ) {
                                $x = 0;
                                $width = $ori_width;
                            } else if ( $ori_height < $height ) {
                                $y = 0;
                                $height = $ori_height;
                            }
                            break;
                        
                        case YD_FS_CROP_ENLARGED:
                        case YD_FS_CROP_BORDERED:
                            break;
                            
                    }
                    
                }
                
                if ( $default ) {
                
                    $crp_img = imagecreatetruecolor( $width, $height ); 
                    
                    if ( $img_type == 'png' ) {
                        imagecopyresized( $crp_img, $dst_img, 0, 0, $x, $y, $width, $height, $width, $height );
                    } else {
                        imagecopyresampled( $crp_img, $dst_img, 0, 0, $x, $y, $width, $height, $width, $height );
                    }
                    
                }
                
                $dst_img = $crp_img;
                
            }
            
            // Get the resulting image
            ob_start();
            if ( $img_type == 'gif' ) {
                if ( ! function_exists( 'imagegif' ) ) {
                    imagepng( $dst_img );
                } else {
                    imagegif( $dst_img );
                }
            } elseif ( $img_type == 'png' ) {
                imagepng( $dst_img );
            } else {
                imagejpeg( $dst_img );
            }
            $image_data = ob_get_contents();
            ob_end_clean();

            // Destroy the images
            imagedestroy( $dst_img ); 
            imagedestroy( $src_img ); 

            // Save the cache if needed
            if ( $cache == true ) {
                $f = new YDFSFile( $cacheFName, true );
                $f->setContents( $image_data );
            }

            // Return the image data
            return $image_data;

        }

        /**
         *  This function creates the cache name for thumbnails.
         *
         *	@param $width	The maximum width of the thumbnail.
         *	@param $height	The maximum height of the thumbnail.
         */
        function _createThumbnailName( $width, $height ) {
            $cacheFName = $this->getAbsolutePath() . '/' . $width . '/' . $height . '/' . $this->getLastModified();
            $cacheFName = YD_TMP_PRE . 'N_' . md5( $cacheFName ) . '.' . strtolower( $this->getExtension() );
            return $cacheFName;
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
         *  This function return the number of files that are in the directory.
         *
         *  @returns    The number of files in the directory.
         */
        function getFileCount(){ 
            $total = 0;
            $dirHandle = opendir( $this->getPath() );
            while ( false !== ( $file = readdir( $dirHandle ) ) ) {
                if ( $file != '.' && $file != '..' && is_file( $this->getPath() .'/'. $file ) ) {
                   $total++;
                }
            }
            closedir( $dirHandle );
            return $total;
        }

        /**
         *  This function return the number of directories that are in the directory.
         *
         *  @returns    The number of directories in the directory.
         */
        function getDirectoryCount(){ 
            $total = 0;
            $dirHandle = opendir( $this->getPath() );
            while ( false !== ( $file = readdir( $dirHandle ) ) ) {
            if ( $file != '.' && $file != '..' && is_dir( $this->getPath() .'/'. $file ) ) {
                    $total++;
                }
            }
            closedir( $dirHandle );
            return $total;
        }

        /**
         *  Returns the total size of the directory.
         *
         *  @param  $recursive  (optional) Recurse into the subdirectories. Default is false.
         *  @param  $formatted  (optional) If set to true, the filesize will be returned in a human readable format.
         *  @param  $decimals   (optional) The number of decimals to use for formatting the filesize.
         *
         *  @returns    The total size of the directory.
         */
        function getSize( $recursive = false, $formatted = false, $decimals = 1 ) { 
            $total = 0; 
            $dirHandle = opendir( $this->getPath() );
            while ( false !== ( $file = readdir( $dirHandle ) ) ) {
                if ( $file == '.' || $file == '..') {
                    continue;
                }
                if ( is_file( $this->getPath() .'/'. $file ) ) {
                    $total += filesize( $this->getPath() .'/'. $file );
                } else if ( $recursive && is_dir( $this->getPath() .'/'. $file ) ) {
                    $subdir = new YDFSDirectory( $this->getPath() .'/'. $file ); 
                    $total += $subdir->getSize(true, false, $decimals); 
                }
            }
            closedir( $dirHandle );
            if ( ! $formatted ) {
                return $total;
            }
            return YDStringUtil::formatFilesize( $total, $decimals );
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
         *  @param $sort_by_date (optional) Sorts the items by date. Default is false.
         *  @param $sort_order    (optional) Whether the sort direction is ascending or descending. Default is "ASC".
         *
         *	@returns	Array of YDFile objects for the files that match the pattern.
         */
        function getContents( $pattern='', $class=null, $classes=array( 'YDFSFile', 'YDFSImage', 'YDFSDirectory' ), $sort_by_date=false, $sort_order='asc' ) {

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
                        if ( YDPath::isImage( $file ) ) {
                            $fileObj = new YDFSImage( $file );
                        } else {
                            $fileObj = new YDFSFile( $file );
                        }
                    }
                }
                if ( $sort_by_date === true ) {
                    $fileList2[ filectime( $file ) . strtolower( $file ) ] = $fileObj;
                } else {
                    $fileList2[ strtolower( $file ) ] = $fileObj;
                }
            }

            // Sort the list of files
            if ( strtolower( $sort_order ) != 'desc' ) {
                ksort( $fileList2 );
            } else {
                krsort( $fileList2 );
            }
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
         *  This function will list all the files in this directory, but will also recurse into the subdirectories.
         *
         *	@param $pattern	(optional) Pattern to which the files should match. If you want multiple items, you can also
         *					pass them as an array. If the pattern is prefixed with an exclamation mark, the files that
         *					match this pattern will not be included in the result.
         *	@param $class	(optional) If you specify a not null value for this option, this function will return the
         *					items in the directory as the indicated class. If an empty string is given, it will return
         *					the list of filenames instead of objects.
         *	@param $classes	(optional) An array with the classes to include. Standard, it includes YDFSImage and
         *					YDFSFile classes. If you only need a single class, you can also specify it as a string.
         *  @param $sort_by_date (optional) Sorts the items by date. Default is false.
         *  @param $sort_order    (optional) Whether the sort direction is ascending or descending. Default is "ASC".
         *
         *	@returns	Array of YDFile objects for the files that match the pattern.
         */
        function getFilesRecursively( $pattern='', $class=null, $classes=array( 'YDFSFile', 'YDFSImage' ), $sort_by_date=false, $sort_order='asc'  ) {
            $files = array();
            foreach ( $this->_getSubdirectories( $this->_path ) as $dir ) {
                $dir = new YDFSDirectory( $dir );
                $files = array_merge( $files, $dir->getContents( $pattern, $class, $classes, $sort_by_date, $sort_order ) );
            }
            return $files;
        }

        /**
         *	Helper function to get the contents of a directory recursively.
         *
         *  @param  $path   The path to get the subdirectories from.
         *
         *  @returns    The list of subdirectories of the given path.
         *
         *  @internal
         */
        function _getSubdirectories( $path ) {
            $dirlist = array( $path );
            $dirHandle = opendir( $path );
            while ( false !== ( $file = readdir( $dirHandle ) ) ) {
                if ( $file != '.' && $file != '..' ) {
                    if ( is_dir( $path . '/' . $file ) ) {
                        array_push( $dirlist, $path . '/' . $file );
                        $dirlist = array_merge( $dirlist, $this->_getSubdirectories( $path . '/' . $file ) );
                    }
                }
            }
            sort( $dirlist );
            return array_unique( $dirlist );
        }

        /**
         *	Function to get the full path of the directory.
         *
         *	@returns	String containing the full path of the directory.
         */
        function getPath() {
            return YDPath::getFullPath( $this->_path );
        }

        /**
         *  This function returns true if the directory is readable.
         *
         *  @returns    Returns true if the directory is readable.
         */
        function isReadable(){
            return is_readable( $this->getPath() );
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
                    'Failed writing to the file "' . $filename . '" in the directory called "' . $this->getPath() . '".',
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
         *	@param $mode		(optional) The mode for the directory. By default, this is 0700.
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
         *	This function will move the directory to the specified path and update the object accordingly.
         *
         *	@param	$path	Target path.
         *
         *	@returns	False on a failure, true on success.
         */
        function moveDirectory( $path ) {
            $result = rename( $this->getAbsolutePath(), $path );
            if ( $result ) {
                $this->_path = realpath( $path );
            }
            return $result;
        }

        /**
         *  This function will check if the specified file/or directory exists in the current path.
         *
         *  @param $obj     The file or directory you are looking for.
         *	@param $classes	(optional) An array with the classes to include. Standard, it includes YDFSImage, YDFSFile
         *					and YDFSDirectory classes. If you only need a single class, you can also specify it as a
         *					string.
         *
         *	@returns	Array of YDFile objects for the files that match the pattern.
         */
        function has( $obj, $classes=array( 'YDFSFile', 'YDFSImage', 'YDFSDirectory' ) ) {

            // Get the contents
            $contents = $this->getContents( $obj, '', $classes );

            // Return the result
            return ( sizeof( $contents ) == 0 ) ? false : true;

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

            // Use the native fnmatch function if it is there
            if ( function_exists( 'fnmatch' ) ) {

                // Match the pattern
                return fnmatch( $pattern, $file );

            }

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