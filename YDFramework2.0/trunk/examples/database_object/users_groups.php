<?php

	// Initialize the Yellow Duck Framework
	require_once( dirname( __FILE__ ) . '/../../YDFramework2/YDF2_init.php' );

	YDConfig::set( 'YD_DBOBJECT_PATH', YD_SELF_DIR . YD_DIRDELIM . 'includes' );

	YDInclude( 'User.php' );
	
	$user = new User();
	
	// Let's begin
	echo "<h1>We have a Many-to-Many relation</h1>";
	echo "<p>This type of relation needs an additional database object (table) to handle the relation.</p>";
	echo "<p>When we load this type of relation 2 objects are instantiated. The foreign object and the join object.</p>";
	
	$user->loadRelation( 'group' );
	
	// Let's truncate the join table
	$user->executeSql( 'TRUNCATE ' . $user->usergroup->getTable() );	
	
	echo "<p>Let's add me to the \"Yellow Duck Framework Group\".</p>";
	
	$user->usergroup->user_id = 1;
	$user->usergroup->group_id = 1;
	$user->usergroup->joined = '20040916';
	$user->usergroup->active =  1;
	
	$user->usergroup->insert();
	$user->resetRelation();

	echo "<p>Done. Now I can get the results of the relation if I want.</p>";
		
	$user->id = 1;
	$user->findRelation();

	YDDebugUtil::dump( $user->group->getValues() );

	echo "<p>And I have some info of the joining table.</p>";
	
	YDDebugUtil::dump( $user->usergroup->getValues() );
	
	echo "<p>And my own information, of course.</p>";
		
	YDDebugUtil::dump( $user->getValues() );
	
	echo "<p>Let's do some more adding.</p>";
		
	// Myself to the second group
	$user->resetRelation();
	$user->usergroup->user_id = 1;
	$user->usergroup->group_id = 2;
	$user->usergroup->active =  1;
	$user->usergroup->insert();

	// Pieter to the first group
	$user->resetRelation();
	$user->usergroup->user_id = 2;
	$user->usergroup->group_id = 1;
	$user->usergroup->active =  1;
	$user->usergroup->insert();
	
	// Francisco to the first group
	$user->resetRelation();
	$user->usergroup->user_id = 3;
	$user->usergroup->group_id = 1;
	$user->usergroup->active =  1;
	$user->usergroup->insert();
		
	// Pieter to the second group
	$user->resetRelation();
	$user->usergroup->user_id = 2;
	$user->usergroup->group_id = 2;
	$user->usergroup->insert();
	
	// Francisco to the second group
	$user->resetRelation();
	$user->usergroup->user_id = 3;
	$user->usergroup->group_id = 2;
	$user->usergroup->insert();
	
	echo "<h1>Now let's see who is in the first group.</h1>";
	
	$user->resetRelation();
	$user->group->id = 1;
	$user->findRelation();
	
	$user->group->fetch();
	YDDebugUtil::dump( $user->group->getValues() );
	while ( $user->fetchRelation() ) {			
		YDDebugUtil::dump( '-----------------------------------' );
		YDDebugUtil::dump( $user->getValues() );
		YDDebugUtil::dump( $user->usergroup->getValues() );
	}
	
	echo "<h1>And how about the second group?</h1>";
	echo "<p>Let's order by name and return only the group name, user name, is_admin and active.</p>";
	echo "<p>And return the values in a single array.</p>";
	
	$user->resetRelation();
	$user->group->id = 2;
	$user->addOrder( 'name' );
	$user->addSelect( 'name', 'is_admin' );
	
	$user->usergroup->addSelect( 'active' );
	$user->group->addSelect( 'name' );
	
	$user->findRelation();
	
	while ( $user->fetchRelation() ) {
		YDDebugUtil::dump( '-----------------------------------' );
		YDDebugUtil::dump( $user->getRelationValues() );
	}
	
	echo "<h1>But I only want active users.</h1>";
	
	$user->resetRelation();	
	$user->group->id = 2;
	$user->usergroup->active = 1;
	$user->findRelation();
	
	while ( $user->fetchRelation() ) {
		YDDebugUtil::dump( $user->getRelationValues() );
	}
	
	echo "<p>&nbsp;</p>";
	echo "<p>Let's find all relations in a single query! <a href=\"users_all.php?YD_DEBUG=" . YDConfig::get( 'YD_DEBUG' ) . "\">Click here</a>.</p>";
	echo "<p></p><p>&nbsp;</p>";
	
?>