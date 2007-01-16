<?php

    // Standard include
    include_once( dirname( __FILE__ ) . '/../YDFramework2/YDF2_init.php' );

    /*

        // ==== Schema description =====================================================================================
        CREATE TABLE nested_tree (
            id int(11) NOT NULL auto_increment,
            parent_id int(11) NOT NULL default '0',
            title varchar(255) NOT NULL default '',
            nleft int(11) NOT NULL default '0',
            nright int(11) NOT NULL default '0',
            nlevel int(11) NOT NULL default '0',
            position int(11) NOT NULL default '0',
            PRIMARY KEY  (id),
            KEY nested_tree_parent_id (parent_id),
            KEY nested_tree_nleft (nleft),
            KEY nested_tree_nright (nright),
            KEY nested_tree_nlevel (nlevel)
        );
        // =============================================================================================================

        // ==== Default values for testing =============================================================================
        INSERT INTO nested_tree VALUES (1,0,'General Resources',1,22,1,0);
        INSERT INTO nested_tree VALUES (2,1,'Code Paste',2,3,2,0);
        INSERT INTO nested_tree VALUES (3,1,'Documentation',4,5,2,0);
        INSERT INTO nested_tree VALUES (4,1,'Books & Publications',6,13,2,0);
        INSERT INTO nested_tree VALUES (5,4,'Apache',7,8,3,0);
        INSERT INTO nested_tree VALUES (6,4,'PostgreSQL',9,10,3,0);
        INSERT INTO nested_tree VALUES (7,4,'MySQL',11,12,3,0);
        INSERT INTO nested_tree VALUES (8,1,'Links',14,21,2,0);
        INSERT INTO nested_tree VALUES (9,8,'Databases',15,16,3,0);
        INSERT INTO nested_tree VALUES (10,8,'Generators',17,18,3,0);
        INSERT INTO nested_tree VALUES (11,8,'Portals',19,20,3,0);
        // =============================================================================================================

        // ==== Sample data dump =======================================================================================
        +----+-----------+----------------------+-------+--------+--------+----------+
        | id | parent_id | title                | nleft | nright | nlevel | position |
        +----+-----------+----------------------+-------+--------+--------+----------+
        |  1 |         0 | General Resources    |     1 |     22 |      1 |        0 |
        |  2 |         1 | Code Paste           |     2 |      3 |      2 |        0 |
        |  3 |         1 | Documentation        |     4 |      5 |      2 |        0 |
        |  4 |         1 | Books & Publications |     6 |     13 |      2 |        0 |
        |  5 |         4 | Apache               |     7 |      8 |      3 |        0 |
        |  6 |         4 | PostgreSQL           |     9 |     10 |      3 |        0 |
        |  7 |         4 | MySQL                |    11 |     12 |      3 |        0 |
        |  8 |         1 | Links                |    14 |     21 |      2 |        0 |
        |  9 |         8 | Databases            |    15 |     16 |      3 |        0 |
        | 10 |         8 | Generators           |    17 |     18 |      3 |        0 |
        | 11 |         8 | Portals              |    19 |     20 |      3 |        0 |
        +----+-----------+----------------------+-------+--------+--------+----------+
        // =============================================================================================================

    */

    // Includes
    YDInclude( 'YDRequest.php' );
    YDInclude( 'YDDatabase.php' );
    YDInclude( 'YDDatabaseTree.php' );

    // Class definition
    class database_tree extends YDRequest {

        // Class constructor
        function database_tree() {

            // Call the parent
            $this->YDRequest();

            // Get the database connection
            $db = YDDatabase::getInstance( 'mysql', 'tree_test', 'root', '', 'localhost' );

            // Get the tree instance
            $this->tree = new YDDatabaseTree( $db, 'nested_tree' );

        }

        // Default action
        function actionDefault() {

            // Show the link to the sitemap action
            echo( '<a href="' . YD_SELF_SCRIPT . '?do=sitemap">Sitemap</a>' );

            // Get a reference to the tree
            $tree = & $this->tree;

            // Get a single node
            YDDebugUtil::dump( $tree->getNode( 2 ), '$tree->getNode( 2 )' );

            // Get the children of a single node
            YDDebugUtil::dump( $tree->getChildren( 2 ), '$tree->getChildren( 2 )' );
            YDDebugUtil::dump( $tree->getChildren( 4 ), '$tree->getChildren( 4 )' );

            // Get the descendants of a single node
            YDDebugUtil::dump( $tree->getDescendants( 4, true ), '$tree->getDescendants( 4, true )' );
            YDDebugUtil::dump( $tree->getDescendants( 0, true ), '$tree->getDescendants( 0, true )' );

            // Get the path to a node
            YDDebugUtil::dump( $tree->getPath( 5 ), '$tree->getPath( 5 )' );
            YDDebugUtil::dump( $tree->getPath( 5, true ), '$tree->getPath( 5, true )' );

            // Check if descendant
            YDDebugUtil::dump( $tree->isDescendantOf( 9, 1 ), '$tree->isDescendantOf( 9, 1 )' );
            YDDebugUtil::dump( $tree->isDescendantOf( 9, 4 ), '$tree->isDescendantOf( 9, 4 )' );

            // Check if child of
            YDDebugUtil::dump( $tree->isChildOf( 9, 8 ), '$tree->isChildOf( 9, 8 )' );
            YDDebugUtil::dump( $tree->isChildOf( 9, 4 ), '$tree->isChildOf( 9, 4 )' );

            // Number of descendants
            YDDebugUtil::dump( $tree->numDescendants( 9 ), '$tree->numDescendants( 9 )' );
            YDDebugUtil::dump( $tree->numDescendants( 4 ), '$tree->numDescendants( 4 )' );
            YDDebugUtil::dump( $tree->numDescendants( 1 ), '$tree->numDescendants( 1 )' );

            // Number of children
            YDDebugUtil::dump( $tree->numChildren( 9 ), '$tree->numChildren( 9 )' );
            YDDebugUtil::dump( $tree->numChildren( 4 ), '$tree->numChildren( 4 )' );
            YDDebugUtil::dump( $tree->numChildren( 1 ), '$tree->numChildren( 1 )' );

            // Get the node's immediate family
            YDDebugUtil::dump( $tree->getImmediateFamily( 4 ), '$tree->getImmediateFamily( 4 )' );

            // Get the tree with all children
            YDDebugUtil::dump( $tree->getTreeWithChildren(), '$tree->getTreeWithChildren()' );

        }

        // Add a number of nodes
        function actionAdd() {

            // Get a reference to the tree
            $tree = & $this->tree;

            // Get the node ID
            $id = ( isset( $_GET['id'] ) && is_numeric( $_GET['id'] ) ) ? $_GET['id'] : 1;
        
            // Add an item
            $values = array( 'title' => 'New Node - ' . time(), 'parent_id' => $id );
            $id = $tree->addNode( $values );

            // Add some subnodes
            $values = array( 'title' => 'New Subnode - ', 'parent_id' => $id );
            foreach ( range( 1, 2 ) as $step ) {
                $values_tmp = $values;
                $values_tmp['title'] = $values_tmp['title'] . $step;
                $id2 = $tree->addNode( $values_tmp );
                foreach ( range( 1, 2 ) as $step2 ) {
                    $values_tmp2 = $values;
                    $values_tmp2['parent_id'] = $id2;
                    $values_tmp2['title'] = 'New Sub Subnode - ' . $step2;
                    $tree->addNode( $values_tmp2 );
                }
            }

            // Move the node
            //$tree->moveNode( $id, 4 );

            // Rebuild the tree
            $tree->rebuild();
            
            // Redirect to the sitemap
            $this->redirectToAction( 'sitemap' );

        }

        // Delete a node and it's subnodes
        function actionDelete() {

            // Get a reference to the tree
            $tree = & $this->tree;

            // Delete a node
            $tree->deleteNode( $_GET['id'] );
            
            // Redirect to the sitemap
            $this->redirectToAction( 'sitemap' );

        }

        // Real life example
        function actionExample() {

            // Get the node ID
            $id = ( isset( $_GET['id'] ) && is_numeric( $_GET['id'] ) ) ? $_GET['id'] : 1;

            // Get the node
            $node     = $this->tree->getNode( $id );
            $path     = $this->tree->getPath( $id );
            $children = $this->tree->getChildren( $id );

            // Show the path
            echo( '<p><b>Path:</b> ' );
            foreach ( $path as $path_item ) {
                echo( '<a href="' . YD_SELF_SCRIPT . '?do=example&id=' . $path_item['id'] . '">' . $path_item['title'] . '</a> &raquo; ' );
            }
            echo( '<a href="' . YD_SELF_SCRIPT . '?do=example&id=' . $node['id'] . '">' . $node['title'] . '</a>' );
            echo( '</p>' );

            // Show the item
            echo( '<p><b>' . $node['title'] . '</b></p> ' );

            // Show the children
            if ( $children ) {
                echo( '<p><b>Children:</b><br/> ' );
                foreach ( $children as $child ) {
                    echo( '<a href="' . YD_SELF_SCRIPT . '?do=example&id=' . $child['id'] . '">' . $child['title'] . '</a><br/>' );
                }
                echo( '</p>' );
            }

        }
        
        // Function to show a sitemap
        function actionSiteMap() {

            // Get a reference to the tree
            $tree = & $this->tree;

            // Get the maximum level
            $max_level = ( isset( $_GET['max_level'] ) && is_numeric( $_GET['max_level'] ) ) ? $_GET['max_level'] : null;

            // Get the node ID
            $id = ( isset( $_GET['id'] ) && is_numeric( $_GET['id'] ) ) ? $_GET['id'] : 1;

            // Get the node, path and children
            $node     = $this->tree->getNode( $id );
            $path     = $this->tree->getPath( $id );
            $path_str = $this->_convertToUrlString( $path );
            $children = $this->tree->getChildren( $id );
            
            // Show the map
            echo( '<h2>Sitemap Tree Example</h2>' );
            foreach ( $tree->getDescendants( 1, true, false, $max_level ) as $child ) {
                
                /*
                // Create the text path
                if ( sizeof( $path ) >= $child['nlevel']+1 ) {
                    //YDDebugUtil::dump( 'remove last'); 
                    array_pop( $path );
                }
                if ( sizeof( $path ) == $child['nlevel']-1 ) {
                    array_push( $path, $child );
                }
                if ( sizeof( $path ) == $child['nlevel'] ) {
                    array_pop( $path );   
                    array_push( $path, $child );
                }
                $tmp_path = $path;
                array_shift( $tmp_path );
                $child['path'] = $this->_convertToUrlString( $tmp_path );
                */
                
                // Show the current title in bold and italic
                //if ( $child['id'] == $id || in_array( $child['id'], array_keys( $path ) ) ) {
                //    $child['title'] = sprintf( '<b><i>%s</i></b>', $child['title'] );
                //}
                //if ( $child['id'] == $id ) {
                //    $child['title'] = sprintf( '<b><i>%s</i></b>', $child['title'] );
                //}
                
                // Show the links
                //YDDebugUtil::dump( $child['nlevel'], '$child[\'nlevel\']' );
                //YDDebugUtil::dump( $node['nlevel'],  '$node[\'nlevel\']' );

                // Get the initial difference
                @define( 'INIT_DIFF', $child['nlevel'] - $node['nlevel'] );

                echo( '[ <a href="' . YD_SELF_SCRIPT . '?do=delete&id=' . $child['id'] . '">delete</a> | ' );
                echo( '<a href="' . YD_SELF_SCRIPT . '?do=add&id=' . $child['id'] . '">add children</a> ] ' );
                echo( str_repeat( '&nbsp;', ( $child['nlevel'] - $node['nlevel'] - INIT_DIFF )*4 ) );
                //echo( '<a href="' . YD_SELF_SCRIPT . '?do=sitemap&id=' . $child['id'] . '&path=' . $child['path'] . '">' . $child['title'] . '</a> ' );
                echo( '<a href="' . YD_SELF_SCRIPT . '?do=sitemap&id=' . $child['id'] . '">' . $child['title'] . '</a> ' );
                echo( '(ID: ' . $this->_convertToUrlID( $child['title'] ) . ' or ' . $child['id'] . ')<br/>' );

            }

            // Show the path
            echo( '<p><b>Path:</b> ' );
            foreach ( $path as $path_item ) {
                echo( '<a href="' . YD_SELF_SCRIPT . '?do=sitemap&id=' . $path_item['id'] . '">' . $path_item['title'] . '</a> &raquo; ' );
            }
            echo( '<a href="' . YD_SELF_SCRIPT . '?do=sitemap&id=' . $node['id'] . '">' . $node['title'] . '</a>' );
            echo( '</p>' );

            // Show the item
            echo( '<p><b>' . $node['title'] . '</b></p> ' );

            // Show the children
            if ( $children ) {
                echo( '<p><b>Children:</b><br/> ' );
                foreach ( $children as $child ) {
                    echo( '<a href="' . YD_SELF_SCRIPT . '?do=sitemap&id=' . $child['id'] . '">' . $child['title'] . '</a><br/>' );
                }
                echo( '</p>' );
            }
            
            // Get the ID from the path info
            if ( isset( $_GET['path'] ) ) {
                $descendants = $tree->getDescendants();
                $path = explode( '/', trim( strtolower( $_GET['path'] ), ' /' ) );
                $item_id = null;
                foreach ( $path as $key=>$path_item ) {
                    foreach ( $descendants as $descendant ) {
                        if ( $this->_convertToUrlID( $descendant['title'] ) == $path_item && $descendant['nlevel'] == strval( $key + 2 ) ) {
                            if ( $key == sizeof( $path )-1 ) {
                                //YDDebugUtil::dump( 'last item' );
                                $item_id = $descendant['id'];
                                break;
                            }
                            continue;
                        }
                    }
                }
                if ( ! is_null( $item_id ) ) {
                    YDDebugUtil::dump( 'Found: ' . $_GET['path'] . ' = ID ' . $item_id );
                } else {
                    YDDebugUtil::dump( 'Not Found: ' . $_GET['path'] );
                }
            }
            
        }

        /*
        // Downloadable from: http://www.destroydrop.com/javascripts/tree/
        // Action to show a nice tree using JS
        function actionShowJSTree() {

            // Get a reference to the tree
            $tree = & $this->tree;

            // Get the complete tree
            $tree_data = $tree->getTreeWithChildren();
            array_shift( $tree_data );
            ksort( $tree_data );

            // Output link to css and script
            echo( '<link rel="StyleSheet" href="dtree.css" type="text/css" />' . YD_CRLF );
            echo( '<script type="text/javascript" src="dtree.js"></script>' . YD_CRLF );

            // Start the tree
            echo( '<script type="text/javascript">' . YD_CRLF );
            echo( 'a = new dTree(\'a\');' . YD_CRLF );
            echo( 'a.config.useCookies=true;' . YD_CRLF );

            // Add the nodes
            foreach ( $tree_data as $node ) {
                $node->parent_id = ( $node->parent_id != 0 ) ? $node->parent_id -1 : -1;
                printf(
                    'a.add( %d, %d, \'%s\', \'javascript: void(0);\' );' . YD_CRLF,
                     $node->id-1, $node->parent_id, $node->title
                );
            };

            // Show the tree
            echo( 'document.write(a);' . YD_CRLF );
            echo( '</script>' . YD_CRLF );

        }
        */
        
        // Convert a string to an URL ID
        function _convertToUrlID( $str ) {
            $str = strtoupper($str); 
            $str = strip_tags( strtolower( $str ) ); 
            $str = preg_replace( '/[àáâãäå]/i','a', $str); 
            $str = preg_replace( '/[ç]/i','c', $str); 
            $str = preg_replace( '/[òóôõöø]/i','o', $str); 
            $str = preg_replace( '/èéêë]/i','e', $str); 
            $str = preg_replace( '/[ìíîï]/i','i', $str); 
            $str = preg_replace( '/[ùúûüÿ]/i','u', $str);    
            $str = preg_replace( '/[ñ]/i','n', $str);    
            $str = preg_replace( '/[\s]+/i',' ', $str); 
            $str = preg_replace( '/[\' ]/' ,'-' , $str ); 
            $str = preg_replace( '/[^a-z0-9_\s-]/i' ,'' , $str );
            $str = preg_replace( '/[-]+/i','-', $str); 
            return $str; 
        }

        // Function to convert a path to an URL string
        function _convertToUrlString( $path ) {
            $url = array();
            foreach ( $path as $item ) {
                array_push( $url, $this->_convertToUrlID( $item['title'] ) );
            }
            return implode( '/', $url ) . '/';
        }

    }

    // Process the request
    YDInclude( 'YDF2_process.php' );

?>
