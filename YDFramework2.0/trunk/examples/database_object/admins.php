<?php

	// Initialize the Yellow Duck Framework
	require_once( dirname( __FILE__ ) . '/../../YDFramework2/YDF2_init.php' );

	YDConfig::set( 'YD_DATABASEOBJECT_PATH', YD_SELF_DIR . YD_DIRDELIM . 'includes' );

	YDInclude( 'Admin.php' );
	
	$adm = new Admin();
	
	// Let's begin
	echo "<h1>Let's see who are the admins</h1>";
	echo "<p>The Admins here are just an extension of the User object. Isn't another table, it's the same, <br>";
	echo "but with some protected fields values that cannot be changed. In this case the \"is_admin\" field.</p>";
	
	YDDebugUtil::dump( $adm->getValues() );	
	
	$total = $adm->find();
	
	echo "<p>We have " . $total . " admins. Let's see who they are.</p>";
	
	while( $adm->fetch() ) {
		YDDebugUtil::dump( $adm->getValues() );	
	}	
	
	echo "<p>We can't change the protected field. It's always going to return to it's protected value.<br>";
	echo "But let's try... changing to is_admin=3 with the set method.</p>";
	
	$adm->set( 'is_admin', 3 );

	YDDebugUtil::dump( $adm->getValues() );	
	
	echo "<p>Nothing... Let's try setting the value directly.</p>";
	
	$adm->is_admin = 3;
	
	YDDebugUtil::dump( $adm->getValues() );	
	
	echo "<p>Same thing. The idea is that the Admin Database Object will only work with the Users that are admins.<br>";	
	echo "If you want to unset a Admin you have to use the User object as it doesn't protect this field.</p>";
	
	echo "<p>&nbsp;</p>";
	echo "<p>Now we are going to define users addressses! <a href=\"users_addresses.php?YD_DEBUG=" . YD_DEBUG . "\">Click here</a>.</p>";
	echo "<p></p><p>&nbsp;</p>";
	
?>