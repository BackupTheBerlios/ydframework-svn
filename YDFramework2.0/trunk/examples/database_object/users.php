<?php

	// Initialize the Yellow Duck Framework
	require_once( dirname( __FILE__ ) . '/../../YDFramework2/YDF2_init.php' );

	YDConfig::set( 'YD_DATABASEOBJECT_PATH', YD_SELF_DIR . YD_DIRDELIM . 'includes' );

	YDInclude( 'User.php' );
	
	$user = new User();
	
	// Let's truncate the table first
	$user->executeSql( 'TRUNCATE ' . $user->getTable() );
	
	// Let's begin
	echo "<h1>Let's add some Users</h1>";
	
	$user->name = 'David Bittencourt';
	$user->email = 'david@host.com';
	$user->is_admin = 1; 
	$user->birthday = '19801120';
	
	$user->insert();	
	YDDebugUtil::dump( $user->getValues() );
	
	echo '<p>The user "' . $user->name . '" have ID = ' . $user->id . '.</p>';
	
	$user->reset(); 
	
	$user->name = 'Pieter Claerhout';
	$user->email = 'pieter@host.com';
	$user->is_admin = 1; 
	$user->birthday = null; // we can have null values if we set the field correctly
	
	$user->insert();
	YDDebugUtil::dump( $user->getValues() );
	
	echo '<p>The user "' . $user->name . '" have ID = ' . $user->id . '.</p>';

	$user->reset();
	$user->name = 'Francisco';
	$user->email = 'francisco@host.com';
	$user->birthday = null;

	$user->insert();
	YDDebugUtil::dump( $user->getValues() );

	echo '<p>The user "' . $user->name . '" have ID = ' . $user->id . '.</p>';

	echo '<p>But wait! There is no "is_admin" field in the table definition. That\'s because we can set<br>';
	echo 'YDDatabaseObject\'s fields as aliases of real column names. So "is_admin" is an alias for "admin".</p>';
		
	echo "<h1>The users are created but let's see how the callback feature works.</h1>";

	echo '<p>We can only have this feature working if we use the "set" method.</p>';
	
	echo "<p>Let's load my data to use this feature.</p>";
	
	$user->reset();
	$user->find(1);	
	
	YDDebugUtil::dump( $user->getValues() );
	
	echo "<p>birth_year? It's not a real column in the table, but is a registered SELECT field.<br>";
	echo "These fields are defined with the registerSelect method and are returned only in SELECT queries.</p>";

	echo "<p>Notice that we have another parameter called \"age\" that was set by the callback.<br>";
	echo "When? It happened when the find method found my record and added it's values to the object.<br>";
	echo "If you try to UPDATE this information, don't worry, because \"age\" will not be included in<br>";
	echo "UPDATE, INSERT or DELETE queries. Only registered fields are included.</p>";

	echo "<p>Let's set my birthday to a different year with the \"set\" method and see it trigger the callback.</p>";
	
	$user->set( 'birthday', '19811120' );
	
	YDDebugUtil::dump( $user->getValues() );
	
	echo "<p>If use use the \"set\" method, the callback it's executed immediately.</p>";
	echo "<p>Notice that the birth_year is not changed because it's a field set only after retrieving a SELECT query.</p>";
	
	$user->update();
	
	echo "<p>&nbsp;</p>";
	echo "<p>Let's control the Admins now! <a href=\"admins.php?YD_DEBUG=" . YDConfig::get( 'YD_DEBUG' ) . "\">Click here</a>.</p>";
	echo "<p></p><p>&nbsp;</p>";
	
?>