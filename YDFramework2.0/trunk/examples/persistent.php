<?php

    // Standard include
    include_once( dirname( __FILE__ ) . '/../YDFramework2/YDF2_init.php' );

    // Includes
    YDInclude( 'YDRequest.php' );
    YDInclude( 'YDPersistent.php' );

    // A sample object
    class MySampleClass {

        // Class constructor
        function MySampleClass() {
            $this->array_1 = array( 1,2,3,4 );
            $this->array_2 = array( 'a'=> 'aaa', 'b' => 'bbb', 'c' => 'ccc' );
        }

    }

    // Class definition
    class persistent extends YDRequest {

        // Class constructor
        function persistent() {
            $this->YDRequest();
        }

        // Default action
        function actionDefault() {

            // Create the object
            $obj = new MySampleClass();

            // Set the persistent data
            YDPersistent::set( 'simple_string_encrypted', 'Pieter Claerhout', 'test' );
            YDPersistent::set( 'my_obj_encypted', $obj, 'test' );
            YDPersistent::set( 'simple_string', 'Pieter Claerhout' );
            YDPersistent::set( 'my_obj', $obj );

            YDPersistent::set( 'example', 'test' );
            YDPersistent::delete( 'example' );

            // Redirect to the check action
            $this->redirectToAction( 'check', array( 'simple_string' => 'overriden value' ) );

        }

        // Check the contents
        function actionCheck() {

            // Get the original objects
            YDDebugUtil::dump(
                YDPersistent::get( 'simple_string' ), 'simple_string'
            );
            YDDebugUtil::dump(
                YDPersistent::get( 'my_obj' ), 'my_obj'
            );
            YDDebugUtil::dump(
                YDPersistent::get( 'simple_string_encrypted' ), 'simple_string_encrypted without password'
            );
            YDDebugUtil::dump(
                YDPersistent::get( 'my_obj_encypted' ), 'my_obj_encypted without password'
            );
            YDDebugUtil::dump(
                YDPersistent::get( 'simple_string_encrypted', '', 'test' ), 'simple_string_encrypted with password'
            );
            YDDebugUtil::dump(
                YDPersistent::get( 'my_obj_encypted', '', 'test' ), 'my_obj_encypted with password'
            );

            // Dunp the complete contents
            YDPersistent::dump();


        }

    }

    // Process the request
    YDInclude( 'YDF2_process.php' );

?>
