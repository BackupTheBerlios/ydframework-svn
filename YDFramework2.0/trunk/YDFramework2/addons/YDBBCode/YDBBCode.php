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

    // Check if the framework is loaded
    if ( ! defined( 'YD_FW_NAME' ) ) {
        die( 'Yellow Duck Framework is not loaded.' );
    }

    /**
     *	This class implements a BBCode parser. By default, it supports a number of standard codes that can be 
     *	implemented. The following codes are supported by default: img, url. mail, email, color, b, i, u, code, quote, p
     */
    class YDBBCode extends YDAddOnModule {

        /**
         *	This is the class constructor for the BBCode parser.
         */
        function YDBBCode() {

            // Initialize the parent class
            $this->YDAddOnModule();

            // Conversions
            $this->_conversions = array();

            // Add the initial conversions
            $this->addRule( "/\[[bB]\](.+?)\[\/[bB]\]/s", '<b>\\1</b>' );
            $this->addRule( "/\[[iI]\](.+?)\[\/[iI]\]/s", '<i>\\1</i>' );
            $this->addRule( "/\[[uU]\](.+?)\[\/[uU]\]/s", '<u>\\1</u>' );
            $this->addRule( "/\[[pP]\](.+?)\[\/[pP]\]/s", '<p>\\1</p>' );
            $this->addRule( "/\[code\](.+?)\[\/code\]/s", '<code>\\1</code>' );
            $this->addRule( "/\[quote\](.+?)\[\/quote\]/s", '<blockquote>\\1</blockquote>' );
            $this->addRule( "/\[url=([^<> \n]+?)\](.+?)\[\/url\]/i", '<a href="\\1">\\2</a>' );
            $this->addRule( "/\[url\]([^<> \n]+?)\[\/url\]/i", '<a href="\\1">\\1</a>' );
            $this->addRule( "/\[mail=([^<> \n]+?)\](.+?)\[\/mail\]/i", '<a href="mailto:\\1">\\2</a>' );
            $this->addRule( "/\[mail\]([^<> \n]+?)\[\/mail\]/i", '<a href="mailto:\\1">\\1</a>' );
            $this->addRule( "/\[email=([^<> \n]+?)\](.+?)\[\/email\]/i", '<a href="mailto:\\1">\\2</a>' );
            $this->addRule( "/\[email\]([^<> \n]+?)\[\/email\]/i", '<a href="mailto:\\1">\\1</a>' );
            $this->addRule( "/\[img=([^<> \n]+?)\](.+?)\[\/img\]/i", '<a href="\\1"><img border="0" src="\\2"></a>' );
            $this->addRule( "/\[img\]([^<> \n]+?)\[\/img\]/i", '<img border="0" src="\\1">' );
            $this->addRule( "/\[color=([^<> \n]+?)\](.+?)\[\/color\]/i", '<font color="\\1">\\2</font>' );

            // Attributes to convert links
            $this->_convertLinks = array(
                "#([\t\r\n ])([a-z0-9]+?){1}://([\w\-]+\.([\w\-]+\.)*[\w]+(:[0-9]+)?(/[^ \"\n\r\t<]*)?)#i" => '\1[url]\2://\3[/url]',
                "#([\t\r\n ])(www|ftp)\.(([\w\-]+\.)*[\w]+(:[0-9]+)?(/[^ \"\n\r\t<]*)?)#i" => '\1[url=http://\2.\3]\2.\3[/url]',
                "#([\n ])([a-z0-9\-_.]+?)@([\w\-]+\.([\w\-\.]+\.)*[\w]+)#i" => "\\1[email]\\2@\\3[/email]"
            );

        }

        /**
         *	You can use this function to add a conversion rule to the parser.
         *
         *	@param $regex	Regular expression matching the tags.
         *	@param $replace	The replacement for the tag regex.
         */
        function addRule( $regex, $replace ) {
            $this->_conversions[ $regex ] = $replace;
        }

        /**
         *	This function will highlight links in the given text.
         *
         *	@remarks
         *		This function can be called statically.
         *
         *	@param $text	The text which should have it's links highlighted.
         *
         *	@returns	The text with links highlighted as BBCode tags.
         */
        function convertLinks( $text ) {
            $search = array_keys( $this->_convertLinks );
            $replace = array_values($this->_convertLinks );
            return preg_replace( $search, $replace, $text  );
        }

        /**
         *	This function will take a piece of text and convert the BBCode tags to their HTML equivalents. You can 
         *	optionally convert line breaks as well as convert the remaining HTML tags to their entities.
         *
         *	@param $data			The data you want to convert.
         *	@param $convertBr		(optional) Boolean to indicate that new lines should be converted to <br/> tags. 
         *							This is turned on by default.
         *	@param $convertTags		(optional) Boolean to indicate that tags should be converted to HTML. This is turned
         *							on by default.
         *	@param $convertLinks	(optional) Boolean to indicate if links should be automatically highlighted or not.
         *							This is turned on by default.
         *
         *	@returns	The HTML equivalent of the string with all the BBCode's converted according to the conversion table
         *				of this class.
         */
        function toHtml( $data, $convertBr=true, $convertTags=true, $convertLinks=true ) {

            // Encode the references
            $data = YDStringUtil::encodeString( $data );

            // Convert the links to BBcode
            if ( $convertLinks === true ) {
                $data = $this->convertLinks( $data );
            }

            // Convert tags if needed
            if ( $convertTags === true ) {
                $data = str_replace( '<', '&lt;', $data );
                $data = str_replace( '>', '&gt;', $data );
            }

            // Convert the tags
            $data = preg_replace( array_keys( $this->_conversions ), array_values( $this->_conversions ), $data  );

            // Open http links in a new window
            $data = str_replace( ' href="http://', ' target="_blank" href="http://', $data );

            // Convert tags if needed
            if ( $convertBr === true ) {
                $data = nl2br( trim( $data ) );
            }

            // Return the data
            return $data;

        }

    }

?>
