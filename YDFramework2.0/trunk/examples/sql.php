<?php

	// Standard include
	require_once( dirname( __FILE__ ) . '/../YDFramework2/YDF2_init.php' );

	// Includes
	YDInclude( 'YDRequest.php' );
	YDInclude( 'YDSqlQuery.php' );

	// Class definition
	class sql extends YDRequest {

		// Class constructor
		function path() {
			$this->YDRequest();
		}

		// Default action
		function actionDefault() {
			
			// Select with one table			
			$q = $q = new YDSqlQuery( 'SELECT', array( 'DISTINCT' ) );
			
			$u = $q->addTable( 'user' );
			$q->addSelect( "$u.id" ); 
			$q->addSelect( "$u.name", 'user_name' );
			$q->addSelect( "SUM( $u.value )", 'total' );
			
			$q->openWhereGroup();
				$q->addWhere( "$u.id = 144" );
			
			$q->openWhereGroup( 'OR' );
				$q->addWhere( "$u.name LIKE 'David%'" );
				$q->addWhere( "$u.id > 13" );
			
			$q->closeWhereGroup(); // optional
			$q->closeWhereGroup(); // optional
			
			$q->addGroup( "$u.id", true ); // DESC
			
			$q->addOrder( "$u.name" );
			$q->addOrder( "total", true ); // DESC
			
			$q->addHaving( "total > 100" );
			
			$q->setLimit( 10, 50 );
			
			YDDebugUtil::dump( $q->getBrokenSql() );
			
			$q->reset();
			
			// Select - join with three tables
			$q->setAction( 'SELECT' );
			
			$u = $q->addTable( 'user', 'u' );
			$q->addSelect( 'id' ); 
			$q->addSelect( 'name' );
			
			$g = $q->addJoin( 'LEFT', 'group', "$u.group_id = g.id", "g" ); 
			$q->addSelect( "$g.id", 'gid' );
			$q->addSelect( "$g.name", 'group_name' );

			$a = $q->addJoin( 'RIGHT', 'attach', "$g.attach_id = a.id", "a" ); 
			$q->addSelect( "$a.id", 'aid' );
			$q->addSelect( "$a.name", 'attach_name' );

			$q->addWhere( "$g.id = 144" );
			$q->addOrder( "name" );
			
			YDDebugUtil::dump( $q->getBrokenSql() );
			
			$q->reset();
			
			// Delete
			$q->setAction( 'DELETE' );
			$q->addTable( 'user' );
			$q->addWhere( "id = 144" );
			$q->setLimit( 1 );

			YDDebugUtil::dump( $q->getBrokenSql() );
			
			$q->reset();
			
			// Update
			$q->setAction( 'UPDATE' );
			$q->addTable( 'user' );
			$q->setValues( array( 'name' => 'David', 'email' => 'email@host.com', 'admin' => 1, 'purple' => null ) );
			$q->addWhere( "id = 144" );
			$q->setLimit( 1 );			

			YDDebugUtil::dump( $q->getBrokenSql() );
			
			// Insert - using the same values
			$q->setAction( 'INSERT' );
			
			YDDebugUtil::dump( $q->getBrokenSql() );

		}

	}

	// Process the request
	YDInclude( 'YDF2_process.php' );

?>