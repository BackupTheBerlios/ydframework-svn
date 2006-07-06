<?php

    // Include the Yellow Duck Framework
    include_once( dirname( __FILE__ ) . '/../include/YDWeblog_init.php' );

    // Includes
    YDInclude( 'YDFileSystem.php' );

    // Class definition
    class images extends YDWeblogAdminRequest {

        // Class constructor
        function images() {

            // Initialize the parent
            $this->YDWeblogAdminRequest();

            // The media files directory object
            $this->dir_rel = '../' . YDConfig::get( 'dir_uploads', 'uploads' ) . '/';

            // Create the image upload form
            $form_url = YD_SELF_SCRIPT . '?do=add';
            if ( isset( $_GET['field'] ) ) {
                $form_url .= '&field=' . $_GET['field'];
            }
            $this->form = new YDWeblogForm( 'uploadForm', 'POST', YD_SELF_SCRIPT . '?do=add' );
            $this->form = new YDWeblogForm( 'uploadForm', 'POST', $form_url );
            $this->form->addElement( 'file', 'image', t('image'), array( 'class' => 'tfM' ) );
            $this->form->addElement( 'hidden', 'action' );
            $this->form->addElement( 'submit', '_cmdSubmit', t('OK'), array( 'class' => 'button' ) );

        }

        // The default action
        function actionDefault() {

            // Set the action for the upload form
            $this->form->setDefault( 'action', $this->getActionName() );

            // Get the list of images
            $images = $this->getImages();

            // Get the pagesize and current page from the URL
            $page = @ $_GET['page'];

            // Create the YDRecordSet object
            $images = new YDRecordSet( $images, $page, YDConfig::get( 'YD_DB_DEFAULTPAGESIZE', 20 ) );

            // Assign it to the template
            $this->tpl->assign( 'uploads_dir', $this->dir_rel );
            $this->tpl->assign( 'images',      $images );

            // Assign the quick upload form to the template
            $this->tpl->assignForm( 'form', $this->form );

            // Output the template
            $this->display();

        }

        // The default action
        function actionAdd() {

            // We return to the default action
            $action = 'default';

            // Process the form
            if ( $this->form->validate() ) {

                // Move the uploaded file
                $file = $this->form->getElement( 'image' );
                if ( $file->isUploaded() ) {

                    // Get the new filename
                    $filename = YDStringUtil::stripSpecialCharacters( $file->getBaseName() );

                    // Move the upload
                    $file->moveUpload( $this->dir_rel, $filename );

                    // Check if it's an image
                    $fileObj = new YDFSFile( $this->dir_rel . $file->getBaseName() );
                    if ( ! $fileObj->isImage() ) {
                        @unlink( $this->dir_rel . $file->getBaseName() );
                    }

                    // Create the thumbnails
                    $thumb = new YDFSImage( $this->dir_rel . $file->getBaseName() );
                    $thumb->_createThumbnail( 100, 100, true );
                    $thumb->_createThumbnail(  48,  48, true );

                }

                // Get the name of the action
                $action = $this->form->getValue( 'action' );

            }

            // Redirect to the list view
            $destination = YD_SELF_SCRIPT . '?do=' . $action;
            if ( isset( $_GET['field'] ) ) {
                $destination .= '&field=' . $_GET['field'];
            }
            $this->redirect( $destination );

        }

        // The default action
        function actionDelete() {

            // Get the image
            $image = $this->getImage();
            $this->redirectIfMissing( $image );

            // Delete the file
            @unlink( realpath( $this->dir_rel . $image ) );

            // Forward to the list view
            $this->redirectToAction();

        }

        // Redirect to an image
        function actionShowImage() {

            // Get the list of files
            $file = $this->getImage();
            $this->redirectIfMissing( $file );

            // Redirect
            $this->redirect( $this->dir_rel . $file );

        }

        // Function to select an image
        function actionSelectorImages() {

            // Set the action for the upload form
            $this->form->setDefault( 'action', $this->getActionName() );

            // Define the default pagesize
            YDConfig::set( 'YD_DB_DEFAULTPAGESIZE', 15 );

            // Get the list of images
            $images = $this->getImages();

            // Get the pagesize and current page from the URL
            $page = @ $_GET['page'];

            // Create the YDRecordSet object
            $images = new YDRecordSet( $images, $page, YDConfig::get( 'YD_DB_DEFAULTPAGESIZE', 20 ) );
            $images->set = YDArrayUtil::convertToTable( $images->set, 5, true );

            // Assign it to the template
            $this->tpl->assign( 'uploads_dir', $this->dir_rel );
            $this->tpl->assign( 'images',      $images );

            // Assign the quick upload form to the template
            $this->tpl->assignForm( 'form', $this->form );

            // Output the template
            $this->display( 'images_selector.tpl' );

        }

        // Function to get the itemtype details from the database
        function getImage() {
            $images = $this->getImages( false );
            return in_array( $_GET['id'], $images ) ? $_GET['id'] : false;
        }

        // Get the list of images
        function getImages( $as_class=true ) {

            // Get the list of files
            $dir = new YDFSDirectory( $this->dir_rel );
            $images = $dir->getContents( null, null, array( 'YDFSImage' ), true, 'desc' );

            // Make the relative path for each file
            foreach ( $images as $key=>$image ) {

                // Set the relative path
                $image->relative_path = str_replace( $dir->getAbsolutePath(), '', $image->getAbsolutePath() );

                // Update the backslashes
                $image->relative_path = str_replace( '\\', '/', $image->relative_path );

                // Remove the leading slash
                if ( substr( $image->relative_path, 0, 1 ) == '/' ) {
                    $image->relative_path = substr( $image->relative_path, 1 );
                }

                // Update the original image
                $images[$key] = $image;

            }

            // Convert to a plain array if needed
            if ( $as_class === false ) {
                foreach ( $images as $key=>$image ) {
                    $images[$key] = $image->relative_path;
                }
            }

            // Return the list
            return $images;

        }

    }

    // Process the request
    YDInclude( 'YDF2_process.php' );

?>
