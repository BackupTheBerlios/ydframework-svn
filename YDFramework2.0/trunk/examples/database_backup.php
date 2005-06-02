<?php

    // Standard include
    include_once( dirname( __FILE__ ) . '/../YDFramework2/YDF2_init.php' );

    // Includes
    YDInclude( 'YDRequest.php' );
    YDInclude( 'YDDatabase.php' );
    YDInclude( 'YDMysqlDump.php' );

    // Class definition
    class backup extends YDRequest {

        // Class constructor
        function backup() {
            $this->YDRequest();
            
            // Get the data
            $this->db = YDDatabase::getInstance( 'mysql', 'test', 'root', '', 'localhost' );
            
            $this->dump = new YDMysqlDump( $this->db );
        }

        // Default action
        function actionDefault() {
            
            $file = $this->dump->backup( true );
            
            YDDebugUtil::dump( $file->getContents(), $this->dump->getFilePath() );
        }
        
        function actionRestore() {
            
            // include filesystem functions
            YDInclude( 'YDFileSystem.php' );
            
            // create file object
            $file = new YDFSFile( $this->dump->getFilePath() );
            $this->dump->restore( $file->getContents() );
            
            YDDebugUtil::dump( $file->getContents(), $this->dump->getFilePath() );
        }

    }

    // Process the request
    YDInclude( 'YDF2_process.php' );

?>
