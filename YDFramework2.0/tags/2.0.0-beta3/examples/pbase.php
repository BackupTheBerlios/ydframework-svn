<?php

	// Some defines we need to override
	define( 'YD_HTTP_CACHE_USEHEAD', 0 );

	// Standard include
	require_once( dirname( __FILE__ ) . '/../YDFramework2/YDF2_init.php' );

	// Includes
	require_once( 'YDUrl.php' );
	require_once( 'YDRequest.php' );
	require_once( 'YDTemplate.php' );
	require_once( 'YDFeedCreator.php' );

	// Class definition
	class pbase extends YDRequest {

		// Class constructor
		function pbase() {

			// Initialize the parent
			$this->YDRequest();

			// Initialize the template
			$this->template = new YDTemplate();

			// Set the home url
			$this->homeUrl = 'http://www.pbase.com/beachshop/';
			$this->template->assign( 'homeUrl', $this->homeUrl );

			// Title for the image gallery
			$this->template->assign( 'galTitle', 'Beachshop Pictures' );

			// Start with no galleries and no selected gallery
			$this->galleries = array();
			$this->gallery = null;

			// Download the gallery index
			$pIndex = '/HREF="http:\/\/www.pbase.com\/beachshop\/(.*?)" class="thumbnail".*? src="http:\/\/.*?.image.pbase.com\/.*?\/.*?\/small\/([0-9]+).*?alt="(.*?)">/ism';
			$objUrl = new YDUrl( $this->homeUrl );
			$contents = $objUrl->getContentsWithRegex( $pIndex );

			// Loop over the matching patterns to construct the galleries list
			for ( $i=0; $i < sizeof( $contents[1] ); $i++ ) {

				// Initialize the array if needed
				if ( ! isset( $this->galleries[ $i ] ) ) { $this->galleries[ $i ] = array(); }

				// Fill in the details
				$this->galleries[ $i ]['title'] = $contents[3][$i];
				$this->galleries[ $i ]['url'] = $this->homeUrl . $contents[1][$i] . '&page=all';
				$this->galleries[ $i ]['thumbnail'] = $this->homeUrl . 'image/' . $contents[2][$i] . '/small.jpg';
				$this->galleries[ $i ]['id'] = md5( $this->galleries[ $i ]['url'] );

				// Get the contents of the URL
				$pGallery = '/www\.pbase\.com\/image\/([0-9]+)/ism';
				$objUrl = new YDUrl( $this->galleries[ $i ]['url'] );
				$pcontents = $objUrl->getContentsWithRegex( $pGallery );

				// Add the list of images
				$this->galleries[ $i ]['images'] = $pcontents[1];

			}

			// If a gallery is selected, assign it to the gallery variable
			if ( isset( $_GET['gal'] ) ) {
				foreach ( $this->galleries as $gallery ) {
					if ( $gallery['id'] == $_GET['gal'] ) {
						$this->gallery = $gallery;
						$this->template->assign( 'gallery', $gallery );
					}
				}
			}

		}

		// Default action
		function actionDefault() {

			// Add the list of galleries
			$this->template->assign( 'galleries', YDArrayUtil::convertToTable( $this->galleries, 4, true ) );

			// Output the template
			$this->template->display();

		}

		// ShowGallery action
		function actionGallery() {

			// Redirect to default if no gallery
			if ( ! $this->gallery ) {
				$this->redirectToAction();
			}

			// Add the template variables
			$this->template->assign( 'images', YDArrayUtil::convertToTable( $this->gallery['images'], 4, true ) );

			// Output the template
			$this->template->display();

		}

		// ShowImage action
		function actionImage() {

			// Redirect to the gallery if no matching image
			if ( ! in_array( $_GET['img'], $this->gallery['images'] ) ) {
				$this->redirectToAction();
			}

			// Get current image number
			$imageCurrent = array_search( $_GET['img'], $this->gallery['images'] );

			// Start with no previous and next image
			$imagePrevious = null;
			$imageNext = null;

			// Get the index of the previous image
			if ( $imageCurrent != 0 ) {
				$imagePrevious = $this->gallery['images'][ $imageCurrent - 1 ];
			}

			// Get the index if the next image
			if ( $imageCurrent < ( sizeof( $this->gallery['images'] ) - 1 ) ) {
				$imageNext = $this->gallery['images'][ $imageCurrent + 1 ];
			}

			// Add them to the template
			$this->template->assign( 'imagePrevious', $imagePrevious );
			$this->template->assign( 'imageCurrent', $_GET['img'] );
			$this->template->assign( 'imageNext', $imageNext );

			// Output the template
			$this->template->display();

		}

	}

	// Process the request
	require_once( dirname( __FILE__ ) . '/../YDFramework2/YDF2_process.php' );

?>
