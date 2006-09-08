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
    include_once( YD_DIR_HOME_CLS . '/YDHttpClient.php' );

    /**
     *	This class is an interface to the Akismet comment spam checking webservice.
     */
    class YDAkismet extends YDAddOnModule {

        /**
         *	This is the class constructor for the YDAkismet module.
         */
        function YDAkismet( $blog_url, $api_key ) {

            // Initialize the parent class
            $this->YDAddOnModule();

            // Setup the module
            $this->_author = 'Pieter Claerhout';
            $this->_version = '1.0';
            $this->_copyright = '(c) Copyright 2002-2005 Pieter Claerhout';
            $this->_description = 'This class is an interface to the Akismet comment spam checking webservice.';

            // Set the variables
            $this->blog_url = $blog_url;
            $this->api_key  = $api_key;

        }

        /**
         *  This function verifies a comment against Akismet.
         *
         *  @param  $comment    The contents of the comment.
         *  @param  $author     The name of the author of the comment.
         *  @param  $email      (optional) The email address of the author of the comment.
         *  @param  $url        (optional) The url of the website of the author of the comment.
         *  @param  $user_ip    (optional) The IP address of the person who submitted the comment.
         *  @param  $user_ip    (optional) The IP address of the person who submitted the comment.
         *  @param  $user_agent (optional) The user agent of the person who submitted the comment.
         *  @param  $referrer   (optional) The HTTP referrer of the person who submitted the comment.
         *
         *  @returns    True if the comment is spam, false if the comment is not spam. If something went wrong, a null
         *              value is returned.
         */
        function checkComment( $comment, $author, $email='', $url='', $user_ip='', $user_agent='', $referrer='' ) {

            // Do the request
            $result = $this->doRequest(
                'rest.akismet.com', '/1.1/verify-key', array( 'key' => $this->api_key, 'blog' => $this->blog_url )
            );

            // If result is null, return null
            if ( is_null( $result ) ) {
                return null;
            }

            // Get the comment data
            $data = array();
            $data['blog'] = $this->blog_url;
            $data['user_ip'] = empty( $user_ip ) ? $_SERVER['REMOTE_ADDR'] : $user_ip;
            $data['user_agent'] = empty( $user_agent ) ? $_SERVER['HTTP_USER_AGENT'] : $user_agent;
            if ( empty( $referrer ) ) {
                if ( isset( $_SERVER['HTTP_REFERER'] ) ) {
                    $data['referrer'] = $_SERVER['HTTP_REFERER'];
                }
            } else {
                $data['referrer'] = $referrer;
            }
            $data['comment_author'] = $author;
            $data['comment_author_email'] = $email;
            $data['comment_author_url'] = $url;
            $data['comment_content'] = $comment;

            // Check the comment
            $result = $this->doRequest( $this->api_key . '.rest.akismet.com', '/1.1/comment-check', $data );

            // Return the result
            if ( is_null( $result ) ) {
                return null;
            } else {
                return ( strtolower( $result ) == 'false' ) ? false : true;
            }

        }

        /**
         *  Do a HTTP request.
         *
         *  @param  $host   The host to post to
         *  @param  $url    The URL to post to
         *  @param  $data   The POST data to send.
         *
         *  @return The data if something was returned, null if something failed.
         */
        function doRequest( $host, $url, $data ) {
            $client = new YDHttpClient( $host, 80 );
            $client->setDebug( false );
            $client->user_agent = YD_FW_NAME . '/' . YD_FW_VERSION . ' | ' . $this->getClassName() . '/' . $this->_version;
            $result = @ $client->post( $url, $data );
            if ( $result == false ) {
                return null;
            } else {
                return @ $client->getContent();
            }
        }

    }

?>
