<?php

    /*

        Yellow Duck Framework version 2.0
        (c) Copyright 2002-2005 Pieter Claerhout

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

    // Check if the framework is loaded
    if ( ! defined( 'YD_FW_NAME' ) ) {
        die( 'Yellow Duck Framework is not loaded.' );
    }

    // Includes
    include_once( YD_DIR_HOME . '/3rdparty/HttpClient.class.php' );

    // Configure the default for this class
    YDConfig::set( 'YD_HTTP_USES_GZIP', 1, false );
    YDConfig::set( 'YD_HTTP_CACHE_TIMEOUT', 3600, false );
    YDConfig::set( 'YD_HTTP_CACHE_USEHEAD', 1, false );

    /**
     *	This is the YDHttpClient class. It extends the HttpClient class and adds support for specifying the content
     *	type.
     *
     *	@todo
     *		Redirects to different sites are not supported, and make retrieving of images from pbase fail. This might
     *		result in the fact that we need to move to a different HTTP library. Another option is to rewrite the ]
     *		library completely.
     */
    class YDHttpClient extends HttpClient {

        /**
         *	This is the class constructor for the YDHttpClient class.
         *
         *	@param $host	The host name to connect to.
         *	@param $port	(optional) The port to connect to (default is 80).
         */
        function YDHttpClient( $host, $port=80 ) {

            // Initialize the HTTP client
            $this->HttpClient( $host, $port );
            $this->user_agent = 'Mozilla/4.0 (compatible; ' . YD_FW_NAMEVERS . ')';
            $this->contenttype = '';
            if ( YDConfig::get( 'YD_HTTP_USES_GZIP' ) == 1 ) {
                $this->useGzip( true );
            } else {
                $this->useGzip( false );
            }
            $this->setDebug( YDConfig::get( 'YD_DEBUG' ) );

        }

        /**
         *  Sets the timeout for the HTTP connection.
         *
         *  @param  $timeout    Timeout in seconds.
         */
        function setTimeout( $timeout ) {
             $this->timeout = $timeout;
        }

        /**
         *	This function will build the actual HTTP request and return it as plain text.
         *
         *	@returns	Plain text version of the HTTP request.
         */
        function buildRequest() {

            // Start with default headers
            $headers = array();
            $headers[] = "{$this->method} {$this->path} HTTP/1.0";
            $headers[] = "Host: {$this->host}";
            $headers[] = "User-Agent: {$this->user_agent}";
            $headers[] = "Accept: {$this->accept}";
            if ( $this->use_gzip ) {
                $headers[] = "Accept-encoding: {$this->accept_encoding}";
            }
            $headers[] = "Accept-language: {$this->accept_language}";
            if ( $this->referer ) {
                $headers[] = "Referer: {$this->referer}";
            }

            // Cookies
            if ( $this->cookies ) {
                $cookie = 'Cookie: ';
                foreach ( $this->cookies as $key => $value ) {
                    $cookie .= "$key=$value; ";
                }
                $headers[] = $cookie;
            }

            // Basic authentication
            if ( $this->username && $this->password ) {
                $headers[] = 'Authorization: BASIC ' . base64_encode(
                    $this->username.':'.$this->password
                );
            }

            // If this is a POST, set the content type and length
            if ( $this->postdata ) {
                if ( empty( $this->contenttype ) ) {
                    $headers[] = 'Content-Type: application/x-www-form-urlencoded';
                } else {
                    $headers[] = 'Content-Type: ' . $this->contenttype;
                }
                $headers[] = 'Content-Length: '.strlen($this->postdata);
            }

            // Build it
            $request = implode("\r\n", $headers)."\r\n\r\n".$this->postdata;

            // Return the request
            return $request;

        }

    }

?>
