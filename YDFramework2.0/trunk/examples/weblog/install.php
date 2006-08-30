<?php

    // Include the Yellow Duck Framework
    include_once( dirname( __FILE__ ) . '/../../YDFramework2/YDF2_init.php' );

    // Includes
    YDInclude( 'YDForm.php' );
    YDInclude( 'YDTemplate.php' );
    YDInclude( 'YDRequest.php' );
    YDInclude( 'YDDatabase.php' );
    YDInclude( 'YDDatabaseMetaData.php' );
    YDInclude( 'YDFileSystem.php' );
    YDInclude( dirname( __FILE__ ) . '/include/YDWeblogAPI.php' );

    // Class definition
    class install extends YDRequest {

        // Class constructor
        function install() {

            // Initialize the parent
            $this->YDRequest();

            // Initialize the template
            $this->tpl = new YDTemplate();

            // Get the shortcuts to the directories
            $this->dir_uploads = YDConfig::get( 'dir_uploads', 'uploads' );
            $this->dir_skins   = YDConfig::get( 'dir_skins',   'skins' ) . '/';

            // Error out if the YDFramework temp directory is not writeable
            $this->_checkWriteableDirectory( YD_DIR_TEMP );
            $this->_checkWriteableDirectory( dirname( __FILE__ ) . '/include' );
            $this->_checkWriteableDirectory( dirname( __FILE__ ) . '/uploads' );

        }

        // Check if a directory is writeable
        function _checkWriteableDirectory( $dir ) {
            if ( ! is_writeable( $dir ) ) {
                echo( '<html><head><title>' . YD_FW_NAME . ' Weblog - Installer</title></head><body>' );
                echo( '<h2>' . YD_FW_NAME . ' Weblog - Installer</h2>' );
                echo( '<p><font color="red"><b>Make sure the directory ' . realpath( $dir ) . ' is writeable before continueing</b></font></p>' );
                die();
            }
        }

        // Default action
        function actionDefault() {

            // Check for the config file
            if ( is_file( dirname( __FILE__ ) . '/include/config.php' ) ) {
                $this->redirectToAction( 'error' );
            }

            // Get the list of skins
            $dir = new YDFSDirectory( dirname( __FILE__ ) . '/' . $this->dir_skins );
            $items = $dir->getContents( '!.*', '', array( 'YDFSDirectory' ) );
            $skins = array();
            foreach ( $items as $item ) {
                $skins[ $item ] = $item;
            }

            // Get the list of languages
            $dir = new YDFSDirectory( dirname( __FILE__ ) . '/include/languages/' );
            $items = $dir->getContents( 'language_*.php', '', array( 'YDFSFile' ) );
            $languages = array();
            foreach ( $items as $item ) {
                $item = substr( $item, 9, -4 );
                $languages[ $item ] = $item;
            }

            // Create the configuration form
            $form = new YDForm( 'configForm' );

            // Add the fields
            $form->addElement( 'text', 'db_host', 'Database host', array( 'class' => 'tfM' ) );
            $form->addElement( 'text', 'db_name', 'Database name', array( 'class' => 'tfM' ) );
            $form->addElement( 'text', 'db_user', 'Database user', array( 'class' => 'tfM' ) );
            $form->addElement( 'password', 'db_pass', 'Database password', array( 'class' => 'tfM' ) );
            $form->addElement( 'text', 'db_prefix', 'Database table prefix', array( 'class' => 'tfM' ) );
            $form->addElement( 'text', 'weblog_title', 'Weblog title', array( 'class' => 'tfM' ) );
            $form->addElement( 'text', 'weblog_description', 'Weblog description', array( 'class' => 'tfM' ) );
            $form->addElement( 'select', 'weblog_skin', 'Weblog skin', array( 'class' => 'tfM', 'style' => 'width: 100%' ), $skins );
            $form->addElement( 'select', 'weblog_language', 'Weblog language', array( 'class' => 'tfM', 'style' => 'width: 100%' ), $languages );
            $form->addElement( 'text', 'name', 'User name', array( 'class' => 'tfM' ) );
            $form->addElement( 'text', 'email', 'User email', array( 'class' => 'tfM' ) );
            $form->addElement( 'password', 'password', 'Password', array( 'class' => 'tfM' ) );
            $form->addElement( 'submit', '_cmdSubmit', 'Install', array( 'class' => 'button' ) );

            // Add the rules
            $form->addRule( 'db_host', 'required', 'Database host is required' );
            $form->addRule( 'db_name', 'required', 'Database name is required' );
            $form->addRule( 'db_user', 'required', 'Database user is required' );
            $form->addRule( 'weblog_title', 'required', 'Weblog title is required' );
            $form->addRule( 'name', 'required', 'User name is required' );
            $form->addRule( 'email', 'required', 'User email is required' );
            $form->addRule( 'email', 'email', 'User email is required' );
            $form->addRule( 'password', 'required', 'Password is required' );
            $form->addFormRule( array( & $this, 'checkInstallParams' ) );

            // Set the defaults
            $form->setDefault( 'db_host', 'localhost' );
            $form->setDefault( 'db_name', 'ydweblog' );
            $form->setDefault( 'db_user', 'root' );
            $form->setDefault( 'db_prefix', 'ydw_' );
            $form->setDefault( 'weblog_title', 'My Weblog' );
            $form->setDefault( 'weblog_description', 'Description of my Weblog' );
            $form->setDefault( 'weblog_language', 'en' );

            // Process the form
            if ( $form->validate() === true ) {

                // Get the form values
                $values = $form->getValues();

                // Connect to the database
                $db = YDDatabase::getInstance(
                    'mysql', $values['db_name'], $values['db_user'], $values['db_pass'], $values['db_host']
                );

                // Create the tables
                $db->executeSql( 'DROP TABLE IF EXISTS ' . $values['db_prefix'] . 'categories;' );
                $db->executeSql(
                    'CREATE TABLE ' . $values['db_prefix'] . 'categories ( id int(11) NOT NULL auto_increment, title varchar(255) NOT NULL default \'\', created int(11) default NULL, modified int(11) default NULL, PRIMARY KEY  (id) ) TYPE=MyISAM;'
                );
                $db->executeSql( 'DROP TABLE IF EXISTS ' . $values['db_prefix'] . 'comments;' );
                $db->executeSql(
                    'CREATE TABLE ' . $values['db_prefix'] . 'comments (
                      id int(11) NOT NULL auto_increment,
                      item_id int(11) NOT NULL default \'1\',
                      username varchar(255) NOT NULL default \'\',
                      useremail varchar(255) NOT NULL default \'\',
                      userwebsite varchar(255) default NULL,
                      userip varchar(20) default NULL,
                      useragent varchar(255) default NULL,
                      userrequrl varchar(255) default NULL,
                      comment longtext NOT NULL,
                      created int(11) default NULL,
                      modified int(11) default NULL,
                      PRIMARY KEY  (id),
                      KEY item_id (item_id)
                    ) TYPE=MyISAM;'
                );
                $db->executeSql( 'DROP TABLE IF EXISTS ' . $values['db_prefix'] . 'items;' );
                $db->executeSql(
                    'CREATE TABLE ' . $values['db_prefix'] . 'items (
                      id int(11) NOT NULL auto_increment,
                      category_id int(11) default \'1\',
                      user_id int(11) NOT NULL default \'1\',
                      title varchar(255) NOT NULL default \'\',
                      body longtext NOT NULL,
                      body_more longtext default NULL,
                      num_comments int(11) NOT NULL default \'0\',
                      allow_comments tinyint(1) NOT NULL default \'1\',
                      created int(11) default NULL,
                      modified int(11) default NULL,
                      PRIMARY KEY  (id),
                      KEY category_id (category_id),
                      KEY user_id (user_id),
                      KEY allow_comments(allow_comments)
                    ) TYPE=MyISAM;'
                );
                $db->executeSql( 'DROP TABLE IF EXISTS ' . $values['db_prefix'] . 'links;' );
                $db->executeSql(
                    'CREATE TABLE ' . $values['db_prefix'] . 'links (
                      id int(11) NOT NULL auto_increment,
                      title varchar(255) NOT NULL default \'\',
                      url varchar(255) NOT NULL default \'\',
                      num_visits int(11) default \'0\',
                      created int(11) default NULL,
                      modified int(11) default NULL,
                      PRIMARY KEY  (id),
                      UNIQUE KEY url (url)
                    ) TYPE=MyISAM;'
                );
                $db->executeSql( 'DROP TABLE IF EXISTS ' . $values['db_prefix'] . 'pages;' );
                $db->executeSql(
                    'CREATE TABLE ' . $values['db_prefix'] . 'pages (
                      id int(11) NOT NULL auto_increment,
                      user_id int(11) NOT NULL default \'1\',
                      title varchar(255) NOT NULL default \'\',
                      body longtext,
                      created int(11) default NULL,
                      modified int(11) default NULL,
                      PRIMARY KEY  (id),
                      KEY user_id (user_id)
                    ) TYPE=MyISAM;'
                );
                $db->executeSql( 'DROP TABLE IF EXISTS ' . $values['db_prefix'] . 'statistics;' );
                $db->executeSql(
                    'CREATE TABLE ' . $values['db_prefix'] . 'statistics (
                      id int(11) NOT NULL auto_increment,
                      date date NOT NULL default \'0000-00-00\',
                      uri varchar(255) NOT NULL default \'0\',
                      browser varchar(10) NOT NULL default \'\',
                      platform varchar(10) NOT NULL default \'\',
                      hits int(11) NOT NULL default \'0\',
                      PRIMARY KEY  (id)
                    ) TYPE=MyISAM;'
                );
                $db->executeSql( 'DROP TABLE IF EXISTS ' . $values['db_prefix'] . 'statistics_init;' );
                $db->executeSql(
                    'CREATE TABLE ' . $values['db_prefix'] . 'statistics_init (
                      created varchar(10) NOT NULL default \'\'
                    ) TYPE=MyISAM;'
                );
                $db->executeSql( 'DROP TABLE IF EXISTS ' . $values['db_prefix'] . 'users;' );
                $db->executeSql(
                    'CREATE TABLE ' . $values['db_prefix'] . 'users (
                      id int(11) NOT NULL auto_increment,
                      name varchar(255) NOT NULL default \'\',
                      email varchar(255) NOT NULL default \'\',
                      password varchar(50) default NULL,
                      created int(11) default NULL,
                      modified int(11) default NULL,
                      PRIMARY KEY  (id),
                      UNIQUE KEY email (email)
                    ) TYPE=MyISAM;'
                );
                $db->executeInsert(
                    $values['db_prefix'] . 'statistics_init', array( 'created' => $db->getDate( '__NOW__' ) )
                );

                // Save the config file
                YDWeblogSaveConfig( $values );

                // Include the config file
                YDInclude( dirname( __FILE__ ) . '/include/config.php' );

                // Update the database prefix
                YDConfig::set( 'YD_DB_TABLEPREFIX', YDConfig::get( 'db_prefix', '' ) );

                // Include the weblog API
                YDInclude( dirname( __FILE__ ) . '/include/YDWeblogAPI.php' );

                // Create the weblog object
                $weblog = new YDWeblogAPI();

                // Add the user
                $user = array();
                $user['name'] = $values['name'];
                $user['email'] = $values['email'];
                $user['password'] = $values['password'];
                $weblog->saveUser( $user );

                // Add a category
                $category = array();
                $category['title'] = 'General';
                $weblog->addCategory( $category );

                // Create a first post
                $item = array();
                $item['category_id'] = 1;
                $item['user_id'] = 1;
                $item['title'] = 'Your first post';
                $item['body'] = 'Welcome to your weblog';
                $item['body_more'] = 'Your extended body';
                $weblog->addItem( $item );

                // Create a second post
                $item = array();
                $item['category_id'] = 1;
                $item['user_id'] = 1;
                $item['title'] = 'Your second post';
                $item['body'] = 'Without an extended body';
                $item['created'] = time() + 100;
                $weblog->addItem( $item );

                // Add a comment
                $comment = array();
                $comment['item_id'] = 1;
                $comment['username'] = 'Yellow Duck User';
                $comment['useremail'] = 'nobody@reply.net';
                $comment['userwebsite'] = YD_FW_HOMEPAGE;
                $comment['comment'] = 'A first comment';
                $weblog->addComment( $comment );

                // Add a sample page
                $page = array();
                $page['user_id'] = 1;
                $page['title'] = 'Your first page';
                $page['body'] = 'The contents of your first page';
                $weblog->addPage( $page );

                // Add a link
                $link = array();
                $link['title'] = 'Yellow Duck Framework';
                $link['url'] = 'http://ydframework.berlios.de/';
                $weblog->addLink( $link );

                // Redirect to the finish action
                $this->redirectToAction( 'finish' );

            }

            // Add it to the template
            $this->tpl->assignForm( 'form', $form );

            // Display the template
            $this->tpl->display();

        }

        // Finish action
        function actionFinish() {

            // Check for the config file
            if ( ! is_file( dirname( __FILE__ ) . '/include/config.php' ) ) {
                $this->redirectToAction();
            }

            // Display the template
            $this->tpl->display();

        }

        // Error action
        function actionError() {

            // Display the template
            $this->tpl->display();

        }

        // Check the user credentials
        function checkInstallParams( $params ) {

            // Check for a database connection
            $conn = @mysql_connect( $params['db_host'], $params['db_user'], $params['db_pass'] );
            if ( ! $conn ) {
                return array( '__ALL__' => 'Database host or username/password is wrong!' );
            }
            if ( ! @mysql_select_db( $params['db_name'], $conn ) ) {
                return array( '__ALL__' => 'Database does not exist!' );
            }

            // All is OK
            return true;

        }

    }

    // Process the request
    YDInclude( 'YDF2_process.php' );

?>