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
    require_once( 'Mail.php' );
    require_once( 'Mail/mime.php' );

    /**
     *  This class defines an email message.
     *
     *  @todo
     *      If you send an email with an embedded image, the email sometimes
     *      shows up with the body in an attachment.
     */
    class YDEmail extends YDBase {

        /**
         *  This is the class constructor for the YDEmail class.
         */
        function YDEmail( $crlf="\n" ) {

            // Initialize YDBase
            $this->YDBase();

            // Intialize a new mime message
            $this->_msg = new Mail_mime( $crlf );

            // Start with empty variables
            $this->sender = '';
            $this->replyto = '';
            $this->from = '';
            $this->to = array();
            $this->to_plain = array();
            $this->subject = '';
            $this->cc = array();
            $this->bcc = array();

        }

        /**
         *  Function to set the from address.
         *
         *  @param $email Email address to use as from
         *  @param $name  Name to use as the from
         */
        function setFrom( $email, $name='' ) {
            $this->from = $this->_mergeEmailName( $email, $name );
            $this->sender = $email;
        }

        /**
         *  Function to set the reply to address.
         *
         *  @param $email Email address to use as reply to
         *  @param $name  Name to use as the relpy to
         */
        function setReplyTo( $email, $name='' ) {
            $this->replyto = $this->_mergeEmailName( $email, $name );
        }

        /**
         *  Function to add an address the to list.
         *
         *  @param $email Email address to add
         *  @param $name  Name to add
         */
        function addTo( $email, $name='' ) {
            array_push(
                $this->to, $this->_mergeEmailName( $email, $name )
            );
            array_push( $this->to_plain, $email );
        }

        /**
         *  Function to add an address the cc list.
         *
         *  @param $email Email address to add
         *  @param $name  Name to add
         */
        function addCc( $email, $name='' ) {
            array_push(
                $this->cc, $this->_mergeEmailName( $email, $name )
            );
            array_push( $this->to_plain, $email );
        }

        /**
         *  Function to add an address the bcc list.
         *
         *  @param $email Email address to add
         *  @param $name  Name to add
         */
        function addBcc( $email, $name='' ) {
            array_push(
                $this->bcc, $this->_mergeEmailName( $email, $name )
            );
            array_push( $this->to_plain, $email );
        }

        /**
         *  Function to set the subject of the email
         *
         *  @param $subject Subject of the email
         */
        function setSubject( $subject ) {
            $this->subject = $subject;
        }

        /**
         *  Sets the plain text part of a message.
         *
         *  @param $text The text to set.
         */
        function setTxtBody( $text ) {
            $this->_msg->setTxtBody( strip_tags( $text ) );
        }

        /**
         *  Sets the HTML part of a message.
         *
         *  @param $text The text to set.
         */
        function setHtmlBody( $text ) {
            $this->_msg->setHtmlBody( $text );
        }

        /**
         *  Adds an attachment to a message.
         *
         *  @param $file     The file name or the data itself.
         *  @param $c_type   (optional) The content type of the image or file.
         *  @param $name     (optional) The suggested file name for the data.
         *                   Only used, if $file contains data.
         *  @param $isfile   (optional) Whether $file is a file name or not.
         *  @param $encoding (optional) Type of transfer encoding to use for the
         *                   file data. Defaults is "base64". For text based
         *                   files (eg. scripts/html etc.) this could be given
         *                   as "quoted-printable".
         */
        function addAttachment(
            $file, $c_type='application/octet-stream', $name='',
            $isfile=true, $encoding='base64'
        ) {
            $this->_msg->addAttachment(
                $file, $c_type, $name, $isfile, $encoding
            );
        }

        /**
         *  If sending an HTML message with embedded images, use this function
         *  to add the image.
         *
         *  @param $file     The image file name or the image data itself.
         *  @param $c_type   (optional) The content type of the image or file.
         *  @param $name     (optional) The filename of the image. Only used, if
         *                   $file contains the image data.
         *  @param $isfile   (optional) Whether $file is a file name or not.
         */
        function addHTMLImage(
            $file, $c_type='application/octet-stream', $name='', $isfile=true
        ) {
            $this->_msg->addHTMLImage(
                $file, $c_type, $name, $isfile
            );
        }

        /**
         *  This function will send the actual email. It accepts a list of
         *  recipients which should be defined as an array. If no recipients are
         *  defined, it will use the one setup in the email message using addTo.
         */
        function send() {

            // Check if there is a to address
            if ( sizeof( $this->to_plain ) == 0 ) {
                new YDFatalError(
                    'You need to specify at least one recipient in the YDEmail '
                    . 'class'
                );
            }

            // Check if there is a to address
            if ( empty( $this->sender ) ) {
                new YDFatalError(
                    'You need to specify who this email message coming from.'
                );
            }

            // Get the email contents
            $body = $this->_msg->get();

            // Build the list of headers
            $headers = array(
                'From'     => $this->from,
                'To'       => implode( '; ', $this->to ),
                'X-Mailer' => YD_FW_NAMEVERS,
            );

            // Check for a reply to
            if ( ! empty( $this->replyto ) ) {
                $headers['Reply-To'] = $this->replyto;
            } else {
                $headers['Reply-To'] = $this->from;
            }

            // Add the CC header
            if ( sizeof( $this->cc ) > 0 ) {
                $headers['CC'] = implode( '; ', $this->cc );
            }

            // Add the BCC header
            if ( sizeof( $this->bcc ) > 0 ) {
                $headers['BCC'] = implode( '; ', $this->bcc );
            }

            // Add the subject
            if ( ! empty( $this->subject ) ) {
                $headers['Subject'] = $this->subject;
            } else {
                $headers['Subject'] = 'Untitled message';
            }

            // Get the email headers
            $headers = $this->_msg->headers( $headers );

            // Send the actual message
            $mail = & Mail::factory( 'mail' );
            $mail->send( $this->to_plain, $headers, $body );

        }

        /**
         *  This will merge the email address and name to the standard format
         *  used in the headers of an email. If you for example merge the email
         *  address "pieter@yellowduck.be" with the name "Pieter Claerhout", the
         *  result of this function will be ""Pieter Claerhout"
         *  <pieter@yellowduck.be>". So, it surrounds the name by double quotes
         *  and appends the email address surrounded by < and >.
         *
         *  @param $address Email address
         *  @param $name    Name
         *
         *  @return String containing formatted email address and name
         *
         *  @internal
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