<?php

    /*
     *  Yellow Duck Framework version 2.0
     *  (c) copyright 2004 Pieter Claerhout, pieter@yellowduck.be
     */

    // Check if the YDFramework is loaded.
    if ( ! defined( 'YD_FW_NAME' ) ) {
        die( 'ERROR: Yellow Duck Framework is not loaded.' );
    }

    // Includes
    require_once( 'YDExecutor.php' );
    require_once( 'YDObjectUtil.php' );

    // Get the name of the executor class
    $clsName = YD_EXECUTOR;

    // Check if the class exists
    if ( ! class_exists( $clsName ) ) {
        new YDFatalError(
            'The class definition for the executor class "' . $clsName . '"'
            . 'is not defined. Aborting processing.'
        );
    }

    // Instantiate the executor class
    $clsInst = new $clsName( YD_SELF_FILE );

    // Check for the execute function
    YDObjectUtil::failOnMissingMethod( $clsInst, 'execute' );

    // Execute the execute function
    $clsInst->execute();

?>
