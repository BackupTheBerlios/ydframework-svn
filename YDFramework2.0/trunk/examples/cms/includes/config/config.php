<?php

    // Set the database connection parameters
    YDConfig::set( 'DB_TYPE', 'mysql' );
    YDConfig::set( 'DB_NAME', 'ydcms' );
    YDConfig::set( 'DB_USER', 'root' );
    YDConfig::set( 'DB_PASS', '' );
    YDConfig::set( 'DB_HOST', 'localhost' );

    // Set the default debug level
    YDConfig::set( 'YD_DEBUG', 1 );

    // The supported languages
    YDConfig::set( 'supported_languages', array( 'nederlands' => 'nl', 'english' => 'en', 'fran&ccedil;ais' => 'fr' ) );

?>
