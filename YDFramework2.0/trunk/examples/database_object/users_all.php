<?php

    // Initialize the Yellow Duck Framework
    require_once( dirname( __FILE__ ) . '/../../YDFramework2/YDF2_init.php' );

    YDConfig::set( 'YD_DBOBJECT_PATH', YD_SELF_DIR . YD_DIRDELIM . 'includes' );

    YDInclude( 'User.php' );
    
    echo "<h1>Loading and finding all relations</h1>";
    echo "<p>There is way of loading all relations and making a single query to all relations.</p>";
    echo "<p>You can use the findAllRelations method or findRelation( 'relation', 'relation', ... ) to<br>";
    echo "join only a few relations in a single query.</p>";
    
    echo "<p>Let's do that with my user.</p>";

    $user = new User();
    $user->loadAllRelations();
    $user->id = 1;
    
    $user->findAllRelations();
    
    while( $user->fetchRelation() ) {
        YDDebugUtil::dump( $user->getRelationValues() );
    }
    
    echo "<p>With getRelationValues I've returned all values in a single array, but we still are<br>";
    echo "able to access the individual relation objects.</p>";
    
    echo "<p>Let's do that with Pieter's user.</p>";
    
    $user->resetRelation();    
    $user->id = 2;
    
    $user->findAllRelations();
    
    while( $user->fetchRelation() ) {
        YDDebugUtil::dump( $user->getRelationValues() );
    }
    
    echo "<p>&nbsp;</p>";
    echo "<p>Now let's combine this with YDRecordSet so we can do some pagination. <a href=\"recordset.php?YD_DEBUG=" . YDConfig::get( 'YD_DEBUG' ) . "\">Click here</a></p>";
    echo "<p></p><p>&nbsp;</p>";

    
?>