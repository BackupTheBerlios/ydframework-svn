<?php

    /*

        This examples demonstrates the array utilities.

    */

    // Standard include
    require_once( dirname( __FILE__ ) . '/../YDFramework2/YDF2_init.php' );

    // Class definition
    class language1Request extends YDRequest {

        // Class constructor
        function language1Request() {

            // Initialize the parent class
            $this->YDRequest();

        }

        // Default action
        function actionDefault() {

            // Get the language negotiator
            echo( 'Supported languages: en' );
            $langn = new YDLanguage();
            YDDebugUtil::dump( $langn->getLanguage() );

            // Get the language negotiator
            echo( 'Supported languages: nl, fr, en' );
            $langn = new YDLanguage(
                array( 'nl', 'fr', 'en' )    
            );
            YDDebugUtil::dump( $langn->getLanguage() );
        
            // Get the different other things
            echo( 'Browser languages:' );
            YDDebugUtil::dump( $langn->getBrowserLanguages() );
        
            // Get the different other things
            echo( 'Locale:' );
            YDDebugUtil::dump( $langn->getLocale() );

        }

    }

    // Process the request
    require_once( dirname( __FILE__ ) . '/../YDFramework2/YDF2_process.php' );

?>