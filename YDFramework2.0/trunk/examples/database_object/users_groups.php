<?php

	error_reporting( E_ALL );

	// Initialize the Yellow Duck Framework
	require_once( dirname( __FILE__ ) . '/../../YDFramework2/YDF2_init.php' );

	define( 'YD_DATABASEOBJECT_PATH', YD_SELF_DIR . YD_DIRDELIM . 'includes' );

	YDInclude( 'User.php' );
	
	$user = new User();
	$user->reset();
	
	// Let's begin
	echo "<h1>We have a Many-to-Many relation</h1>";
	echo "<p>This type of relation needs an additional database object (table) to handle the relation.</p>";
	echo "<p>When we load this type of relation 2 objects are instantiated. The foreign object and the join object.</p>";
	
	$user->loadRelation( 'groups' );
	
	// Let's truncate the join table
	$user->executeSql( 'TRUNCATE ' . $user->join_groups->getTable() );	
	
	$user->resetRelation();
	
	echo "<p>Let's add me (ID=1) to the \"Yellow Duck Framework Group\" (ID=1).</p>";
	
	$user->join_groups->user_id = 1;
	$user->join_groups->group_id = 1;
	$user->join_groups->joined = '20040916';
	$user->join_groups->active =  1;
	
	$user->join_groups->insert();
	$user->resetRelation();
	
	echo "<p>Done. Now I can get the results of the relation if I want.</p>";
		
	$user->user_id = 1;
	$user->getRelation();

	YDDebugUtil::dump( $user->groups->getValues() );

	echo "<p>And I have some info of the joining table.</p>";
	
	YDDebugUtil::dump( $user->join_groups->getValues() );
	
	echo "<p>And my own information, of course.</p>";
		
	YDDebugUtil::dump( $user->getValues() );
	
	echo "<p>Let's add me to the other group and add the other users to them too.</p>";
		
	// Myself to the second group
	$user->resetRelation();
	$user->join_groups->user_id = 1;
	$user->join_groups->group_id = 2;
	$user->join_groups->active =  1;
	$user->join_groups->insert();

	// Pieter to the first group
	$user->resetRelation();
	$user->join_groups->user_id = 2;
	$user->join_groups->group_id = 1;
	$user->join_groups->active =  1;
	$user->join_groups->insert();
	
	// Francisco to the first group
	$user->resetRelation();
	$user->join_groups->user_id = 3;
	$user->join_groups->group_id = 1;
	$user->join_groups->active =  1;
	$user->join_groups->insert();
		
	// Pieter to the second group
	$user->resetRelation();
	$user->join_groups->user_id = 2;
	$user->join_groups->group_id = 2;
	$user->join_groups->insert();
	
	// Francisco to the second group
	$user->resetRelation();
	$user->join_groups->user_id = 3;
	$user->join_groups->group_id = 2;
	$user->join_groups->insert();
	
	echo "<h1>Now let's see who is in the first group.</h1>";
	
	$user->resetRelation();
	$user->groups->id = 1;
	$total = $user->getRelation();
	
	if ( $total > 1 ) {
		$user->groups->fetch();
		YDDebugUtil::dump( $user->groups->getValues() );
		while ( $user->fetchRelation() ) {			
			YDDebugUtil::dump( '-----------------------------------' );
			YDDebugUtil::dump( $user->getValues() );
			YDDebugUtil::dump( $user->join_groups->getValues() );
		}
	}
	
	echo "<h1>And how about the second group?</h1>";
	echo "<p>Let's order by name and return only the group name, user name, is_admin and active.</p>";
	
	$user->resetRelation();
	$user->groups->id = 2;
	$user->resetSelect();
	$user->addSelect( 'users.name', 'name' );
	$user->addSelect( 'users.admin', 'is_admin' );
	$user->addOrder( 'users.name' );

	$user->join_groups->resetSelect();
	$user->join_groups->addSelect( 'users_groups.active', 'active' );
	
	$user->groups->resetSelect();
	$user->groups->addSelect( 'groups.name', 'name' );
	
	$total = $user->getRelation();
	
	if ( $total > 1 ) {
		$user->groups->fetch();
		YDDebugUtil::dump( $user->groups->getValues() );
		while ( $user->fetchRelation() ) {
			YDDebugUtil::dump( '-----------------------------------' );
			YDDebugUtil::dump( $user->getValues() );
			YDDebugUtil::dump( $user->join_groups->getValues() );
		}
	}
	
	echo "<h1>But I only want it's active users.</h1>";
	
	$user->resetRelation();	
	$user->groups->id = 2;
	$user->join_groups->active = 1;
	$total = $user->getRelation();
	
	YDDebugUtil::dump( $user->groups->getValues() );
	YDDebugUtil::dump( '-----------------------------------' );
	YDDebugUtil::dump( $user->getValues() );
	YDDebugUtil::dump( $user->join_groups->getValues() );
	
	echo "<p>&nbsp;</p>";
	echo "<p>That's it! Take a look at other methods available in the class source code.</p>";
	echo "<p><a href=\"index.php\">Click here to return</a></p>";
	echo "<p></p><p>&nbsp;</p>";
	
?>