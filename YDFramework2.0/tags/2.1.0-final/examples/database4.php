<?php

    // Standard include
    include_once( dirname( __FILE__ ) . '/../YDFramework2/YDF2_init.php' );

    // Includes
    YDInclude( 'YDRequest.php' );
    YDInclude( 'YDDatabase.php' );

    // Class definition
    class database4 extends YDRequest {

        // Class constructor
        function database4() {
            $this->YDRequest();
        }

        // Default action
        function actionDefault() {

            // Get the data
            $db = YDDatabase::getInstance( 'postgres', 'template1', 'pieter', 'kermit', 'yellowduck' );

            // Output the server version
            YDDebugUtil::dump( $db->getServerVersion(), 'Version:' );

            // Output some queries
            YDDebugUtil::dump( $db->getRecords( 'select now()' ), 'select now();' );

            // Test string escaping
            YDDebugUtil::dump( $db->escape( "Pieter's Framework" ), '$db->escape' );

            // Show number of queries
            YDDebugUtil::dump( $db->getSqlCount(), 'Number of queries' );

            // Test timestamps
            YDDebugUtil::dump( $db->getDate(), 'getDate()' );
            YDDebugUtil::dump( $db->getTime(), 'getTime()' );
            YDDebugUtil::dump( $db->getDate( '__NOW__' ), 'getDate( \'__NOW__\' )' );
            YDDebugUtil::dump( $db->getTime( '__NOW__' ), 'getTime( \'__NOW__\' )' );
            YDDebugUtil::dump( $db->getDate( '28-FEB-1977' ), 'getDate( \'28-FEB-1977\' )' );
            YDDebugUtil::dump( $db->getTime( '28-FEB-1977' ), 'getTime( \'28-FEB-1977\' )' );
            YDDebugUtil::dump( $db->escapeSql( $db->getDate() ), 'escapeSql( getDate() )' );
            YDDebugUtil::dump( $db->escapeSql( $db->getTime() ), 'escapeSql( getTime() )' );
            YDDebugUtil::dump( $db->escapeSql( $db->getDate( '__NOW__' ) ), 'escapeSql( getDate( \'__NOW__\' ) )' );
            YDDebugUtil::dump( $db->escapeSql( $db->getTime( '__NOW__' ) ), 'escapeSql( getTime( \'__NOW__\' ) )' );

            // Test limits
            YDDebugUtil::dump( $db->_prepareSqlForLimit( 'SELECT * FROM TABLE', 10 ) );
            YDDebugUtil::dump( $db->_prepareSqlForLimit( 'SELECT * FROM TABLE', 10, 25 ) );

            // Test errors
            YDDebugUtil::dump( $db->getRecords( 'xx' ), 'should return error' );

            // Close the database connection
            $db->close();

        }

    }

    // Process the request
    YDInclude( 'YDF2_process.php' );

?>
