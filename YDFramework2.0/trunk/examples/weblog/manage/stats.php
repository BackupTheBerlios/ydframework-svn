<?php

    // Include the Yellow Duck Framework
    include_once( dirname( __FILE__ ) . '/../include/YDWeblog_init.php' );

    // Class definition
    class stats extends YDWeblogAdminRequest {

        // Class constructor
        function stats() {

            // Initialize the parent
            $this->YDWeblogAdminRequest();

            // Register the graph template function
            $this->tpl->register_function( 'graph', array( & $this, 'templateGraph' ) );

        }

        // Special template function to draw a graph
        function templateGraph( $params ) {

            // Calculate the widths
            if ( empty( $params['width'] ) ) {
                $params['width'] = '1';
                $params['rest']  = '100';
            } else {
                $params['rest']  = 100 - intval( $params['width'] );
            }

            // Construct the graph
            return '<table width="100%"  cellspacing="0" cellpadding="0" border="0"><tr>'
                 . '<td class="adminGraphD" width="' . $params['width'] . '" height="10"></td>'
                 . '<td class="adminGraphL" width="' . $params['rest']  . '" height="10"></td>'
                 . '</tr></table>';

        }

        // Default action
        function actionDefault() {

            // Get the install date
            $installDate   = $this->weblog->getInstallDate();
            $daysOnline    = round( ( time() - $installDate ) / 86400 );
            $totalHits     = $this->weblog->getTotalHits();
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

            // Get the list of the last 6 months
            $last6Months  = $this->weblog->getStatsMonths();
            $last7Days    = $this->weblog->getStatsDays();
            $top10Urls    = $this->weblog->getStatsUrls();
            $browserStats = $this->weblog->getStatsBrowser();
            $osStats      = $this->weblog->getStatsOs();

            // Assign these to the template
            $this->tpl->assign( 'last6Months',  $this->_calculateGraph( $last6Months ) );
            $this->tpl->assign( 'last7Days',    $this->_calculateGraph( $last7Days ) );
            $this->tpl->assign( 'top10Urls',    $this->_calculateGraph( $top10Urls ) );
            $this->tpl->assign( 'browserStats', $this->_calculateGraph( $browserStats ) );
            $this->tpl->assign( 'osStats',      $this->_calculateGraph( $osStats ) );

            // Display the template
            $this->display();

        }

        // The showMonths action
        function actionShowMonths() {

            // Get the list of all the months
            $months = $this->weblog->getStatsMonths( -1 );
            $this->tpl->assign( 'months', $this->_calculateGraph( $months ) );

            // Display the template
            $this->display();

        }

        // The showDays action
        function actionShowDays() {

            // Get the list of all the days
            $days = $this->weblog->getStatsDays( -1 );
            $this->tpl->assign( 'days', $this->_calculateGraph( $days ) );

            // Display the template
            $this->display();

        }

        // The showUrls action
        function actionShowUrls() {

            // Get the list of all the days
            $urls = $this->weblog->getStatsUrls( -1 );
            $this->tpl->assign( 'urls', $this->_calculateGraph( $urls ) );

            // Display the template
            $this->display();

        }

        // Function to calculate the width of the graph elements
        function _calculateGraph( $data, $field='hits' ) {
            if ( sizeof( $data ) > 0 ) {
                $maxHits = 0;
                foreach ( $data as $key=>$val ) {
                    if ( $val[$field] > $maxHits ) {
                        $maxHits = $val[$field];
                    }
                }
                foreach ( $data as $key=>$val ) {
                    $data[$key][$field .'_pct'] = intval( ( $val[$field] / $maxHits ) * 100 ) . '%';
                    if ( $data[$key][$field .'_pct'] == '0' ) {
                        $data[$key][$field .'_pct'] = '1';
                    }
                }
            }
            return $data;
        }

    }

    // Process the request
    YDInclude( 'YDF2_process.php' );

?>
