<?php

    /*

        Yellow Duck Framework version 2.1
        (c) Copyright 2002-2007 Pieter Claerhout

        This library is free software; you can redistribute it and/or
        modify it under the terms of the GNU Lesser General Public
        License as published by the Free Software Foundation; either
        version 2.1 of the License, or (at your option) any later version.

        This library is distributed in the hope that it will be useful,
        but WITHOUT ANY WARRANTY; without even the implied warranty of
        MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU
        Lesser General Public License for more details.

        You should have received a copy of the GNU Lesser General Public
        License along with this library; if not, write to the Free Software
        Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA 02111-1307 USA

    */

    /**
     * @addtogroup YDSimpleCMS Addons - Simple CMS
     */

    /**
     *  The page module which handles a basic page object.
     *
     *  @ingroup YDSimpleCMS
     */
    class module_page extends YDSimpleCMSModule {

        /**
         *  The nice name of the CMS module.
         */
        var $name        = 'SimpleCMS Page Module';

        /**
         *  The full description of the CMS module.
         */
        var $description = 'SimpleCMS module to manage pages.';

        /**
         *  The version number of the CMS module.
         */
        var $version       = '1.0';

        /**
         *  The name of the author of the CMS module.
         */
        var $authorName    = 'Pieter Claerhout';

        /**
         *  The email address of the author of the CMS module.
         */
        var $authorEmail   = 'pieter@yellowduck.be';

        /**
         *  The website address of the author of the CMS module.
         */
        var $authorUrl     = 'http://www.yellowduck.be';

        /**
         *  This is the default public action which currently just displays the template.
         */
        function action_public_show() {
            $this->display();
        }

        /**
         *  This is the default admin action for the page module.
         */
        function action_admin_show() {
            $this->display();
        }

    }

    /**
     *  The admin module which implements most of the standard admin functions.
     *
     *  @ingroup YDSimpleCMS
     */
    class module_admin extends YDSimpleCMSModule {

        /**
         *  The nice name of the CMS module.
         */
        var $name         = 'SimpleCMS Admin Module';

        /**
         *  The full description of the CMS module.
         */
        var $description  = 'SimpleCMS module to manage everything ;-)';

        /**
         *  The version number of the CMS module.
         */
        var $version       = '1.0';

        /**
         *  The name of the author of the CMS module.
         */
        var $authorName    = 'Pieter Claerhout';

        /**
         *  The email address of the author of the CMS module.
         */
        var $authorEmail   = 'pieter@yellowduck.be';

        /**
         *  The website address of the author of the CMS module.
         */
        var $authorUrl     = 'http://www.yellowduck.be';

        /**
         *  This is the default admin action for the admin module.
         */
        function action_admin_show() {
            $this->display();
        }

        /**
         *  This action will list all the plugins which are available on the system.
         */
        function action_admin_modules() {
            $moduleManager = & YDSimpleCMS::getModuleManager();
            $this->tpl->assign( 'modules', $moduleManager->getModuleList() );
            $this->display();
        }

        /**
         *  This action will allow you to change the settings.
         */
        function action_admin_settings() {
            $this->display();
        }

    }

?>