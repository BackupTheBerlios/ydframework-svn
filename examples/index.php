<?php

    // Standard include
    require_once( dirname( __FILE__ ) . '/../YDFramework2/YDF2_init.php' );

    // Class definition
    class indexRequest extends YDRequest {

        // Class constructor
        function indexRequest() {

            // Initialize the parent class
            $this->YDRequest();

        }

        // Default action
        function actionDefault() {

            // Output the template
            $this->outputTemplate();

        }

        // Function to show the highlighted source file
        function actionSource() {

            // Get the basename of the file
            $file = $_GET['id'];

            // Filepath should start with the directory of this file
            $basePath = strtolower( dirname( __FILE__ ) );
            $filePath = strtolower( dirname( realpath( $_GET['id'] ) ) );

            // Check if the file is in the right directory
            if ( substr( $filePath, 0, strlen( $basePath ) ) != $basePath ) {
                $this->forward( 'default' );
                return;
            }

            // Check if the file exists
            if ( ! is_file( $file ) ) {
                $this->forward( 'default' );
                return;
            }

            // Show the highlighted source
            $this->setVar( 'file', $file );
            $this->setVar( 'source', highlight_file( $file, 1 ) );

            // Output the template
            $this->outputTemplate();

        }

    }

    // Process the request
    require_once( dirname( __FILE__ ) . '/../YDFramework2/YDF2_process.php' );

?>
