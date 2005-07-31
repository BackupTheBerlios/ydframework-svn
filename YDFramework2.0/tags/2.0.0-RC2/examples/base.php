<?php

    // Standard include
    include_once( dirname( __FILE__ ) . '/../YDFramework2/YDF2_init.php' );

    // Includes
    YDInclude( 'YDRequest.php' );

    // Our first class
    class SubClassA extends YDBase {

        // Class constructor
        function SubClassA() {
            $this->YDBase();
        }
        
        // Add a method
        function MyMethod() {
        }

    }

    // Add another class
    class SubClassB extends SubClassA {

        // Class constructor
        function SubClassB() {
            $this->SubClassA();
        }

    }

    // Class definition
    class base extends YDRequest {

        // Class constructor
        function base() {
            $this->YDRequest();
        }

        // Default action
        function actionDefault() {

            // Instantiate our objects
            $objA = new SubClassA();
            $objB = new SubClassB();
            
            // Get the names of both classes
            YDDebugUtil::dump( $this->getClassName(), 'Class Name $this' );
            YDDebugUtil::dump( $objA->getClassName(), 'Class Name $objA' );
            YDDebugUtil::dump( $objB->getClassName(), 'Class Name $objB' );

            // Test the hasMethod function
            YDDebugUtil::dump( $objA->hasMethod( 'MyMethod' ), 'Check $objA->MyMethod' );
            YDDebugUtil::dump( $objA->hasMethod( 'xx' ), 'Check $objA->xx' );

            // Test the isSubclass function
            YDDebugUtil::dump( $objA->isSubclass( 'YDBase' ), 'Is $objA subclass of YDBase?' );
            YDDebugUtil::dump( $objB->isSubclass( 'YDBase' ), 'Is $objB subclass of YDBase?' );
            YDDebugUtil::dump( $objB->isSubclass( 'SubClassA' ), 'Is $objB subclass of SubClassA?' );
            YDDebugUtil::dump( $objB->isSubclass( 'YDRequest' ), 'Is $objB subclass of YDRequest?' );
            
            // Get the ancestors of both classes
            YDDebugUtil::dump( $this->getAncestors(), 'Ancestors of $this' );
            YDDebugUtil::dump( $objA->getAncestors(), 'Ancestors of $objA' );
            YDDebugUtil::dump( $objB->getAncestors(), 'Ancestors of $objB' );
            
        }

    }

    // Process the request
    YDInclude( 'YDF2_process.php' );

?>
