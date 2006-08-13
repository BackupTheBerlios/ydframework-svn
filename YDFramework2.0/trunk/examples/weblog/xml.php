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

        // Function to output the feed
        function outputXml( $type ) {
            $this->feed->outputXml( $type );
            $this->_logRequest();
        }

        // Default action
        function actionDefault() {
            $this->redirectToAction( 'rss' );
        }

        // RSS action
        function actionRss() {
            $this->initMainFeed();
            $this->outputXml( 'RSS2.0' );
        }

        // ATOM action
        function actionAtom() {
            $this->initMainFeed();
            $this->outputXml( 'ATOM' );
        }

        // RSS / Comments action
        function actionRssComments() {
            $this->initCommentsFeed();
            $this->outputXml( 'RSS2.0' );
        }

        // ATOM / Comments action
        function actionAtomComments() {
            $this->initCommentsFeed();
            $this->outputXml( 'ATOM' );
        }

        // RSS / Gallery action
        function actionRssGallery() {
            $this->initGalleryFeed();
            $this->outputXml( 'RSS2.0' );
        }

        // ATOM / Gallery action
        function actionAtomGallery() {
            $this->initGalleryFeed();
            $this->outputXml( 'ATOM' );
        }

        // Function to init the main feed
        function initMainFeed() {

            // Get the weblog items
            $items = $this->weblog->getPublicItems( YDConfig::get( 'max_syndicated_items', 15 ) );
            $items = array_reverse( $items );

            // Initialize the feed
            $this->feed = new YDFeedCreator();
            $this->feed->setTitle( YDConfig::get( 'weblog_title', 'Untitled Weblog' ) );
            $this->feed->setDescription( YDConfig::get( 'weblog_description', 'Untitled Weblog Description' ) );
            $this->feed->setLink( YDUrl::makeLinkAbsolute( 'index.php' ) );

            // Add the items
            foreach ( $items as $item ) {

                // Make the body
                $body = $item['body'];

                // Add the images if any
                if ( sizeof( $item['images'] ) > 0 ) {
                    $body .= '<p>';
                    foreach ( $item['images'] as $image ) {
                        $body .= '<img src="' . YDTplModLinkThumb( $image ) . '"/> ';
                    }
                    $body .= '</p>';
                }

                // Add it to the feed
                $this->feed->addItem(
                    $item['title'], YDTplModLinkItem( $item ),
                    YDTemplate_modifier_bbcode( $body ), YDTplModLinkItem( $item )
                );

            }

        }

        // Function to init the comments feed
        function initCommentsFeed() {

            // Get the weblog items
            $comments = $this->weblog->getComments( null, 'CREATED DESC', YDConfig::get( 'max_syndicated_items', 15 ), -1, true );

            // Initialize the feed
            $this->feed = new YDFeedCreator();
            $this->feed->setTitle( YDConfig::get( 'weblog_title', 'Untitled Weblog' ) . ' - ' . t('comments') );
            $this->feed->setDescription( YDConfig::get( 'weblog_description', 'Untitled Weblog Description' ) );
            $this->feed->setLink( YDUrl::makeLinkAbsolute( 'index.php' ) );

            // Add the items
            foreach ( $comments as $comment ) {
                $body = $comment['comment'] . "\n\n" . t('by') . ' ' . $comment['username'];
                $this->feed->addItem(
                    $comment['item_title'], YDTplModLinkItem( $comment['item_id'], '#comment' ),
                    YDTemplate_modifier_bbcode( $body ), YDTplModLinkItem( $comment['item_id'], '#comment' )
                );
            }

        }

        // Function to init the gallery feed
        function initGalleryFeed() {

            // Get the weblog items
            $items = $this->weblog->getPublicItems();

            // Initialize the feed
            $this->feed = new YDFeedCreator();
            $this->feed->setTitle( YDConfig::get( 'weblog_title', 'Untitled Weblog' ) . ' - ' . t('archives_gallery') );
            $this->feed->setDescription( YDConfig::get( 'weblog_description', 'Untitled Weblog Description' ) );
            $this->feed->setLink( YDUrl::makeLinkAbsolute( 'index.php' ) );

            // Add the items
            foreach ( $items as $item ) {
                if ( sizeof( $item['images'] ) > 0 ) {
                    $body = '';
                    foreach ( $item['images'] as $image ) {
                        $body .= '<img src="' . YDTplModLinkThumb( $image ) . '"/> ';
                    }
                    $this->feed->addItem(
                        $item['title'], YDTplModLinkItemGallery( $item ),
                        YDTemplate_modifier_bbcode( $body ), YDTplModLinkItemGallery( $item )
                    );
                }
            }

        }

    }

    // Process the request
    YDInclude( 'YDF2_process.php' );

?>