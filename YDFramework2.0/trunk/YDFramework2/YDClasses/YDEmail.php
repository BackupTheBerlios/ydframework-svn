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
    YDInclude( 'YDFileSystem.php' );
    YDInclude( 'phpmailer/class.phpmailer.php' );

    // Constants
    define( 'YD_EMAIL_PRIORITY_HIGH', '1' );
    define( 'YD_EMAIL_PRIORITY_NORMAL', '3' );
    define( 'YD_EMAIL_PRIORITY_LOW', '5' );

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
            $this->_msg = new PHPMailer();

            // Set the language
            $this->_msg->SetLanguage( 'en', YD_DIR_HOME . '/3rdparty/phpmailer/language/' );

            // Start with empty variables
            $this->sender = '';
            $this->to_plain = array();

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
            $this->_msg->From = $email;
            if ( ! empty( $name ) ) {
                $this->_msg->FromName = $name;
            }
            $this->sender = $email;
        }

        /**
         *	Function to set the reply to address.
         *
         *	@param $email	Email address to use as reply to
         *	@param $name	Name to use as the relpy to
         */
        function setReplyTo( $email, $name='' ) {
            $this->_msg->AddReplyTo( $email, $name );
        }

        /**
         *	Function to set the return receipt address.
         *
         *	@param $email	Email address to use as return receipt address
         *	@param $name	Name to use as the return receipt address
         */
        function setReturnReceipt ($email, $name = '') {
            $this->_msg->AddCustomHeader( 'Return-Receipt-To:' . $this->_mergeEmailName( $email, $name ) );
        }

        /**
         *	Function to set the priority of the email. This can be one of the following constants:
         *	YD_EMAIL_PRIORITY_HIGH, YD_EMAIL_PRIORITY_NORMAL, YD_EMAIL_PRIORITY_LOW.
         *
         *	@param $priority	(optional) The priority of the email. Default is YD_EMAIL_PRIORITY_NORMAL.
         */
        function setPriority( $priority=YD_EMAIL_PRIORITY_NORMAL ) {
            if ( ! in_array( $priority, array( 1, 3, 5 ) ) ) {
                $priority = YD_EMAIL_PRIORITY_NORMAL;
            }
            $this->_msg->Priority = $priority;
        }

        /**
         *	Function to add an address the to list.
         *
         *	@param $email	Email address to add
         *	@param $name	Name to add
         */
        function addTo( $email, $name='' ) {
            $this->_msg->AddAddress( $email, $name );
            array_push( $this->to_plain, $email );
        }

        /**
         *	Function to add an address the cc list.
         *
         *	@param $email	Email address to add
         *	@param $name	Name to add
         */
        function addCc( $email, $name='' ) {
            $this->_msg->AddCC( $email, $name );
            array_push( $this->to_plain, $email );
        }

        /**
         *	Function to add an address the bcc list.
         *
         *	@param $email	Email address to add
         *	@param $name	Name to add
         */
        function addBcc( $email, $name='' ) {
            $this->_msg->AddBCC( $email, $name );
            array_push( $this->to_plain, $email );
        }

        /**
         *	Function to set the subject of the email
         *
         *	@param $subject	Subject of the email
         */
        function setSubject( $subject ) {
            $this->_msg->Subject = strip_tags( $subject );
        }

        /**
         *	Sets the plain text part of a message. All HTML tags are automatically removed from the text.
         *
         *	@param $text	The text to set.
         */
        function setTxtBody( $text ) {
            $this->_msg->AltBody = strip_tags( $text );
        }

        /**
         *	Sets the HTML part of a message.
         *
         *	@param $html	The HTML version of the message.
         *	@param $text	The plain text version of the message.
         */
        function setHtmlBody( $html, $text='' ) {
            $this->_msg->IsHTML( true );
            $this->_msg->Body = $html;
        }

        /**
         *	Sets the SMTP parameters and indicates that the message needs to be send with SMTP.
         *
         *	@param $host	SMTP server.
         *	@param $port	SMTP server port (defaults to 25)
         *	@param $helo	Which helo command that needs to be send
         *	@param $auth	Boolean indicating if authentication needs to be used
         *	@param $user	Username used for authentication
         *	@param $pass	Password used for authentication
         */
        function setSMTP( $host='localhost', $port=25, $helo='', $auth=false, $user='', $pass='' ) {
            $this->_msg->IsSMTP( true );
            $this->_msg->Host = $host;
            $this->_msg->Port = $port;
            $this->_msg->Helo = $helo;
            if ( $auth ) {
                $this->_msg->SMTPAuth = true;
                $this->_msg->Username = $user;
                $this->_msg->Password = $pass;
            }
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
            if ( empty( $name ) ) {
                $name = $file->getBaseName();
            }
            $this->_msg->AddAttachment( $file->getAbsolutePath(), $name, 'base64', $c_type );
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
            if ( empty( $name ) ) {
                $name = $file->getBaseName();
            }
            if ( empty( $c_type ) ) {
                $c_type = $file->getMimeType();
            }
            $this->_msg->AddEmbeddedImage( $file->getAbsolutePath(), $name, $name, 'base64', $c_type );
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
            
            // Check if there is a text body but no HTML body
            if ( strlen( $this->_msg->AltBody ) && ! strlen( $this->_msg->Body ) ) {
                $this->_msg->IsHTML( false );
                $this->_msg->Body = $this->_msg->AltBody;
            }

            // Get the original the message
            $message = & $this->_msg;

            // Set the headers
            $message->AddCustomHeader( 'X-Mailer: ' . YD_FW_NAMEVERS );

            // Send the message
            if ( ! $message->Send() ) {
                trigger_error( 'Mailer Error: ' . $message->ErrorInfo, YD_WARNING );
                return false;
            } else {
                return true;
            }

        }

    }

?>