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
            $dbAliasses = $GLOBALS['dbAliasses'];
            if ( ! isset( $dbAliasses[ $_GET['id'] ] ) ) {
                $this->setVar( 'error', 'Alias "' . $_GET['id'] . '" is not defined.' );
            } else {

                // Connect to the database
                $db = new YDDatabase( $dbAliasses[ $_GET['id'] ] );
                $conn = $db->getConnection( false );

                // Check for errors
                if ( YDError::isError( $conn ) ) {

                    // Assign the error object
                    $this->setVar( 'error', $conn->getError() );
                    
                    // Set the result to none
                    $this->setVar( 'result', null );

                } else {

                    // Perform a database query
                    $sql = new YDSqlRaw( 'show processlist' );
                    $result = $sql->executeSelect( $dbAliasses[ $_GET['id'] ] );

                    // Assign the database result
                    $this->setVar( 'result', $result );

                }

            }

            // Output the template
            $this->outputTemplate();

        }

    }

    // Process the request
    require_once( dirname( __FILE__ ) . '/../YDFramework2/YDF2_process.php' );

?>
