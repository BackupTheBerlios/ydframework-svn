<?php

    // Standard include
    include_once( dirname( __FILE__ ) . '/../YDFramework2/YDF2_init.php' );

    // Includes
    YDInclude( 'YDError.php' );
    YDInclude( 'YDRequest.php' );

    // Test class
    class MyClass { 

        // Test function
        function test() { 
            return YDError::fatal( 'We couldn\'t do something' ); 
            // return YDError::warning( 'message' ); 
            // return YDError::notice( 'message' ); 
        }


    } 

    // Class definition
    class error extends YDRequest {

        // Class constructor
        function error() {
            $this->YDRequest();
        }

        // Default action
        function actionDefault() {

            // Create new instance of MyClass and execute test()
            $my = new MyClass(); 
            $res = $my->test(); 

            // Check for errors
            while ( $err = YDError::catch( $res ) ) {
                YDDebugUtil::dump( $err, '$err' );
                $err->level; // 3 
                $err->name; // fatal 
                $err->message; // We couldn't do something 
                $err->file; // ..../MyClass.php 
                $err->line; // x 
            } 

            // We can also set automatic reporting 
            YDError::reporting( YD_ERROR_FATAL );   // display fatals, warnings and notices 
            YDError::reporting( YD_ERROR_WARNING ); // display warnings and notices 
            YDError::reporting( YD_ERROR_NOTICE );  // display notices 
            YDError::reporting( YD_ERROR_NONE );    // don't display errors 

            // We can get the last errors array 
            $errors = YDError::getAll(); 
            YDDebugUtil::dump( $errors, '$errors' );

            // Or we could dump the error 
            if ( $err = YDError::catch( $res ) ) { 
                $err->dump();
            } 

        }

    }

    // Process the request
    YDInclude( 'YDF2_process.php' );

?>