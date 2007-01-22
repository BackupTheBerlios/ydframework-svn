<?php

    // The page module
    class module_page extends YDSimpleCMSModule {

        // Class variables
        var $name        = 'SimpleCMS Page Module';
        var $description = 'SimpleCMS module to manage pages.';
        var $version     = '1.0';
        var $authorName  = 'Pieter Claerhout';
        var $authorEmail = 'pieter@yellowduck.be';
        var $authorUrl   = 'http://www.yellowduck.be';

        // Public main function
        function action_public_show() {
            $this->display();
        }

        // Admin main function
        function action_admin_show() {
            $this->display();
        }

    }

    // The admin module
    class module_admin extends YDSimpleCMSModule {

        // Class variables
        var $name         = 'SimpleCMS Admin Module';
        var $description  = 'SimpleCMS module to manage everything ;-)';
        var $version      = '1.0';
        var $authorName   = 'Pieter Claerhout';
        var $authorEmail  = 'pieter@yellowduck.be';
        var $authorUrl    = 'http://www.yellowduck.be';

        // Admin main function
        function action_admin_show() {
            $this->display();
        }

        // Admin logout function
        function action_admin_logout() {
            YDRequest::redirect( YD_SELF_SCRIPT . '?do=logout' );
        }

        // List the plugins
        function action_admin_modules() {
            $this->tpl->assign( 'modules', $this->manager->getModuleList() );
            $this->display();
        }

        // The settings action
        function action_admin_settings() {
            $this->display();
        }

    }

?>