<?php

	// Yellow Duck Framework version 2.0
	// (c) copyright 2004 Pieter Claerhout, pieter@yellowduck.be

	if ( ! defined( 'YD_FW_NAME' ) ) {
		die( 'Yellow Duck Framework is not loaded.' );
	}

	require_once( 'YDBase.php' );
	require_once( 'YDFSFile.php' );
	require_once( 'YDFSImage.php' );
	require_once( 'YDObjectUtil.php' );
	require_once( 'htmlMimeMail/htmlMimeMail.php' );

	/**
	 *	This class defines an email message.
	 */
	class YDEmail extends YDBase {

		/**
		 *	This is the class constructor for the YDEmail class.
		 */
		function YDEmail( $crlf="\n" ) {

			// Initialize YDBase
			$this->YDBase();

			// Intialize a new mime message
			$this->_msg = new htmlMimeMail();

			// Start with empty variables
			$this->sender = '';
			$this->to = array();
			$this->to_plain = array();
			$this->cc = array();
			$this->bcc = array();

		}

		/**
		 *	Function to set the from address.
		 *
		 *	@param $email	Email address to use as from
		 *	@param $name	Name to use as the from
		 */
		function setFrom( $email, $name='' ) {
			$this->_msg->setFrom( $this->_mergeEmailName( $email, $name ) );
			$this->sender = $email;
		}

		/**
		 *	Function to set the reply to address.
		 *
		 *	@param $email	Email address to use as reply to
		 *	@param $name	Name to use as the relpy to
		 */
		function setReplyTo( $email, $name='' ) {
			$this->_msg->setReturnPath( $this->_mergeEmailName( $email, $name ) );
		}

		/**
		 *	Function to add an address the to list.
		 *
		 *	@param $email	Email address to add
		 *	@param $name	Name to add
		 */
		function addTo( $email, $name='' ) {
			array_push( $this->to, $this->_mergeEmailName( $email, $name ) );
			array_push( $this->to_plain, $email );
		}

		/**
		 *	Function to add an address the cc list.
		 *
		 *	@param $email	Email address to add
		 *	@param $name	Name to add
		 */
		function addCc( $email, $name='' ) {
			array_push( $this->cc, $this->_mergeEmailName( $email, $name ) );
			array_push( $this->to_plain, $email );
		}

		/**
		 *	Function to add an address the bcc list.
		 *
		 *	@param $email	Email address to add
		 *	@param $name	Name to add
		 */
		function addBcc( $email, $name='' ) {
			array_push( $this->bcc, $this->_mergeEmailName( $email, $name ) );
			array_push( $this->to_plain, $email );
		}

		/**
		 *	Function to set the subject of the email
		 *
		 *	@param $subject	Subject of the email
		 */
		function setSubject( $subject ) {
			$this->_msg->setSubject( strip_tags( $subject ) );
		}

		/**
		 *	Sets the plain text part of a message. All HTML tags are automatically removed from the text.
		 *
		 *	@param $text	The text to set.
		 */
		function setTxtBody( $text ) {
			$this->_msg->setText( strip_tags( $text ) );
		}

		/**
		 *	Sets the HTML part of a message.
		 *
		 *	@param $html	The HTML version of the message.
		 *	@param $text	The plain text version of the message.
		 */
		function setHtmlBody( $html, $text='' ) {
			$this->_msg->setHtml( $html, $text );
		}

		/**
		 *	Adds an attachment to a message.
		 *
		 *	@param $file		The file path.
		 *	@param $c_type		(optional) The content type of the image or file.
		 *	@param $name		(optional) The suggested file name for the data.
		 */
		function addAttachment( $file, $c_type='application/octet-stream', $name='' ) {
			if ( ! YDObjectUtil::isSubClass( $file, 'YDFSFile' ) ) {
				$file = new YDFSFile( $file );
			}
			$data = $file->getContents();
			if ( empty( $name ) ) { 
				$name = $file->getBaseName();
			}
			$this->_msg->addAttachment( $data, $name, $c_type );
		}

		/**
		 *	If sending an HTML message with embedded images, use this function
		 *	to add the image.
		 *
		 *	@param $file	The image file path.
		 *	@param $c_type	(optional) The content type of the image or file.
		 *	@param $name	(optional) The filename of the image.
		 */
		function addHTMLImage( $file, $c_type='', $name='' ) {
			if ( ! YDObjectUtil::isSubClass( $file, 'YDFSImage' ) ) {
				$file = new YDFSImage( $file );
			}
			$data = $file->getContents();
			if ( empty( $name ) ) { 
				$name = $file->getBaseName();
			}
			if ( empty( $c_type ) ) { 
				$c_type = $file->getMimeType();
			}
			$this->_msg->addHTMLImage( $data, $name, $c_type );
		}

		/**
		 *	This function will send the actual email. It accepts a list of recipients which should be defined as an 
		 *	array. If no recipients are defined, it will use the one setup in the email message using addTo.
		 *
		 *	@returns	Boolean indicating if the message was send succesfully or not.
		 */
		function send() {

			// Check if there is a to address
			if ( sizeof( $this->to_plain ) == 0 ) {
				YDFatalError( 'You need to specify at least one recipient in the YDEmail class'	);
			}

			// Check if there is a to address
			if ( empty( $this->sender ) ) {
				YDFatalError( 'You need to specify who this email message coming from.' );
			}

			// Get the original the message
			$message = $this->_msg;

			// Add the CC info
			if ( sizeof( $this->cc ) > 0 ) {
				$message->setCC( implode( '; ', $this->cc ) );
			}

			// Add the BCC info
			if ( sizeof( $this->bcc ) > 0 ) {
				$message->setBCC( implode( '; ', $this->bcc ) );
			}

			// Set the header
			$message->setHeader( 'To', implode( ', ', $this->to ) );
			$message->setHeader( 'X-Mailer', YD_FW_NAMEVERS );

			// Send the email
			$result = @ $message->send( $this->to_plain );

			// Return the result
			return $result;

		}

		/**
		 *	This will merge the email address and name to the standard format used in the headers of an email. If you 
		 *	for example merge the email address "pieter@yellowduck.be" with the name "Pieter Claerhout", the result of
		 *	this function will be ""Pieter Claerhout" <pieter@yellowduck.be>". So, it surrounds the name by double 
		 *	quotes and appends the email address surrounded by < and >.
		 *
		 *	@param $address	Email address
		 *	@param $name	Name
		 *
		 *	@return	String containing formatted email address and name
		 *
		 *	@internal
		 */
		function _mergeEmailName( $address, $name='' ) {
			if ( $name == '' ) {
				return $address;
			} else {
				return "\"$name\" <$address>";
			}
		}

	}

?>