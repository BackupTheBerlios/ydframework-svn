<?php

    // Standard include
    include_once( dirname( __FILE__ ) . '/../YDFramework2/YDF2_init.php' );

    // Includes
    YDInclude( 'YDRequest.php' );
    YDInclude( 'YDSqlQuery.php' );

    // Class definition
    class sqlquery extends YDRequest {

        // Class constructor
        function sqlquery() {
            $this->YDRequest();
        }

        // Default action
        function actionDefault() {
            
            // Instantiate the class
            $q = YDSqlQuery::getInstance( 'mysql' );
            $r = $q->reserved;
            
            // Select action
            $q->select();
            
            // Additional options
            $q->options( array( 'DISTINCT' ) );
            
            // Add a table to the query
            // It returns the table alias if defined
            $u = $q->from( 'user' );
            
            // Select expressions
            $q->expression( "id", '', true ); // quote reserved
            $q->expression( "name", 'user_name', true ); // quote reserved
            $q->expression( "SUM( " . $r . "value" . $r . " )", 'total' );
            
            // Add a where statement
            $q->openWhere();
                $q->where( $r . "id" . $r . "= 144" );
            
            // Add another where statement inside the above statement
            $q->openWhere( 'OR' );
                $q->where( $r . "name" . $r . " LIKE 'David%'" );
                $q->where( $r . "id" . $r . " > 13" );
            
            $q->closeWhere(); // optional
            $q->closeWhere(); // optional
            
            // Add a group by clause
            $q->group( "id", true, true ); // DESC and quote reserved
            
            // Add ordering
            $q->order( "name", false, true ); // ASC and quote reserved
            $q->order( "total", true, true ); // DESC and quote reserved
            
            // Add a having clause
            $q->having( $r . "total" . $r . " > 100" );
            
            // Add a limit
            $q->limit( 100 );
            
            // Add an offset
            $q->offset( 50 );
            
            // Show the SQL statement
            YDDebugUtil::dump( $q->getSql() );
            
            // Reset the contents of the query 
            $q->reset();
            
            // ------------------------------------------------------------
            
            // Select query
            $q->select();
            
            // Add table with alias 'u'
            $u = $q->from( 'user', 'u' );
            
            // Add select expressions for this table
            // The expr method is an alias of the expression method
            $q->expr( 'id', '', true ); 
            $q->expr( 'name', '', true );
            
            // Add a left join
            $g = $q->join( 'LEFT', 'group', "g" );
            
            // Specify the fields to use for the join
            $q->on( "$u.group_id = $g.id" );
            
            // Add the joined fields
            $q->expr( "$g.id", 'gid', true );
            $q->expr( "$g.name", 'group_name', true );
    
            // Add an inner join
            $a = $q->join( 'INNER', 'attach', "a" );
            
            // Specify the fields to use for the join
            $q->on( "$g.attach_id = $a.id" );
            
                // Open a group
                $q->openOn( 'OR' );
                
                // Specify more fields to use for the join group
                $q->on( "$a.size > 150" );
                $q->on( "$a.status = 3" );
            
            // Add some more fields
            $q->expr( "$a.id", 'aid', true );
            $q->expr( "$a.name", 'attach_name', true );
    
            // Add a where clause
            $q->where( "$g.id = 144" );
            
            // Add ordering
            $q->order( "$a.name", false, true );
            
            // Add a limit
            $q->limit( 50 );
    
            // Show the SQL statement
            YDDebugUtil::dump( $q->getSql() );
            
            // Reset the query
            $q->reset();
            
            // ------------------------------------------------------------
            
            // Delete query
            $q->delete();
    
            // Add a table
            $q->from( 'user' );
    
            // Specify the where clause
            $q->where( "id = 144" );
    
            // Show the SQL statement
            YDDebugUtil::dump( $q->getSql() );
            
            // Reset the query
            $q->reset();
            
            // ------------------------------------------------------------
            
            $values = array(
                        'name' => 'David',
                        'email' => 'email@host.com',
                        'admin' => 1,
                        'purple' => null
             );
              
            // Insert query
            $q->insert();
        
            // Add a table
            // The into method is an alias of the from method
            $q->into( 'user' );
    
            // Set the values
            $q->values( $values );
    
            // Add the where clause
            $q->where( "id = 144" );
    
            // Show the SQL statement
            YDDebugUtil::dump( $q->getSql() );
            
            // Reset the query
            $q->reset();
            
            // ------------------------------------------------------------
            
            // Update query
            $q->update();
    
            // Add a table
            // The table method is an alias of the from method
            $q->table( 'user' );
    
            // Set the values
            // The set method is an alias of the values method
            $q->set( $values );
    
            // Add the where clause
            $q->where( "id = 144" );
    
            // Show the SQL statement
            YDDebugUtil::dump( $q->getSql() );

        }        

    }

    // Process the request
    YDInclude( 'YDF2_process.php' );

?>