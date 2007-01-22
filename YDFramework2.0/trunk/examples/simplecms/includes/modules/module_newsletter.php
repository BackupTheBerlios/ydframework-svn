<?php

    // Class definition
    class module_newsletter extends YDSimpleCMSModule {

        // Class variables
        var $name        = 'SimpleCMS Newsletter Module';
        var $description = 'SimpleCMS module to manage newsletters.';
        var $version     = '1.0';
        var $authorName  = 'Pieter Claerhout';
        var $authorEmail = 'pieter@yellowduck.be';
        var $authorUrl   = 'http://www.yellowduck.be';

        // Main function
        function action_public_show() {
        }

    }

    // Add menu items
    YDSimpleCMS::addAdminMenu( 'Newsletters', 'Create Newsletter',    'newsletter', 'create' );
    YDSimpleCMS::addAdminMenu( 'Newsletters', 'Archived Newsletters', 'newsletter', 'show' );

?>