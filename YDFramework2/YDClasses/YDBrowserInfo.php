<?php

    /*
     *  Yellow Duck Framework version 2.0
     *  (c) copyright 2004 Pieter Claerhout, pieter@yellowduck.be
     */

    // Check if the YDFramework is loaded.
    if ( ! defined( 'YD_FW_NAME' ) ) {
        die( 'ERROR: Yellow Duck Framework is not loaded.' );
    }

    // Includes
    require_once( 'YDBase.php' );

    /**
     *  This class uses the HTTP_USER_AGENT varaible to get information about
     *  the browser the visitor used to perform the request. We determine the
     *  browser name, the version and the platform it's running on.
     */
    class YDBrowserInfo extends YDBase {

        /**
         *  The class constructor analyzes for the YDBrowserInfo class. The
         *  constructor takes no arguments and uses the
         *  $_SERVER['HTTP_USER_AGENT'] variable to parse the browser info.
         */
        function YDBrowserInfo() {

            // Initialize YDBase
            $this->YDBase();

            // Check if the user agent was specified
            if ( ! isset( $_SERVER['HTTP_USER_AGENT'] ) ) {

                // Mark everything as unknown
                $this->_agent = 'unknown';
                $this->_browser = 'unknown';
                $this->_version = 'unknown';
                $this->_platform = 'unknown';

            } else {

                // Get the user agent
                $this->_agent = $_SERVER['HTTP_USER_AGENT'];

                // Determine the browser name
                if (
                    ereg( 'MSIE ([0-9].[0-9]{1,2})', $this->_agent, $ver )
                ) {
                    $this->_version = $ver[1];
                    $this->_browser = 'ie';
                } elseif (
                    ereg( 'Safari\/([0-9]+)', $this->_agent, $ver
                ) ) {
                    $this->_version = '1.0b' . $ver[1];
                    $this->_browser = 'safari';
                } elseif (
                    ereg( 'Opera ([0-9].[0-9]{1,2})', $this->_agent, $ver )
                ) {
                    $this->_version = $ver[1];
                    $this->_browser = 'opera';
                } elseif (
                    ereg( 'Mozilla/([0-9].[0-9]{1,2})', $this->_agent, $ver )
                ) {
                    $this->_version = $ver[1];
                    $this->_browser = 'mozilla';
                } else {
                    $this->_version = 0;
                    $this->_browser = 'other';
                }

                // Determine the platform
                if ( stristr( $this->_agent,'Win' ) ) {
                    $this->_platform = 'win';
                } elseif ( stristr( $this->_agent,'Mac' ) ) {
                    $this->_platform = 'mac';
                } elseif ( stristr( $this->_agent,'Linux' ) ) {
                    $this->_platform = 'linux';
                } elseif ( stristr( $this->_agent,'Unix' ) ) {
                    $this->_platform = 'unix';
                } else {
                    $this->_platform = 'other';
                }

            }

        }

        /** Function to get the complete HTTP_USER_AGENT contents.
         *
         *  @returns String containing the HTTP_USER_AGENT contents.
         */
        function getAgent() {
            return $this->_agent;
        }

        /** Function to get the name of the browser.
         *
         * @returns String containing the name of the browser.
         */
        function getBrowser() {
            return $this->_browser;
        }

        /** Function to get the version of the browser.
         *
         * @returns String containing the version of the browser.
         */
        function getVersion() {
            return $this->_version;
        }

        /** Function to get the platform of the browser.
         *
         * @returns String containing the platform of the browser.
         */
        function getPlatform() {
            return $this->_platform;
        }

    }

?>