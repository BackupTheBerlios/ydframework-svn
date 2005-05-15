<?php

    // Initialize the Yellow Duck Framework
    include_once( dirname( __FILE__ ) . '/../../YDFramework2/YDF2_init.php' );

    YDConfig::set( 'YD_DBOBJECT_PATH', YD_SELF_DIR . YD_DIRDELIM . 'includes' );

    YDInclude( 'YDDatabaseObject.php' );
    YDInclude( 'YDTemplate.php' );
    
    echo "<h1>YDRecordSet pagination</h1>";
    echo "<p>Because YDRecordSet handles simple arrays, we just need to get an array of results.</p>";
    echo "<p>For that we have the getResults and getRelationResults methods.</p>";
    echo "<p>Let's load our groups and users relation with 3 records per page.</p>";

    $user = YDDatabaseObject::getInstance( 'User' );
    $user->loadAll();
    
    $user->find( 'group' );
    $results = $user->getResults();

    $page = @ $_GET['page'];
    $size = @ $_GET['size'] ? $_GET['size'] : 3;
                
    $recordset = new YDRecordSet( $results, $page, $size );

    // Setup the template
    $template = new YDTemplate();
    $template->assign( 'recordset', $recordset );
    $template->display( 'recordset' );
    

    echo "<p>&nbsp;</p>";
    echo "<p>That's it! Enjoy! =)</p>";
    echo "<p><a href=\"index.php?YD_DEBUG=" . YDConfig::get( 'YD_DEBUG' ) . "\">Click here to return</a></p>";
    echo "<p></p><p>&nbsp;</p>";

    
?>