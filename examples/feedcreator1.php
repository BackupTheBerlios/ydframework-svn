<?php

    /*
     *  This examples demonstrates:
     *  - How to create XML feeds using YDFeedCreator
     */

    // Standard include
    require_once( dirname( __FILE__ ) . '/../YDFramework2/YDF2_init.php' );

    // Includes
    require_once( 'YDRequest.php' );
    require_once( 'YDFeedCreator.php' );

    // Class definition
    class feedcreator1Request extends YDRequest {

        // Class constructor
        function feedcreator1Request() {

            // Initialize the parent class
            $this->YDRequest();

            // We use the same feed for all the examples
            $this->fc = new YDFeedCreator();
            $this->fc->setTitle( 'Yellow Duck Framework & Co.' );
            $this->fc->setDescription( 'News about the Yellow Duck Framework & Co.' );
            $this->fc->setLink( $this->getCurrentUrl() );
            $this->fc->addItem(
                'Title 1 & Co.',
                $this->getCurrentUrl() . '#1',
                'Description 1 & Co.'
            );
            $this->fc->addItem(
                'Title 2 & Co.',
                $this->getCurrentUrl() . '#2',
                'Description <b>2</b> & Co.'
            );
            $this->fc->addItem(
                '<h3>Title 3 & Co.</h3>',
                $this->getCurrentUrl() . '#3',
                'Description 3 & Co.'
            );
            $this->fc->addItem(
                '<h3>Title 4 & Co.</h3>',
                $this->getCurrentUrl() . '#4',
                '<img src="fsimage1.jpg" />'
            );

        }

        // Default action
        function actionDefault() {

            // Output the feed in RSS0.91
            echo( '<h3>RSS 0.91</h3>' );
            echo( $this->fc->getColoredXml( 'RSS0.91' ) );

            // Output the feed in RSS1.0
            echo( '<h3>RSS 1.0</h3>' );
            echo( $this->fc->getColoredXml( 'RSS1.0' ) );

            // Output the feed in the default format (RSS2.0)
            echo( '<h3>RSS 2.0 (default)</h3>' );
            echo( $this->fc->getColoredXml() );

            // Output the feed in ATOM
            echo( '<h3>ATOM</h3>' );
            echo( $this->fc->getColoredXml( 'ATOM' ) );

        }

        // Output the feed in RSS 0.91
        function actionRss091() {
            $this->fc->outputXml( 'RSS0.91' );
        }

        // Output the feed in RSS 1.0
        function actionRss10() {
            $this->fc->outputXml( 'RSS1.0' );
        }

        // Output the feed in RSS 2.0
        function actionRss20() {
            $this->fc->outputXml( 'RSS2.0' );
        }

        // Output the feed in ATOM
        function actionAtom() {
            $this->fc->outputXml( 'ATOM' );
        }

    }

    // Process the request
    require_once( dirname( __FILE__ ) . '/../YDFramework2/YDF2_process.php' );

?>