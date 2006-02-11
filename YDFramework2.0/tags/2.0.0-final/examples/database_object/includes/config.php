<?php

    // Init the framework
    include_once( dirname( __FILE__ ) . '/../../../YDFramework2/YDF2_init.php' );
    
    YDInclude( 'YDRequest.php' );
    YDInclude( 'YDTemplate.php' );
    YDInclude( 'YDDatabase.php' );
    YDInclude( 'YDDatabaseObject.php' );

    // Database connection
    $GLOBALS['YDDBOBJ_CONN'] = YDDatabase::getInstance( 'mysql', 'test', 'root', '', 'localhost' );
    
    // Data objects paths
    YDConfig::set( 'YD_DBOBJECT_PATH', dirname( __FILE__ ) );

    class YDDatabaseObjectRequest extends YDRequest {
        
        function YDDatabaseObjectRequest() {
            $this->YDRequest();
            $this->template = new YDTemplate();
            $this->template->assign( 'YD_DEBUG', YDConfig::get( 'YD_DEBUG' ) );
        }
        
        function actionDefault() {
            $this->template->display();
        }
        
    }


?>