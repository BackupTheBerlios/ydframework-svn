<?php

    // Standard include
    include_once( dirname( __FILE__ ) . '/../YDFramework2/YDF2_init.php' );

    // Includes
    YDInclude( 'YDRequest.php' );
    YDInclude( 'YDUtil.php' );

    // Class definition
    class browserinfo extends YDRequest {

        // Class constructor
        function browserinfo() {
            $this->YDRequest();
        }


        // Default action
        function actionDefault() {
            $browser = new YDBrowserInfo();
            YDDebugUtil::dump( $browser->agent, 'Agent' );
            YDDebugUtil::dump( $browser->browser, 'Browser name' );
            YDDebugUtil::dump( $browser->version, 'Version' );
            YDDebugUtil::dump( $browser->platform, 'Platform' );
            YDDebugUtil::dump( $browser->dotnet, 'Installed .NET runtimes' );
            YDDebugUtil::dump( $browser->getBrowserLanguages(), 'Languages supported by the browser' );
            YDDebugUtil::dump( $browser->getBrowserLanguagesAndCountries(), 'Languages and countries supported by the browser' );
            YDDebugUtil::dump( $browser->getLanguage(), 'Negotiated language' );
        }

    }

    // Process the request
    YDInclude( 'YDF2_process.php' );

?>
