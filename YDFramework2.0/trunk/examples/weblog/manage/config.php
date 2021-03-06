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
            $this->YDWeblogAdminRequest( true );

        }

        // Default action
        function actionDefault() {

            // Get the config
            $config = $this->getConfig();

            // Fix the boolean values
            $config['email_new_comment']   = ( $config['email_new_comment'] ) ? t('yes') : t('no');
            $config['email_new_item']      = ( $config['email_new_item'] ) ? t('yes') : t('no');
            $config['use_cache']           = ( $config['use_cache'] ) ? t('yes') : t('no');
            $config['friendly_urls']       = ( $config['friendly_urls'] ) ? t('yes') : t('no');
            $config['include_debug_info']  = ( $config['include_debug_info'] ) ? t('yes') : t('no');
            $config['dflt_is_draft']       = ( $config['dflt_is_draft'] ) ? t('yes') : t('no');

            // Hide the database password if any
            $config['db_pass'] = str_repeat( '*', strlen( $config['db_pass'] ) );

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
            $items = $dir->getContents( null, '', array( 'YDFSDirectory' ) );
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
            $form->addElement( 'password', 'db_pass', t( 'cfg_db_pass' ), array( 'class' => 'tfM' ) );
            $form->addElement( 'text', 'db_prefix', t( 'cfg_db_prefix' ), array( 'class' => 'tfM' ) );

            $form->addElement( 'text', 'weblog_title', t( 'cfg_weblog_title' ), array( 'class' => 'tfM' ) );
            $form->addElement( 'text', 'weblog_description', t( 'cfg_weblog_description' ), array( 'class' => 'tfM' ) );
            $form->addElement( 'text', 'weblog_entries_fp', t( 'cfg_weblog_entries_fp' ), array( 'class' => 'tfM' ) );
            $form->addElement( 'select', 'weblog_skin', t( 'cfg_weblog_skin' ), array( 'class' => 'tfM', 'style' => 'width: 100%' ), $skins );
            $form->addElement( 'select', 'weblog_language', t( 'cfg_weblog_language' ), array( 'class' => 'tfM', 'style' => 'width: 100%' ), $languages );
            $form->addElement( 'checkbox', 'include_debug_info', t( 'cfg_include_debug_info' ), array( 'style' => 'border: none;' ) );
            $form->addElement( 'text', 'google_analytics', t( 'cfg_weblog_google_analytics' ), array( 'class' => 'tfM' ) );
            $form->addElement( 'text', 'weblog_title', t( 'cfg_weblog_title' ), array( 'class' => 'tfM' ) );
            $form->addElement( 'text', 'max_img_size_x', t( 'cfg_max_img_size' ), array( 'class' => 'tfXS' ) );
            $form->addElement( 'text', 'max_img_size_y', t( 'cfg_max_img_size' ), array( 'class' => 'tfXS' ) );

            $form->addElement( 'checkbox', 'email_new_comment', t( 'cfg_notification_email_comment' ),  array( 'style' => 'border: none;' ) );
            $form->addElement( 'checkbox', 'email_new_item', t( 'cfg_notification_email_item' ),  array( 'style' => 'border: none;' ) );
            $form->addElement( 'text', 'max_syndicated_items', t( 'cfg_rss_max_syndicated_items' ), array( 'class' => 'tfM' ) );

            $form->addElement( 'checkbox', 'use_cache', t( 'cfg_use_cache_comment' ), array( 'style' => 'border: none;' ) );

            $form->addElement( 'checkbox', 'friendly_urls', t( 'cfg_friendly_urls' ), array( 'style' => 'border: none;' ) );

            $form->addElement( 'checkbox', 'dflt_is_draft', t( 'is_draft' ), array( 'style' => 'border: none;' ) );

            $form->addElement( 'textarea', 'blocked_ips', t( 'cfg_blocked_ips' ), array( 'class' => 'tfMNoMCE' ) );
            $form->addElement( 'text', 'akismet_key', t( 'cfg_akismet_key' ), array( 'class' => 'tfM' ) );
            $form->addElement( 'text', 'comment_interval', t( 'cfg_comment_interval' ), array( 'class' => 'tfM' ) );
            $form->addElement( 'text', 'max_comment_length', t( 'max_comment_length' ), array( 'class' => 'tfM' ) );
            $form->addElement( 'text', 'max_comment_links', t( 'max_comment_links' ), array( 'class' => 'tfM' ) );

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
            $form->addRule( 'comment_interval', 'numeric', t( 'err_comment_interval_num' ) );
            $form->addRule( 'max_comment_length', 'numeric', t( 'err_max_comment_length' ) );
            $form->addRule( 'max_comment_links', 'numeric', t( 'err_max_comment_links' ) );

            // Add the filters
            $form->addFilters( 
                array( 'db_host', 'db_name', 'db_user', 'db_prefix', 'weblog_title', 'weblog_description' ),
                'strip_html'
            );

            // Process the form
            if ( $form->validate() === true ) {

                // Get the form values
                $values = $form->getValues();

                // Format the list of blocked IP numbers
                $values['blocked_ips'] = YDFormatStringWithListValues( $values['blocked_ips'] );

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

        // Action to test Akismet
        function actionTestAkismet() {
            echo( '<html>' );
            echo( '<head>' );
            echo( '    <title>Testing Akismet</title>' );
            echo( '    <link rel="stylesheet" type="text/css" href="manage.css" />' );
            echo( '</head>' );
            echo( '<body>' );
            echo( '    <p class="title">Testing Akismet service...</p>' );
            if ( is_null( $this->weblog->akismet ) ) {
                echo( '&nbsp;<br/><span style="font-weight:bold;color:red;">ERROR: No akismet key is specified.</span>' );
            } else {
                echo( 'Akismet key used for this request: ' . YDConfig::get( 'akismet_key', '' ) . '<br/>' );
                echo( 'Sending test request to Akismet...<br/>' );
                $this->weblog->akismet->debug = true;
                $result = $this->weblog->akismet->checkComment(
                    'Fuck%XXKEVDJX!!http://porn.z0rder.com/anal-porn.htm [url= http://porn.z0rder.com/anal-porn.htm ]http://porn.z0rder.com/anal-porn.htm[/url]',
                    'PCNFZUII',
                    '',
                    '',
                    '80.77.80.187',
                    ''
                );
                if ( is_null( $this->weblog->akismet->error ) ) {
                    if ( $result == NULL || $result === false ) {
                        echo( 'Test request was marked as not spam.<br/>' );
                    } else {
                        echo( 'Test request was marked as spam.<br/>' );
                    }
                    echo( '&nbsp;<br/><span style="font-weight:bold;color:green;">Akismet connectivity is working properly</span>' );
                } else {
                    echo( '&nbsp;<br/><span style="font-weight:bold;color:red;">ERROR: ' . $this->weblog->akismet->error . '</span>' );
                }
            }
            echo( '&nbsp;<br/>&nbsp;<br/>' );
            echo( '[ <a href="" onClick="window.close();">close</a> ]' );
            echo( '</body>' );
            echo( '</html>' );
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
            $config['google_analytics']     = YDConfig::get( 'google_analytics',     '' );
            $config['include_debug_info']   = YDConfig::get( 'include_debug_info',   false );
            $config['email_new_comment']    = YDConfig::get( 'email_new_comment',    true );
            $config['email_new_item']       = YDConfig::get( 'email_new_item',       true );
            $config['max_syndicated_items'] = YDConfig::get( 'max_syndicated_items', 20 );
            $config['use_cache']            = YDConfig::get( 'use_cache',            false );
            $config['friendly_urls']        = YDConfig::get( 'friendly_urls',        false );
            $config['dflt_is_draft']        = YDConfig::get( 'dflt_is_draft',        false );
            $config['blocked_ips']          = YDConfig::get( 'blocked_ips',          YDFormatStringWithListValues( '' ) );
            $config['akismet_key']          = YDConfig::get( 'akismet_key',          '' );
            $config['max_img_size_x']       = YDConfig::get( 'max_img_size_x',       '' );
            $config['max_img_size_y']       = YDConfig::get( 'max_img_size_y',       '' );
            $config['comment_interval']     = YDConfig::get( 'comment_interval',     10 );
            $config['max_comment_length']   = YDConfig::get( 'max_comment_length',   1500 );
            $config['max_comment_links']    = YDConfig::get( 'max_comment_links',    1 );

            // Return the configuration
            return $config;

        }

    }

    // Process the request
    YDInclude( 'YDF2_process.php' );

?>
