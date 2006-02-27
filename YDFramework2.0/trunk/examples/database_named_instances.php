<?php

    // Standard include
    include_once( dirname( __FILE__ ) . '/../YDFramework2/YDF2_init.php' );

    // Includes
    YDInclude( 'YDRequest.php' );
    YDInclude( 'YDDatabase.php' );

    // Class definition
    class database_named_instances extends YDRequest {

        // Class constructor
        function database_named_instances() {
            $this->YDRequest();
        }

        // Default action
        function actionDefault() {

            // Register the named instances
            YDDatabase::registerInstance( 'default',  'mysql', 'ydweblog', 'root', '', 'localhost' );
            YDDatabase::registerInstance( 'db_mysql', 'mysql', 'mysql',    'root', '', 'localhost' );
            YDDatabase::registerInstance( 'db_test',  'mysql', 'test',     'root', '', 'localhost' );

            // Get the default instance
            $db1 = YDDatabase::getNamedInstance();
            YDDebugUtil::dump( $db1, 'default instance, no name given' );

            // Get the default instance using it's name
            $db1 = YDDatabase::getNamedInstance( 'DEFAULT' );
            YDDebugUtil::dump( $db1, 'default instance, using name' );

            // Get the db_mysql instance using it's name
            $db1 = YDDatabase::getNamedInstance( 'db_mysql' );
            YDDebugUtil::dump( $db1, 'db_mysql instance' );

            // Get the db_bba_v2 instance using it's name
            $db1 = YDDatabase::getNamedInstance( 'db_test' );
            YDDebugUtil::dump( $db1, 'db_test instance' );

        }

    }

    // Process the request
    YDInclude( 'YDF2_process.php' );

?>
