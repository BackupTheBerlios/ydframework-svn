<?php

    /*
     *  This examples demonstrates the language utilities.
     */

    // Standard include
    require_once( dirname( __FILE__ ) . '/../YDFramework2/YDF2_init.php' );

    // Includes
    require_once( 'YDRequest.php' );
    require_once( 'YDLanguage.php' );
    require_once( 'YDDebugUtil.php' );

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

        }

    }

    // Process the request
    require_once( dirname( __FILE__ ) . '/../YDFramework2/YDF2_process.php' );

?>