<?php

    // Include the Yellow Duck Framework
    include_once( dirname( __FILE__ ) . '/include/YDWeblog_init.php' );

    // Includes
    YDInclude( 'YDFeedCreator.php' );

    // Class definition
    class xml extends YDWeblogRequest {

        // Class constructor
        function xml() {

            // Initialize the parent
            $this->YDWeblogRequest();

        }

        // Default action
        function actionDefault() {
            $this->redirectToAction( 'rss' );
        }

        // RSS action
        function actionRss() {
            $this->initMainFeed();
            $this->feed->outputXml( 'RSS2.0' );
        }

        // ATOM action
        function actionAtom() {
            $this->initMainFeed();
            $this->feed->outputXml( 'ATOM' );
        }

        // RSS / Comments action
        function actionRssComments() {
            $this->initCommentsFeed();
            $this->feed->outputXml( 'RSS2.0' );
        }

        // ATOM / Comments action
        function actionAtomComments() {
            $this->initCommentsFeed();
            $this->feed->outputXml( 'ATOM' );
        }

        // Function to init the main feed
        function initMainFeed() {

            // Get the weblog items
            $items = $this->weblog->getItems( YDConfig::get( 'max_syndicated_items', 15 ) );
            $items = array_reverse( $items );

            // Initialize the feed
            $this->feed = new YDFeedCreator();
            $this->feed->setTitle( YDConfig::get( 'weblog_title', 'Untitled Weblog' ) );
            $this->feed->setDescription( YDConfig::get( 'weblog_description', 'Untitled Weblog Description' ) );
            $this->feed->setLink( YDUrl::makeLinkAbsolute( 'index.php' ) );

            // Add the items
            foreach ( $items as $item ) {
                $this->feed->addItem( $item['title'], YDTplModLinkItem( $item ), YDTplModBBCode( $item['body'] ) );
            }

        }

        // Function to init the comments feed
        function initCommentsFeed() {

            // Get the weblog items
            $comments = $this->weblog->getComments( null, 'CREATED DESC', YDConfig::get( 'max_syndicated_items', 15 ) );

            // Initialize the feed
            $this->feed = new YDFeedCreator();
            $this->feed->setTitle( YDConfig::get( 'weblog_title', 'Untitled Weblog' ) . ' - ' . t('comments') );
            $this->feed->setDescription( YDConfig::get( 'weblog_description', 'Untitled Weblog Description' ) );
            $this->feed->setLink( YDUrl::makeLinkAbsolute( 'index.php' ) );

            // Add the items
            foreach ( $comments as $comment ) {
                $body = $comment['comment'] . "\n\n" . t('by') . ' ' . $comment['username'];
                $this->feed->addItem(
                    $comment['item_title'], YDTplModLinkItem( $comment['item_id'], '#comment' ), YDTplModBBCode( $body )
                );
            }

        }

    }

    // Process the request
    YDInclude( 'YDF2_process.php' );

?>