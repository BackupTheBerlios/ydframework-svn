<?php

	// Yellow Duck Framework version 2.0
	// (c) copyright 2004 Pieter Claerhout, pieter@yellowduck.be

	if ( ! defined( 'YD_FW_NAME' ) ) {
		die( 'Yellow Duck Framework is not loaded.' );
	}

	require_once( 'HttpClient.class.php' );

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
			if ( YD_HTTP_USES_GZIP == 1 ) {
				$this->useGzip( true );
			} else {
				$this->useGzip( false );
			}
			$this->setDebug( YD_DEBUG );

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
