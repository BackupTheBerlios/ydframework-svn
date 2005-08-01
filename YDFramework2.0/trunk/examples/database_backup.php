<?php

    // Standard include
    include_once( dirname( __FILE__ ) . '/../YDFramework2/YDF2_init.php' );

    // Includes
    YDInclude( 'YDRequest.php' );
    YDInclude( 'YDDatabase.php' );
    YDInclude( 'YDMysqlDump.php' );

    // Class definition
    class database_backup extends YDRequest {

        // Class constructor
        function database_backup() {
            $this->YDRequest();

            // Get the data
            $this->db = YDDatabase::getInstance( 'mysql', 'test', 'root', '', 'localhost' );

            // Create the YDMysqlDump object
            $this->dump = new YDMysqlDump( $this->db );

        }

        // Default action
        function actionDefault() {

            // Make a backup of the database
            $file = $this->dump->backup( true );

            // Show the properties of the database backup
            YDDebugUtil::dump( $file->getContents(), $this->dump->getFilePath() );
        }

        // Restore action
        function actionRestore() {

            // Include filesystem functions
            YDInclude( 'YDFileSystem.php' );

            // Create file object
            $file = new YDFSFile( $this->dump->getFilePath() );

            // Restore the database dump
            $this->dump->restore( $file->getContents() );

            // Show the properties of the database backup
            YDDebugUtil::dump( $file->getContents(), $this->dump->getFilePath() );

        }

    }

    // Process the request
    YDInclude( 'YDF2_process.php' );

?>
