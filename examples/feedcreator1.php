<?php

    // Standard include
    require_once( dirname( __FILE__ ) . '/../YDFramework2/YDF2_init.php' );

    // Class definition
    class feedcreator1Request extends YDRequest {

        // Class constructor
        function feedcreator1Request() {

            // Initialize the parent class
            $this->YDRequest();

            // We use the same feed for all the examples
            $this->fc = new YDFeedCreator();
            $this->fc->setTitle( 'Yellow Duck Framework' );
            $this->fc->setDescription( 'News about the Yellow Duck Framework' );
            $this->fc->setLink( 'http://www.yellowduck.be/ydf2/' );
            $this->fc->addItem(
                'Title 1',
                'http://www.yellowduck.be/ydf2/#1',
                'Description 1'
            );
            $this->fc->addItem(
                'Title 2',
                'http://www.yellowduck.be/ydf2/#2',
                'Description <b>2</b>'
            );
            $this->fc->addItem(
                '<h3>Title 3</h3>',
                'http://www.yellowduck.be/ydf2/#2',
                'Description 3'
            );

        }

        // Default action
        function actionDefault() {

            // Output the feed in RSS0.91
            echo( '<h3>RSS 0.91</h3>' );
            echo( $this->formatXml( $this->fc->toXml( 'RSS0.91' ) ) );

            // Output the feed in RSS1.0
            echo( '<h3>RSS 1.0</h3>' );
            echo( $this->formatXml( $this->fc->toXml( 'RSS1.0' ) ) );

            // Output the feed in the default format (RSS2.0)
            echo( '<h3>RSS 2.0 (default)</h3>' );
            echo( $this->formatXml( $this->fc->toXml() ) );

            // Output the feed in ATOM
            echo( '<h3>ATOM</h3>' );
            echo( $this->formatXml( $this->fc->toXml( 'ATOM' ) ) );

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

        // Function to output preformatted XML
        function formatXml( $xml ) {

            // Convert to pre and HTML entities
            $xml = '<pre>' . htmlentities( $xml ) . '</pre>';

            // Color code the xml
            $xml = str_replace( '&lt;', '<font color="darkred">&lt;', $xml );
            $xml = str_replace( '&gt;', '&gt;</font>', $xml );
            
            // Return the XML
            return $xml;

        }

    }

    // Process the request
    require_once( dirname( __FILE__ ) . '/../YDFramework2/YDF2_process.php' );

?>