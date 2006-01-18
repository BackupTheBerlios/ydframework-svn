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
    
    // Auto execute config value. Default: true.
    YDConfig::set( 'YD_AUTO_EXECUTE', true, false );

    /**
     *	This is the executor class that contains all the logic for executing requests. It will instantiate the request
     *	class and execute the right functions to get the request processed correctly.
     */
    class YDExecutor extends YDBase {

        /**
         *	The class constructor for the YDExecutor class.
         *
         *	@param $file	Full path to the file that contains the request.
         */
        function YDExecutor( $file ) {
            $this->_file = $file;
        }

        /**
         *	The execute function will start the YDExecutor class and process the request.
         */
        function execute() {

            // Do nothing if we already processed a request
            if ( defined( 'YD_REQ_PROCESSED' ) ) {
                return;
            }

            // Construct the name of the request class
            $clsName = basename( $this->_file, YD_SCR_EXT );
            $this->clsInst = new $clsName();

            // Check if the object a YDRequest object
            if ( ! YDObjectUtil::isSubClass( $this->clsInst, 'YDRequest' ) ) {
                $ancestors = YDObjectUtil::getAncestors( $this->clsInst );
                trigger_error(
                    'Class "' . $clsName . '" should be derived from the YDRequest class. Currently, this class has the '
                    . 'following ancestors: ' . implode( ' -&gt; ', $ancestors ), YD_ERROR
                );
            }

            // Check if the class is properly initialized
            if ( $this->clsInst->isInitialized() != true ) {
                trigger_error(
                    'Class "' . $clsName . '" is not initialized properly. Make  sure loaded the base class YDRequest '
                    . 'and initialized it.', YD_ERROR
                );
            }

            // Only if authentication is required
            if ( $this->clsInst->getRequiresAuthentication() ) {
                $result = $this->clsInst->isAuthenticated();
                if ( $result ) {
                    $this->clsInst->authenticationSucceeded();
                } else {
                    $this->clsInst->authenticationFailed();
                    $this->finish();
                }
            }

            // Get the action name
            $action = 'action' . $this->clsInst->getActionName();

            // Check if the action exists
            if ( ! $this->clsInst->hasMethod( $action ) ) {
                $this->clsInst->errorMissingAction( $action );
                $this->finish();
            }

            // Check if the current action is allowed or not and execute errorActionNotAllowed if failed
            if ( ! $this->clsInst->isActionAllowed() ) {
                $this->clsInst->errorActionNotAllowed();
                $this->finish();
            }

            // Process the request
            $this->clsInst->process();
            $this->finish();

        }

        /**
         *	This is the function we use for finishing the request.
         *
         *	@internal
         */
        function finish() {

            // Mark that the request is processed
            define( 'YD_REQ_PROCESSED', 1 );

            // Stop the timer
            $GLOBALS['timer']->finish();

            // Show debugging info if needed
            if ( YDConfig::get( 'YD_DEBUG' ) == 1 || YDConfig::get( 'YD_DEBUG' ) == 2 ) {

                // Total size of include files
                $includeFiles = get_included_files();

                // Calculate the total size
                $includeFilesSize = 0;
                $includeFilesWithSize = array();
                foreach ( $includeFiles as $key=>$includeFile ) {
                    $includeFilesSize += filesize( $includeFile );
                    $includeFilesWithSize[ filesize( $includeFile ) ] = realpath( $includeFile );
                }
                $includeFilesSize = YDStringUtil::formatFileSize( $includeFilesSize );

                // Sort the list of include files by file size
                krsort( $includeFilesWithSize );

                // Convert to a string
                $includeFiles = array();
                foreach ( $includeFilesWithSize as $size=>$file ) {
                    array_push( $includeFiles, YDStringUtil::formatFileSize( $size ) . "\t  " . realpath( $file ) );
                }

                // Create the debug messages
                $debug = YD_CRLF . YD_CRLF;

                // Create the timings report
                $debug .= 'PROCESSING TIME:' . YD_CRLF . YD_CRLF;
                $debug .= "\tElapsed\t  Diff\t  Marker" . YD_CRLF;
                $timings = $GLOBALS['timer']->getReport();
                foreach ( $timings as $timing ) {
                    $debug .= "\t" . $timing[0] . ' ms' . "\t  " . $timing[1] . ' ms' . "\t  " .$timing[2] . YD_CRLF;
                }
                $debug .= YD_CRLF;

                // Create the include file details
                $debug .= 'INCLUDED FILES (' . sizeof( $includeFiles ) . ' files - ' . $includeFilesSize . ')' . YD_CRLF . YD_CRLF;
                $debug .= "\t" . implode( "\n\t", $includeFiles ) . YD_CRLF . YD_CRLF;

                // Create the list of include directories
                $include_dirs = explode( YD_PATHDELIM, ini_get( 'include_path' ) );
                $debug .= 'INCLUDES SEARCH PATH (' . sizeof( $include_dirs ) . ' directories)' . YD_CRLF . YD_CRLF;
                foreach ( $include_dirs as $path ) {
                    if ( realpath( $path ) ) {
                        $debug .= "\t" . realpath( $path ) . YD_CRLF;
                    } else {
                        $debug .= "\t" . $path . YD_CRLF;
                    }
                }
                $debug .= YD_CRLF;

                // Add the queries if any
                if ( sizeof( $GLOBALS['YD_SQL_QUERY'] ) > 0 ) {
                    $debug .= 'EXECUTED SQL QUERIES (' . sizeof( $GLOBALS['YD_SQL_QUERY'] ) . ' queries)' . YD_CRLF . YD_CRLF;
                    foreach ( $GLOBALS['YD_SQL_QUERY'] as $key=>$query ) {
                        $debug .= "\t" . ($key+1) . ': ' . trim( $query ) . YD_CRLF . YD_CRLF;
                    }
                }

                // Output the debug message
                YDDebugUtil::debug( $debug );

            }

            // Add the elapsed time
            $elapsed = $GLOBALS['timer']->getElapsed();
            echo( YD_CRLF . '<!-- ' . $elapsed . ' ms -->' );

            // Stop the execution of the request
            die();

        }

    }

    if ( YDConfig::get( 'YD_AUTO_EXECUTE', true ) == true ) { 
    
        // Instantiate the executor class
        $clsInst = new YDExecutor( YD_SELF_FILE );

        // Execute the execute function
        $clsInst->execute();

    }
    
?>
