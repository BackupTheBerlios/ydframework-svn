<?php

    // Initialize the Yellow Duck Framework
    include_once( dirname( __FILE__ ) . '/../../YDFramework2/YDF2_init.php' );

    // Database setup
    $db['type'] = 'mysql';
    $db['name'] = 'mysql';
    $db['user'] = 'root';
    $db['pass'] = '';
    $db['host'] = 'localhost';

    // Includes
    YDInclude( 'YDTemplate.php' );
    YDInclude( 'YDDatabase.php' );

    // Class definition for the index request
    class index extends YDRequest {

        // Class constructor
        function index() {

            // Initialize the parent class
            $this->YDRequest();

            // Initialize the template object
            $this->template = new YDTemplate();

            // Make the database connection
            $this->db = YDDatabase::getInstance(
                $GLOBALS['db']['type'],
                $GLOBALS['db']['name'],
                $GLOBALS['db']['user'],
                $GLOBALS['db']['pass'],
                $GLOBALS['db']['host']
            );

        }

        // Default action
        function actionDefault() {
            $this->template->display();
        }

        // Get the records as XML
        function actionGetRecords() {
            header( 'Content-type: text/xml' );
            echo( YDArrayUtil::toXml( $this->db->getRecords( 'show status' ) ) );
        }

    }

    // Process the request
    YDInclude( 'YDF2_process.php' );

?>