<?php

    /*
     *  Yellow Duck Framework version 2.0
     *  (c) copyright 2004 Pieter Claerhout, pieter@yellowduck.be
     */

    // Check if the YDFramework is loaded.
    if ( ! defined( 'YD_FW_NAME' ) ) {
        die(  'Yellow Duck Framework is not loaded.' );
    }

    // Includes
    require_once( 'YDBase.php' );

    /**
     *  This class implements a BBCode parser. By default, it supports a number
     *  of standard codes that can be implemented. The following codes are
     *  supported by default:
     *
     *  - [img]http://elouai.com/images/star.gif[/img]
     *  - [img=http://www.yellowduck.be/]http://elouai.com/images/star.gif[/img]
     *  - [url="http://elouai.com"]eLouai[/url]
     *  - [url]http://elouai.com[/url]
     *  - [mail="webmaster\@elouai.com"]Webmaster[/mail]
     *  - [mail]webmaster\@elouai.com[/mail]
     *  - [email="webmaster\@elouai.com"]Webmaster[/email]
     *  - [email]webmaster\@elouai.com[/email]
     *  - [color="red"]RED[/color]
     *  - [b]bold[/b]
     *  - [i]italic[/i]
     *  - [u]underline[/u]
     *  - [code]value="123";[/code]
     *  - [quote]a quote[/quote]
     *  - [p]paragraph[/p]
     */
    class YDBBCode extends YDBase {

        /**
         *  This is the class constructor for the BBCode parser.
         */
        function YDBBCode() {

            // Initialize the parent class
            $this->YDBase();

            // Conversions
            $this->_conversions = array();

            // Add the initial conversions
            $this->addRule( "/\[[bB]\](.+?)\[\/[bB]\]/s", '<b>\\1</b>' );
            $this->addRule( "/\[[iI]\](.+?)\[\/[iI]\]/s", '<i>\\1</i>' );
            $this->addRule( "/\[[uU]\](.+?)\[\/[uU]\]/s", '<u>\\1</u>' );
            $this->addRule( "/\[[pP]\](.+?)\[\/[pP]\]/s", '<p>\\1</p>' );
            $this->addRule( "/\[code\](.+?)\[\/code\]/s", '<code>\\1</code>' );
            $this->addRule(
                "/\[quote\](.+?)\[\/quote\]/s", '<blockquote>\\1</blockquote>'
            );
            $this->addRule(
                "/\[url=([^<> \n]+?)\](.+?)\[\/url\]/i",
                '<a href="\\1">\\2</a>'
            );
            $this->addRule(
                "/\[url\]([^<> \n]+?)\[\/url\]/i",
                '<a href="\\1">\\1</a>'
            );
            $this->addRule(
                "/\[mail=([^<> \n]+?)\](.+?)\[\/mail\]/i",
                '<a href="mailto:\\1">\\2</a>'
            );
            $this->addRule(
                "/\[mail\]([^<> \n]+?)\[\/mail\]/i",
                '<a href="mailto:\\1">\\1</a>'
            );
            $this->addRule(
                "/\[email=([^<> \n]+?)\](.+?)\[\/email\]/i",
                '<a href="mailto:\\1">\\2</a>'
            );
            $this->addRule(
                "/\[email\]([^<> \n]+?)\[\/email\]/i",
                '<a href="mailto:\\1">\\1</a>'
            );
            $this->addRule(
                "/\[img=([^<> \n]+?)\](.+?)\[\/img\]/i",
                '<a href="\\1"><img border="0" src="\\2"></a>'
            );
            $this->addRule(
                "/\[img\]([^<> \n]+?)\[\/img\]/i",
                '<img border="0" src="\\1">'
            );
            $this->addRule(
                "/\[color=([^<> \n]+?)\](.+?)\[\/color\]/i",
                '<font color="\\1">\\2</font>'
            );

        }

        /**
         *  You can use this function to add a conversion rule to the parser.
         *
         *  @param $regex   Regular expression matching the tags.
         *  @param $replace The replacement for the tag regex.
         */
        function addRule( $regex, $replace ) {
            $this->_conversions[ $regex ] = $replace;
        }

        /**
         *  This function will take a piece of text and convert the BBCode tags
         *  to their HTML equivalents. You can optionally convert line breaks as
         *  well as convert the remaining HTML tags to their entities.
         *
         *  @param $data        The data you want to convert.
         *  @param $convertBr   (optional) Boolean to indicate that new lines
         *                      should be converted to <br/> tags. This is
         *                      turned on by default.
         *  @param $convertTags (optional) Boolean to indicate that tags should
         *                      be converted to HTML. This is turned on by
         *                      default.
         *
         *  @returns The HTML equivalent of the string with all the BBCode's
         *           converted according to the conversion table of this class.
         */
        function toHtml( $data, $convertBr=true, $convertTags=true ) {

            // Convert tags if needed
            if ( $convertTags == true ) {
                $data = str_replace( '<', '&lt;', $data );
                $data = str_replace( '>', '&gt;', $data );
            }

            // Convert the tags
            $data = preg_replace(
                array_keys( $this->_conversions ),
                array_values( $this->_conversions ),
                $data 
            );

            // Open http links in a new window
            $data = str_replace(
                ' href="http://', ' target="_blank" href="http://', $data
            );

            // Convert tags if needed
            if ( $convertBr == true ) {
                $data = nl2br( $data );
            }

            // Return the data
            return $data;

        }

    }

?>
