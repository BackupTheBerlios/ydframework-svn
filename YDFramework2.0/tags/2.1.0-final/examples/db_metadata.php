<?php

    // Standard include
    include_once( dirname( __FILE__ ) . '/../YDFramework2/YDF2_init.php' );

    // Includes
    YDInclude( 'YDRequest.php' );
    YDInclude( 'YDDatabase.php' );
    YDInclude( 'YDDatabaseMetaData.php' );

    // Class definition
    class db_metadata extends YDRequest {

        // Class constructor
        function db_metadata() {
            $this->YDRequest();
        }

        // Default action
        function actionDefault() {

            // Get the database connections
            $db1 = YDDatabase::getInstance( 'mysql', 'ydweblog', 'root', '', 'localhost' );
            $db2 = YDDatabase::getInstance( 'oracle', 'creodb_directeur', 'prinergy', 'araxi' );
            $db3 = YDDatabase::getInstance( 'postgres', 'test_db', 'postgres', 'Ster4484', 'localhost' );
            $db4 = YDDatabase::getInstance( 'sqlite', 'db_metadata.db' );

            // Get the metadata
            $db1_meta = new YDDatabaseMetaData( $db1 );
            $db2_meta = new YDDatabaseMetaData( $db2 );
            $db3_meta = new YDDatabaseMetaData( $db3 );
            $db4_meta = new YDDatabaseMetaData( $db4 );

            // Get the list of tables
            YDDebugUtil::dump( $db1_meta->getTables(), 'mysql - getTables' );
            YDDebugUtil::dump( $db2_meta->getTables(), 'oracle - getTables' );
            YDDebugUtil::dump( $db3_meta->getTables(), 'postgres - getTables' );
            YDDebugUtil::dump( $db4_meta->getTables(), 'sqlite - getTables' );

            // Get the list of fields for a table
            YDDebugUtil::dump( $db1_meta->getFields( 'ydw_items' ), 'mysql - getFields' );
            YDDebugUtil::dump( $db2_meta->getFields( 'dbcs_jacket' ), 'oracle - getFields' );
            YDDebugUtil::dump( $db3_meta->getFields(), 'postgres - getFields' );
            YDDebugUtil::dump( $db4_meta->getFields( 'notes' ), 'sqlite - getFields' );

            // Get the list of indexes for a table
            YDDebugUtil::dump( $db1_meta->getIndexes( 'ydw_items' ), 'mysql - getIndexes' );
            YDDebugUtil::dump( $db2_meta->getIndexes( 'dbcs_jacket' ), 'oracle - getIndexes' );
            YDDebugUtil::dump( $db3_meta->getIndexes(), 'postgres - getIndexes' );
            YDDebugUtil::dump( $db4_meta->getIndexes( 'notes' ), 'sqlite - getIndexes' );

        }

    }

    // Process the request
    YDInclude( 'YDF2_process.php' );

?>