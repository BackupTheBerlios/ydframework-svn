<?php

    // Standard include
    require_once( dirname( __FILE__ ) . '/../YDFramework2/YDF2_init.php' );

    // Includes
    YDInclude( 'YDUrl.php' );
    YDInclude( 'YDRequest.php' );

    // Class definition
    class url extends YDRequest {

        // Class constructor
        function url() {
            $this->YDRequest();
        }

        // Default action
        function actionDefault() {

            // Create the URL object
            $url = new YDUrl( 'http://www.yellowduck.be/directory/test/index.xml' );

            // The different parts
            echo( '<br>Original URL: ' . $url->_url );
            echo( '<br>URL: ' . $url->getUrl() );
            echo( '<br>URI: ' . $url->getUri() );
            echo( '<br>Scheme: ' . $url->getScheme() );
            echo( '<br>Host: ' . $url->getHost() );
            echo( '<br>Port: ' . $url->getPort() );
            echo( '<br>User: ' . $url->getUser() );
            echo( '<br>Password: ' . $url->getPassword() );
            echo( '<br>Path: ' . $url->getPath() );
            echo( '<br>Path segments: ' . implode( ', ', $url->getPathSegments() ) );
            echo( '<br>Path directories: ' . implode( ', ', $url->getPathDirectories() ) );
            echo( '<br>isDirectory( test ): ' . var_export( $url->isDirectory( 'test' ), 1 ) );
            echo( '<br>isDirectory( xx ): ' . var_export( $url->isDirectory( 'xx' ), 1 ) );
            echo( '<br>Query: ' . var_export( $url->getQuery(), 1 ) );
            echo( '<br>Fragment: ' . $url->getFragment() );

            // Create the URL object
            $url = new YDUrl( 'http://pieter@www.yellowduck.be/directory/test/index?x[]=22&x[]=23#22' );

            // The different parts
            echo( '<br><br>Original URL: ' . $url->_url );
            echo( '<br>URL: ' . $url->getUrl() );
            echo( '<br>URI: ' . $url->getUri() );
            echo( '<br>Scheme: ' . $url->getScheme() );
            echo( '<br>Host: ' . $url->getHost() );
            echo( '<br>Port: ' . $url->getPort() );
            echo( '<br>User: ' . $url->getUser() );
            echo( '<br>Password: ' . $url->getPassword() );
            echo( '<br>Path: ' . $url->getPath() );
            echo( '<br>Path segments: ' . implode( ', ', $url->getPathSegments() ) );
            echo( '<br>Path directories: ' . implode( ', ', $url->getPathDirectories() ) );
            echo( '<br>Query: ' . var_export( $url->getQuery(), 1 ) );
            echo( '<br>Fragment: ' . $url->getFragment() );

            // Create the URL object
            $url = new YDUrl( 'http://pieter:yellowduck@www.yellowduck.be:8080/directory/test/?do=x&id=1#10' );

            // The different parts
            echo( '<br><br>Original URL: ' . $url->_url );
            echo( '<br>URL: ' . $url->getUrl() );
            echo( '<br>URI: ' . $url->getUri() );
            echo( '<br>Scheme: ' . $url->getScheme() );
            echo( '<br>Host: ' . $url->getHost() );
            echo( '<br>Port: ' . $url->getPort() );
            echo( '<br>User: ' . $url->getUser() );
            echo( '<br>Password: ' . $url->getPassword() );
            echo( '<br>Path: ' . $url->getPath() );
            echo( '<br>Path segments: ' . implode( ', ', $url->getPathSegments() ) );
            echo( '<br>Path directories: ' . implode( ', ', $url->getPathDirectories() ) );
            echo( '<br>Query: ' . var_export( $url->getQuery(), 1 ) );
            echo( '<br>Fragment: ' . $url->getFragment() );
            echo( '<br>Getting query variable do: ' . $url->getQueryVar( 'do' ) );
            echo( '<br>Setting query variable do to y: ' . $url->setQueryVar( 'do', 'y' ) );
            echo( '<br>New URL: ' . $url->getUrl() );
            echo( '<br>Deleting query variable do: ' . $url->deleteQueryVar( 'do' ) );
            echo( '<br>New URL: ' . $url->getUrl() );

            // Create the URL object
            $url = new YDUrl( 'http://www.yellowduck.be/rss.xml' );

            // The different parts
            echo( '<br><br>Original URL: ' . $url->_url );
            echo( '<br>URL: ' . $url->getUrl() );
            echo( '<br>URI: ' . $url->getUri() );
            echo( '<br>Scheme: ' . $url->getScheme() );
            echo( '<br>Host: ' . $url->getHost() );
            echo( '<br>Port: ' . $url->getPort() );
            echo( '<br>User: ' . $url->getUser() );
            echo( '<br>Password: ' . $url->getPassword() );
            echo( '<br>Path: ' . $url->getPath() );
            echo( '<br>Path segments: ' . implode( ', ', $url->getPathSegments() ) );
            echo( '<br>Path directories: ' . implode( ', ', $url->getPathDirectories() ) );
            echo( '<br>Query: ' . var_export( $url->getQuery(), 1 ) );
            echo( '<br>Fragment: ' . $url->getFragment() );

            // Test the getPathSubdirectories function
            $url = new YDUrl( 'http://www.yellowduck.be/ydf2/forum/cool.html' );
            YDDebugUtil::dump( $url->getUrl() );
            YDDebugUtil::dump( $url->getPathSubdirectories('ydf2'), "getPathSubdirectories('ydf2')" );
            YDDebugUtil::dump( $url->getPathSubdirectories('forum'), "getPathSubdirectories('forum')" );
            YDDebugUtil::dump( $url->getPathSubdirectories('test'), "getPathSubdirectories('test')" );
            $url = new YDUrl( 'http://www.yellowduck.be/ydf2/forum/ydf2/forum/cool.html' ); 
            YDDebugUtil::dump( $url->getUrl() );
            YDDebugUtil::dump( $url->getPathSubdirectories('ydf2'), "getPathSubdirectories('ydf2')" );
            YDDebugUtil::dump( $url->getPathSubdirectories('forum'), "getPathSubdirectories('forum')" );
            YDDebugUtil::dump( $url->getPathSubdirectories('test'), "getPathSubdirectories('test')" );

            // Test the getPathSubsegments function
            $url = new YDUrl( 'http://www.yellowduck.be/ydf2/forum/cool.html' );
            YDDebugUtil::dump( $url->getUrl() );
            YDDebugUtil::dump( $url->getPathSubsegments('ydf2'), "getPathSubsegments('ydf2')" );
            YDDebugUtil::dump( $url->getPathSubsegments('forum'), "getPathSubsegments('forum')" );
            YDDebugUtil::dump( $url->getPathSubsegments('test'), "getPathSubsegments('test')" );
            $url = new YDUrl( 'http://www.yellowduck.be/ydf2/forum/ydf2/forum/cool.html' ); 
            YDDebugUtil::dump( $url->getUrl() );
            YDDebugUtil::dump( $url->getPathSubsegments('ydf2'), "getPathSubsegments('ydf2')" );
            YDDebugUtil::dump( $url->getPathSubsegments('forum'), "getPathSubsegments('forum')" );
            YDDebugUtil::dump( $url->getPathSubsegments('test'), "getPathSubsegments('test')" );

            // Get the contents
            $url = new YDUrl( 'http://www.yellowduck.be/rss.xml' );
            YDDebugUtil::dump( $url->getContents(), 'URL contents' );

        }

        // Action to retrieve an image
        function actionImage1() {

            // Create the URL object
            $url = new YDUrl( 'http://www.yellowduck.be/images/uploads/kuifjeseend.jpg' );

            // Get the contents
            header( 'Content-type: image/jpeg' );
            echo( $url->getContents() );

        }

        // Function to get the header for a URL
        function actionHeaders() {

            // Create the URL object
            $url = new YDUrl( 'http://www.yellowduck.be/index.xml' );

            // Dump the headers
            YDDebugUtil::dump( $url->getHeaders() );

        }

        // Function to get the status for a URL
        function actionStatus() {

            // Create the URL object
            $url = new YDUrl( 'http://www.yellowduck.be/index.xml' );

            // Dump the headers
            YDDebugUtil::dump( $url->getStatus() );

        }

        // Default action
        function actionAlter() {

            // Create the URL object
            $url = new YDUrl( 'http://www.yellowduck.be:8080/directory/test/?do=x&id=1#10' );

            // Show the original URL
            YDDebugUtil::dump( $url->getUrl(), 'The original URL' );

            // Get the contents of a query variable
            $do = $url->getQueryVar( 'do', 'y' );

            // Update a query variable
            $url->setQueryVar( 'do', 'y' );

            // setQueryVar can also be used to add a query variable
            $url->setQueryVar( 'new', 'value' );

            // Deleting a query variable
            $url->deleteQueryVar( 'new' );

            // Set a named part
            $url->setNamedPart( 'host', 'yellowduck.be' );
            $url->setNamedPart( 'user', 'pieter' );
            $url->setNamedPart( 'pass', 'kermit' );
            $url->setNamedPart( 'port', '8081' );
            $url->setNamedPart( 'path', '/dir/index.php' );
            $url->setNamedPart( 'fragment', '12' );
            $url->setQueryVar( 'id', '22' );

            // Show the new URL
            YDDebugUtil::dump( $url->getUrl(), 'The new URL' );

        }

    }

    // Process the request
    YDInclude( 'YDF2_process.php' );

?>