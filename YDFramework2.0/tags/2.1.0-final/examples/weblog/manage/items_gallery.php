<?php

    // Include the Yellow Duck Framework
    include_once( dirname( __FILE__ ) . '/../include/YDWeblog_init.php' );

    // Includes
    YDInclude( 'YDFileSystem.php' );

    // Class definition
    class items_gallery extends YDWeblogAdminRequest {

        // Class constructor
        function items_gallery() {

            // Initialize the parent
            $this->YDWeblogAdminRequest();

            // Get the ID from the query string
            $id = $this->getIdFromQS();

            // Get the weblog details and go to the default view if none is matched
            $this->item = @ $this->weblog->getItemById( $id );
            if ( ! $this->item ) {
                $this->redirect( 'items.php' );
            }

            // The media files directory object
            $this->dir_rel = '../' . YDConfig::get( 'dir_uploads', 'uploads' ) . '/item_' . $this->item['id'] .'/';

            // Create the image upload form
            $this->form = new YDWeblogForm( 'uploadForm', 'POST', YD_SELF_SCRIPT . '?do=add&id=' . $this->item['id'] );
            $this->form->addElement( 'file', 'image', t('image'), array( 'class' => 'tfM' ) );
            $this->form->addElement( 'submit', '_cmdSubmit', t('OK'), array( 'class' => 'button' ) );

        }

        // The default action
        function actionDefault() {

            // Get the pagesize and current page from the URL
            $page = @ $_GET['page'];

            // Create the YDRecordSet object
            $images = new YDRecordSet( $this->item['images'], $page, YDConfig::get( 'YD_DB_DEFAULTPAGESIZE', 20 ) );

            // Assign it to the template
            $this->tpl->assign( 'item',        $this->item );
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
                    if ( ! is_dir( $this->dir_rel ) ) {
                        @mkdir( $this->dir_rel );
                    }
                    @ $file->moveUpload( $this->dir_rel, $filename );

                    // Convert it to an object
                    $fileObj = new YDFSFile( $this->dir_rel . $file->getBaseName() );

                    // Check if it's a ZIP file
                    if ( strtolower( $fileObj->getExtension() == 'zip' ) ) {

                        // Include the unzip library
                        include( YD_DIR_HOME . '/3rdparty/zip/unzip.lib.php' );

                        // Convert it to a ZIP object
                        $zip = new SimpleUnzip( $fileObj->getAbsolutePath() );

                        // Get the directory as a path
                        $dir = new YDFSDirectory( $this->dir_rel );

                        // Extract the images
                        foreach( $zip->Entries as $entry ) {

                            // Save it as a filee
                            $entryFile = $dir->createFile( $entry->Name, $entry->Data );

                            // Delete it if it's not an image
                            if ( ! $entryFile->isImage() ) {
                                @ unlink( $entryFile->getAbsolutePath() );
                            } else {
                                $entryFile = $this->weblog->resizeUploadedImage( $entryFile );
                            }

                        }

                    }

                    // Check if it's an image
                    if ( ! $fileObj->isImage() ) {
                        @unlink( $fileObj->getAbsolutePath() );
                    } else {
                        $fileObj = $this->weblog->resizeUploadedImage( $fileObj );
                    }

                    // Delete the thumbnails
                    @unlink( $this->dir_rel . 's_' . $file->getBaseName() );
                    @unlink( $this->dir_rel . 'm_' . $file->getBaseName() );

                }

            }

            // Redirect to the list view
            $this->redirect( YD_SELF_SCRIPT . '?id=' . $this->item['id'] );

        }

        // The default action
        function actionDelete() {

            // Get the image
            $image = $this->getImage();
            $this->redirectIfMissing( $image );

            // Remove the image metadata
            $this->weblog->removeImageMetaData( $image );

            // The path to the image
            $path = realpath( '../' . YDConfig::get( 'dir_uploads', 'uploads' ) . '/' . $image );

            // Delete the file
            @unlink( $path );

            // Delete the thumbnails
            @unlink( dirname( $path ) . '/s_' . basename( $path ) );
            @unlink( dirname( $path ) . '/m_' . basename( $path ) );

            // Redirect to the list view
            $this->redirect( YD_SELF_SCRIPT . '?id=' . $this->item['id'] );

        }

        // Redirect to an image
        function actionShowImage() {

            // Get the list of files
            $file = $this->getImage();
            $this->redirectIfMissing( $file );

            // Redirect
            $this->redirect( '../' . YDConfig::get( 'dir_uploads', 'uploads' ) . '/' . $file );

        }

        // Action to edit the metadata
        function actionEditMetaData() {

            // Update the image metadata for the submitted items
            foreach ( $_POST['metadata'] as $item => $data ) {
                $this->weblog->setItemImageMetaData( $item, $data );
            }

            // Redirect to the list view
            $this->redirect( YD_SELF_SCRIPT . '?id=' . $this->item['id'] );

        }

        // Function to get the itemtype details from the database
        function getImage() {
            foreach ( $this->item['images'] as $image ) {
                if ( $image->relative_path == $_GET['img'] ) {
                    return $_GET['img'];
                }
            }
            return false;
        }

    }

    // Process the request
    YDInclude( 'YDF2_process.php' );

?>
