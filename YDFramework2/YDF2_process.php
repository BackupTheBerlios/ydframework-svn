<?php

    /*
     *  Yellow Duck Framework version 2.0
     *  (c) copyright 2004 Pieter Claerhout, pieter@yellowduck.be
     */

    // Check if the YDFramework is loaded.
    if ( ! defined( 'YD_FW_NAME' ) ) {
        die( 'ERROR: Yellow Duck Framework is not loaded.' );
    }

    // If we have one of the files in the YDFramework2 directory, do not process
    if ( defined( 'YD_FRAMEWORK_REQUEST' ) ) {
        die();
    }

    // Only execute this once
    if ( ! defined( 'YD_REQ_PROCESSED' ) ) {

        // Function used to finish the request
        if ( ! function_exists( 'YDFinishRequest' ) ) {

            // Define the YDFinishRequest function
            function YDFinishRequest() {

                // Mark that the request is processed
                define( 'YD_REQ_PROCESSED', 1 );

                // Show debugging info if needed
                if ( YD_DEBUG == 1 ) {

                    // Includes
                    require_once( 'YDDebugUtil.php' );

                    // Stop the timer
                    $elapsed = $GLOBALS['timer']->getElapsed();

                    // Show the timings
                    YDDebugUtil::debug( 'Processing time:', $elapsed, 'ms' );

                    // Total size of include files
                    $includeFiles = get_included_files();
                    $includeFilesSize = 0;

                    // Calculate the total size
                    foreach( $includeFiles as $includeFile ) {
                        $includeFilesSize += filesize( $includeFile );
                    }

                    // Format it as KBytes
                    $includeFilesSize = intval( $includeFilesSize / 1024 );

                    // Show the total size of the include files
                    YDDebugUtil::debug(
                        'Total size include files:', $includeFilesSize, 'KB'
                    );

                    // List of include files
                    YDDebugUtil::debug(
                        'Includes:' . "\n" . implode( "\n", $includeFiles )
                    );

                }

                // Stop the execution of the request
                die();

            }
        }

        // Construct the name of the request class
        $clsName = strtolower(
            basename( YD_SELF_FILE, YD_SCR_EXT ) . 'Request'
        );

        // Check if the class exists
        if ( ! class_exists( $clsName ) ) {
            new YDFatalError(
                'No class definition found for the class "' . $clsName . '".'
            );
        }

        // Instantiate the class
        $clsInst = new $clsName();

        // Check if the object a YDRequest object
        if ( ! YDObjectUtil::isSubClass( $clsInst, 'YDRequest' ) ) {

            // Get the list of ancestors
            $ancestors = YDObjectUtil::getAncestors( $clsInst );

            // Fail with an error
            new YDFatalError(
                'Class "' . $clsName . '" should be derived from the YDRequest '
                . 'class. Currently, this class has the following ancestors: '
                . implode( ' -&gt; ', $ancestors )
            );

        }

        // Check if the class is properly initialized
        if ( $clsInst->isInitialized() != true ) {
            new YDFatalError(
                'Class "' . $clsName . '" is not initialized properly. Make '
                . 'sure loaded the base class YDRequest and initialized it.'
            );
        }

        // Check if we have a process function
        YDObjectUtil::failOnMissingMethod( $clsInst, 'process' );

        // Only if authentication is required
        if ( $clsInst->getRequiresAuthentication() ) {

            // Check for an isAuthenticated function
            YDObjectUtil::failOnMissingMethod( $clsInst, 'isAuthenticated' );

            // Perform the authentication
            $result = $clsInst->isAuthenticated();

            // Execute authentication fails/succeeds
            if ( $result ) {
                YDObjectUtil::failOnMissingMethod(
                    $clsInst, 'authenticationSucceeded'
                );
                $clsInst->authenticationSucceeded();
            } else {
                YDObjectUtil::failOnMissingMethod(
                    $clsInst, 'authenticationFailed'
                );
                $clsInst->authenticationFailed();
                YDFinishRequest();
            }

        }

        // Check for an isActionAllowed function
        YDObjectUtil::failOnMissingMethod( $clsInst, 'isActionAllowed' );

        // Check if the current action is allowed or not
        $result = $clsInst->isActionAllowed();

        // Execute actionNotAllowed if failed
        if ( $result == false ) {
            YDObjectUtil::failOnMissingMethod( $clsInst, 'actionNotAllowed' );
            $clsInst->actionNotAllowed();
            YDFinishRequest();
        }

        // Process the request
        $clsInst->process();
        YDFinishRequest();

    }

?>
