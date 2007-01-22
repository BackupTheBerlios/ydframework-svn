<?php

    // Class definition
    class module_contactus extends YDSimpleCMSModule {

        // Class variables
        var $name        = 'SimpleCMS Contact Us Module';
        var $description = 'SimpleCMS module to manage a contact form.';
        var $version     = '1.0';
        var $authorName  = 'Pieter Claerhout';
        var $authorEmail = 'pieter@yellowduck.be';
        var $authorUrl   = 'http://www.yellowduck.be';

        // Main function
        function action_public_show() {
        }

    }

    // Add menu items
    YDSimpleCMS::addAdminMenu( 'Content',    'Contact Us', 'contactus', 'show' );

?>