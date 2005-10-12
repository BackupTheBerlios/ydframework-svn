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
            PRIMARY KEY  (id),
            KEY nested_tree_parent_id (parent_id),
            KEY nested_tree_nleft (nleft),
            KEY nested_tree_nright (nright),
            KEY nested_tree_nlevel (nlevel)
        );
        // =============================================================================================================

        // ==== Default values for testing =============================================================================
        INSERT INTO nested_tree VALUES (1,0,'General Resources',1,22,1);
        INSERT INTO nested_tree VALUES (2,1,'Code Paste',2,3,2);
        INSERT INTO nested_tree VALUES (3,1,'Documentation',4,5,2);
        INSERT INTO nested_tree VALUES (4,1,'Books & Publications',6,13,2);
        INSERT INTO nested_tree VALUES (5,4,'Apache',7,8,3);
        INSERT INTO nested_tree VALUES (6,4,'PostgreSQL',9,10,3);
        INSERT INTO nested_tree VALUES (7,4,'MySQL',11,12,3);
        INSERT INTO nested_tree VALUES (8,1,'Links',14,21,2);
        INSERT INTO nested_tree VALUES (9,8,'Databases',15,16,3);
        INSERT INTO nested_tree VALUES (10,8,'Generators',17,18,3);
        INSERT INTO nested_tree VALUES (11,8,'Portals',19,20,3);
        // =============================================================================================================

        // ==== Sample data dump =======================================================================================
        +----+-----------+----------------------+-------+--------+--------+
        | id | parent_id | title                | nleft | nright | nlevel |
        +----+-----------+----------------------+-------+--------+--------+
        |  1 |         0 | General Resources    |     1 |     22 |      1 |
        |  2 |         1 | Code Paste           |     2 |      3 |      2 |
        |  3 |         1 | Documentation        |     4 |      5 |      2 |
        |  4 |         1 | Books & Publications |     6 |     13 |      2 |
        |  5 |         4 | Apache               |     7 |      8 |      3 |
        |  6 |         4 | PostgreSQL           |     9 |     10 |      3 |
        |  7 |         4 | MySQL                |    11 |     12 |      3 |
        |  8 |         1 | Links                |    14 |     21 |      2 |
        |  9 |         8 | Databases            |    15 |     16 |      3 |
        | 10 |         8 | Generators           |    17 |     18 |      3 |
        | 11 |         8 | Portals              |    19 |     20 |      3 |
        +----+-----------+----------------------+-------+--------+--------+
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
        
            // Add an item
            $values = array( 'title' => 'New Node - ' . time(), 'parent_id' => 8 );
            $id = $tree->addNode( $values );

            // Add some subnodes
            $values = array( 'title' => 'New Subnode - ', 'parent_id' => $id );
            foreach ( range( 1, 10 ) as $step ) {
                $values_tmp = $values;
                $values_tmp['title'] = $values_tmp['title'] . $step;
                $id2 = $tree->addNode( $values_tmp );
                foreach ( range( 1, 10 ) as $step2 ) {
                    $values_tmp2 = $values;
                    $values_tmp2['parent_id'] = $id2;
                    $values_tmp2['title'] = $values_tmp2['title'] . $step2;
                    $tree->addNode( $values_tmp2 );
                }
            }

            // Move the node
            $tree->moveNode( $id, 4 );

            // Rebuild the tree
            $tree->rebuild();

            // Get the path to the new element
            YDDebugUtil::dump( $tree->getPath( $id, true ), '$tree->getPath( $id )' );

        }

        // Delete a node and it's subnodes
        function actionDelete() {

            // Get a reference to the tree
            $tree = & $this->tree;

            // Delete a node
            $tree->deleteNode( $_GET['id'] );

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

    }

    // Process the request
    YDInclude( 'YDF2_process.php' );

?>
