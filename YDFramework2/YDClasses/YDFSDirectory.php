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
         *  @param $path Path of the directory
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
         *  @param $pattern Pattern to which the files should match
         *  
         *  @returns Array of YDFile objects for the files that match the pattern
         */
        function getContents( $pattern='') {

            // Start with an empty list
            $fileList = array();
           
            // Get a handle to the directory
            $dirHandle = opendir( $this->_path );

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
                $file = $this->_path . '/' . $file;
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

    }

?>
