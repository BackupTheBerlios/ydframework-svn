<?php

    // Initialize the Yellow Duck Framework
    require_once( dirname( __FILE__ ) . '/../../YDFramework2/YDF2_init.php' );

    YDConfig::set( 'YD_DBOBJECT_PATH', YD_SELF_DIR . YD_DIRDELIM . 'includes' );
    
    YDInclude( 'YDDatabaseObject.php' );
    
    $group = YDDatabaseObject::getInstance( 'Group' );
    
    // Let's truncate the table before
    $group->executeSql( 'TRUNCATE ' . $group->getTable() );
    
    // Let's begin

    echo "<h1>Let's add some Groups</h1>";
    
    $group->name = 'Yellow Duck Framework Group';
    $group->date = date('Ymd'); // today

    $id = $group->insert();
    
    echo '<p>The "' . $group->name . '" have ID = ' . $id . '.</p>';
    
    $group->reset(); // deletes all information in the object
    $group->name = 'Beautiful Group';
    $group->date = '20050412'; 

    $group->insert(); // let's not get the ID here, it's already set

    echo '<p>The "' . $group->name . '" have ID = ' . $group->id . '.</p>';

    $group->reset();
    $group->name = 'Yo! Ugly Group';
    $group->date = '19001231'; 

    $group->insert();

    echo '<p>The "' . $group->name . '" have ID = ' . $group->id . '.</p>';
    echo "<p>Oops... it's not " . substr( $group->date, 0, 4 ) . " but 1999. Let's <b>update</b> it.</p>";
    
    $group->date = '19991231';
    $group->update();
    
    echo "<p>Now we have the correct year: " . substr( $group->date, 0, 4 ) . ".</p>";
    
    $group->reset();
    
    echo "<h1>Let's find the groups starting with Y</h1>";
    
    $group->addWhere( "name LIKE 'Y%'" );
    $group->addOrder( "id" );
    $total = $group->find();
    
    echo "<p>We've found " . $total . " groups starting with Y.</p>";
    
    // we can use the count method to get the same result
    if ( $group->count() > 1 ) {
        while( $group->fetch() ) {
            YDDebugUtil::dump( $group->getValues() );
        }
    }
    
    echo "<h1>But I really want is the first group.</h1>";
    
    $group->reset();
    $group->find( 1 );
    YDDebugUtil::dump( $group->getValues() );
    
    echo "<h1>And I don't like the 'Yo! Ugly Group, so I'll delete it. I will reset the object and execute a delete.</h1>";
    
    $group->reset();
    $group->delete();

    echo "<p>A PHP notice saying that I have no conditions... This is a protection defined by the YDConfig<br>";
    echo "YD_DBOBJECT_DELETE. A similar config is available for UPDATEs that don't have conditions<br>";
    echo "YD_DBOBJECT_UPDATE. The default is not letting the query to be executed for both.</p>";
    
    echo "<p>Let's set the ID.</p>";
    
    $group->set( 'id', 3 ); // using set method it's an alternative
    $group->delete();
    
    echo "<h1>Great! Now we have the following groups.</h1>";
    
    $group->reset();
    $group->find();
    
    if ( $group->count() > 1 ) {
        while( $group->fetch() ) {
            YDDebugUtil::dump( $group->getValues() );
        }
    }
    
    echo "<p>&nbsp;</p>";
    echo "<p>Let's add some Users! <a href=\"users.php?YD_DEBUG=" . YDConfig::get( 'YD_DEBUG' ) . "\">Click here</a>.</p>";
    echo "<p></p><p>&nbsp;</p>";
    
?>