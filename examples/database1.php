<?php

    /*

        This examples demonstrates:
        - How to include a configuration file
        - How to check arguments passed by the url
        - How to make a database connection
        - How to use arrays in templates
        - How to check if an object is an error or not

    */

    // Standard include
    require_once( dirname( __FILE__ ) . '/../YDFramework2/YDF2_init.php' );

    // Include the configuration
    require_once( dirname( __FILE__ ) . '/config.php' );

    // Class definition
    class database1Request extends YDRequest {

        // Class constructor
        function database1Request() {

            // Initialize the parent class
            $this->YDRequest();

        }

        // Default action
        function actionDefault() {
            
            // Assign the list of database aliasses
            $this->setVar( 'dbAliasses', $GLOBALS['dbAliasses'] );

            // Output the template
            $this->outputTemplate();

        }

        // Test a database connection
        function actionTest() {

            // Return to default action if no ID is given
            if ( empty( $_GET['id'] ) ) {
                $this->forward( 'default' );
                return;
            }

            // Check if the url is defined
            if ( ! isset( $GLOBALS['dbAliasses'][ $_GET['id'] ] ) ) {
                $this->setVar( 'error', 'Alias "' . $_GET['id'] . '" is not defined.' );
            } else {

                // Connect to the database
                $conn = new YDDbConn( $GLOBALS['dbAliasses'][ $_GET['id'] ] );
                $result = $conn->connect();
                if ( YDError::isError( $result ) ) {
                    $this->setVar( 'error', $result->getError() );
                }

            }

            // Output the template
            $this->outputTemplate();

        }

    }

    // Process the request
    require_once( dirname( __FILE__ ) . '/../YDFramework2/YDF2_process.php' );

?>
