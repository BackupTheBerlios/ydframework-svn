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

            // Get the list of files and file sizes
            $cache_pub_lbl = 'Public cache ' . $this->_getTotalSizeAndCountAsText( YD_WEBLOG_CACHE_PREFIX . '*.' . YD_WEBLOG_CACHE_SUFFIX );
            $cache_tmb_lbl = 'Thumbnail cache ' . $this->_getTotalSizeAndCountAsText( YD_TMP_PRE . 'N_*.*' );
            $cache_web_lbl = 'Web download cache ' . $this->_getTotalSizeAndCountAsText( '*.wch' );
            $cache_tpl_lbl = 'Template cache ' . $this->_getTotalSizeAndCountAsText( '*.tpl.php' );

            // Create the delete form
            $form = new YDForm( 'clearCacheForm' );
            $form->addElement( 'checkbox', 'cache_pub', $cache_pub_lbl, array( 'style' => 'border: none;' ) );
            $form->addElement( 'checkbox', 'cache_tmb', $cache_tmb_lbl, array( 'style' => 'border: none;' ) );
            $form->addElement( 'checkbox', 'cache_web', $cache_web_lbl, array( 'style' => 'border: none;' ) );
            $form->addElement( 'checkbox', 'cache_tpl', $cache_tpl_lbl, array( 'style' => 'border: none;' ) );
            $form->addElement( 'submit', '_cmdSubmit', t( 'cleanup' ), array( 'class' => 'button' ) );
            $form->setDefaults( array( 'cache_pub' => 1, 'cache_web' => 1, 'cache_tmb' => 1, 'cache_tpl' => 1 ) );


            // Validate the form
            if ( $form->validate() == true ) {

                // Check if we need to delete the thumbnail objects
                if ( $form->getValue( 'cache_pub' ) ) {
                    $this->_deleteCacheFiles( YD_WEBLOG_CACHE_PREFIX . '*.' . YD_WEBLOG_CACHE_SUFFIX );
                }

                // Check if we need to delete the thumbnail objects
                if ( $form->getValue( 'cache_tmb' ) == 1 ) {
                    $this->_deleteCacheFiles(  YD_TMP_PRE . 'N_*.*' );
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
            $files = $this->_getFileList( $pattern );
            foreach ( $files as $file ) {
                @unlink( $file->getAbsolutePath() );
            }
        }

        // Helper function to get the total file size and number of files
        function _getTotalSizeAndCount( $pattern ) {
            $count = 0;
            $size  = 0;
            $files = $this->_getFileList( $pattern );
            foreach ( $files as $file ) {
                $count++;
                $size += $file->getSize();
            }
            return array( $count, $size );
        }

        // Get the total size and count as a piece of text
        function _getTotalSizeAndCountAsText( $pattern ) {
            $res = $this->_getTotalSizeAndCount( $pattern );
            return sprintf( '(%s %s, %s)', $res[0], t('items'), YDStringUtil::formatFileSize( $res[0] ) );
        }

        // Get a filelist
        function _getFileList( $pattern ) {
            $dir = new YDFSDirectory( YD_DIR_TEMP );
            return $dir->getContents( $pattern, null );
        }

    }

    // Process the request
    YDInclude( 'YDF2_process.php' );

?>
