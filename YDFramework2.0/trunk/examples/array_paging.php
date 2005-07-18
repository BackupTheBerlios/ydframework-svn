<?php

    // Standard include
    include_once( dirname( __FILE__ ) . '/../YDFramework2/YDF2_init.php' );

    // Includes
    YDInclude( 'YDRequest.php' );
    YDInclude( 'YDDatabase.php' );
    YDInclude( 'YDFileSystem.php' );
    YDInclude( 'YDTemplate.php' );

    // Configure the default persistence lifetime
    YDConfig::set( 'YD_PERSISTENT_DEFAULT_LIFETIME', time()+31536000 );

    // Define the default pagesize
    YDConfig::set( 'YD_DB_DEFAULTPAGESIZE', 15 );

    // Class definition
    class array_paging extends YDRequest {

        // Class constructor
        function array_paging() {
            $this->YDRequest();
            $this->template = new YDTemplate();
        }

        // Default action
        function actionDefault() {

            // Get the pagesize and current page using YDPersistent
            $page = YDPersistent::get( 'page', -1 );
            $size = YDPersistent::get( 'size', 15 );

            // Get the list of files in the current directory
            $dir = new YDFSDirectory();
            $files = $dir->getContents( '*.*', null, 'YDFSFile' );

            // Create the YDRecordSet object
            $recordset = new YDRecordSet( $files, $page, $size );

            // Setup the template
            $this->template->assign( 'recordset', $recordset );

            // Display the template
            $this->template->display();

        }

    }

    // Process the request
    YDInclude( 'YDF2_process.php' );

?>
