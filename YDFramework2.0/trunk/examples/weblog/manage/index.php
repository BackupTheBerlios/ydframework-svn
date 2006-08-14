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
            $items = $this->weblog->getItems( 5, -1, 'created desc', 'AND is_draft = 0 AND allow_comments = 1' );

            // Assign it to the template
            $this->tpl->assign( 'items', $items );

            // Get the global statistics
            $installDate   = $this->weblog->getInstallDate();
            $daysOnline    = round( ( time() - $installDate ) / 86400 );
            $totalItems    = $this->weblog->getStatsItemCount();
            $totalComments = $this->weblog->getStatsCommentCount();

            // Assign these to the template
            $this->tpl->assign( 'installDate',   $installDate );
            $this->tpl->assign( 'daysOnline',    $daysOnline );
            $this->tpl->assign( 'totalItems',    $totalItems );
            $this->tpl->assign( 'totalComments', $totalComments );

            // Check if we need to log statistics or not
            if ( YDConfig::get( 'keep_stats', true ) ) {

                // Get the averages
                $totalHits     = $this->weblog->getTotalHits();
                $avg_hitsaday  = @ intval( $totalHits / $daysOnline );

                // Assign these to the template
                $this->tpl->assign( 'totalHits',     $totalHits );
                $this->tpl->assign( 'avg_hitsaday',  $avg_hitsaday );

            }

            // Display the template
            $this->display();

        }

    }

    // Process the request
    YDInclude( 'YDF2_process.php' );

?>
