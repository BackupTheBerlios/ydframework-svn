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
     *  - [url="http://elouai.com"]eLouai[/url]
     *  - [mail="webmaster@elouai.com"]Webmaster[/mail]
     *  - [size="25"]HUGE[/size]
     *  - [color="red"]RED[/color]
     *  - [b]bold[/b]
     *  - [i]italic[/i]
     *  - [u]underline[/u]
     *  - [list][*]item[*]item[*]item[/list]
     *  - [code]value="123";[/code]
     *  - [quote]John said yadda yadda yadda[/quote]
     *  - [p]paragraph[/p]
     *
     *  This class has been constructed in such a way that it's easy to add and
     *  deleted BBCodes. You can also have the remaining HTML tags converted to
     *  HTML entities if needed.
     *
     *  @todo
     *      We will probably need the inverse of this as well (function that
     *      converts from HTML to BBCode tags.
     */
    class YDBBCode extends YDBase {

        /**
         *  This is the class constructor for the BBCode parser.
         */
        function YDBBCode() {

            // The variable that hold the conversions
            $this->_conversions = array();
            $this->addCode( '[list]', '<ul>' );
            $this->addCode( '[*]', '<li>' );
            $this->addCode( '[/list]', '</ul>' );
            $this->addCode( '[img]', '<img src="' );
            $this->addCode( '[/img]', '">' );
            $this->addCode( '[b]', '<b>' );
            $this->addCode( '[/b]', '</b>' );
            $this->addCode( '[i]', '<i>' );
            $this->addCode( '[/i]', '</i>' );
            $this->addCode( '[u]', '<u>' );
            $this->addCode( '[/u]', '</u>' );
            $this->addCode( '[color="', '<span style="color:' );
            $this->addCode( '[color=', '<span style="color:' );
            $this->addCode( '[/color]', '</span>' );
            $this->addCode( '[size="', '<span style="font-size:' );
            $this->addCode( '[size=', '<span style="font-size:' );
            $this->addCode( '[/size]', '</span>' );
            $this->addCode( '[url="', '<a href="' );
            $this->addCode( '[url=', '<a href="' );
            $this->addCode( '[/url]', '</a>' );
            $this->addCode( '[mail="', '<a href="mailto:' );
            $this->addCode( '[mail=', '<a href="mailto:' );
            $this->addCode( '[/mail]', '</a>' );
            $this->addCode( '[code]', '<code>' );
            $this->addCode( '[/code]', '</code>' );
            $this->addCode( '[quote]', '<table bgcolor="lightgray"><tr><td bgcolor="white">' );
            $this->addCode( '[/quote]', '</td></tr></table>' );
            $this->addCode( '[p]', '<p>' );
            $this->addCode( '[/p]', '</p>' );
            $this->addCode( '"]', '">' );
            $this->addCode( ']', '">' );

        }

        /**
         *  This function will add a new code to the list of conversions.
         *
         *  @param $tag     The tag to look for.
         *  @param $replace The text to replace the tag with.
         */
        function addCode( $tag, $replace ) {
            $this->_conversions[ $tag ] = $replace;

        }

        /**
         *  This function will remove a code from the list of conversions.
         *
         *  @param $tag     The tag to remove.
         */
        function removeCode( $tag ) {
            unset( $this->_conversions[ $tag ] );
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
            $data = str_replace( 
                array_keys( $this->_conversions ),
                array_values( $this->_conversions ),
                $data 
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
