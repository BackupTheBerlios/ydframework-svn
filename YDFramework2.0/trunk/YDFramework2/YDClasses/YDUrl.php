<?php

	/*
	
		Yellow Duck Framework version 2.0
		Copyright (C) (c) copyright 2004 Pieter Claerhout
	
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

	if ( ! defined( 'YD_FW_NAME' ) ) {
		die( 'Yellow Duck Framework is not loaded.' );
	}

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
			$this->defaults = array(
				'scheme' => '', 'host' => '', 'port' => '', 'user' => '', 'pass' => '', 'path' => '',
				'query' => '', 'fragment' => ''
			);

			// Parse the URL
			$this->_url_parsed = array_merge( $this->defaults, parse_url( $url ) );

			// Parse the query string
			parse_str( $this->_url_parsed['query'], $this->_url_parsed['query'] );

		}

		/**
		 *	This function will check if the argument is one of the directories specified in the URL path. It will
		 *	basically check if the argument is in the array resulting from the getPathDirectories function.
		 *
		 *	@param	$arg	Directory to check.
		 *
		 *	@returns	Boolean indicating if the argument is one of the directories in the URL path or not.
		 */
		function isDirectory( $arg ) {
			return in_array( $arg, $this->getPathDirectories() );
		}

		/**
		 *	This function will return the original URL.
		 *
		 *	@returns	The original URL.
		 */
		function getUrl() {

			// Add the scheme
			$url = $this->getNamedPart( 'scheme' ) . '://';

			// Add the user information
			if ( $this->getNamedPart( 'user' ) != '' ) {
				$url .= $this->getNamedPart( 'user' );
				if ( $this->getNamedPart( 'pass' ) != '' ) {
					$url .= ':' . $this->getNamedPart( 'pass' );
				}
				$url .= '@';
			}

			// Add the host information
			$url .= $this->getNamedPart( 'host' );

			// Add the port information
			if ( $this->getNamedPart( 'port' ) != '' ) {
				$url .= ':' . $this->getNamedPart( 'port' );
			}

			// Add the URI
			$url .= $this->getUri();

			// Return the URL
			return $url;

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
		 *	This function will set the named part to the new contents.
		 *
		 *	@param $name	The name of the part to update
		 *	@param $value	The new value for the part
		 */
		function setNamedPart( $name, $value ) {
			$name = strtolower( $name );
			if ( in_array( $name, array_keys( $this->defaults ) ) ) {
				$this->_url_parsed[$name] = $value;
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
		 *	This function will return the path part of the URL as an array.
		 *
		 *	@returns	The path part of the URL.
		 */
		function getPathSegments() {
			$segments = explode( '/', $this->getNamedPart( 'path' ) );
			return array_slice( $segments, 1 );
		}

		/**
		 *	This function will return the path part of the URL as an array, only including directories. If the last part
		 *	of the URL is not a forward slash, the last part is considered to be a regular file.
		 *
		 *	@returns	The path part of the URL.
		 */
		function getPathDirectories() {
			$dirs = $this->getPathSegments();
			return array_slice( $dirs, 0, -1 );
		}

		/**
		 *	This function will return the list of directories that appear after the first occurence of the indicated
		 *	directory in the path part of the string. The following code examples show you how this can be used:
		 *
		 *	@code
		 *	$url = new YDUrl( 'http://www.yellowduck.be/ydf2/forum/cool.html' );
		 *	$r = $url->getSubDirectories('ydf2');   // returns: ['forum'];
		 *	$r = $url->getSubDirectories('forum');  // returns: [];
		 *	$r = $url->getSubDirectories('test');   // returns: [];
		 *	
		 *	// it gets the sub-directories of the first ocurrence:
		 *	$url = new YDUrl( 'http://www.yellowduck.be/ydf2/forum/ydf2/forum/cool.html' );
		 *	$r = $url->getSubDirectories('ydf2');   // returns: ['forum', 'ydf2', 'forum'];
		 *	$r = $url->getSubDirectories('forum');  // returns: ['ydf2', 'forum'];
		 *	$r = $url->getSubDirectories('test');   // returns: [];
		 *	@endcode
		 *
		 *	@param	$dir	The directory to get the subdirectories from.
		 *
		 *	@returns	Array with the directories that appear after the first occurence of the indicated directory in
		 *	the path part of the string
		 */
		function getPathSubdirectories( $dir ) {

			// Get the list of directories in the path string
			$dirs = $this->getPathDirectories();

			// Get the position of the directory in the list of directories
			$pos = array_search( $dir, $dirs );

			// Return an empty array if not found
			if ( $pos === false ) {
				return array();
			}

			// If found, return the list of subdirectories
			return array_slice( $dirs, $pos+1 );

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

			// Build the query string
			$querystr = '';
			foreach ( $this->getNamedPart( 'query' ) as $key=>$value ) {
				if ( is_array( $value ) ) {
					foreach ( $value as $key1=>$val ) {
						$querystr .= ( strlen( $querystr ) < 1 ) ? '' : '&';
						$querystr .= $key . '[]=' . rawurlencode( $val );
					}
				} else {
					$querystr .= ( strlen( $querystr ) < 1 ) ? '' : '&';
					$querystr .= $key . '=' . rawurlencode( $value );
				}
			}

			// Build the URI
			$uri = $this->getNamedPart( 'path' ) ? $this->getNamedPart( 'path' ) : '';
			$uri .= $querystr ? '?'.$querystr : '';
			$uri .= $this->getNamedPart( 'fragment' ) ? '#'.$this->getNamedPart( 'fragment' ) : '';

			// Return the URI
			return $uri;

		}

		/**
		 *	This function will retrieve the contents of a named query string variable. It will return an empty string if
		 *	the named query variable is not existing. If the name of the query variable indicates that it should be an
		 *	array, it will makes sure an array is returned.
		 *
		 *	@param	$name	The name of the query variable to retrieve.
		 *
		 *	@returns	The contents of the query variable.
		 */
		function getQueryVar( $name ) {
			if ( isset( $this->_url_parsed['query'][$name] ) ) {
				if ( substr( $name, -2, 2 ) == '[]' ) {
					if ( ! is_array( $this->_url_parsed['query'][$name] ) ) {
						$this->_url_parsed['query'][$name] = array( $this->_url_parsed['query'][$name] );
					}
				}
				return $this->_url_parsed['query'][$name];
			} else {
				return ( substr( $name, -2, 2 ) == '[]' ) ? array() : '';
			}
		}

		/**
		 *	This function will set the indicated query variable to the new value. If the name of the variable indicates
		 *	that the value is an array, this function will convert it to an array if it's not an array.
		 *
		 *	If the query variable is already existing, it's value will be updated. If it's not existing yet, the query
		 *	variable will be created.
		 *
		 *	@param	$name	The name of the query variable to update or set.
		 *	@param	$val	The value for the query variable.
		 */
		function setQueryVar( $name, $val ) {
			if ( substr( $name, -2, 2 ) == '[]' ) {
				$val = is_array( $val ) ? $val : array( $val );
			}
			$this->_url_parsed['query'][$name] = $val;
		}

		/**
		 *	This function will delete the indicated query variable if it exists.
		 *
		 *	@param	$name	The name of the query variable to delete.
		 */
		function deleteQueryVar( $name ) {
			if ( isset( $this->_url_parsed['query'][$name] ) ) {
				unset( $this->_url_parsed['query'][$name] );
			}
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
				
				// Include the filesystem library
				YDInclude( 'YDFileSystem.php' );

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
						$cacheFName = YD_TMP_PRE . 'W_' . md5( $cacheFName ) . '.wch';
						$cacheFName = YD_DIR_TEMP . '/' . $cacheFName;
					}

				}

				// If the cache filename is null, use the default one
				if ( $cacheFName == null ) {
					$cacheFName = YD_DIR_TEMP . '/' . YD_TMP_PRE . 'W_' . md5( $this->getUrl() ) . '.wch';
				}

				// Use the cache file if any
				if ( is_file( $cacheFName ) ) {
					$file = new YDFSFile( $cacheFName );
					$cacheValidTime = $file->getLastModified() + YD_HTTP_CACHE_TIMEOUT;
					if ( time() < $cacheValidTime ) {
						return $file->getContents();
					}
				};

			}

			// Create a new HTTP client
			$client = $this->_getHttpClient();

			// Now send the request
			$result = $client->doRequest();

			// Check if there was a result
			if ( $result == false ) {
				trigger_error(
					'Failed to retrieve the data from the url "' . $this->getUrl() . '". ' . $client->getError(),
					YD_ERROR
				);
			} else {
				$data = $client->getContent();
			}

			// Check if caching is enabled
			if ( $cache == true ) {

				// Save the cached data
				if ( $cacheFName != null ) {
					$dir = new YDFSDirectory( YD_DIR_TEMP );
					$dir->createFile( $cacheFName, $data );
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
				trigger_error(
					'Failed to retrieve the data from the url "' . $this->getUrl() . '". ' . $client->getError(),
					YD_ERROR
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
				trigger_error(
					'Failed to retrieve the data from the url "' . $this->getUrl() . '". ' . $client->getError(),
					YD_ERROR
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
			YDInclude( 'YDHttpClient.php' );
			if ( $this->getScheme() != 'http' ) {
				trigger_error( 'getContents: Only HTTP URLs are supported.', YD_ERROR );
			}
			$client = new YDHttpClient( $this->getHost(), $this->getPort() );
			$client->path = $this->getUri();
			$client->referer = $this->getUrl();
			$client->method = 'GET';
			return $client;
		}

	}

?>
