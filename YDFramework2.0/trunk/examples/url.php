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

			// Get the contents
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

	}

	// Process the request
	YDInclude( 'YDF2_process.php' );

?>