<?php

    /*

       Yellow Duck Framework version 2.0
       (c) copyright 2004 Pieter Claerhout, pieter@yellowduck.be

    */

    // Check if the YDFramework is loaded.
    if ( ! defined( 'YD_FW_NAME' ) ) {
        die( 'ERROR: Yellow Duck Framework is not loaded.' );
    }

    /**
     *  This class defines a filesystem file.
     */
    class YDFSFile {

        /**
         *  The class constructor of the YDFSFile class takes the path to the 
         *  file as it's only argument. It will then provide you with a number
         *  of functions to get the properties of the file.
         *
         *  @param $path Path of the file
         */
        function YDFSFile( $path ) {

            // Check if the file exists
            if ( ! file_exists( $path ) ) {
                new YDFatalError(
                    'The file with path "' . $path . '" does not exist.'
                );
            }

            // Fail if directory
            if ( ! is_file( $path ) ) {
                new YDFatalError(
                    'The path "' . $path . '" is not a file and can not be '
                    . 'converted into YDFSFile object.'
                );
            }

            // Save the path
            $this->_path = realpath( $path );

        }

        /**
         *  Function to get the filename of the object. This does not include the
         *  path information.
         *
         *  @returns String containing the name of the object.
         */
        function getBasename() {
            return basename( $this->_path );
        }

        /**
         *  Function to get the extension of the file.
         *
         *  @returns String containing the extension of the file
         */
        function getExtension() {
            ereg( ".*\.([a-zA-z0-9]{0,5})$", $this->_path, $regs );
            return( $regs[1] );
        }

        /**
         *  Function to get the full path of the object.
         *
         *  @returns String containing the full path of the object.
         */
        function getPath() {
            return realpath( dirname( $this->_path ) );
        }

        /**
         *  Function to get the full absolute path of the object.
         *
         *  @returns String containing the full absolute path of the object.
         */
        function getAbsolutePath() {
            return realpath( $this->_path );
        }

        /**
         *  Function to get the last modification date of the object.
         *
         *  @returns String containing the last modification date of the object.
         */
        function getLastModified() {
            return filemtime( $this->_path );
        }

        /**
         *  Function to get the size of the file.
         *
         *  @returns Double containing the length of the file
         */
        function getSize() {
            return filesize( $this->_path );
        }

        /**
         *  Function to get the contents of the file. Depending on the file
         *  contents, this will be returned as binary or textual data.
         *  
         *  If no start byte is given, it will start reading from the beginning 
         *  of the file. 
         *
         *  If the length is not given, it will read the rest of  the file
         *  starting from the start byte.
         *
         *  @param $start  Byte to start reading from
         *  @param $length Number of bytes to read
         *
         *  @returns String containing the contents of the file
         */
        function getContents( $start=null, $length=null ) {

            // No start given
            if ( $start == null ) {

                // Start from the first byte
                $start = 0;

            } else {
                
                // Check if the start is an integer
                if ( ! is_int( $start ) ) {
                    new YDFatalError( 'getContents: Start byte should be an integer.' );
                }

            }

            // No legnth given
            if ( $length == null ) {

                // Take the length of the file starting from the start byte
                $length = filesize( $this->_path ) - $start;

            }

            // Check that length is a positive integer
            if ( ! is_int( $length ) ) {
                new YDFatalError( 'getContents: Length should be an integer.' );
            }
            if ( $length < 1 ) {
                new YDFatalError(
                    'getContents: Length should be a positive integer.' 
                );
            }

            // Variable to hold the return data
            $result = '';

            // Open the file in read binary mode
            $file = fopen( $this->_path, 'rb' );

            // Check if we were able to open the file
            if ( $file === false ) {
                return new YDFatalError(
                    'The file with path "' . $path . '" could not be read.'
                );
            }

            // Find the start position
            fseek( $file, $start );

            // Get the contents of the file
            //$result = fread( $file, filesize( $this->_path ) );
            $result = fread( $file, $length );

            // Close the file handle    
            fclose( $file );

            // Return the result
            return $result;

        }

        /**
         *  Function to determine if the file is an image or not. This function
         *  will read the header of the file to find out if it's an image or not.
         *
         *  @remarks
         *      You need to have the GD library enabled in PHP in order to use
         *      this function.
         *
         *  @returns Boolean indicating if the file is an image or not.
         */
        function isImage() {

            // Check for the getimagesize function
            if ( ! function_exists( 'getimagesize' ) ) {
                new YDFatalError(
                    'The "getimagesize" function does not exists. Make sure '
                    . 'that the GD libraries are loaded before using the '
                    . 'YDFSImage::getImageSize function.'
                );
            }

            // Get the parameters
            $params = getimagesize( $this->getAbsolutePath() );

            // Return false if not an image
            if ( $params === false ) {
                return false;
            }

            // Check if it's a supported image
            return in_array( $params[2], array( 1, 2, 3 ) );

        }

    }

?>
