<?php

    // Include the Yellow Duck Framework
    include_once( dirname( __FILE__ ) . '/../include/YDWeblog_init.php' );

    // Class definition
    class index extends YDWeblogAdminRequest {

        // Class constructor
        function index() {

            // Initialize the parent
            $this->YDWeblogAdminRequest();

        }

        // Default action
        function actionDefault() {

            // Get the 5 newest items
            $items = $this->weblog->getItems( 5 );

            // Assign it to the template
            $this->tpl->assign( 'items', $items );

            // Get the install date
            $installDate   = $this->weblog->getInstallDate();
            $totalHits     = $this->weblog->getTotalHits();
            $daysOnline    = round( ( time() - $installDate ) / 86400 );
            $avg_hitsaday  = @ intval( $totalHits / $daysOnline );
            $totalItems    = $this->weblog->getStatsItemCount();
            $totalComments = $this->weblog->getStatsCommentCount();

            // Assign these to the template
            $this->tpl->assign( 'installDate',   $installDate );
            $this->tpl->assign( 'daysOnline',    $daysOnline );
            $this->tpl->assign( 'totalHits',     $totalHits );
            $this->tpl->assign( 'avg_hitsaday',  $avg_hitsaday );
            $this->tpl->assign( 'totalItems',    $totalItems );
            $this->tpl->assign( 'totalComments', $totalComments );

            // Display the template
            $this->display();

        }

    }

    // Process the request
    YDInclude( 'YDF2_process.php' );

?>
