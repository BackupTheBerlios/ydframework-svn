<?php

	// Standard include
	require_once( dirname( __FILE__ ) . '/../YDFramework2/YDF2_init.php' );

	// Includes
	require_once( 'YDRequest.php' );
	require_once( 'YDUrl.php' );
	require_once( 'YDDebugUtil.php' );
	require_once( 'YDArrayUtil.php' );
	require_once( 'YDFeedCreator.php' );

	// Class definition
	class pbaseRequest extends YDRequest {

		// Class constructor
		function pbaseRequest() {

			// Initialize the parent
			$this->YDRequest();

			// The list of galleries
			$this->galleries = array(
				array(
					'id' => 1, 'title' => 'SYCOD Race II 2004', 'thumbnail' => '29526953',
					'url' => 'http://www.pbase.com/bba/odk2_2004&page=all',
				), array(
					'id' => 2, 'title' => 'Krab Rally 2004', 'thumbnail' => '27371231',
					'url' => 'http://www.pbase.com/bba/krab04&page=all',
				), array(
					'id' => 3, 'title' => 'Belgian Championship 2003', 'thumbnail' => '18158440',
					'url' => 'http://www.pbase.com/bba/bc&page=all',
				), array(
					'id' => 4, 'title' => 'Krab Rally 2003', 'thumbnail' => '14133887',
					'url' => 'http://www.pbase.com/bba/krab_zaterdag&page=all',
				), array(
					'id' => 5, 'title' => 'Beachshop Testdag 02/05/2004', 'thumbnail' => '28557554',
					'url' => 'http://www.pbase.com/beachshop/testdag_mei_2004&page=all',
				), array(
					'id' => 6, 'title' => 'Les Hemmes 27280304', 'thumbnail' => '27409483',
					'url' => 'http://www.pbase.com/beachshop/les_hemmes__27280304&page=all',
					
				), array(
					'id' => 7, 'title' => 'Libre 4 Wheels Buggy', 'thumbnail' => '27446851',
					'url' => 'http://www.pbase.com/beachshop/4w_buggy',
				),
			);

			// If a gallery is selected, get the data
			if ( isset( $_GET['gal'] ) ) {

				// Get the ID of the gallery
				$this->gallery = null;

				// Check if the specified gallery exists
				foreach ( $this->galleries as $gallery ) {

					// Check if the ID matches
					if ( $gallery['id'] == $_GET['gal'] ) {

						// Save the gallery ID
						$this->gallery = $gallery;

						// Add it to the template
						$this->setVar( 'gallery', $gallery );

					}

				}

				// Get the gallery contents if it exists
				if ( $this->gallery ) {

					// The fixed url of our gallery
					$this->url = $this->gallery['url'];
					$this->setVar( 'url', $this->url );
					
					// The regex pattern to match the images
					$this->pattern = '/www\.pbase\.com\/image\/([0-9]+)/ism';

					// Get the list of images
					$objUrl = new YDUrl( $this->url );
					$contents = $objUrl->getContentsWithRegex( $this->pattern );
					$this->images = $contents[1];

				}

			}

		}

		// Default action
		function actionDefault() {

			// Add the list of galleries
			$this->setVar( 'galleries', YDArrayUtil::convertToTable( $this->galleries, 4, true ) );

			// Show the template
			$this->outputTemplate();

		}

		// ShowGallery action
		function actionGallery() {

			// Redirect to default if no gallery
			if ( ! $this->gallery ) { 
				$this->redirectToAction();
			}

			// Add the template variables
			$this->setVar( 'images', YDArrayUtil::convertToTable( $this->images, 4, true ) );

			// Output the template
			$this->outputTemplate();

		}

		// ShowImage action
		function actionImage() {

			// Redirect to the gallery if no matching image
			// TODO: make it redirect correctly!
			if ( ! in_array( $_GET['img'], $this->images ) ) { 
				$this->redirectToAction();
			}

			// Get current image number
			$imageCurrent = array_search( $_GET['img'], $this->images );

			// Start with no previous and next image
			$imagePrevious = null;
			$imageNext = null;

			// Get the index of the previous image
			if ( $imageCurrent != 0 ) {
				$imagePrevious = $this->images[ $imageCurrent - 1 ];
			}

			// Get the index if the next image
			if ( $imageCurrent < sizeof( $this->images ) ) {
				$imageNext = $this->images[ $imageCurrent + 1 ];
			}

			// Add them to the template
			$this->setVar( 'imagePrevious', $imagePrevious );
			$this->setVar( 'imageCurrent', $_GET['img'] );
			$this->setVar( 'imageNext', $imageNext );

			// Output the template
			$this->outputTemplate();

		}

	}

	// Process the request
	require_once( dirname( __FILE__ ) . '/../YDFramework2/YDF2_process.php' );

?>
