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
     *  This class defines a url.
     */
    class YDUrl extends YDBase {

        /**
         *  This is the class constructor for the YDUrl class. You need to
         *  specify the URL as it's only argument.
         *
         *  @param $url The url you want to have as a YDUrl object.
         */
        function YDUrl( $url ) {

            // Initialize YDBase
            $this->YDBase();
            
            // Save the path
            $this->_url = $url;

            // Set the defaults
            $parts_default = array(
                'scheme'   => '', 
                'host'     => '', 
                'port'     => '', 
                'user'     => '', 
                'pass'     => '', 
                'path'     => '', 
                'query'    => '', 
                'fragment' => ''
            );

            // Parse the URL
            $this->_url_parsed = array_merge($parts_default, parse_url( $url ) );

        }

        /**
         *  This function will return the original URL.
         *
         *  @returns The original URL.
         */
        function getUrl() {
            return $this->_url;
        }

        /**
         *  This function will return a named part of the URL. The following
         *  names are recognized: scheme, host, port, user, pass, path, query
         *  and fragment.
         *  
         *  This function will return an empty string if the part doesn't exist
         *  or if an invalid part has been specified.
         *
         *  @internal
         *
         *  @param $name The name of the part.
         *
         *  @returns The textual contents of the indicated part.
         */
        function getNamedPart( $name ) {
            if ( isset( $this->_url_parsed[$name] ) ) {
                return $this->_url_parsed[$name];
            } else {
                return '';
            }
        }

        /**
         *  This function will return the scheme of the URL.
         *
         *  @returns The scheme of the URL (http, ftp, ...).
         */
        function getScheme() {
            return strtolower( $this->getNamedPart( 'scheme' ) );
        }

        /**
         *  This function will return the host part of the URL.
         *
         *  @returns The host part of the URL.
         */
        function getHost() {
            return strtolower( $this->getNamedPart( 'host' ) );
        }

        /**
         *  This function will return the port part of the URL.
         *
         *  @returns The port part of the URL.
         */
        function getPort() {
            $port = $this->getNamedPart( 'port' );
            if ( is_numeric( $port ) ) {
                return intval( $port );
            } else {
                return null;
            }
        }

        /**
         *  This function will return the user part of the URL.
         *
         *  @returns The user part of the URL.
         */
        function getUser() {
            return $this->getNamedPart( 'user' );
        }

        /**
         *  This function will return the password part of the URL.
         *
         *  @returns The password part of the URL.
         */
        function getPassword() {
            return $this->getNamedPart( 'password' );
        }

        /**
         *  This function will return the path part of the URL.
         *
         *  @returns The path part of the URL.
         */
        function getPath() {
            return $this->getNamedPart( 'path' );
        }

        /**
         *  This function will return the query part of the URL. This is
         *  everything after the ? mark.
         *
         *  @returns The query part of the URL.
         */
        function getQuery() {
            return $this->getNamedPart( 'query' );
        }

        /**
         *  This function will return the fragment part of the URL. This is
         *  everything after the # mark.
         *
         *  @returns The fragment part of the URL.
         */
        function getFragment() {
            return $this->getNamedPart( 'fragment' );
        }

        /**
         *  Function to get the contents of the URL. It will get the contents
         *  using Gzip compression if possible in order to save bandwidth. It
         *  uses the HTTP Client class from Simon Willison to do the dirty work.
         *
         *  More information about the HTTP client class can be found on:
         *  http://scripts.incutio.com/httpclient/
         *
         *  If it fails to retrieve the data, it will raise a fatal error.
         *
         *  If not port was specified in the URL, it will revert to port 80.
         *
         *  By default, it will cache the downloaded data based on the etag and
         *  last-modified headers. The cache files are stored in the temp
         *  directory of the Yellow Duck framework and have the extension
         *  "wch". You can delete these automatically as they will be recreated
         *  on the fly if needed.
         *
         *  @remark
         *      This function only works for HTTP connections.
         *
         *  @param $cache (optional) Indicate if the web content should be 
         *                cached or not. By default, caching is turned on.
         *
         *  @returns Returns the contents of the URL.
         */
        function getContents( $cache=true ) {

            // Check if caching is enabled
            $cacheFName = null;
            if ( $cache === true ) {

                // Get the headers
                $headers = $this->getHeaders();

                // Check if we have etag or last modified
                if (
                    isset( $headers['etag'] )
                    ||
                    isset( $headers['last-modified'] )
                ) {

                    // Generate the cache file name
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

                    // Use the cache file if any
                    if ( is_file( $cacheFName ) ) {

                        // Create a file object for the cache file
                        $file = new YDFSFile( $cacheFName );

                        // Output the contents
                        return gzuncompress( $file->getContents() );
                        
                    };

                }

            }

            // Create a new HTTP client
            $client = $this->_getHttpClient();

            // Now send the request
            $result = $client->doRequest();

            // Check if there was a result
            if ( $result == false ) {
                new YDFatalError(
                    'Failed to retrieve the data from the url "' 
                    . $this->getUrl() . '". ' . $client->getError()
                );
            } else {
                $data = $client->getContent();
            }

            // Check if caching is enabled
            if ( $cache === true ) {

                // Save the cached data
                if ( $cacheFName != null ) {

                    // Save the cache data
                    $dir = new YDFSDirectory( YD_DIR_TEMP );
                    $dir->createFile( $cacheFName, gzcompress( $data ) );

                }

            }

            // Return the data
            return $data;

        }

        /**
         *  Function to get the contents of the URL and automatically applies a
         *  regular expression to the result. It will get the contents using 
         *  Gzip compression if possible in order to save bandwidth. It uses the
         *  HTTP Client class from Simon Willison to do the dirty work.
         *
         *  This function is based on the preg library and requires the preg
         *  syntax to function properly.
         *
         *  If it fails to retrieve the data, it will raise a fatal error.
         *
         *  If not port was specified in the URL, it will revert to port 80.
         *
         *  More information about the HTTP client class can be found on:
         *  http://scripts.incutio.com/httpclient/
         *
         *  @remark
         *      This function only works for HTTP connections.
         *
         *  @param $regex The regular expression you want to apply to the
         *                the contents of the URL.
         *
         *  @returns Array with the regex matches from the URL contents.
         */
        function getContentsWithRegex( $regex ) {

            // Get the contents
            $contents = $this->getContents();

            // Apply the regular expression
            preg_match_all( $regex, $contents, $matches );

            // Return the matches
            return $matches;

        }

        /**
         *  This function retrieves the header information for the specified URL.
         *
         *  @return Array containing the headers fors the URL.
         */
        function getHeaders() {

            // Get the head of the file
            $client = $this->_getHttpClient();
            $client->method = 'HEAD';
            $client->headers_only = true;
            $result = $client->doRequest();

            // Check the result
            if ( $result === false ) {
                new YDFatalError(
                    'Failed to retrieve the data from the url "' 
                    . $this->getUrl() . '". ' . $client->getError()
                );
            } else {
                return $client->headers;
            }

        }

        /**
         *  This function retrieves the header information for the specified URL.
         *
         *  @return Array containing the headers fors the URL.
         */
        function getStatus() {

            // Get the head of the file
            $client = $this->_getHttpClient();
            $client->method = 'HEAD';
            $client->headers_only = true;
            $result = $client->doRequest();

            // Check the result
            if ( $result === false ) {
                new YDFatalError(
                    'Failed to retrieve the data from the url "' 
                    . $this->getUrl() . '". ' . $client->getError()
                );
            } else {
                return intval( $client->status );
            }

        }

        /**
         *  This function will return an already setup HTTP client object.
         *
         *  @internal
         *
         *  @returns A new HttpClient class instance.
         */
        function _getHttpClient() {

            // Include the HTTP client
            require_once( YD_DIR_3RDP . '/HttpClient.class.php' );

            // Check the URL scheme
            if ( $this->getScheme() != 'http' ) {
                new YDFatalError( 'getContents: Only HTTP URLs are supported.' );
            }

            // Default to port 80
            if ( $this->getPort() == null ) {
                $port = 80;
            } else {
                $port = $this->getPort();
            }

            // Get the head of the file
            $client = new HttpClient( $this->getHost(), $port );
            $client->useGzip( true );
            $client->setDebug( YD_DEBUG );
            $client->path = $this->getUrl();
            $client->referer = $this->getUrl();
            $client->method = 'GET';

            // Return the client
            return $client;

        }

    }

?>
