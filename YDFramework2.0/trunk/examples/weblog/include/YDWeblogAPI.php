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
        if ( ! isset( $values['auto_close_items'] ) ) {
            $values['auto_close_items'] = 45;
        }
        if ( ! isset( $values['include_debug_info'] ) ) {
            $values['include_debug_info'] = 0;
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

            // Add any missing items field
            $fields = $this->db->getValuesByName( 'show fields from #_items', 'field' );
            if ( ! in_array( 'body_more', $fields ) ) {
                $this->db->executeSql( 'ALTER TABLE #_items ADD body_more LONGTEXT AFTER body' );
            }
            if ( ! in_array( 'allow_comments', $fields ) ) {
                $this->db->executeSql( 'ALTER TABLE #_items ADD allow_comments TINYINT(1) DEFAULT "1" NOT NULL AFTER num_comments' );
            }
            if ( ! in_array( 'auto_close', $fields ) ) {
                $this->db->executeSql( 'ALTER TABLE #_items ADD auto_close TINYINT(1) DEFAULT "1" NOT NULL AFTER allow_comments' );
            }
            if ( ! in_array( 'is_draft', $fields ) ) {
                $this->db->executeSql( 'ALTER TABLE #_items ADD is_draft TINYINT(1) DEFAULT "0" NOT NULL AFTER auto_close' );
            }

            // Add any missing pages field
            $fields = $this->db->getValuesByName( 'show fields from #_pages', 'field' );
            if ( ! in_array( 'is_draft', $fields ) ) {
                $this->db->executeSql( 'ALTER TABLE #_pages ADD is_draft TINYINT(1) DEFAULT "0" NOT NULL AFTER body' );
            }

            // Get the list of indexes for the items table
            $indexes = $this->db->getValuesByName( 'show keys from #_items', 'key_name' );
            if ( ! in_array( 'allow_comments', $indexes ) ) {
                $this->db->executeSql( 'ALTER TABLE #_items ADD INDEX allow_comments (allow_comments)' );
            }
            if ( ! in_array( 'auto_close', $indexes ) ) {
                $this->db->executeSql( 'ALTER TABLE #_items ADD INDEX auto_close (auto_close)' );
            }
            if ( ! in_array( 'is_draft', $indexes ) ) {
                $this->db->executeSql( 'ALTER TABLE #_items ADD INDEX is_draft (is_draft)' );
            }

            // Get the list of indexes for the items table
            $indexes = $this->db->getValuesByName( 'show keys from #_pages', 'key_name' );
            if ( ! in_array( 'is_draft', $indexes ) ) {
                $this->db->executeSql( 'ALTER TABLE #_pages ADD INDEX is_draft (is_draft)' );
            }

            // Get the list of indexes
            $indexes = $this->db->getValuesByName( 'show keys from #_users', 'key_name' );
            if ( in_array( 'email', $indexes ) ) {
                $this->db->executeSql( 'ALTER TABLE #_users DROP INDEX email' );
            }
            if ( ! in_array( 'name', $indexes ) ) {
                $this->db->executeSql( 'ALTER TABLE #_users ADD UNIQUE name (name)' );
            }

            // Auto close the old items if needed
            $auto_close_items = YDConfig::get( 'auto_close_items', '' );
            if ( $auto_close_items != '' && is_numeric( $auto_close_items ) ) {

                // Calculate the treshold
                $treshold = time() - ( $auto_close_items * 86400 );

                // Close the items
                $this->db->executeSql( 'UPDATE #_items SET allow_comments=1 WHERE auto_close = 1' );
                $this->db->executeSql( 'UPDATE #_items SET allow_comments=0 WHERE auto_close = 1 AND created < ' . $treshold );

            }

        }

        // Function to log a request to the statistics
        function logRequestToStats( $values ) {

            // Default to index.php
            if ( ! empty( $values['uri'] ) ) {

                // Create the where clause
                $where = array();
                foreach ( $values as $key=>$val ) {
                    array_push( $where, $key . '=' . $this->str( $val ) );
                }
                $where =implode( ' and ', $where );

                // Generate the SQL statement
                $sql = $this->db->_createSqlUpdate( '#_statistics', $values, $where );
                $sql = str_replace( ' WHERE', ',hits=hits+1 WHERE', $sql );

                // Execute the SQL
                $affected = $this->db->executeSql( $sql );
                if ( $affected === 0 ) {
                    $values['hits'] = '1';
                    $this->db->executeInsert( '#_statistics', $values );
                }

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
                    $image->relative_path = str_replace( '\\', '/', $image->relative_path );

                    // Remove the leading slash
                    if ( substr( $image->relative_path, 0, 1 ) == '/' ) {
                        $image->relative_path = substr( $image->relative_path, 1 );
                    }

                    // Make links to the thumbnails
                    $image->relative_path_s = dirname( $image->relative_path ) . '/s_' . basename( $image->relative_path );
                    $image->relative_path_m = dirname( $image->relative_path ) . '/m_' . basename( $image->relative_path );

                    // Update the original image
                    $images[$key] = $image;

                }

                // Add it to the item
                $item['images'] = $images;

            } else {

                // No images for this item
                $item['images'] = array();

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
                 . 'i.allow_comments, i.auto_close, i.is_draft, '
                 . 'c.title as category, u.email as user_email, u.name as user_name FROM #_items i, #_categories c, '
                 . '#_users u WHERE i.category_id = c.id AND i.user_id = u.id ';
            $sql = $this->_prepareQuery( $sql . $where, $order );
            return $this->_fixItems( $this->db->getRecords( $sql, $limit, $offset ) );
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
        function getComments( $item_id=null, $order='created', $limit=-1, $offset=-1 ) {
            if ( $item_id ) {
                $sql = $this->_prepareQuery( 'SELECT * FROM #_comments WHERE item_id = ' . $this->str( $item_id ), $order );
            } else {
                $sql = $this->_prepareQuery( 'SELECT c.id as id, c.item_id as item_id, c.username as username, c.useremail as useremail, c.userwebsite as userwebsite, c.userip as userip, c.comment as comment, c.created as created, c.modified as modified, i.title as item_title FROM #_comments c, #_items i WHERE c.item_id = i.id', $order );
            }
            $records = $this->db->getRecords( $sql, $limit, $offset );
            foreach ( $records as $key=>$record ) {
                $records[$key]['comment'] = trim( strip_tags( $record['comment'] ) );
            }
            return $records;
        }

        // Get a comment by it's ID
        function getCommentById( $comment_id ) {
            $sql = $this->_prepareQuery( 'SELECT c.id as id, c.item_id as item_id, c.username as username, c.useremail as useremail, c.userwebsite as userwebsite, c.userip as userip, c.comment as comment, c.created as created, c.modified as modified, i.title as item_title FROM #_comments c, #_items i WHERE c.item_id = i.id and c.id = ' . $this->str( $comment_id ) );
            $record  = $this->db->getRecord( $sql );
            $record['comment'] = trim( strip_tags( $record['comment'] ) );
            return $record;
        }

        // Add a comment
        function addComment( $values ) {
            $values['userip'] = $_SERVER['REMOTE_ADDR'];
            $result = $this->_executeInsert( '#_comments', $values );
            $sql = 'UPDATE #_items SET num_comments = num_comments+1 WHERE id = ' . $this->str( $values['item_id'] );
            $this->db->executeSql( $sql );
            return $result;
        }

        // Update a comment
        function updateComment( $values ) {
            return $this->db->executeUpdate( '#_comments', $values, 'id = ' . $this->str( $values['id'] ) );
        }

        // Delete a comment
        function deleteComment( $comment_id ) {
            $comment = $this->getCommentById( $comment_id );
            $result = $this->_deleteFromTableUsingID( '#_comments', $comment_id );
            $sql = 'UPDATE #_items SET num_comments = num_comments-1 WHERE id = ' . $this->str( $comment['item_id'] );
            $this->db->executeSql( $sql );
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

        // Get the install date
        function getInstallDate() {
            return strtotime( $this->db->getValue( 'SELECT created FROM #_statistics_init LIMIT 1' ) );
        }

        // Get the total num of hits
        function getTotalHits() {
            return $this->db->getValue( 'SELECT SUM(hits) FROM #_statistics' );
        }

        // Get the number of items
        function getStatsItemCount() {
            return $this->db->getValue( 'SELECT count(*) FROM #_items' );
        }

        // Get the number of comments
        function getStatsCommentCount() {
            return $this->db->getValue( 'SELECT count(*) FROM #_comments' );
        }

        // Get the stats from the last 6 months
        function getStatsMonths( $limit=6 ) {
            YDInclude( 'YDDate.php' );
            $records = $this->db->getRecords(
                'SELECT DATE_FORMAT(date,\'%Y-%m\') AS YearMonth, SUM(hits) AS hits FROM #_statistics '
              . ' GROUP BY YearMonth ORDER BY date DESC', $limit
            );
            foreach ( $records as $key=>$record ) {
                $date = new YDDate( $record['yearmonth'] . '-01' );
                $records[$key]['yearmonth'] = $record['yearmonth'] . ' (' . strtolower( $date->getMonthName() ) . ')';
            }
            return $records;
        }

        // Get the stats from the last 7 days
        function getStatsDays( $limit=7 ) {
            YDInclude( 'YDDate.php' );
            $records = $this->db->getRecords(
                'SELECT date, SUM(hits) AS hits FROM #_statistics GROUP BY date ORDER BY date DESC', $limit
            );
            foreach ( $records as $key=>$record ) {
                $date = new YDDate( $record['date'] );
                $records[$key]['date'] = $record['date'] . ' (' . strtolower( $date->getDayName() ) . ')';
            }
            return $records;
        }

        // Get the top 10 URLs
        function getStatsUrls( $limit=10 ) {
            return $this->db->getRecords(
                'SELECT uri, SUM(hits) AS hits FROM #_statistics GROUP BY uri ORDER BY hits DESC', $limit
            );
        }

        // Get the browser statistics
        function getStatsBrowser() {
            $browsers = array(
                'ie' => 'Internet Explorer', 'mozilla' => 'Netscape/Mozilla', 'safari' => 'Apple Safari',
                'opera' => 'Opera', 'firefox' => 'FireFox', 'other' => t('other'), 'unknown' => t('other')
            );
            $browserStats = $this->db->getRecords(
                'SELECT browser, SUM(hits) AS hits FROM #_statistics GROUP BY browser ORDER BY hits DESC'
            );
            if ( sizeof( $browserStats ) > 0 ) {
                foreach ( $browserStats as $key=>$val ) {
                    $browserStats[$key]['browser'] = $browsers[ $val['browser'] ];
                }
            }
            return $browserStats;
        }

        // Get the OS statistics
        function getStatsOs() {
            $platforms = array(
                'win' => 'Windows', 'mac' => 'Macintosh', 'linux' => 'Linux', 'unix' => 'Unix', 'other' => 'Other', 'unknown' => t('other')
            );
            $osStats = $this->db->getRecords(
                'SELECT platform, SUM(hits) AS hits FROM #_statistics GROUP BY platform ORDER BY hits DESC'
            );
            if ( sizeof( $osStats ) > 0 ) {
                foreach ( $osStats as $key=>$val ) {
                    $osStats[$key]['platform'] = $platforms[ $val['platform'] ];
                }
            }
            return $osStats;
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
