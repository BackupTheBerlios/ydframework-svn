<?php

	// Yellow Duck Framework version 2.0
	// (c) copyright 2004 Pieter Claerhout, pieter@yellowduck.be

	if ( ! defined( 'YD_FW_NAME' ) ) {
		die( 'Yellow Duck Framework is not loaded.' );
	}

	/**
	 *	This class uses the HTTP_USER_AGENT varaible to get information about the browser the visitor used to perform
	 *	the request. We determine the browser name, the version and the platform it's running on.
	 */
	class YDBrowserInfo extends YDBase {

		/**
		 *	The class constructor analyzes for the YDBrowserInfo class. The constructor takes no arguments and uses the
		 *	$_SERVER['HTTP_USER_AGENT'] variable to parse the browser info.
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
				$this->_dotnet = 'unknown';

			} else {

				// Get the user agent
				$this->_agent = $_SERVER['HTTP_USER_AGENT'];

				// Determine the browser name
				if ( preg_match( '/MSIE ([0-9].[0-9]{1,2})/', $this->_agent, $ver ) ) {
					$this->_version = $ver[1];
					$this->_browser = 'ie';
				} elseif ( preg_match( '/Safari\/([0-9]+)/', $this->_agent, $ver ) ) {
					$this->_version = '1.0b' . $ver[1];
					$this->_browser = 'safari';
				} elseif ( preg_match( '/Opera ([0-9].[0-9]{1,2})/', $this->_agent, $ver ) ) {
					$this->_version = $ver[1];
					$this->_browser = 'opera';
				} elseif ( preg_match( '/Mozilla\/([0-9].[0-9]{1,2})/', $this->_agent, $ver ) ) {
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

				// Get the .NET runtime version
				preg_match_all( '/.NET CLR ([0-9][.][0-9])/i', $this->_agent, $ver );
				$this->_dotnet = $ver[1];

			}

		}

		/**
		 *	Function to get the complete HTTP_USER_AGENT contents.
		 *
		 *	@returns String containing the HTTP_USER_AGENT contents.
		 */
		function getAgent() {
			return $this->_agent;
		}

		/**
		 *	Function to get the name of the browser.
		 *
		 *	@returns String containing the name of the browser.
		 */
		function getBrowser() {
			return $this->_browser;
		}

		/** 
		 *	Function to get the version of the browser.
		 *
		 *	@returns String containing the version of the browser.
		 */
		function getVersion() {
			return $this->_version;
		}

		/**
		 *	Function to get the platform of the browser.
		 *
		 *	@returns String containing the platform of the browser.
		 */
		function getPlatform() {
			return $this->_platform;
		}

		/**
		 *	Function to get the list of supported Microsoft.NET runtimes.
		 *
		 *	@returns	Array containing the list of supported Microsoft.NET runtimes.
		 */
		function getDotNetRuntimes() {
			return $this->_dotnet;
		}

		/**
		 *	This function returns an array with the languages that are supported by the browser. This is done by using
		 *	the HTTP_ACCEPT_LANGUAGE server variable that gets send with the HTTP headers.
		 *
		 *	@return Array containing the list of supported languages
		 */
		function getBrowserLanguages() {

			// We parse the language headers sent by the browser
			if ( ! isset( $_SERVER['HTTP_ACCEPT_LANGUAGE'] ) ) {
				return array();
			}
			$browserLanguages = explode( ',', $_SERVER['HTTP_ACCEPT_LANGUAGE'] );

			// Normalize the browser language headers
			for ( $i = 0; $i < sizeof( $browserLanguages ); $i++ ) {
				$browserLanguage = explode( ';', $browserLanguages[$i] );
				$browserLanguages[$i] = substr( $browserLanguage[0], 0, 2 );
			}

			// Remove the duplicates
			$browserLanguages = array_unique( $browserLanguages );

			// Return the browser languages
			return array_values( $browserLanguages );

		}

		/**
		 *	This function will get the most appropriate language for the browser, considering the list of supported
		 *	languages by both the browser and the web application.
		 *
		 *	@param $supported	(optional) An array with the list of supported languages. By default, only english is
		 *						supported.
		 */
		function getLanguage( $supported=array( 'en' ) ) {

			// Start with the default language
			$language = $supported[0];

			// Get the list of languages supported by the browser
			$browserLanguages = $this->getBrowserLanguages();

			// Now, we look if the browser specified one
			if ( isset( $_SERVER['HTTP_ACCEPT_LANGUAGE'] ) ) {
				foreach ( $browserLanguages as $browserLanguage ) {
					if ( in_array( $browserLanguage, $supported ) ) {
						$language = $browserLanguage;
						break;
					}
				}
			}

			// Return the language
			return $language;

		}

	}

?>