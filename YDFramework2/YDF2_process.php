<?php

    /*
     *  Yellow Duck Framework version 2.0
     *  (c) copyright 2004 Pieter Claerhout, pieter@yellowduck.be
     */

    // Check if the YDFramework is loaded.
    if ( ! defined( 'YD_FW_NAME' ) ) {
        die(  'Yellow Duck Framework is not loaded.' );
    }

    // Includes
    require_once( 'YDExecutor.php' );

    // Get the name of the executor class
    $clsName = YD_EXECUTOR;

    // Instantiate the executor class
    $clsInst = new $clsName( YD_SELF_FILE );

    // Execute the execute function
    $clsInst->execute();

?>
