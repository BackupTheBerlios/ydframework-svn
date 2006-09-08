<?php

    // Function to save the configuration
    function YDWeblogSaveConfig( $values ) {

        // Remove some unneeded values
        unset( $values['name'] );
        unset( $values['email'] );
        unset( $values['password'] );

        // Add some default ones
        if ( ! isset( $values['weblog_entries_fp'] ) ) {
            $values['weblog_entries_fp'] = 5;
        }
        if ( ! isset( $values['email_new_comment'] ) ) {
            $values['email_new_comment'] = true;
        }
        if ( ! isset( $values['use_cache'] ) ) {
            $values['use_cache'] = false;
        }
        if ( ! isset( $values['max_syndicated_items'] ) ) {
            $values['max_syndicated_items'] = 20;
        }
        if ( ! isset( $values['include_debug_info'] ) ) {
            $values['include_debug_info'] = 0;
        }
        if ( ! isset( $values['dflt_is_draft'] ) ) {
            $values['dflt_is_draft'] = false;
        }

        // Construct the new config text
        $cfg = '<?php' . "\n\n";
        $cfg .= '    // Do not edit this file manually!' . "\n";
        $cfg .= '    // Only edit this file using the admin tools!' . "\n\n";

        // Loop over the config values
        foreach ( $values as $key=>$val ) {

            // Ignore items starting with an underscore
            if ( substr( $key, 0, 1 ) != '_' ) {

                // Escape strings
                $key = str_replace( "'", "\\'", $key );
                $val = str_replace( "'", "\\'", $val );

                // Don't enclose numeric values with quotes
                if ( is_bool( $val ) ) {
                    $val = ( $val ) ? 'true' : 'false';
                    $cfg .= "    YDConfig::set( '" . $key . "', " . $val . " );\n";
                } elseif ( is_numeric( $val ) ) {
                    $cfg .= "    YDConfig::set( '" . $key . "', " . $val . " );\n";
                } else {
                    $cfg .= "    YDConfig::set( '" . $key . "', '" . $val . "' );\n";
                }
            }
        }
        $cfg .= "\n" . '?>';

        // Open the config file
        $f = fopen( dirname( __FILE__ ) . '/config.php', 'w' );
        fwrite( $f, $cfg );
        fclose( $f );

        // Set the right permissions
        //@chmod( dirname( __FILE__ ) . '/config.php', 0700 );

        // Create a .htaccess file if needed
        $htaccessPath = dirname( dirname( __FILE__ ) ) . '/.htaccess';
        if ( ! is_file( $htaccessPath ) ) {

            // The htaccess data
            $data = 'RewriteEngine on' . YD_CRLF;
            $data .= 'Options +FollowSymlinks' . YD_CRLF;
            $data .= 'RewriteRule ^item_([0-9]+).php?$ item.php?id=$1 [L]' . YD_CRLF;
            $data .= 'RewriteRule ^page_([0-9]+).php?$ page.php?id=$1 [L]' . YD_CRLF;
            $data .= 'RewriteRule ^category_([0-9]+).php?$ category.php?id=$1 [L]' . YD_CRLF;
            $data .= 'RewriteRule ^link_([0-9]+).php?$ link.php?id=$1 [L]' . YD_CRLF;
            $data .= 'RewriteRule ^image/(.*) item_gallery.php?id=$1 [L]' . YD_CRLF;

            // Open the config file
            $f = fopen( $htaccessPath, 'w' );
            fwrite( $f, $data );
            fclose( $f );

            // Set the right permissions
            //@chmod( $htaccessPath, 0700 );

        }

    }

    // Function to format a string with list values
    function YDFormatStringWithListValues( $string ) {
        $string = str_replace( ';', ',', $string );
        $string = explode( ',', $string );
        foreach ( $string as $k=>$v ) {
            if ( trim( $v ) == '' ) {
                unset( $string[$k] );
            } else {
                $string[$k] = trim( $v );
            }
        }
        return implode( ', ', $string );
    }

    // Class defining our weblog API
    class YDWeblogAPI extends YDBase {

        // Class constructor
        function YDWeblogAPI() {

            // Initialize the parent
            $this->YDBase();

            // Get the database connection
            $this->db = YDDatabase::getInstance(
                'mysql',
                YDConfig::get( 'db_name', 'YDWeblog' ), YDConfig::get( 'db_user', 'root' ),
                YDConfig::get( 'db_pass', '' ), YDConfig::get( 'db_host', 'localhost' )
            );

            // Get a link to the database metadata
            $this->dbmeta = new YDDatabaseMetaData( $this->db );

            // Upgrade the schema if needed
            $this->upgradeSchemaIfNeeded();

            // The array that will hold the image metadata
            $this->imagemetadata = null;

        }

        // Get the schema version
        function getSchemaVersion() {

            // Create the schemaversion table if it doesn't exists
            if ( ! $this->dbmeta->tableExists( '#_schemaversion' ) ) {
                $sql = 'CREATE TABLE #_schemaversion (installed INT(11), version INT (11))';
                $this->db->executeSql( $sql );
            }

            // Check the schema version
            $schema_version = $this->db->getValue( 'select version from #_schemaversion order by installed desc, version desc' );

            // Return the schema version
            return ( $schema_version === false  ) ? 0 : intval( $schema_version );

        }

        // Get the full schema information
        function getFullSchemaVersion() {
            $this->getSchemaVersion();
            return $this->db->getRecord( 'select installed, version from #_schemaversion order by installed desc, version desc' );
        }

        // Function to set the schema version
        function setSchemaVersion( $version ) {
            $values = array();
            $values['installed'] = time();
            $values['version'] = $version;
            $this->db->executeInsert( '#_schemaversion', $values );
        }

        // Upgrade the schema if needed
        function upgradeSchemaIfNeeded() {

            // The current weblog schema version
            $current_schema = 6;

            // Get the schema version
            $installed_schema = $this->getSchemaVersion();

            // Check if we have the required schema version
            if ( $installed_schema < $current_schema ) {

                // Add any missing comments fields
                $fields = $this->dbmeta->getFields( '#_comments' );
                $this->executeIfMissing( 'useragent', $fields, 'ALTER TABLE #_comments ADD useragent varchar(255) AFTER userip' );
                $this->executeIfMissing( 'userrequrl', $fields, 'ALTER TABLE #_comments ADD userrequrl varchar(255) AFTER useragent' );
                $this->executeIfMissing( 'is_spam', $fields, 'ALTER TABLE #_comments ADD is_spam TINYINT(1) DEFAULT "0" NOT NULL AFTER comment' );

                // Add any missing items fields
                $fields = $this->dbmeta->getFields( '#_items' );
                $this->executeIfMissing( 'body_more', $fields, 'ALTER TABLE #_items ADD body_more LONGTEXT AFTER body' );
                $this->executeIfMissing( 'is_draft', $fields, 'ALTER TABLE #_items ADD is_draft TINYINT(1) DEFAULT "0" NOT NULL AFTER auto_close' );
                $this->executeIfPresent( 'allow_comments', $fields, 'ALTER TABLE #_items DROP allow_comments' );
                $this->executeIfPresent( 'auto_close', $fields, 'ALTER TABLE #_items DROP auto_close' );

                // Add any missing pages field
                $fields = $this->dbmeta->getFields( '#_pages' );
                $this->executeIfMissing( 'is_draft', $fields, 'ALTER TABLE #_pages ADD is_draft TINYINT(1) DEFAULT "0" NOT NULL AFTER body' );

                // Get the list of indexes for the items table
                $indexes = $this->dbmeta->getIndexes( '#_items' );
                $this->executeIfMissing( 'is_draft', $indexes, 'ALTER TABLE #_items ADD INDEX is_draft (is_draft)' );

                // Get the list of indexes for the pages table
                $indexes = $this->dbmeta->getIndexes( '#_pages' );
                $this->executeIfMissing( 'is_draft', $indexes, 'ALTER TABLE #_pages ADD INDEX is_draft (is_draft)' );

                // Get the list of indexes for the users table
                $indexes = $this->dbmeta->getIndexes( '#_users' );
                $this->executeIfPresent( 'email', $indexes, 'ALTER TABLE #_users DROP INDEX email' );
                $this->executeIfMissing( 'name',  $indexes, 'ALTER TABLE #_users ADD UNIQUE name (name)' );

                // Fix the shemaversion table if needed
                $this->db->executeSql( 'ALTER TABLE #_schemaversion CHANGE installed installed INT(11)' );
                $this->db->executeSql( 'UPDATE #_schemaversion SET installed = unix_timestamp() WHERE installed = 0' );

                // Drop the statistics tables
                $this->db->executeSql( 'DROP TABLE IF EXISTS #_statistics' );
                $this->db->executeSql( 'DROP TABLE IF EXISTS #_statistics_init' );

                // Create the image_metadata table
                if ( ! $this->dbmeta->tableExists( '#_imagemetadata' ) ) {
                    $this->db->executeSql(
                          'CREATE TABLE #_imagemetadata ('
                        . '  id int(11) NOT NULL auto_increment,'
                        . '  img_path varchar(255) NOT NULL default \'\','
                        . '  title varchar(255) default NULL,'
                        . '  description longtext,'
                        . '  created int(11) default NULL,'
                        . '  modified int(11) default NULL,'
                        . '  PRIMARY KEY  (id),'
                        . '  UNIQUE KEY img_path (img_path)'
                        . ')'
                    );
                }

                // Update the schema information
                $this->setSchemaVersion( $current_schema );

            }

        }

        // Function to execute SQL if the item is missing
        function executeIfMissing( $needle, $haystack, $sql ) {
            if ( ! in_array( $needle, $haystack ) ) {
                $this->db->executeSql( $sql );
            }
        }

        // Function to execute SQL if the item is not missing
        function executeIfPresent( $needle, $haystack, $sql ) {
            if ( in_array( $needle, $haystack ) ) {
                $this->db->executeSql( $sql );
            }
        }

        // Function to auto add some records to an item
        function _fixItem( $item, $order='created desc, title' ) {

            // Return false if no item
            if ( ! $item ) {
                return $item;
            }

            // Get the item indexes for the indicated weblog (cached to minimize the number of SQL queries)
            $cacheName = 'YD_CACHE_WEBLOG_ITEMIDS_' . md5( strtolower( $order ) );
            if ( ! isset( $GLOBALS[ $cacheName ] ) ) {
                $sql = $this->_prepareQuery( 'SELECT id FROM #_items', $order );
                $item_ids = $this->db->getValuesByName( $sql, 'id' );
                $GLOBALS[ $cacheName ] = $item_ids;
            }

            // Get the ID of the previous and the next item
            $pos = array_search( $item['id'], $GLOBALS[ $cacheName ] );
            $item['newer_id'] = ( $pos == 0 ) ? false : $GLOBALS[ $cacheName ][$pos-1] ;
            $item['older_id'] = ( $pos == sizeof( $GLOBALS[ $cacheName ] )-1 ) ? false : $GLOBALS[ $cacheName ][$pos+1] ;

            // Add the year, month and yearmonth fields
            $item['yearmonth'] = ucwords( strftime( '%B %Y', $item['created'] ) );
            $item['month']     = ucwords( strftime( '%B',    $item['created'] ) );
            $item['year']      = ucwords( strftime( '%Y',    $item['created'] ) );

            // Get the list of images related to this item
            $imgPath = dirname( __FILE__ ) . '/../' . YDConfig::get( 'dir_uploads', 'uploads' ) . '/item_' . $item['id'] .'/';

            // Get the list of pictures if any
            if ( is_dir( $imgPath ) ) {

                // Get a handle to the directory
                $dir = new YDFSDirectory( dirname( __FILE__ ) . '/../' . YDConfig::get( 'dir_uploads', 'uploads' ) . '/item_' . $item['id'] .'/' );

                // Get the list of files
                $images = $dir->getContents( array( '!index.html', '!index.php', '!m_*.*', '!s_*.*' ), null, array( 'YDFSImage' ) );

                // Make the relative path for each file
                foreach ( $images as $key=>$image ) {

                    // Generate the thumbnails if not there yet
                    if ( ! is_file( $dir->getAbsolutePath() . '/s_' . $image->getBasename() ) ) {
                        $image->saveThumbnail( 48, 48, $dir->getAbsolutePath() . '/s_' . $image->getBasename() );
                    }
                    if ( ! is_file( $dir->getAbsolutePath() . '/m_' . $image->getBasename() ) ) {
                        $image->saveThumbnail( 100, 100, $dir->getAbsolutePath() . '/m_' . $image->getBasename() );
                    }

                    // Set the relative path
                    $dir_uploads = dirname( __FILE__ ) . '/../' . YDConfig::get( 'dir_uploads', 'uploads' );
                    $dir_uploads = new YDFSDirectory( $dir_uploads );
                    $image->relative_path = str_replace( $dir_uploads->getAbsolutePath(), '', $image->getAbsolutePath() );

                    // Update the backslashes
                    $image->relative_path = ltrim( str_replace( '\\', '/', $image->relative_path ), '/' );

                    // Merge the title and description if any
                    $image = $this->getItemImageMetaData( $image );

                    // Make links to the thumbnails
                    $image->relative_path_s = dirname( $image->relative_path ) . '/s_' . basename( $image->relative_path );
                    $image->relative_path_m = dirname( $image->relative_path ) . '/m_' . basename( $image->relative_path );

                    // Add the thumbnails as objects
                    $image->relative_path_s_obj = new YDFSImage( $dir_uploads->getAbsolutePath() . '/' . $image->relative_path_s );
                    $image->relative_path_m_obj = new YDFSImage( $dir_uploads->getAbsolutePath() . '/' . $image->relative_path_m );

                    // Update the original image
                    $images[$key] = $image;

                }

                // Add it to the item
                $item['images'] = $images;

            } else {

                // No images for this item
                $item['images'] = array();

            }

            // Fix the image paths
            if( strtolower( basename( dirname( YD_SELF_FILE ) ) ) != 'manage' ) {
                $uploads = YDConfig::get( 'dir_uploads', 'uploads' );
                $item['body'] = str_replace( '../' . $uploads, $uploads, $item['body'] );
                $item['body_more'] = str_replace( '../' . $uploads, $uploads, $item['body_more'] );
            }

            // Get the count of images
            $item['num_images'] = sizeof( $item['images'] );

            // Return the fixed item
            return $item;

        }

        // Fix a list of items
        function _fixItems( $items, $order='created desc, title' ) {
            foreach ( $items as $key=>$item ) {
                $items[$key] = $this->_fixItem( $item, $order );
            }
            return $items;
        }

        // Get the title and description for an item image
        function getItemImageMetaData( $image ) {

            // Get the image metadata if not there yet
            if ( is_null( $this->imagemetadata ) ) {
                $this->imagemetadata = $this->db->getRecords( 'SELECT * FROM #_imagemetadata' );
                $this->imagemetadata = YDArrayUtil::convertToNested( $this->imagemetadata, 'img_path' );
                foreach ( $this->imagemetadata as $key=>$val ) {
                    $this->imagemetadata[$key] = $val[0];
                }
            }

            // Start with the default settings
            $image->title = $image->getBasenameNoExt();
            $image->description = '';

            // Add the image data if any
            if ( isset( $this->imagemetadata[ $image->relative_path ] ) ) {
                $image->title = $this->imagemetadata[ $image->relative_path ]['title'];
                $image->description = $this->imagemetadata[ $image->relative_path ]['description'];
            }

            // Return the image metadata
            return $image;

        }

        // Update the image data for an item in the database
        function setItemImageMetaData( $item, $data ) {
            $values = $data;
            $values['img_path'] = $item;
            $result = $this->_executeUpdate( '#_imagemetadata', $values, 'img_path = ' . $this->str( $item ) );
            if ( $result == '0' ) {
                $result = $this->_executeInsert( '#_imagemetadata', $values );
            }
            return $result;
        }

        // Function to get a single item
        function getItem( $where ) {
            $result = $this->getItems( 1, 0, 'created desc, title', $where );
            return ( $result && sizeof( $result ) == 1 ) ? $result[0] : $result;
        }

        // Get the non draft items
        function getPublicItems( $limit=-1, $offset=-1, $order='created desc, title', $where='AND is_draft = 0' ) {
            return $this->getItems( $limit, $offset, $order, $where );
        }

        // Function to get the items of the weblog
        function getItems( $limit=-1, $offset=-1, $order='created desc, title', $where='' ) {
            $sql = 'SELECT i.id, i.category_id, i.title, i.body, i.body_more, i.num_comments, i.created, i.modified, '
                 . 'i.is_draft, '
                 . 'c.title as category, u.email as user_email, u.name as user_name FROM #_items i, #_categories c, '
                 . '#_users u WHERE i.category_id = c.id AND i.user_id = u.id ';
            $sql = $this->_prepareQuery( $sql . $where, $order );
            return $this->_fixItems( $this->db->getRecords( $sql, $limit, $offset ) );
        }

        // Get related items
        function getRelatedItemsByItem( $limit, $item ) {

            // Get the items from the same category
            $items = $this->getItems(
                -1, -1, 'created desc, title', 'AND is_draft = 0 AND i.category_id = '
              . $this->str( $item['category_id'] ) . ' and i.id <> ' . $this->str( $item['id'] )
            );

            // Shuffle the items
            shuffle( $items );

            // Get the right rows
            $items = array_slice( $items, 0, $limit );

            // Sort them by date
            $sorted = array();
            foreach ( $items as $key=>$item ) {
                $sorted[ $key ] = intval( $item['created'] );
            }
            sort( $sorted );

            // Rearrange the array
            $sorted_items = array();
            foreach ( $sorted as $key=>$date ) {
                array_push( $sorted_items, $items[ $key ] );
            }

            // Return the items
            return array_values( $sorted_items );

        }

        // Get a publc item by it's ID
        function getPublicItemByID( $item_id ) {
            return $this->getItem( 'AND is_draft = 0 AND i.id = ' . $this->str( $item_id ) );
        }

        // Get an item by it's ID
        function getItemById( $item_id ) {
            return $this->getItem( 'AND i.id = ' . $this->str( $item_id ) );
        }

        // Add an item
        function addItem( $values ) {
            return $this->_executeInsert( '#_items', $values );
        }

        // Update an item
        function updateItem( $values ) {
            unset( $values['user_id'] );
            return $this->db->executeUpdate( '#_items', $values, 'id = ' . $this->str( $values['id'] ) );
        }

        // Delete an item
        function deleteItem( $item_id ) {
            $sql = $this->_prepareQuery( 'DELETE FROM #_comments WHERE item_id = ' . $this->str( $item_id ) );
            $this->db->executeSql( $sql );
            return $this->_deleteFromTableUsingID( '#_items', $item_id );
        }

        // Get the comments for an item
        function getComments( $item_id=null, $order='created', $limit=-1, $offset=-1, $public_only=false, $spam_only=false ) {
            $query = 'SELECT c.id as id, c.item_id as item_id, c.username as username, c.useremail as useremail, c.userwebsite as userwebsite, c.userip as userip, c.useragent as useragent, c.userrequrl as userrequrl, c.comment as comment, c.is_spam as is_spam, c.created as created, c.modified as modified, i.title as item_title, i.is_draft as item_is_draft FROM #_comments c, #_items i WHERE c.item_id = i.id';
            if ( $item_id ) {
                $query .= ' and item_id = ' . $this->str( $item_id );
            }
            if ( $public_only == true ) {
                $query .= ' and i.is_draft = 0';
            }
            if ( $spam_only == true ) {
                $query .= ' and c.is_spam = 1';
            } else {
                $query .= ' and c.is_spam = 0';
            }
            $records = $this->db->getRecords( $this->_prepareQuery( $query, $order ), $limit, $offset );
            foreach ( $records as $key=>$record ) {
                $records[$key]['comment'] = trim( $record['comment'] );
            }
            return $records;
        }

        // Get a comment by it's ID
        function getCommentById( $comment_id ) {
            $sql = $this->_prepareQuery( 'SELECT c.id as id, c.item_id as item_id, c.username as username, c.useremail as useremail, c.userwebsite as userwebsite, c.userip as userip, c.useragent as useragent, c.userrequrl as userrequrl, c.comment as comment, c.is_spam as is_spam, c.created as created, c.modified as modified, i.title as item_title FROM #_comments c, #_items i WHERE c.item_id = i.id and c.id = ' . $this->str( $comment_id ) );
            $record  = $this->db->getRecord( $sql );
            $record['comment'] = trim( $record['comment'] );
            return $record;
        }

        // Add a comment
        function addComment( $values ) {

            // Update the values
            $values['userip'] = $_SERVER['REMOTE_ADDR'];
            $values['useragent'] = $_SERVER['HTTP_USER_AGENT'];
            $values['userrequrl'] = $_SERVER['REQUEST_URI'];
            $values['is_spam'] = 0;

            // Check against akismet if a key is there
            if ( YDConfig::get( 'akismet_key', '' ) != '' ) {

                // Include the YDAkismet addon
                include_once( YD_DIR_HOME_ADD . '/YDAkismet/YDAkismet.php' );

                // Get the URL of the weblog
                $weblog_url = dirname( YDRequest::getCurrentUrl( true ) ) . '/';

                // Initialize YDAkismet
                $akismet = new YDAkismet( $weblog_url, YDConfig::get( 'akismet_key', '' ) );

                // Check if it's spam or not
                $result = $akismet->checkComment(
                    $values['comment'], $values['username'], $values['useremail'], $values['userwebsite'],
                    $values['userip'], $values['useragent']
                );

                // Update the comment values
                if ( $result == NULL || $result === false ) {
                    $values['is_spam'] = 0;
                } else {
                    $values['is_spam'] = 1;
                }

            }

            // Check the amount of hyperlinks in the comments. More than 3 is marked as spam.
            $count1 = preg_match_all( "/href=/i", $values['comment'], $matches1 );
            $count2 = preg_match_all( "/\[url=/i", $values['comment'], $matches2 );
            if ( ( $count1 + $count2 ) > 3 ) {
                $values['is_spam'] = 1;
            }

            // Add the comment
            $result = $this->_executeInsert( '#_comments', $values );
            $comment_id = $this->db->getLastInsertID();

            // Only update the items table if not spam
            if ( ! $values['is_spam'] ) {
                $sql = 'UPDATE #_items SET num_comments = num_comments+1 WHERE id = ' . $this->str( $values['item_id'] );
                $this->db->executeSql( $sql );
            }

            // Update the comment
            $values['id'] = $comment_id;

            // Return the comment id
            return $values;

        }

        // Update a comment
        function updateComment( $values ) {
            return $this->db->executeUpdate( '#_comments', $values, 'id = ' . $this->str( $values['id'] ) );
        }

        // Update a comment and mark it as spam
        function updateCommentAsSpam( $comment_id ) {
            $comment = $this->getCommentById( $comment_id );
            $comment['is_spam'] = 1;
            unset( $comment['item_title'] );
            $this->updateComment( $comment );
            $sql = 'UPDATE #_items SET num_comments = num_comments-1 WHERE id = ' . $this->str( $comment['item_id'] );
            $this->db->executeSql( $sql );
        }

        // Update a comment and unmark it as spam
        function updateCommentAsNotSpam( $comment_id ) {
            $comment = $this->getCommentById( $comment_id );
            $comment['is_spam'] = 0;
            unset( $comment['item_title'] );
            $this->updateComment( $comment );
            $sql = 'UPDATE #_items SET num_comments = num_comments+1 WHERE id = ' . $this->str( $comment['item_id'] );
            $this->db->executeSql( $sql );
        }

        // Delete a comment
        function deleteComment( $comment_id ) {
            $comment = $this->getCommentById( $comment_id );
            $result = $this->_deleteFromTableUsingID( '#_comments', $comment_id );
            if ( ! $comment['is_spam'] ) {
                $sql = 'UPDATE #_items SET num_comments = num_comments-1 WHERE id = ' . $this->str( $comment['item_id'] );
                $this->db->executeSql( $sql );
            }
            return $result;
        }

        // Get the comment subscriber list
        function getCommentSubscribers( $item_id ) {
            $sql = $this->_prepareQuery(
                'SELECT DISTINCT useremail FROM #_comments WHERE item_id = ' . $this->str( $item_id )
              . ' AND NOT ISNULL( useremail ) AND useremail <> \'\''
            );
            return $this->db->getValuesByName( $sql, 'useremail' );
        }

        // Get the categories
        function getCategories( $order='title' ) {
            $result = $this->_selectFromTable( '#_categories', $order );
            $sql = $this->_prepareQuery( 'SELECT category_id, COUNT(*) AS num_items FROM #_items i WHERE is_draft=0 GROUP BY category_id' );
            $num_items = $this->db->getAsAssocArray( $sql, 'category_id', 'num_items' );
            foreach ( $result as $key=>$val ) {
                if ( isset( $num_items[ $val['id'] ] ) ) {
                    $result[ $key ]['num_items'] = $num_items[ $val['id'] ];
                } else {
                    $result[ $key ]['num_items'] = '0';
                }
            }
            return $result;
        }

        // Get the categories as an associative array
        function getCategoriesAsAssoc( $order='title' ) {
            $sql = $this->_prepareQuery( 'SELECT id, title FROM #_categories', $order );
            return $this->db->getAsAssocArray( $sql, 'id', 'title' );
        }

        // Get a category by it's ID
        function getCategoryById( $category_id ) {
            return $this->_selectFromTableUsingID( '#_categories', $category_id );
        }

        // Get a category by it's name
        function getCategoryByName( $name ) {
            $sql = $this->_prepareQuery( 'SELECT * FROM #_categories WHERE title = ' . $this->str( $name ) );
            return $this->db->getRecord( $sql );
        }

        // Get the items by category
        function getItemsByCategoryId( $category_id, $limit=-1, $offset=-1, $order='created desc, title' ) {
            return $this->getItems( $limit, $offset, $order, 'AND is_draft = 0 AND i.category_id = ' . $this->str( $category_id ) );
        }

        // Add a category
        function addCategory( $values ) {
            return $this->_executeInsert( '#_categories', $values );
        }

        // Update a category
        function updateCategory( $values ) {
            return $this->db->executeUpdate( '#_categories', $values, 'id = ' . $this->str( $values['id'] ) );
        }

        // Delete a category
        function deleteCategory( $category_id ) {
            return $this->_deleteFromTableUsingID( '#_categories', $category_id );
        }

        // Get the public pages
        function getPublicPages( $order='title', $where=' AND p.is_draft=0' ) {
            return $this->getPages( $order, $where );
        }

        // Get the pages
        function getPages( $order='title', $where='' ) {
            $sql = $this->_prepareQuery(
                'SELECT p.id, p.title, p.body, p.is_draft, p.created, p.modified, u.email as user_email, u.name as user_name '
              . 'FROM #_pages p, #_users u WHERE p.user_id = u.id'
            );
            if ( ! empty( $where ) ) {
                $sql .= ' ' . $where;
            }
            return $this->db->getRecords( $sql );
        }

        // Get a public page by it's ID
        function getPublicPageByID( $page_id, $where=' AND p.is_draft=0' ) {
            return $this->getPageByID( $page_id, $where );
        }

        // Get a page by it's ID
        function getPageByID( $page_id, $where='' ) {
            $sql = $this->_prepareQuery(
                'SELECT p.id, p.title, p.body, p.is_draft, p.created, p.modified, u.email as user_email, u.name as user_name FROM '
                . '#_pages p, #_users u WHERE p.user_id = u.id AND p.id = ' . $this->str( $page_id )
            );
            if ( ! empty( $where ) ) {
                $sql .= ' ' . $where;
            }
            return $this->db->getRecord( $sql );
        }

        // Add a page
        function addPage( $values ) {
            return $this->_executeInsert( '#_pages', $values );
        }

        // Update a page
        function updatePage( $values ) {
            unset( $values['user_id'] );
            return $this->db->executeUpdate( '#_pages', $values, 'id = ' . $this->str( $values['id'] ) );
        }

        // Delete a page
        function deletePage( $page_id ) {
            return $this->_deleteFromTableUsingID( '#_pages', $page_id );
        }

        // Get the links
        function getLinks( $order='title' ) {
            return $this->_selectFromTable( '#_links', $order );
        }

        // Get a link by it's ID
        function getLinkByID( $link_id ) {
            return $this->_selectFromTableUsingID( '#_links', $link_id );
        }

        // Get a link by it's url
        function getLinkByUrl( $url ) {
            $sql = $this->_prepareQuery( 'SELECT * FROM #_links WHERE url = ' . $this->str( $url ) );
            return $this->db->getRecord( $sql );
        }

        // Add a link
        function addLink( $values ) {
            return $this->_executeInsert( '#_links', $values );
        }

        // Update a link
        function updateLink( $values ) {
            return $this->db->executeUpdate( '#_links', $values, 'id = ' . $this->str( $values['id'] ) );
        }

        // Delete a link
        function deleteLink( $link_id ) {
            return $this->_deleteFromTableUsingID( '#_links', $link_id );
        }

        // Function to increase the link num_visits
        function updateLinkNumVisits( $link_id ) {
            $sql = 'UPDATE #_links SET num_visits = num_visits+1 WHERE id = ' . $this->str( $link_id );
            $this->db->executeSql( $sql );
        }

        // Check the user login
        function checkLogin( $user, $password ) {
            return $this->db->getRecord(
                'SELECT * FROM #_users WHERE name = ' . $this->str( $user ) . ' AND password = ' . $this->str( $password )
            );
        }

        // Get the number of items
        function getStatsItemCount() {
            return $this->db->getValue( 'SELECT count(*) FROM #_items' );
        }

        // Get the number of comments
        function getStatsCommentCount() {
            return $this->db->getValue( 'SELECT count(*) FROM #_comments' );
        }

        // Get the commenter statistics
        function getCommenterStats( $limit=10 ) {
            return $this->db->getRecords(
                'SELECT username, count(*) as hits FROM #_comments GROUP BY username ORDER BY hits DESC', $limit
            );
        }

        // Get the list of users
        function getUsers( $order='name' ) {
            $sql = $this->_prepareQuery( 'SELECT * FROM #_users', $order );
            return $this->db->getRecords( $sql );
        }

        // Function to get a user by it's ID
        function getUserByID( $user_id ) {
            return $this->_selectFromTableUsingID( '#_users', $user_id );
        }

        // Function to get a user by his name
        function getUserByName( $user_name ) {
            $sql = 'SELECT * FROM #_users WHERE LOWER( name ) = ' . $this->str( strtolower( $user_name ) );
            return $this->db->getRecord( $sql );
        }

        // Replace the user with another one
        function replaceUser( $old, $new ) {
            $sql = 'UPDATE #_items SET user_id = ' . $this->str( $new ) . ' WHERE user_id = ' . $this->str( $old );
            $this->db->executeSql( $sql );
            $sql = 'UPDATE #_pages SET user_id = ' . $this->str( $new ) . ' WHERE user_id = ' . $this->str( $old );
            $this->db->executeSql( $sql );
        }

        // Function to save a user
        function saveUser( $user ) {

            // Unset the submit value
            unset( $user['cmdSubmit'] );

            // Convert the password to md5
            if ( ! empty( $user['password'] ) ) {
                $user['password'] = md5( $user['password'] );
            } else {
                unset( $user['password'] );
            }

            // Check if we need to update or insert
            if ( empty( $user['id'] ) ) {
                $user['created']= time();
                $user['modified'] = time();
                unset( $user['id'] );
                return $this->db->executeInsert( '#_users', $user );
            } else {
                if ( empty( $user['created'] ) ) {
                    $user['created']= time();
                }
                $user['modified'] = time();
                unset( $user['name'] );
                return $this->db->executeUpdate( '#_users', $user, 'id = ' . $this->str( $user['id'] ) );
            }

        }

        // Delete a user from the database
        function deleteUser( $user_id ) {
            $sql = 'DELETE FROM #_users WHERE id = ' . $this->str( $user_id );
            $this->db->executeSql( $sql );
        }

        // Prepare a query
        function _prepareQuery( $sql, $order=null ) {
            return is_null( $order ) ? $sql : $sql . ' ORDER BY ' . $order;
        }

        // Execute an insert
        function _executeInsert( $table, $values ) {
            if ( @ empty( $values['created'] ) ) {
                $values['created'] = time();
            }
            if ( @ empty( $values['modified'] ) ) {
                $values['modified'] = time();
            }
            foreach ( $values as $key=>$val ) {
                if ( substr( $key, 0, 3 ) == 'cmd' ) {
                    unset( $values[$key] );
                }
            }
            return $this->db->executeInsert( $table, $values );
        }

        // Execute an update
        function _executeUpdate( $table, $values, $where ) {
            if ( @ empty( $values['created'] ) ) {
                $values['created'] = time();
            }
            $values['modified'] = time();
            foreach ( $values as $key=>$val ) {
                if ( substr( $key, 0, 3 ) == 'cmd' ) {
                    unset( $values[$key] );
                }
            }
            return $this->db->executeUpdate( $table, $values, $where );
        }

        // Select items from a table with a given table name
        function _selectFromTable( $table, $order=null, $limit=-1, $offset=-1 ) {
            $sql = $this->_prepareQuery( 'SELECT * FROM ' . $table, $order, $limit, $offset );
            return $this->db->getRecords( $sql );
         }

        // Select items from a table with a given id
        function _selectFromTableUsingID( $table, $id ) {
            $sql = $this->_prepareQuery( 'SELECT * FROM ' . $table . ' WHERE id = ' . $this->str( $id ) );
            return $this->db->getRecord( $sql );
        }

        // Delete items from a table with a given id
        function _deleteFromTableUsingID( $table, $id ) {
            $sql = $this->_prepareQuery( 'DELETE FROM ' . $table . ' WHERE id = ' . $this->str( $id ) );
            return $this->db->executeSql( $sql );
        }

        // Escapes a string
        function str( $t ) {
            return '\''. $this->db->escape( $t ) . '\'';
        }

        // Get a translation from the translation table
        function t( $t ) {
            return YDTplModT( array( 'w' => $t ) );
        }

    }

?>
