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

            // Output visible debug info as a comment
            if ( YDConfig::get( 'YD_DEBUG' ) == 1 ) {

                // Total size of include files
                $includeFiles = get_included_files();

                // Calculate the total size
                $includeFilesSize = 0;
                $includeFilesWithSize = array();
                foreach ( $includeFiles as $key=>$includeFile ) {
                    if ( is_file( $includeFile ) ) {
                        $includeFilesSize += filesize( $includeFile );
                        $includeFilesWithSize[ filesize( $includeFile ) ] = realpath( $includeFile );
                    }
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
                $debug = YD_CRLF . YD_CRLF . YD_FW_NAMEVERS . ' DEBUG INFORMATION ' . YD_CRLF . YD_CRLF;

                // Create the timings report
                $debug .= 'PROCESSING TIME:' . YD_CRLF . YD_CRLF;
                $debug .= "    Elapsed\t  Diff\t  Marker" . YD_CRLF;
                $timings = $GLOBALS['timer']->getReport();
                foreach ( $timings as $timing ) {
                    $debug .= "    " . $timing[0] . ' ms' . "\t  " . $timing[1] . ' ms' . "\t  " .$timing[2] . YD_CRLF;
                }
                $debug .= YD_CRLF;

                // Create the include file details
                $debug .= 'INCLUDED FILES (' . sizeof( $includeFiles ) . ' files - ' . $includeFilesSize . ')' . YD_CRLF . YD_CRLF;
                $debug .= "    " . implode( "\n    ", $includeFiles ) . YD_CRLF . YD_CRLF;

                // Add the queries if any
                if ( sizeof( $GLOBALS['YD_SQL_QUERY'] ) > 0 ) {
                    $debug .= 'EXECUTED SQL QUERIES (' . sizeof( $GLOBALS['YD_SQL_QUERY'] ) . ' queries)' . YD_CRLF . YD_CRLF;
                    foreach ( $GLOBALS['YD_SQL_QUERY'] as $key=>$query ) {
                        $debug .= "    " . '---- SQL QUERY ' . ($key+1) . ' ----' . YD_CRLF;
                        foreach ( explode( YD_CRLF, trim( $query['trace'] ) ) as $line ) {
                            $debug .= "    " . rtrim( $line ) . YD_CRLF;
                        }
                        $debug .= YD_CRLF . "    SQL Query:" . YD_CRLF;
                        foreach ( explode( YD_CRLF, trim( $query['sql'] ) ) as $line ) {
                            $debug .= "        " . rtrim( $line ) . YD_CRLF;
                        }
                        $debug .= YD_CRLF . "    Query Time: " . $query['time'] . ' ms' . YD_CRLF;
                        $debug .= YD_CRLF;
                    }
                }

                // Output the debug message
                YDDebugUtil::debug( $debug );

            }

            // Output visible debug info
            if ( YDConfig::get( 'YD_DEBUG' ) == 2 ) {

                // Total size of include files
                $includeFiles = get_included_files();

                // Calculate the total size
                $includeFilesSize = 0;
                $includeFilesWithSize = array();
                foreach ( $includeFiles as $key=>$includeFile ) {
                    if ( is_file( $includeFile ) ) {
                        $includeFilesSize += filesize( $includeFile );
                        $includeFilesWithSize[ filesize( $includeFile ) ] = realpath( $includeFile );
                    }
                }
                $includeFilesSize = YDStringUtil::formatFileSize( $includeFilesSize );

                // Sort the list of include files by file size
                krsort( $includeFilesWithSize );

                // Get the timing report
                $timings = $GLOBALS['timer']->getReport();

                // The font to use for the debug output
                $font = ' style="font-family: Arial, Helvetica, sans-serif; text-align: left; vertical-align: top;"';

                // Include the needed libraries
                $debug  = '';

                // Create the output
                $debug .= '<br clear="all"/><hr size="1"/>';
                $debug .= '<p ' . $font . '><b>' . YD_FW_NAMEVERS . ' Debug Information</b></p>';

                // Add the processing time
                $debug .= '<p ' . $font . '><i>Processing time</i></p>';
                $debug .= '<dd><p><table width="90%" border="1" cellspacing="0" cellpadding="2">';
                $debug .= sprintf(
                    '<tr><th %s>Elapsed</th><th %s>Diff</th><th %s>Marker</th></tr>',
                    $font, $font, $font
                );
                foreach ( $timings as $timing ) {
                    $debug .= sprintf(
                        '<tr><td %s>%s ms</td><td %s>%s ms</td><td %s>%s</td></tr>',
                        $font, $timing[0], $font, $timing[0], $font, $timing[2]
                    );
                }
                $debug .= '</table></p></dd>';

                // Add the included files
                $debug .= '<p ' . $font . '><i>Included files</i></p>';
                $debug .= '<dd><p><table width="90%" border="1" cellspacing="0" cellpadding="2">';
                $debug .= sprintf( '<tr><th %s>File</th><th %s>Size</th></tr>', $font, $font );
                foreach ( $includeFilesWithSize as $size=>$file ) {
                    $debug .= sprintf(
                        '<tr><td %s>%s</td><td %s>%s</td></tr>',
                        $font, realpath( $file ), $font, YDStringUtil::formatFileSize( $size )
                    );
                }
                $debug .= '</table></p></dd>';

                // Add the query log
                if ( sizeof( $GLOBALS['YD_SQL_QUERY'] ) > 0 ) {
                    $debug .= '<script src="' . YD_SELF_SCRIPT . '?do=JsShMain"></script>';
                    $debug .= '<script src="' . YD_SELF_SCRIPT . '?do=JsShSql"></script>';
                    $debug .= '<link rel="stylesheet" href="' . YD_SELF_SCRIPT . '?do=CssShStyle" type="text/css" />';
                    $debug .= sprintf(
                        '<p ' . $font . '><i>SQL Query Log (%s queries)</i></p>',
                        sizeof( $GLOBALS['YD_SQL_QUERY'] )
                    );
                    $debug .= '<dd><p><table width="90%" border="1" cellspacing="0" cellpadding="2">';
                    $debug .= sprintf(
                        '<tr><th %s>Query #</th><th %s>Trace</th><th %s>SQL Statement</th><th %s>Elapsed</th></tr>',
                        $font, $font, $font, $font );
                    foreach ( $GLOBALS['YD_SQL_QUERY'] as $key=>$query ) {
                        $debug .= sprintf(
                            '<tr><td width="5%%" %s>%s</td><td width="15%%" %s>%s ms</td><td width="40%%" %s><pre>%s</pre></td><td width="40%%" %s><pre class="sh_sql">%s</pre></td></tr>',
                            $font, $key, $font, $query['time'], $font, $query['trace'], $font, $query['sql']
                        );
                    }
                    $debug .= '</table></p></dd>';
                    $debug .= '<script>sh_highlightDocument();</script>';
                    $debug .= '<script>sh_highlightDocument();</script>';
                }

                // Add the elapsed time
                $debug .= '<p ' . $font . '><i>Elapsed Time</i></p>';
                $debug .= '<dd><p>' . $GLOBALS['timer']->getElapsed() . ' ms</p></dd>';

                // Output the debug message
                echo( $debug );

            }

            // Add the elapsed time
            die( YD_CRLF . '<!-- ' . $GLOBALS['timer']->getElapsed() . ' ms -->' );

        }

    }

    // Check if we need to auto execute
    if ( YDConfig::get( 'YD_AUTO_EXECUTE', true ) == true ) { 
        $clsInst = new YDExecutor( YD_SELF_FILE );
        @session_start();
        $clsInst->execute();
    }

?>