<?php

    // Standard include
    include_once( dirname( __FILE__ ) . '/../YDFramework2/YDF2_init.php' );

    // Includes
    YDInclude( 'YDRequest.php' );
    YDInclude( 'YDBBCode.php' );
    YDInclude( 'YDFileSystem.php' );
    YDInclude( 'YDUtil.php' );

    // Class definition
    class timer extends YDRequest {

        // Class constructor
        function timer() {
            $this->YDRequest();
        }

        // Default action
        function actionDefault() {

            // The original data
            YDGlobalTimerMarker( 'Reading file' );
            $file = new YDFSFile( 'bbcode.txt' );
            $data = $file->getContents();
            YDGlobalTimerMarker( 'Finished reading file' );

            // The converter
            YDGlobalTimerMarker( 'YDBBCode object' );
            $conv = new YDBBCode();

            // Show the converted data
            YDGlobalTimerMarker( 'Conversion to BBCode' );
            echo( '<pre>' . htmlentities( $data ) . '</pre>' );
            echo( '<pre>' . htmlentities( $conv->toHtml( $data ) ) . '</pre>' );
            echo( '<p>' . $conv->toHtml( $data, true, false ) . '</p>' );

        }

        // Default action
        function actionStandaloneTimer() {

            // Instantiate the timer
            $timer = new YDTimer();
            
            // The original data
            $timer->addMarker( 'Reading file' );
            $file = new YDFSFile( 'bbcode.txt' );
            $data = $file->getContents();
            $timer->addMarker( 'Finished reading file' );
            
            // The converter
            $timer->addMarker( 'YDBBCode object' );
            $conv = new YDBBCode();
            
            // Show the converted data
            $timer->addMarker( 'Conversion to BBCode' );
            echo( '<pre>' . htmlentities( $data ) . '</pre>' );
            echo( '<pre>' . htmlentities( $conv->toHtml( $data ) ) . '</pre>' );
            echo( '<p>' . $conv->toHtml( $data, true, false ) . '</p>' );
            
            // Get the report
            $report = $timer->getReport();

            // Dump the contents of the report
            YDDebugUtil::dump( $report, 'Timing report' );
            
        }
        
    }

    // Process the request
    YDInclude( 'YDF2_process.php' );

?>