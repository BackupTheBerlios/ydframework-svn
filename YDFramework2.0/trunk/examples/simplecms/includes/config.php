<?php

    // The generic settings
    YDConfig::set( 'YD_SIMPLECMS_SITEID', 'SAMPLESITE' );
    YDConfig::set( 'YD_DEBUG', 1 );
    YDConfig::set( 'YD_SIMPLECMS_PUBLIC_SKIN', 'default' );

    // The database settings
    YDConfig::set( 'db_host',   'localhost' );
    YDConfig::set( 'db_name',   'ydsimplecms' );
    YDConfig::set( 'db_user',   'root' );
    YDConfig::set( 'db_pass',   '' );
    YDConfig::set( 'db_prefix', 'cms_' );

    // The defintion of the languages for the website
    YDConfig::set(
        'site_languages',
        array(
            1 => array( 'nl', 'Nederlands' ),
            2 => array( 'fr', 'Fran&ccedil;ais' ),
            3 => array( 'en', 'English' ),
            4 => array( '', '' ),
        )
    );
    YDConfig::set( 'site_default_lang', 'en' );

?>