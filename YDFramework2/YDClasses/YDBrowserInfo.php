<?php

    /*

       Yellow Duck Framework version 2.0
       (c) copyright 2004 Pieter Claerhout, pieter@yellowduck.be

    */

    // Check if the YDFramework is loaded.
    if ( ! defined( 'YD_FW_NAME' ) ) {
        die( 'ERROR: Yellow Duck Framework is not loaded.' );
    }

    /**
     *  This class uses the HTTP_USER_AGENT varaible to get information about 
     *  the browser the visitor used to perform the request. We determine the 
     *  browser name, the version and the platform it's running on.
     *
     *  @todo
     *      Use the phpSniff class instead of our custom one.
     *      http://phpsniff.sourceforge.net/
     */
    class YDBrowserInfo {

        /** 
         *  The class constructor analyzes for the YDBrowserInfo class.
         */
        function YDBrowserInfo() {

            // Get the user agent
            $this->agent = $_SERVER['HTTP_USER_AGENT'];

            // Determine the browser name
            if ( ereg( 'MSIE ([0-9].[0-9]{1,2})', $this->agent, $ver ) ) {
                $this->version = $ver[1];
                $this->browser = 'ie';
            } elseif ( ereg( 'Safari\/([0-9]+)', $this->agent, $ver ) ) {
                $this->version = '1.0b' . $ver[1];
                $this->browser = 'safari';
            } elseif ( ereg( 'Opera ([0-9].[0-9]{1,2})', $this->agent, $ver ) ) {
                $this->version = $ver[1];
                $this->browser = 'opera';
            } elseif ( ereg( 'Mozilla/([0-9].[0-9]{1,2})', $this->agent, $ver ) ) {
                $this->version = $ver[1];
                $this->browser = 'mozilla';
            } else {
                $this->version = 0;
                $this->browser = 'other';
            }

            // Determine the platform
            if ( strstr( $this->agent,'Win' ) ) {
                $this->platform = 'win';
            } elseif ( strstr( $this->agent,'Mac' ) ) {
                $this->platform = 'mac';
            } elseif ( strstr( $this->agent,'Linux' ) ) {
                $this->platform = 'linux';
            } elseif ( strstr( $this->agent,'Unix' ) ) {
                $this->platform = 'unix';
            } else {
                $this->platform = 'other';
            }

        }

        /** Function to get the complete HTTP_USER_AGENT contents.
         *
         *  @returns String containing the HTTP_USER_AGENT contents.
         */
        function getAgent() {
            return $this->agent;
        }

        /** Function to get the name of the browser.
         *
         * @returns String containing the name of the browser.
         */
        function getBrowser() {
            return $this->browser;
        }

        /** Function to get the version of the browser.
         *
         * @returns String containing the version of the browser.
         */
        function getVersion() {
            return $this->version;
        }

        /** Function to get the platform of the browser.
         *
         * @returns String containing the platform of the browser.
         */
        function getPlatform() {
            return $this->platform;
        }

    }

?>