<?php

	// Yellow Duck Framework version 2.0
	// (c) copyright 2004 Pieter Claerhout, pieter@yellowduck.be

	if ( ! defined( 'YD_FW_NAME' ) ) {
		die(  'Yellow Duck Framework is not loaded.' );
	}

	require_once( 'YDBase.php' );

	/**
	 *  This class defines a url.
	 */
	class YDUrl extends YDBase {

		/**
		 *	This is the class constructor for the YDUrl class. You need to specify the URL as it's only argument.
		 *
		 *	@param $url	The url you want to have as a YDUrl object.
		 */
		function YDUrl( $url ) {

			// Initialize YDBase
			$this->YDBase();

			// Save the path
			$this->_url = $url;

			// Set the defaults
			$defaults = array(
				'scheme'   => '', 'host'	 => '', 'port'	 => '', 'user'	 => '', 'pass'	 => '', 'path'	 => '',
				'query'	=> '', 'fragment' => ''
			);

			// Parse the URL
			$this->_url_parsed = array_merge( $defaults, parse_url( $url ) );

		}

		/**
		 *	This function will return the original URL.
		 *
		 *	@returns	The original URL.
		 */
		function getUrl() {
			return $this->_url;
		}

		/**
		 *	This function will return a named part of the URL. The following names are recognized: scheme, host, port,
		 *	user, pass, path, query and fragment. This function will return an empty string if the part doesn't exist
		 *	or if an invalid part has been specified.
		 *
		 *	@param $name	The name of the part.
		 *
		 *	@returns	The textual contents of the indicated part.
		 *
		 *	@internal
		 */
		function getNamedPart( $name ) {
			if ( isset( $this->_url_parsed[$name] ) ) {
				return $this->_url_parsed[$name];
			} else {
				return '';
			}
		}

		/**
		 *	This function will return the scheme of the URL.
		 *
		 *	@returns	The scheme of the URL (http, ftp, ...).
		 */
		function getScheme() {
			return strtolower( $this->getNamedPart( 'scheme' ) );
		}

		/**
		 *	This function will return the host part of the URL.
		 *
		 *	@returns	The host part of the URL.
		 */
		function getHost() {
			return strtolower( $this->getNamedPart( 'host' ) );
		}

		/**
		 *	This function will return the port part of the URL. It will return 80 by default.
		 *
		 *	@returns	The port part of the URL.
		 */
		function getPort() {
			$port = $this->getNamedPart( 'port' );
			if ( is_numeric( $port ) ) {
				return intval( $port );
			} else {
				return 80;
			}
		}

		/**
		 *	This function will return the user part of the URL.
		 *
		 *	@returns	The user part of the URL.
		 */
		function getUser() {
			return $this->getNamedPart( 'user' );
		}

		/**
		 *	This function will return the password part of the URL.
		 *
		 *	@returns	The password part of the URL.
		 */
		function getPassword() {
			return $this->getNamedPart( 'password' );
		}

		/**
		 *	This function will return the path part of the URL.
		 *
		 *	@returns	The path part of the URL.
		 */
		function getPath() {
			return $this->getNamedPart( 'path' );
		}

		/**
		 *	This function will return the query part of the URL. This is everything after the ? mark.
		 *
		 *	@returns	The query part of the URL.
		 */
		function getQuery() {
			return $this->getNamedPart( 'query' );
		}

		/**
		 *	This function will return the fragment part of the URL. This is everything after the # mark.
		 *
		 *	@returns	The fragment part of the URL.
		 */
		function getFragment() {
			return $this->getNamedPart( 'fragment' );
		}

		/**
		 *	This function will return the URI part of the URL.
		 *
		 *	@returns The URI parts of the URL.
		 */
		function getUri() {
			$uri = $this->_url_parsed['path'] ? $this->_url_parsed['path'] : '';
			$uri .= $this->_url_parsed['query'] ? '?'.$this->_url_parsed['query'] : '';
			$uri .= $this->_url_parsed['fragment'] ? '#'.$this->_url_parsed['fragment'] : '';
			return $uri; 
		}

		/**
		 *	Function to get the contents of the URL. It will get the contents using Gzip compression if possible in
		 *	order to save bandwidth. It uses the HTTP Client class from Simon Willison to do the dirty work.
		 *
		 *	More information about the HTTP client class can be found on: http://scripts.incutio.com/httpclient/
		 *
		 *	If it fails to retrieve the data, it will raise a fatal error.
		 *
		 *	By default, it will cache the downloaded data based on the etag and last-modified headers. The cache files 
		 *	are stored in the temp directory of the Yellow Duck framework and have the extension "wch". You can delete 
		 *	these automatically as they will be recreated on the fly if needed.
		 *
		 *	For configuring the cache, there are two constants you can redefine if needed:
		 *	YD_HTTP_CACHE_TIMEOUT: the lifetime of the cache in seconds (default: 3600).
		 *	YD_HTTP_CACHE_USEHEAD: if a HEAD HTTP request should be used to verify the cache validity (default: 1).
		 *
		 *	@param $cache	(optional) Indicate if the web content should be cached or not. By default, caching is 
		 *					turned on.
		 *
		 *	@returns	Returns the contents of the URL.
		 */
		function getContents( $cache=true ) {

			// Check if caching is enabled
			$cacheFName = null;

			// Check the cache
			if ( $cache == true ) {

				// Check if we need to use the HTTP HEAD function
				if ( YD_HTTP_CACHE_USEHEAD == 1 ) {

					// Get the headers
					$headers = $this->getHeaders();

					// Check if we have etag or last modified
					if ( isset( $headers['etag'] ) || isset( $headers['last-modified'] ) ) {
						$cacheFName = $this->getUrl();
						if ( isset( $headers['etag'] ) ) {
							$cacheFName .= $headers['etag'];
						}
						if ( isset( $headers['last-modified'] ) ) {
							$cacheFName .= $headers['last-modified'];
						}
						if ( isset( $headers['content-length'] ) ) {
							$cacheFName .= $headers['content-length'];
						}
						$cacheFName = YD_TMP_PRE . md5( $cacheFName ) . '.wch';
						$cacheFName = YD_DIR_TEMP . '/' . $cacheFName;
					}

				}

				// If the cache filename is null, use the default one
				if ( $cacheFName == null ) {
					$cacheFName = YD_DIR_TEMP . '/' . YD_TMP_PRE . md5( $this->getUrl() ) . '.wch';
				}

				// Use the cache file if any
				if ( is_file( $cacheFName ) ) {
					require_once( 'YDFSFile.php' );
					$file = new YDFSFile( $cacheFName );
					$cacheValidTime = $file->getLastModified() + YD_HTTP_CACHE_TIMEOUT;
					if ( time() < $cacheValidTime ) {
						return gzuncompress( $file->getContents() );
					}
				};

			}

			// Create a new HTTP client
			$client = $this->_getHttpClient();

			// Now send the request
			$result = $client->doRequest();

			// Check if there was a result
			if ( $result == false ) {
				YDFatalError(
					'Failed to retrieve the data from the url "' . $this->getUrl() . '". ' . $client->getError()
				);
			} else {
				$data = $client->getContent();
			}

			// Check if caching is enabled
			if ( $cache == true ) {

				// Save the cached data
				if ( $cacheFName != null ) {
					require_once( 'YDFSDirectory.php' );
					$dir = new YDFSDirectory( YD_DIR_TEMP );
					$dir->createFile( $cacheFName, gzcompress( $data ) );
				}

			}

			// Return the data
			return $data;

		}

		/**
		 *	Function to get the contents of the URL and automatically applies a regular expression to the result. This
		 *	function is based on the preg library and requires the preg syntax to function properly. If it fails to
		 *	retrieve the data, it will raise a fatal error. If no port was specified in the URL, it will default to 80.
		 *
		 *	@param $regex	The regular expression you want to apply to the the contents of the URL.
		 *	@param $cache	(optional) Indicate if the web content should be cached or not. By default, this is turned on.
		 *
		 *	@returns	Array with the regex matches from the URL contents.
		 */
		function getContentsWithRegex( $regex, $cache=true ) {
			$contents = $this->getContents( $cache );
			preg_match_all( $regex, $contents, $matches );
			return $matches;
		}

		/**
		 *	This function retrieves the header information for the specified URL.
		 *
		 *	@return	Array containing the headers for the URL.
		 */
		function getHeaders() {
			$client = $this->_getHttpClient();
			$client->method = 'HEAD';
			$client->headers_only = true;
			$result = $client->doRequest();
			if ( $result == false ) {
				YDFatalError(
					'Failed to retrieve the data from the url "' . $this->getUrl() . '". ' . $client->getError()
				);
			} else {
				return $client->headers;
			}
		}

		/**
		 *	This function retrieves the header information for the specified URL.
		 *
		 *	@return	Array containing the headers fors the URL.
		 */
		function getStatus() {
			$client = $this->_getHttpClient();
			$client->method = 'HEAD';
			$client->headers_only = true;
			$result = $client->doRequest();
			if ( $result == false ) {
				YDFatalError(
					'Failed to retrieve the data from the url "' . $this->getUrl() . '". ' . $client->getError()
				);
			} else {
				return intval( $client->status );
			}
		}

		/**
		 *	This function will return an already setup HTTP client object.
		 *
		 *	@internal
		 *
		 *	@returns	A new HttpClient class instance.
		 */
		function _getHttpClient() {
			require_once( 'YDHttpClient.php' );
			if ( $this->getScheme() != 'http' ) {
				YDFatalError( 'getContents: Only HTTP URLs are supported.' );
			}
			$client = new YDHttpClient( $this->getHost(), $this->getPort() );
			$client->path = $this->getUri();
			$client->referer = $this->getUrl();
			$client->method = 'GET';
			return $client;
		}

	}

?>
