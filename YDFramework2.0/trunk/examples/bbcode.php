<?php

    // Standard include
    include_once( dirname( __FILE__ ) . '/../YDFramework2/YDF2_init.php' );

    // Includes
    YDInclude( 'YDRequest.php' );
    YDInclude( 'YDBBCode.php' );
    YDInclude( 'YDFileSystem.php' );

    // Class definition
    class bbcode extends YDRequest {

        // Class constructor
        function bbcode() {
            $this->YDRequest();
        }

        // Default action
        function actionDefault() {
            
            // The original data
            $file = new YDFSFile( 'bbcode.txt' );
            $data = $file->getContents();

            // The converter
            $conv = new YDBBCode();

            // Show the converted data
            echo( '<pre>' . htmlentities( $data ) . '</pre>' );
            echo( '<pre>' . htmlentities( $conv->toHtml( $data ) ) . '</pre>' );
            echo( '<p>' . $conv->toHtml( $data, true, false ) . '</p>' );

        }

    }

    // Process the request
    YDInclude( 'YDF2_process.php' );

?>
