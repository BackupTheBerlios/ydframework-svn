<?php

    // Initialize the Yellow Duck Framework
    require_once( dirname( __FILE__ ) . '/../../YDFramework2/YDF2_init.php' );

    YDConfig::set( 'YD_DBOBJECT_PATH', YD_SELF_DIR . YD_DIRDELIM . 'includes' );

    YDInclude( 'YDDatabaseObject.php' );
    
    $user = YDDatabaseObject::getInstance( 'User' );
    
    // Let's begin
    echo "<h1>Let's start working with relations</h1>";
    echo "<p>Let's load the \"address\" relation so we can start.</p>";
    
    $user->loadRelation( 'address' );
    
    echo "<p>The loadRelation method includes the necessary files in the relation and instantiates<br>";
    echo "the relation objects as parameters of the current object.</p>";

    // Let's truncate the table
    $user->executeSql( 'TRUNCATE ' . $user->address->getTable() );    
        
    echo "<p>Let's check if I have my address defined. This relation is a One-to-One relationship. My ID = 1.</p>";

    $user->id = 1;
    
    // you don't have to pass the relation id if you want to use the same as last time
    $total = $user->findRelation(); 
    
    echo "<p>I have " . $total . " addresses defined? That's not true. We have 1 row returned because<br>";
    echo "this is a LEFT join. Let's see the results in the address object.</p>";
    
    YDDebugUtil::dump( $user->address->getValues() );
    
    echo "<p>See? So let's add an address.</p>";
    
    $user->address->user_id = $user->id;
    $user->address->address = 'Street';
    $user->address->city    = 'City';
    $user->address->state   = 'State';
    $user->address->country = 'Country';
    $user->address->insert();
    
    YDDebugUtil::dump( $user->address->getValues() );
    
    echo "<p>Done. Now I'll check again if I have any addresses.</p>";
    
    // I reset all info so I'll just check with my ID
    // The resetRelation method executes a reset at each related object
    $user->resetRelation();
    
    $user->id = 1;
    $user->findRelation();
    
    echo "<p>I have " . $user->count() . " address defined. I don't have to fetch it because<br>";
    echo "single results are automatically fetched.</p>";

    YDDebugUtil::dump( $user->address->getValues() );
    
    echo "<p>And my info...</p>";

    YDDebugUtil::dump( $user->getValues() );

    echo "<p>Let's filter the results a little. We'll select only the id, name and the country.</p>";
    
    $user->resetRelation();
    $user->id = 1;

    $user->addSelect( 'id', 'name' );
    $user->address->addSelect( 'country' );
    
    $user->findRelation();
    
    echo "<p>Still have " . $user->count() . " address defined.</p>";

    YDDebugUtil::dump( $user->getValues() );
    YDDebugUtil::dump( $user->address->getValues() );

    echo "<p>But we can get a single array with getRelationValues. By default this method<br>";
    echo "returns the relation values with a prefix (the variable name).</p>";
    
    YDDebugUtil::dump( $user->getRelationValues() );

    echo "<p>You can take out this prefix setting false the prefix parameter, but is risky<br>";
    echo "if you have same field names in different tables.</p>";

    YDDebugUtil::dump( $user->getRelationValues( false, false, false ) );
        
    echo "<p>&nbsp;</p>";
    echo "<p>Let's do some more complex relations with Users Groups! <a href=\"users_groups.php?YD_DEBUG=" . YDConfig::get( 'YD_DEBUG' ) . "\">Click here</a>.</p>";
    echo "<p></p><p>&nbsp;</p>";
    
?>