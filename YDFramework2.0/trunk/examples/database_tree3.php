<?php

    // Standard include
    include_once( dirname( __FILE__ ) . '/../YDFramework2/YDF2_init.php' );

    /*

	    CREATE TABLE nested_tree (
            id int NOT NULL auto_increment,
            parent_id int NULL,
			lineage varchar(255) NOT NULL default '//',
            level int NOT NULL default '1',
            position int NOT NULL default '1',
            title varchar(255) NOT NULL default '',
            PRIMARY KEY (id),
			FOREIGN KEY (parent_id)
                REFERENCES nested_tree(id)
                     ON DELETE CASCADE
                     ON UPDATE CASCADE
        )TYPE=InnoDB;

		Note: root node must have ID 1, PARENT null and LINEAGE '' !

        INSERT INTO nested_tree VALUES ( 1, null, '',         0, 1, '');
        INSERT INTO nested_tree VALUES ( 2,    1, '//',       1, 1, 'General Resources');
        INSERT INTO nested_tree VALUES ( 3,    2, '//2/',     2, 1, 'Code Paste');
        INSERT INTO nested_tree VALUES ( 4,    2, '//2/',     2, 2, 'Documentation');
        INSERT INTO nested_tree VALUES ( 5,    2, '//2/',     2, 3, 'Books & Publications');
        INSERT INTO nested_tree VALUES ( 6,    5, '//2/5/',   3, 1, 'Apache');
        INSERT INTO nested_tree VALUES ( 7,    5, '//2/5/',   3, 2, 'PostgreSQL');
        INSERT INTO nested_tree VALUES ( 8,    5, '//2/5/',   3, 3, 'MySQL');
        INSERT INTO nested_tree VALUES ( 9,    2, '//2/',     2, 4, 'Links');
        INSERT INTO nested_tree VALUES (10,    9, '//2/9/',   3, 1, 'Databases');
        INSERT INTO nested_tree VALUES (11,    9, '//2/9/',   3, 2, 'Generators');
        INSERT INTO nested_tree VALUES (12,    9, '//2/9/',   3, 3, 'Portals');

        // ==== Sample data dump =======================================================================================
		+----+-----------+---------+-------+----------+----------------------+
		| id | parent_id | lineage | level | position | title                |
		+----+-----------+---------+-------+----------+----------------------+
		|  1 |      NULL |         |     0 |        1 |                      |
		|  2 |         1 | //      |     1 |        1 | General Resources    |
		|  3 |         2 | //2/    |     2 |        1 | Code Paste           |
		|  4 |         2 | //2/    |     2 |        2 | Documentation        |
		|  5 |         2 | //2/    |     2 |        3 | Books & Publications |
		|  6 |         5 | //2/5/  |     3 |        1 | Apache               |
		|  7 |         5 | //2/5/  |     3 |        2 | PostgreSQL           |
		|  8 |         5 | //2/5/  |     3 |        3 | MySQL                |
		|  9 |         2 | //2/    |     2 |        4 | Links                |
		| 10 |         9 | //2/9/  |     3 |        1 | Databases            |
		| 11 |         9 | //2/9/  |     3 |        2 | Generators           |
		| 12 |         9 | //2/9/  |     3 |        3 | Portals              |
		+----+-----------+---------+-------+----------+----------------------+

        // =============================================================================================================

    */

    // Includes
    YDInclude( 'YDRequest.php' );
    YDInclude( 'YDDatabase.php' );
    YDInclude( 'YDDatabaseTree3.php' );

	YDConfig::set( 'YD_DEBUG', 2 );

    // Class definition
    class database_tree3 extends YDRequest {

        // Class constructor
        function database_tree3() {

            // Call the parent
            $this->YDRequest();

            // Get the database connection
            $db = YDDatabase::getInstance( 'mysql', 'tree_test', 'root', '', 'localhost' );

            // Get the tree instance
            $this->tree = new YDDatabaseTree3( 'nested_tree', $db );
			
			// register extra column
			$this->tree->registerField( 'title', true );

        }

        // Default action
        function actionDefault() {

            // Show the link to the sitemap action
            echo( '<a href="' . YD_SELF_SCRIPT . '?do=example">Sitemap</a>' );

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
            YDDebugUtil::dump( $tree->isDescendantOf( 9999, 1 ), '$tree->isDescendantOf( 9999, 1 )' );
            YDDebugUtil::dump( $tree->isDescendantOf( 10, 2 ), '$tree->isDescendantOf( 10, 2 )' );

            // Check if child of
            YDDebugUtil::dump( $tree->isChildOf( 9, 8 ), '$tree->isChildOf( 9, 8 )' );
            YDDebugUtil::dump( $tree->isChildOf( 9, 2 ), '$tree->isChildOf( 9, 2 )' );

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

        }
		
        // Add a number of nodes
        function actionAdd() {

            // Get the node ID
            $id = ( isset( $_GET['id'] ) && is_numeric( $_GET['id'] ) ) ? $_GET['id'] : 1;
        
            // Add an item
            $values = array( 'title' => 'New Node - ' . time() );
            
			$this->tree->addNode( $values, $id );

            $this->actionExample( true );
        }


        // Delete a node and it's subnodes
        function actionDelete() {

            // Get a reference to the tree
            $tree = & $this->tree;

            // Delete a node
            $tree->deleteNode( $_GET['id'] );
            
            // Redirect to the sitemap
            $this->actionExample( true );

        }

        // Real life example
        function actionExample( $root = false) {

  			YDConfig::set( 'YD_DEBUG', 0 );
  
            // Get node ID
            $id = ( $root == false && isset( $_GET['id'] ) && is_numeric( $_GET['id'] ) ) ? $_GET['id'] : 1;

            // compute path
            echo( '<p><b>Path:</b> ROOT &raquo; ' );
            foreach ( $this->tree->getPath( $id ) as $path_item ) {
                echo( '<a href="' . YD_SELF_SCRIPT . '?do=example&id=' . $path_item['id'] . '">' . $path_item['title'] . '</a> &raquo; ' );
            }

			// compute node name
            $node = $this->tree->getNode( $id );
            echo( '<a href="' . YD_SELF_SCRIPT . '?do=example&id=' . $node['id'] . '">' . $node['title'] . '</a>' );
            echo( '</p>' );

            // Show the item
            echo( '<p><b>' . $node['title'] . '</b></p> ' );

            // Show the children in a table
			echo( '<table><tr><td><b>Children:</b></td><td>actions</td></tr>' );
            foreach ( $this->tree->getChildren( $id ) as $child ) {
				echo( '<tr><td>' );

					// show child action
                	echo( '<a href="' . YD_SELF_SCRIPT . '?do=example&id=' . $child['id'] . '">' . $child['title'] . '</a>&nbsp;&nbsp;&nbsp;' );
				echo( '</td><td>' );

					// delete action
	                echo( '<a href="' . YD_SELF_SCRIPT . '?do=delete&id='  . $child['id'] . '">Delete (' . $child['title'] . ')</a><br/>' );
				echo( '</td></tr>' );
            }
  
  			// add node action
			echo( '<tr><td></td><td>' );
	                echo( '<a href="' . YD_SELF_SCRIPT . '?do=add&id='  . $node['id'] . '">Add new node to (' . $node['title'] . ')</a><br/>' );
			echo( '</td></tr>' );

            echo( '</table>' );
        }


    }

    // Process the request
    YDInclude( 'YDF2_process.php' );

?>
