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

	YDInclude( 'YDFileSystem.php' );
	YDInclude( 'htmlMimeMail/htmlMimeMail.php' );

	// General constants
	define( 'YDEMAIL_PRIORITY_HIGH', '1' );
	define( 'YDEMAIL_PRIORITY_NORMAL', '3' );
	define( 'YDEMAIL_PRIORITY_LOW', '5' );

	/**
	 *	This class defines an email message.
	 */
	class YDEmail extends YDBase {

		/**
		 *	This is the class constructor for the YDEmail class.
		 */
		function YDEmail() {

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

			// The SMTP indication
			$this->smtp = false;

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
			$this->_msg->setHeader( 'Reply-To', $this->_mergeEmailName( $email, $name ) );
		}

		/**
		 *	Function to set the return receipt address.
		 *
		 *	@param $email	Email address to use as return receipt address
		 *	@param $name	Name to use as the return receipt address
		 */
		function setReturnReceipt ($email, $name = '') { 
			$this->_msg->setHeader( 'Return-Receipt-To', $this->_mergeEmailName( $email, $name ) ); 
		}

		/**
		 *	Function to set the priority of the email. This can be one of the following constants:
		 *	YDEMAIL_PRIORITY_HIGH, YDEMAIL_PRIORITY_NORMAL, YDEMAIL_PRIORITY_LOW.
		 *
		 *	@param $priority	(optional) The priority of the email. Default is YDEMAIL_PRIORITY_NORMAL.
		 */
		function setPriority( $priority=YDEMAIL_PRIORITY_HIGH ) { 
			$this->_msg->setHeader( 'X-Priority', $priority ); 
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
		}

		/**
		 *	Function to add an address the bcc list.
		 *
		 *	@param $email	Email address to add
		 *	@param $name	Name to add
		 */
		function addBcc( $email, $name='' ) {
			array_push( $this->bcc, $this->_mergeEmailName( $email, $name ) );
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
		 *	Sets the SMTP parameters and indicates that the message needs to be send with SMTP.
		 *
		 *	@param $host	SMTP server.
		 *	@param $port	SMTP server port
		 *	@param $helo	Which helo command that needs to be send
		 *	@param $auth	Boolean indicating if authentication needs to be used
		 *	@param $user	Username used for authentication
		 *	@param $pass	Password used for authentication
		 */
		function setSMTP( $host=null, $port=null, $helo=null, $auth=null, $user=null, $pass=null ) {
			$this->smtp = true;
			$this->_msg->setSMTPParams( $host, $port, $helo, $auth, $user, $pass); 
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
		 *	If the SMTP parameters were set, it will try to send the message with SMTP, otherwise the mail function from
		 *	PHP is going to be used.
		 *
		 *	@returns	Boolean indicating if the message was send succesfully or not.
		 */
		function send() {

			// Check if there is a to address
			if ( sizeof( $this->to_plain ) == 0 ) {
				trigger_error( 'You need to specify at least one recipient in the YDEmail class', YD_ERROR );
			}

			// Check if there is a to address
			if ( empty( $this->sender ) ) {
				trigger_error( 'You need to specify who this email message coming from.', YD_ERROR );
			}

			// Get the original the message
			$message = & $this->_msg;

			// Add the CC info
			if ( sizeof( $this->cc ) > 0 ) {
				$message->setCC( implode( '; ', $this->cc ) );
			}

			// Add the BCC info
			if ( sizeof( $this->bcc ) > 0 ) {
				$message->setBCC( implode( '; ', $this->bcc ) );
			}

			// Set the headers
			$message->setHeader( 'To', implode( ', ', $this->to ) );
			$message->setHeader( 'X-Mailer', YD_FW_NAMEVERS );

			// Build the message
			if ( ! defined( 'CRLF' ) ) { $message->setCrlf( "\n" ); }
			if ( ! $message->is_built ) { $message->buildMessage(); }

			// Add the subject
			$subject = '';
			if ( ! empty( $message->headers['Subject'] ) ) {
				$subject = $message->_encodeHeader(
					$message->headers['Subject'], $message->build_params['head_charset']
				);
				unset( $message->headers['Subject'] );
			}

			// Get flat representation of headers
			foreach ( $message->headers as $name => $value ) {
				$headers[] = $name . ': ' . $message->_encodeHeader( $value, $message->build_params['head_charset'] );
			}

			// Get the to addresses
			$to = $message->_encodeHeader( implode( ', ', $this->to_plain ), $message->build_params['head_charset'] );

			// Send the email, try SMTP if possible
			if ( $this->smtp == true ) {
				$result =  $this->_msg->send( $to, 'smtp' );
			} else {
				$result = mail( $to, $subject, $message->output, implode( CRLF, $headers ) );
			}
				
			// Reset the subject in case mail is resent
			if ( $subject !== '' ) {
				$message->headers['Subject'] = $subject;
			}

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