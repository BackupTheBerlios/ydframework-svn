<?php

    // Standard include
    require_once( dirname( __FILE__ ) . '/../YDFramework2/YDF2_init.php' );

    // Class definition
    class feedcreator1Request extends YDRequest {

        // Class constructor
        function feedcreator1Request() {

            // Initialize the parent class
            $this->YDRequest();

        }

        // Default action
        function actionDefault() {

            // Create the feed
            $fc = new YDFeedCreator();
            $fc->setTitle( 'Yellow Duck Framework' );
            $fc->setDescription( 'News about the Yellow Duck Framework' );
            $fc->setLink( 'http://www.yellowduck.be/ydf2/' );
            $fc->addItem(
                'Title 1', 'http://www.yellowduck.be/ydf2/#1', 'Description 1'
            );
            $fc->addItem(
                'Title 2', 'http://www.yellowduck.be/ydf2/#2', 'Description 2'
            );
            $fc->addItem(
                'Title 3', 'http://www.yellowduck.be/ydf2/#2', 'Description 3'
            );

            // Output the feed in the default format (RSS2.0)
            echo( '<h3>RSS 2.0</h3>' );
            YDDebugUtil::dump( $fc->toXml() );

            // Output the feed in RSS1.0
            echo( '<h3>RSS 1.0</h3>' );
            YDDebugUtil::dump( $fc->toXml( 'RSS1.0' ) );

            // Output the feed in ATOM
            echo( '<h3>ATOM</h3>' );
            YDDebugUtil::dump( $fc->toXml( 'PIE0.1' ) );

            // Output the feed in MBOX
            echo( '<h3>MBOX</h3>' );
            YDDebugUtil::dump( $fc->toXml( 'MBOX' ) );

            // Output the feed in MBOX
            echo( '<h3>OPML</h3>' );
            YDDebugUtil::dump( $fc->toXml( 'OPML' ) );

        }

        // Default action
        function actionAtom() {

            // Create the feed
            $fc = new YDFeedCreator();
            $fc->setTitle( 'Yellow Duck Framework' );
            $fc->setDescription( 'News about the Yellow Duck Framework' );
            $fc->setLink( 'http://www.yellowduck.be/ydf2/' );
            $fc->addItem(
                'Title 1', 'http://www.yellowduck.be/ydf2/#1', 'Description 1'
            );
            $fc->addItem(
                'Title 2', 'http://www.yellowduck.be/ydf2/#2', 'Description 2'
            );
            $fc->addItem(
                'Title 3', 'http://www.yellowduck.be/ydf2/#2', 'Description 3'
            );

            // Output the feed in the default format (RSS2.0)
            $fc->outputXml( 'ATOM' );

        }

    }

    // Process the request
    require_once( dirname( __FILE__ ) . '/../YDFramework2/YDF2_process.php' );

?>