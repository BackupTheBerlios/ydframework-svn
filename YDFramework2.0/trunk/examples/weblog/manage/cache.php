<?php

    // Include the Yellow Duck Framework
    include_once( dirname( __FILE__ ) . '/../include/YDWeblog_init.php' );

    // Class definition
    class cache extends YDWeblogAdminRequest {

        // Class constructor
        function cache() {

            // Initialize the parent
            $this->YDWeblogAdminRequest();

        }

        // Default action
        function actionDefault() {

            // Create the delete form
            $form = new YDForm( 'clearCacheForm' );
            $form->addElement( 'checkbox', 'cache_tmb', 'Thumbnail cache', array( 'style' => 'border: none;' ) );
            $form->addElement( 'checkbox', 'cache_web', 'Web download cache', array( 'style' => 'border: none;' ) );
            $form->addElement( 'checkbox', 'cache_tpl', 'Template cache', array( 'style' => 'border: none;' ) );
            $form->addElement( 'submit', '_cmdSubmit', t( 'cleanup' ), array( 'class' => 'button' ) );
            $form->setDefaults( array( 'cache_web' => 1, 'cache_tmb' => 1, 'cache_tpl' => 1 ) );

            // Validate the form
            if ( $form->validate() == true ) {

                // Check if we need to delete the thumbnail objects
                if ( $form->getValue( 'cache_tmb' ) ) {
                    $this->_deleteCacheFiles( '*.tmn' );
                }

                // Check if we need to delete the web objects
                if ( $form->getValue( 'cache_web' ) ) {
                    $this->_deleteCacheFiles( '*.wch' );
                }

                // Check if we need to delete the template objects
                if ( $form->getValue( 'cache_tpl' ) ) {
                    $this->_deleteCacheFiles( '*.tpl.php' );
                }

                // Add a status message
                $this->tpl->assign( 'message', t( 'cache_cleaned_up' ) );

            }

            // Add the form to the template
            $this->tpl->assignForm( 'form', $form );

            // Display the template
            $this->display();

        }

        // Helper function to delete files
        function _deleteCacheFiles( $pattern ) {
            $dir = new YDFSDirectory( YD_DIR_TEMP );
            $filesToDelete = $dir->getContents( $pattern, null );
            foreach ( $filesToDelete as $file ) {
                @unlink( $file->getAbsolutePath() );
            }
        }

    }

    // Process the request
    YDInclude( 'YDF2_process.php' );

?>
