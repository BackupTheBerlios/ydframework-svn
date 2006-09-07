<?php

    // Include the Yellow Duck Framework
    include_once( dirname( __FILE__ ) . '/include/YDWeblog_init.php' );

    // Class definition
    class category extends YDWeblogRequest {

        // Class constructor
        function category() {
            $this->YDWeblogRequest();
        }

        // Default action
        function actionDefault() {

            // Get the ID from the query string
            $id = $this->getIdFromQS();

            // Get the weblog details and go to the default view if none is matched
            $category  = $this->weblog->getCategoryById( $id );
            $this->redirectIfMissing( $category );

            // Get the category items
            $items = $this->weblog->getItemsByCategoryId( $id );

            // Assign the variables to the template
            $this->tpl->assign( 'category', $category );
            $this->tpl->assign( 'items',    $items );
            $this->tpl->assign(
                'title', t('archive_for_the') . '\'' . $category['title'] . '\' ' .  strtolower( t('category') )
            );

            // Display the template
            $this->display();

        }

    }

    // Process the request
    YDInclude( 'YDF2_process.php' );

?>
