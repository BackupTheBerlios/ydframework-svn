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
     *  This class defines a filesystem directory.
     */
    class YDFSDirectory {

        /**
         *  This is the class constructor of the YDDirectory class.
         *
         *  @param $path Path of the directory.
         */
        function YDFSDirectory( $path ) {

            // Check if the file exists
            if ( ! file_exists( $path ) ) {
                new YDFatalError(
                    'The directory with path "' . $path . '" does not exist.'
                );
            }

            // Fail if directory
            if ( ! is_dir( $path ) ) {
                new YDFatalError(
                    'The path "' . $path . '" is not a directory and can not '
                    . 'be converted into YDFSFile object.'
                );
            }

            // Save the path
            $this->_path = realpath( $path );

        }

        /**
         *  This function will get a file list using a pattern. You can compare
         *  this function with the dir command from DOS or the ls command from
         *  Unix. The pattern syntax is the same as well.
         *
         *  @remarks
         *      This will not work recursively on the subdirectories.
         *
         *  @param $pattern (optional) Pattern to which the files should match.
         *  
         *  @returns Array of YDFile objects for the files that match the 
         *           pattern.
         */
        function getContents( $pattern='') {

            // Start with an empty list
            $fileList = array();
           
            // Get a handle to the directory
            $dirHandle = opendir( $this->getPath() );

            // Loop over the directory contents
            while ( false !== ( $file = readdir( $dirHandle ) ) ) { 
                if ( $file != '.' && $file != '..' ) {
                    if ( $pattern != '' ) {
                        if ( YDFileUtil::match( $pattern, $file ) ) {
                            array_push( $fileList, $file );
                        }
                    } else {
                        array_push( $fileList, $file );
                    }
                }
            }

            // Sort the list of files
            sort( $fileList );

            // Convert the list of a list of YDFile objects
            $fileList2 = array();
            foreach ( $fileList as $file ) {
                $file = $this->getPath() . '/' . $file;
                if ( is_dir( $file ) ) {
                    $fileObj = new YDFSDirectory( $file );
                } else {
                    $fileObj = new YDFSFile( $file );
                    if ( $fileObj->isImage() ) {
                        $fileObj = new YDFSImage( $file );
                    }
                }
                array_push( $fileList2, $fileObj );
            }

            // Return the file list
            return $fileList2;

        }

        /**
         *  Function to get the full path of the directory.
         *
         *  @returns String containing the full path of the directory.
         */
        function getPath() {
            return realpath( $this->_path );
        }

        // should return file object
        /**
         *  This function will create a new file in the current directory, and
         *  will write the specified contents to the file. Once finished, it
         *  will return a new YDFSFile object pointing to the file. All
         *  directory paths are relative to the current directory.
         *
         *  @param $filename The filename of the new file.
         *  @param $contents The contents of the new file.
         *
         *  @returns YDFSFile or YDFSImage object pointing to the new file.
         */
        function createFile( $filename, $contents ) {

            // Set the directory of this object as the working directory
            chdir( $this->getPath() );

            // Create the new file
            $fp = fopen( $filename, 'wb' );

            // Save the contents to the file
            $result = fwrite( $fp, $contents );

            // Check for errors
            if ( $result === false ) {
                new YDFatalError(
                    'Failed writing to the file "' . $file . '" in the  '
                    . ' directory called "' . $this->getPath() . '".'
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
         *  This function will delete a file from the current directory.
         *
         *  @param $filename    The file you want to delete.
         *  @param $failOnError (optional) Indicate if a YDFatalError needs to 
         *                      be raised if deleting the file failed.
         */
        function deleteFile( $filename, $failOnError=false ) {

            // Set the directory of this object as the working directory
            chdir( $this->getPath() );

            // Check if the file exists
            if ( file_exists( $filename ) ) {

                // Try to delete the file
                $result = unlink( $filename );

                // Check for errors
                if ( $result === false ) {

                    // Check if we need to raise an error
                    if ( $failOnError === true ) {
                        new YDFatalError(
                            'Failed deleting the file "' . $file . '" in the  '
                            . ' directory called "' . $this->getPath() . '".'
                        );
                    }

                }

            }

        }

    }

?>
