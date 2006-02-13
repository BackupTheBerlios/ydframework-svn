<?php

    // Include the Yellow Duck Framework
    include_once( dirname( __FILE__ ) . '/../include/YDWeblog_init.php' );

    // Includes
    YDInclude( 'YDFileSystem.php' );

    // Class definition
    class config extends YDWeblogAdminRequest {

        // Class constructor
        function config() {

            // Initialize the parent
            $this->YDWeblogAdminRequest();

        }

        // Default action
        function actionDefault() {

            // Get the config
            $config = $this->getConfig();

            // Fix the boolean values
            $config['email_new_comment'] = ( $config['email_new_comment'] ) ? t('yes') : t('no');
            $config['use_cache']         = ( $config['use_cache'] ) ? t('yes') : t('no');
            $config['friendly_urls']     = ( $config['friendly_urls'] ) ? t('yes') : t('no');

            // Assign it to the template
            $this->tpl->assign( 'config', $config );

            // Display the template
            $this->display();

        }

        // Edit the configuration
        function actionEdit() {

            // Get the configuration
            $config = $this->getConfig();

            // Get the list of skins
            $dir = new YDFSDirectory( dirname( __FILE__ ) . '/../' . $this->dir_skins );
            $items = $dir->getContents( '!.*', '', array( 'YDFSDirectory' ) );
            $skins = array();
            foreach ( $items as $item ) {
                $skins[ $item ] = $item;
            }

            // Get the list of languages
            $dir = new YDFSDirectory( dirname( __FILE__ ) . '/../include/languages/' );
            $items = $dir->getContents( 'language_*.php', '', array( 'YDFSFile' ) );
            $languages = array();
            foreach ( $items as $item ) {
                $item = substr( $item, 9, -4 );
                $languages[ $item ] = $item;
            }

            // Create the configuration form
            $form = new YDWeblogForm( 'configForm' );

            // Add the fields
            $form->addElement( 'text', 'db_host', t( 'cfg_db_host' ), array( 'class' => 'tfM' ) );
            $form->addElement( 'text', 'db_name', t( 'cfg_db_name' ), array( 'class' => 'tfM' ) );
            $form->addElement( 'text', 'db_user', t( 'cfg_db_user' ), array( 'class' => 'tfM' ) );
            $form->addElement( 'text', 'db_pass', t( 'cfg_db_pass' ), array( 'class' => 'tfM' ) );
            $form->addElement( 'text', 'db_prefix', t( 'cfg_db_prefix' ), array( 'class' => 'tfM' ) );

            $form->addElement( 'text', 'weblog_title', t( 'cfg_weblog_title' ), array( 'class' => 'tfM' ) );
            $form->addElement( 'text', 'weblog_description', t( 'cfg_weblog_description' ), array( 'class' => 'tfM' ) );
            $form->addElement( 'text', 'weblog_entries_fp', t( 'cfg_weblog_entries_fp' ), array( 'class' => 'tfM' ) );
            $form->addElement( 'select', 'weblog_skin', t( 'cfg_weblog_skin' ), array( 'class' => 'tfM', 'style' => 'width: 100%' ), $skins );
            $form->addElement( 'select', 'weblog_language', t( 'cfg_weblog_language' ), array( 'class' => 'tfM', 'style' => 'width: 100%' ), $languages );

            $form->addElement( 'checkbox', 'email_new_comment', t( 'cfg_notification_email_comment' ),  array( 'style' => 'border: none;' ) );
            $form->addElement( 'text', 'max_syndicated_items', t( 'cfg_rss_max_syndicated_items' ), array( 'class' => 'tfM' ) );

            $form->addElement( 'checkbox', 'use_cache', t( 'cfg_use_cache_comment' ), array( 'style' => 'border: none;' ) );

            $form->addElement( 'checkbox', 'friendly_urls', t( 'cfg_friendly_urls' ), array( 'style' => 'border: none;' ) );

            $form->addElement( 'text', 'auto_close_items', t( 'cfg_auto_close_items' ), array( 'class' => 'tfM' ) );

            $form->addElement( 'submit', '_cmdSubmit', t('OK'), array( 'class' => 'button' ) );

            // Set the defaults
            $form->setDefaults( $config );

            // Add the rules
            $form->addRule( 'db_host', 'required', t( 'err_db_host' ) );
            $form->addRule( 'db_name', 'required', t( 'err_db_name' ) );
            $form->addRule( 'db_user', 'required', t( 'err_db_user' ) );
            $form->addRule( 'weblog_title', 'required', t( 'err_weblog_title' ) );
            $form->addRule( 'weblog_entries_fp', 'required', t( 'err_weblog_entries_fp' ) );
            $form->addRule( 'weblog_entries_fp', 'numeric', t( 'err_weblog_entries_fp_num' ) );
            $form->addRule( 'max_syndicated_items', 'required', t( 'err_max_syndicated_items' ) );
            $form->addRule( 'max_syndicated_items', 'numeric', t( 'err_max_syndicated_items_num' ) );
            $form->addRule( 'auto_close_items', 'numeric', t( 'err_auto_close_items' ) );

            // Add the filters
            $form->addFilters( 
                array( 'db_host', 'db_name', 'db_user', 'db_prefix', 'weblog_title', 'weblog_description' ),
                'strip_html'
            );

            // Process the form
            if ( $form->validate() === true ) {

                // Get the form values
                $values = $form->getValues();

                // Save the config
                YDWeblogSaveConfig( $values );

                // Redirect to the default acton
                $this->redirectToAction();

            }

            // Add it to the template
            $this->tpl->assignForm( 'form', $form );

            // Display the template
            $this->display();

        }

        // Function to get the configuration
        function getConfig() {

            // Start with an empty array
            $config = array();

            // Get the config values
            $config['db_host']              = YDConfig::get( 'db_host',              'localhost' );
            $config['db_name']              = YDConfig::get( 'db_name',              'yellowd_ydweblog' );
            $config['db_user']              = YDConfig::get( 'db_user',              'root' );
            $config['db_pass']              = YDConfig::get( 'db_pass',              '' );
            $config['db_prefix']            = YDConfig::get( 'db_prefix',            '' );
            $config['weblog_title']         = YDConfig::get( 'weblog_title',         'Untitled weblog' );
            $config['weblog_description']   = YDConfig::get( 'weblog_description',   '' );
            $config['weblog_entries_fp']    = YDConfig::get( 'weblog_entries_fp',    1 );
            $config['weblog_skin']          = YDConfig::get( 'weblog_skin',          'default' );
            $config['weblog_language']      = YDConfig::get( 'weblog_language',      'nl' );
            $config['email_new_comment']    = YDConfig::get( 'email_new_comment',    true );
            $config['max_syndicated_items'] = YDConfig::get( 'max_syndicated_items', 20 );
            $config['use_cache']            = YDConfig::get( 'use_cache',            false );
            $config['friendly_urls']        = YDConfig::get( 'friendly_urls',        false );
            $config['auto_close_items']     = YDConfig::get( 'auto_close_items',     30 );

            // Return the configuration
            return $config;

        }

    }

    // Process the request
    YDInclude( 'YDF2_process.php' );

?>
