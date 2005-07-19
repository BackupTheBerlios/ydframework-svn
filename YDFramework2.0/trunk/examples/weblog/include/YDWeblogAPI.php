<?php

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
                $sql = $this->db->_createSqlUpdate( YDConfig::get( 'db_prefix', '' ) . 'statistics', $values, $where );
                $sql = str_replace( ' WHERE', ',hits=hits+1 WHERE', $sql );

                // Execute the SQL
                $affected = $this->db->executeSql( $sql );
                if ( $affected === 0 ) {
                    $values['hits'] = '1';
                    $this->db->executeInsert( YDConfig::get( 'db_prefix', '' ) . 'statistics', $values );
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
                $sql = $this->_prepareQuery( 'SELECT id FROM ' . YDConfig::get( 'db_prefix', '' ) . 'items', $order );
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

        // Function to get the items of the weblog
        function getItems( $limit=-1, $offset=-1, $order='created desc, title', $where='' ) {
            $sql = 'SELECT i.id, i.category_id, i.title, i.body, i.num_comments, i.created, i.modified, '
                 . 'c.title as category, u.email as user_email, u.name as user_name FROM ' . YDConfig::get( 'db_prefix', '' ) . 'items i, ' . YDConfig::get( 'db_prefix', '' ) . 'categories c, '
                  . YDConfig::get( 'db_prefix', '' ) . 'users u WHERE i.category_id = c.id AND i.user_id = u.id ';
            $sql = $this->_prepareQuery( $sql . $where, $order );
            return $this->_fixItems( $this->db->getRecords( $sql, $limit, $offset ) );
        }

        // Get an item by it's ID
        function getItemById( $item_id ) {
            return $this->getItem( 'AND i.id = ' . $this->str( $item_id ) );
        }

        // Add an item
        function addItem( $values ) {
            return $this->_executeInsert( YDConfig::get( 'db_prefix', '' ) . 'items', $values );
        }

        // Update an item
        function updateItem( $values ) {
            return $this->db->executeUpdate( YDConfig::get( 'db_prefix', '' ) . 'items', $values, 'id = ' . $this->str( $values['id'] ) );
        }

        // Delete an item
        function deleteItem( $item_id ) {
            $sql = $this->_prepareQuery( 'DELETE FROM ' . YDConfig::get( 'db_prefix', '' ) . 'comments WHERE item_id = ' . $this->str( $item_id ) );
            $this->db->executeSql( $sql );
            return $this->_deleteFromTableUsingID( YDConfig::get( 'db_prefix', '' ) . 'items', $item_id );
        }

        // Get the comments for an item
        function getComments( $item_id=null, $order='created', $limit=-1, $offset=-1 ) {
            if ( $item_id ) {
                $sql = $this->_prepareQuery( 'SELECT * FROM ' . YDConfig::get( 'db_prefix', '' ) . 'comments WHERE item_id = ' . $this->str( $item_id ), $order );
            } else {
                $sql = $this->_prepareQuery( 'SELECT c.id as id, c.item_id as item_id, c.username as username, c.useremail as useremail, c.userwebsite as userwebsite, c.userip as userip, c.comment as comment, c.created as created, c.modified as modified, i.title as item_title FROM ' . YDConfig::get( 'db_prefix', '' ) . 'comments c, ' . YDConfig::get( 'db_prefix', '' ) . 'items i WHERE c.item_id = i.id', $order );
            }
            return $this->db->getRecords( $sql, $limit, $offset );
        }

        // Get a comment by it's ID
        function getCommentById( $comment_id ) {
            $sql = $this->_prepareQuery( 'SELECT c.id as id, c.item_id as item_id, c.username as username, c.useremail as useremail, c.userwebsite as userwebsite, c.userip as userip, c.comment as comment, c.created as created, c.modified as modified, i.title as item_title FROM ' . YDConfig::get( 'db_prefix', '' ) . 'comments c, ' . YDConfig::get( 'db_prefix', '' ) . 'items i WHERE c.item_id = i.id and c.id = ' . $this->str( $comment_id ) );
            return $this->db->getRecord( $sql );
        }

        // Add a comment
        function addComment( $values ) {
            $values['userip'] = $_SERVER["REMOTE_ADDR"];
            $result = $this->_executeInsert( YDConfig::get( 'db_prefix', '' ) . 'comments', $values );
            $sql = 'UPDATE ' . YDConfig::get( 'db_prefix', '' ) . 'items SET num_comments = num_comments+1 WHERE id = ' . $this->str( $values['item_id'] );
            $this->db->executeSql( $sql );
            return $result;
        }

        // Update a comment
        function updateComment( $values ) {
            return $this->db->executeUpdate( YDConfig::get( 'db_prefix', '' ) . 'comments', $values, 'id = ' . $this->str( $values['id'] ) );
        }

        // Delete a comment
        function deleteComment( $comment_id ) {
            $comment = $this->getCommentById( $comment_id );
            $result = $this->_deleteFromTableUsingID( YDConfig::get( 'db_prefix', '' ) . 'comments', $comment_id );
            $sql = 'UPDATE ' . YDConfig::get( 'db_prefix', '' ) . 'items SET num_comments = num_comments-1 WHERE id = ' . $this->str( $comment['item_id'] );
            $this->db->executeSql( $sql );
            return $result;
        }

        // Get the comment subscriber list
        function getCommentSubscribers( $item_id ) {
            $sql = $this->_prepareQuery(
                'SELECT DISTINCT useremail FROM ' . YDConfig::get( 'db_prefix', '' ) . 'comments WHERE item_id = ' . $this->str( $item_id )
              . ' AND NOT ISNULL( useremail ) AND useremail <> \'\''
            );
            return $this->db->getValuesByName( $sql, 'useremail' );
        }

        // Get the categories
        function getCategories( $order='title' ) {
            $result = $this->_selectFromTable( YDConfig::get( 'db_prefix', '' ) . 'categories', $order );
            $sql = $this->_prepareQuery( 'SELECT category_id, COUNT(*) AS num_items FROM ' . YDConfig::get( 'db_prefix', '' ) . 'items i GROUP BY category_id' );
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
            $sql = $this->_prepareQuery( 'SELECT id, title FROM ' . YDConfig::get( 'db_prefix', '' ) . 'categories', $order );
            return $this->db->getAsAssocArray( $sql, 'id', 'title' );
        }

        // Get a category by it's ID
        function getCategoryById( $category_id ) {
            return $this->_selectFromTableUsingID( YDConfig::get( 'db_prefix', '' ) . 'categories', $category_id );
        }

        // Get a category by it's name
        function getCategoryByName( $name ) {
            $sql = $this->_prepareQuery( 'SELECT * FROM ' . YDConfig::get( 'db_prefix', '' ) . 'categories WHERE title = ' . $this->str( $name ) );
            return $this->db->getRecord( $sql );
        }

        // Get the items by category
        function getItemsByCategoryId( $category_id, $limit=-1, $offset=-1, $order='created desc, title' ) {
            return $this->getItems( $limit, $offset, $order, 'AND i.category_id = ' . $this->str( $category_id ) );
        }

        // Add a category
        function addCategory( $values ) {
            return $this->_executeInsert( YDConfig::get( 'db_prefix', '' ) . 'categories', $values );
        }

        // Update a category
        function updateCategory( $values ) {
            return $this->db->executeUpdate( YDConfig::get( 'db_prefix', '' ) . 'categories', $values, 'id = ' . $this->str( $values['id'] ) );
        }

        // Delete a category
        function deleteCategory( $category_id ) {
            return $this->_deleteFromTableUsingID( YDConfig::get( 'db_prefix', '' ) . 'categories', $category_id );
        }

        // Get the pages
        function getPages( $order='title' ) {
            $sql = $this->_prepareQuery(
                'SELECT p.id, p.title, p.body, p.created, p.modified, u.email as user_email, u.name as user_name FROM '
                . YDConfig::get( 'db_prefix', '' ) . 'pages p, '
                . YDConfig::get( 'db_prefix', '' ) . 'users u WHERE p.user_id = u.id'
            );
            return $this->db->getRecords( $sql );
        }

        // Get a page by it's ID
        function getPageByID( $page_id ) {
            $sql = $this->_prepareQuery(
                'SELECT p.id, p.title, p.body, p.created, p.modified, u.email as user_email, u.name as user_name FROM '
                . YDConfig::get( 'db_prefix', '' ) . 'pages p,'
                . YDConfig::get( 'db_prefix', '' ) . 'users u WHERE p.user_id = u.id AND p.id = ' . $this->str( $page_id )
            );
            return $this->db->getRecord( $sql );
        }

        // Add a page
        function addPage( $values ) {
            return $this->_executeInsert( YDConfig::get( 'db_prefix', '' ) . 'pages', $values );
        }

        // Update a page
        function updatePage( $values ) {
            return $this->db->executeUpdate( YDConfig::get( 'db_prefix', '' ) . 'pages', $values, 'id = ' . $this->str( $values['id'] ) );
        }

        // Delete a page
        function deletePage( $page_id ) {
            return $this->_deleteFromTableUsingID( YDConfig::get( 'db_prefix', '' ) . 'pages', $page_id );
        }

        // Get the links
        function getLinks( $order='title' ) {
            return $this->_selectFromTable( YDConfig::get( 'db_prefix', '' ) . 'links', $order );
        }

        // Get a link by it's ID
        function getLinkByID( $link_id ) {
            return $this->_selectFromTableUsingID( YDConfig::get( 'db_prefix', '' ) . 'links', $link_id );
        }

        // Get a link by it's url
        function getLinkByUrl( $url ) {
            $sql = $this->_prepareQuery( 'SELECT * FROM ' . YDConfig::get( 'db_prefix', '' ) . 'links WHERE url = ' . $this->str( $url ) );
            return $this->db->getRecord( $sql );
        }

        // Add a link
        function addLink( $values ) {
            return $this->_executeInsert( YDConfig::get( 'db_prefix', '' ) . 'links', $values );
        }

        // Update a link
        function updateLink( $values ) {
            return $this->db->executeUpdate( YDConfig::get( 'db_prefix', '' ) . 'links', $values, 'id = ' . $this->str( $values['id'] ) );
        }

        // Delete a link
        function deleteLink( $link_id ) {
            return $this->_deleteFromTableUsingID( YDConfig::get( 'db_prefix', '' ) . 'links', $link_id );
        }

        // Function to increase the link num_visits
        function updateLinkNumVisits( $link_id ) {
            $sql = 'UPDATE ' . YDConfig::get( 'db_prefix', '' ) . 'links SET num_visits = num_visits+1 WHERE id = ' . $this->str( $link_id );
            $this->db->executeSql( $sql );
        }

        // Check the user login
        function checkLogin( $user, $password ) {
            return $this->db->getRecord(
                'SELECT * FROM ' . YDConfig::get( 'db_prefix', '' ) . 'users WHERE name = ' . $this->str( $user ) . ' AND password = ' . $this->str( $password )
            );
        }

        // Get the install date
        function getInstallDate() {
            return strtotime( $this->db->getValue( 'SELECT created FROM ' . YDConfig::get( 'db_prefix', '' ) . 'statistics_init LIMIT 1' ) );
        }

        // Get the total num of hits
        function getTotalHits() {
            return $this->db->getValue( 'SELECT SUM(hits) FROM ' . YDConfig::get( 'db_prefix', '' ) . 'statistics' );
        }

        // Get the number of items
        function getStatsItemCount() {
            return $this->db->getValue( 'SELECT count(*) FROM ' . YDConfig::get( 'db_prefix', '' ) . 'items' );
        }

        // Get the number of comments
        function getStatsCommentCount() {
            return $this->db->getValue( 'SELECT count(*) FROM ' . YDConfig::get( 'db_prefix', '' ) . 'comments' );
        }

        // Get the stats from the last 6 months
        function getStatsMonths( $limit=6 ) {
            return $this->db->getRecords(
                'SELECT DATE_FORMAT(date,\'%Y-%m\') AS YearMonth, SUM(hits) AS hits FROM ' . YDConfig::get( 'db_prefix', '' ) . 'statistics '
              . ' GROUP BY YearMonth ORDER BY date DESC', $limit
            );
        }

        // Get the stats from the last 7 days
        function getStatsDays( $limit=7 ) {
            return $this->db->getRecords(
                'SELECT date, SUM(hits) AS hits FROM ' . YDConfig::get( 'db_prefix', '' ) . 'statistics GROUP BY date ORDER BY date DESC', $limit
            );
        }

        // Get the top 10 URLs
        function getStatsUrls( $limit=10 ) {
            return $this->db->getRecords(
                'SELECT uri, SUM(hits) AS hits FROM ' . YDConfig::get( 'db_prefix', '' ) . 'statistics GROUP BY uri ORDER BY hits DESC', $limit
            );
        }

        // Get the browser statistics
        function getStatsBrowser() {
            $browsers = array(
                'ie' => 'Internet Explorer', 'mozilla' => 'Netscape/Mozilla', 'safari' => 'Apple Safari',
                'opera' => 'Opera', 'firefox' => 'FireFox', 'other' => t('other'), 'unknown' => t('other')
            );
            $browserStats = $this->db->getRecords(
                'SELECT browser, SUM(hits) AS hits FROM ' . YDConfig::get( 'db_prefix', '' ) . 'statistics GROUP BY browser ORDER BY hits DESC'
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
                'SELECT platform, SUM(hits) AS hits FROM ' . YDConfig::get( 'db_prefix', '' ) . 'statistics GROUP BY platform ORDER BY hits DESC'
            );
            if ( sizeof( $osStats ) > 0 ) {
                foreach ( $osStats as $key=>$val ) {
                    $osStats[$key]['platform'] = $platforms[ $val['platform'] ];
                }
            }
            return $osStats;
        }

        // Get the list of users
        function getUsers( $order='name' ) {
            $sql = $this->_prepareQuery( 'SELECT * FROM ' . YDConfig::get( 'db_prefix', '' ) . 'users', $order );
            return $this->db->getRecords( $sql );
        }

        // Function to get a user by it's ID
        function getUserByID( $user_id ) {
            return $this->_selectFromTableUsingID( YDConfig::get( 'db_prefix', '' ) . 'users', $user_id );
        }

        // Function to get a user by his name
        function getUserByName( $user_name ) {
            $sql = 'SELECT * FROM ' . YDConfig::get( 'db_prefix', '' ) . 'users WHERE LOWER( name ) = ' . $this->str( strtolower( $user_name ) );
            return $this->db->getRecord( $sql );
        }

        // Replace the user with another one
        function replaceUser( $old, $new ) {
            $sql = 'UPDATE ' . YDConfig::get( 'db_prefix', '' ) . 'items SET user_id = ' . $this->str( $new ) . ' WHERE user_id = ' . $this->str( $old );
            $this->db->executeSql( $sql );
            $sql = 'UPDATE ' . YDConfig::get( 'db_prefix', '' ) . 'pages SET user_id = ' . $this->str( $new ) . ' WHERE user_id = ' . $this->str( $old );
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
                return $this->db->executeInsert( YDConfig::get( 'db_prefix', '' ) . 'users', $user );
            } else {
                if ( empty( $user['created'] ) ) {
                    $user['created']= time();
                }
                $user['modified'] = time();
                unset( $user['name'] );
                return $this->db->executeUpdate( YDConfig::get( 'db_prefix', '' ) . 'users', $user, 'id = ' . $this->str( $user['id'] ) );
            }

        }

        // Delete a user from the database
        function deleteUser( $user_id ) {
            $sql = 'DELETE FROM ' . YDConfig::get( 'db_prefix', '' ) . 'users WHERE id = ' . $this->str( $user_id );
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
            return '\''. $this->db->string( $t ) . '\'';
        }

        // Get a translation from the translation table
        function t( $t ) {
            return YDTplModT( array( 'w' => $t ) );
        }

    }

?>
