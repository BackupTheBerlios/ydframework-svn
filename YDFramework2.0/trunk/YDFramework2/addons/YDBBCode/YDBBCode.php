<?php

    /*

        Yellow Duck Framework version 2.1
        (c) Copyright 2002-2007 Pieter Claerhout

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

    /**
     *  @addtogroup YDBBCode Addons - BBCode
     */

    // Check if the framework is loaded
    if ( ! defined( 'YD_FW_NAME' ) ) {
        die( 'Yellow Duck Framework is not loaded.' );
    }

    // Includes
    YDInclude( dirname( __FILE__ ) . '/stringparser_bbcode.class.php' );

    // Configure the default for this class
    YDConfig::set( 'YD_BBCODE_SMILIES_DIR', null, false );
    YDConfig::set( 'YD_BBCODE_SMILIES_URL', null, false );

    /**
     *	This class implements a BBCode parser. By default, it supports a number of standard codes that can be
     *	implemented. The following codes are supported by default: img, url, mail, email, color, b, i, u, code, quote, p
     *
     *  @ingroup YDBBCode
     */
    class YDBBCode extends YDAddOnModule {

        /**
         *	This is the class constructor for the BBCode parser.
         */
        function YDBBCode() {

            // Initialize the parent class
            $this->YDAddOnModule();

            // Setup the module
            $this->_author = 'Pieter Claerhout';
            $this->_version = '1.0';
            $this->_copyright = '(c) Copyright 2002-2007 Pieter Claerhout';
            $this->_description = 'This class implements a BBCode parser. By default, it supports a number of standard '
                               . 'codes that can be implemented. The following codes are supported by default: img, '
                               . 'url, mail, email, color, b, i, u, code, quote, p, size';

            // Create a new parser class
            $this->parser = new StringParser_BBCode();

            // Make tags not case sensitive and allow mixed attributes
            $this->parser->setGlobalCaseSensitive( false );
            $this->parser->setMixedAttributeTypes( true );

            // Register the codes
            $this->addCode( 'b', 'simple_replace', 'inline', null, array('start_tag'=>'<b>', 'end_tag'=>'</b>') );
            $this->addCode( 'i', 'simple_replace', 'inline', null, array('start_tag'=>'<i>', 'end_tag'=>'</i>') );
            $this->addCode( 'u', 'simple_replace', 'inline', null, array('start_tag'=>'<u>', 'end_tag'=>'</u>') );
            $this->addCode( 'p', 'simple_replace', 'inline', null, array('start_tag'=>'<p>', 'end_tag'=>'</p>') );
            $this->addCode( 'code', 'usecontent', 'block', array( &$this, 'doTagCode' ) );
            $this->addCode( 'color', 'callback_replace', 'inline', array( &$this, 'doTagColor' ) );
            $this->addCode( 'size', 'callback_replace', 'inline', array( &$this, 'doTagSize' ) );
            $this->addCode( 'quote', 'callback_replace', 'block', array( &$this, 'doTagQuote' ) );
            $this->addCode( 'url', 'usecontent?', 'link', array( &$this, 'doTagUrl' ), array('usecontent_param'=>'default') );
            $this->addCode( 'link', 'usecontent?', 'link', array( &$this, 'doTagUrl' ), array('usecontent_param'=>'default') );
            $this->addCode( 'mail', 'usecontent?', 'link', array( &$this, 'doTagEmail' ), array('usecontent_param'=>'default') );
            $this->addCode( 'email', 'usecontent?', 'link', array( &$this, 'doTagEmail' ), array('usecontent_param'=>'default') );
            $this->addCode( 'img', 'usecontent?', 'image', array( &$this, 'doTagImg' ), array('usecontent_param'=>'default') );
            $this->addCode( 'hr', 'simple_replace', 'inline', null, array('start_tag'=>'<hr />','end_tag'=>'') );

            // Add list handling
            $this->addCode( 'list', 'simple_replace', 'list', null, array('start_tag'=>'<ul>', 'end_tag'=>'</ul>') );
            $this->addCode( '*', 'simple_replace', 'listitem', null, array('start_tag'=>'<li>', 'end_tag'=>'</li>') );

            // Add some parser flags
            $this->parser->setCodeFlag( '*', 'closetag', BBCODE_CLOSETAG_OPTIONAL );
            $this->parser->setCodeFlag( 'hr', 'closetag', BBCODE_CLOSETAG_FORBIDDEN );
            $this->parser->setCodeFlag( 'list', 'opentag.before.newline', BBCODE_NEWLINE_DROP );
            $this->parser->setCodeFlag( 'list', 'closetag.before.newline', BBCODE_NEWLINE_DROP );
            $this->parser->setCodeFlag( 'hr', 'opentag.before.newline', BBCODE_NEWLINE_DROP );

            // Attributes to convert links
            $this->_convertLinks = array(
                "#([\t\r\n ])([a-z0-9]+?){1}://([\w\-]+\.([\w\-]+\.)*[\w]+(:[0-9]+)?(/[^ \"\n\r\t<]*)?)#i" => '\1[url]\2://\3[/url]',
                "#([\t\r\n ])(www|ftp)\.(([\w\-]+\.)*[\w]+(:[0-9]+)?(/[^ \"\n\r\t<]*)?)#i" => '\1[url=http://\2.\3]\2.\3[/url]',
                "#([\n ])([a-z0-9\-_.]+?)@([\w\-]+\.([\w\-\.]+\.)*[\w]+)#i" => "\\1[email]\\2@\\3[/email]"
            );

            // The list of smilies that this class supports
            $this->smilies = array(
                ':D' => 'icon_biggrin.gif',
                ':-D' => 'icon_biggrin.gif',
                ':grin:' => 'icon_biggrin.gif',
                ':)' => 'icon_smile.gif',
                ':-)' => 'icon_smile.gif',
                ':smile:' => 'icon_smile.gif',
                ':(' => 'icon_sad.gif',
                ':-(' => 'icon_sad.gif',
                ':sad:' => 'icon_sad.gif',
                ':o' => 'icon_surprised.gif',
                ':-o' => 'icon_surprised.gif',
                ':eek:' => 'icon_surprised.gif',
                ':shock:' => 'icon_eek.gif',
                ':?' => 'icon_confused.gif',
                ':-?' => 'icon_confused.gif',
                ':???:' => 'icon_confused.gif',
                '8)' => 'icon_cool.gif',
                '8-)' => 'icon_cool.gif',
                ':cool:' => 'icon_cool.gif',
                ':lol:' => 'icon_lol.gif',
                ':x' => 'icon_mad.gif',
                ':-x' => 'icon_mad.gif',
                ':mad:' => 'icon_mad.gif',
                ':P' => 'icon_razz.gif',
                ':-P' => 'icon_razz.gif',
                ':razz:' => 'icon_razz.gif',
                ':oops:' => 'icon_redface.gif',
                ':cry:' => 'icon_cry.gif',
                ':evil:' => 'icon_evil.gif',
                ':twisted:' => 'icon_twisted.gif',
                ':roll:' => 'icon_rolleyes.gif',
                ':wink:' => 'icon_wink.gif',
                ';)' => 'icon_wink.gif',
                ';-)' => 'icon_wink.gif',
                ':!:' => 'icon_exclaim.gif',
                ':?:' => 'icon_question.gif',
                ':idea:' => 'icon_idea.gif',
                ':arrow:' => 'icon_arrow.gif',
                ':|' => 'icon_neutral.gif',
                ':-|' => 'icon_neutral.gif',
                ':neutral:' => 'icon_neutral.gif',
                ':mrgreen:' => 'icon_mrgreen.gif',
                ':--' => 'icon_mrgreen.gif',
            );

        }

        /**
         *  Function to add a new code to the parser.
         *
         *  @param  $code           The code to register
         *  @param  $type           The type of the code to register
         *  @param  $content_type   The content type of the code to register
         *  @param  $callback       The callback function to handle the code
         *  @param  $params         (optional) The parameters for the callback function.
         */
        function addCode( $code, $type, $content_type, $callback=null, $params=array() ) {
            $this->parser->addCode(
                $code, $type, $callback, $params, $content_type, array( 'block', 'inline', 'listitem', 'list', 'link' ), array( 'image' )
            );
        }

        /**
         *  Function to handle the code tag.
         *
         *  @param $action         The action that is executed.
         *  @param $attributes     The attributes specified in the tag
         *  @param $content        The content value of the tag.
         *  @param $params         The parameters for the tag
         *  @param $node_object    A reference to the node object.
         *
         *  @returns    The html version of the tag.
         */
        function doTagCode( $action, $attributes, $content, $params, $node_object ) {
            if ( $action == 'validate' ) {
                return true;
            } else {
                if ( isset( $attributes['default'] ) && strtolower( $attributes['default'] == 'bbcode' ) ) {
                    $content = str_replace( '[', '<font color="blue">[', $content );
                    $content = str_replace( ']', ']</font>', $content );
                }
                return '<code>' . $content . '</code>';
            }
        }

        /**
         *  Function to handle the color tag.
         *
         *  @param $action         The action that is executed.
         *  @param $attributes     The attributes specified in the tag
         *  @param $content        The content value of the tag.
         *  @param $params         The parameters for the tag
         *  @param $node_object    A reference to the node object.
         *
         *  @returns    The html version of the tag.
         */
        function doTagColor( $action, $attributes, $content, $params, $node_object ) {
            if ( $action == 'validate' ) {
                return true;
            } else {
                if ( isset( $attributes['default'] ) && ! empty( $attributes['default'] ) ) {
                    return '<font color="' . $attributes['default'] . '">' . $content . '</font>';
                } else {
                    return $content;
                }
            }
        }

        /**
         *  Function to handle the size tag.
         *
         *  @param $action         The action that is executed.
         *  @param $attributes     The attributes specified in the tag
         *  @param $content        The content value of the tag.
         *  @param $params         The parameters for the tag
         *  @param $node_object    A reference to the node object.
         *
         *  @returns    The html version of the tag.
         */
        function doTagSize( $action, $attributes, $content, $params, $node_object ) {
            if ( $action == 'validate' ) {
                return true;
            } else {
                if ( isset( $attributes['default'] ) && is_numeric( $attributes['default'] ) ) {
                    return '<font size="' . $attributes['default'] . '">' . $content . '</font>';
                } else {
                    return $content;
                }
            }
        }

        /**
         *  Function to handle the quote tag.
         *
         *  @param $action         The action that is executed.
         *  @param $attributes     The attributes specified in the tag
         *  @param $content        The content value of the tag.
         *  @param $params         The parameters for the tag
         *  @param $node_object    A reference to the node object.
         *
         *  @returns    The html version of the tag.
         */
        function doTagQuote( $action, $attributes, $content, $params, $node_object ) {
            if ( $action == 'validate' ) {
                return true;
            } else {
                if ( isset( $attributes['default'] ) && ! empty( $attributes['default'] ) ) {
                    $content = '<b>' . $attributes['default'] . ' ' . t('wrote') . "</b>\n" . $content;
                }
                return '<blockquote>' . $content . '</blockquote>';
            }
        }

        /**
         *  Function to handle the url tag.
         *
         *  @param $action         The action that is executed.
         *  @param $attributes     The attributes specified in the tag
         *  @param $content        The content value of the tag.
         *  @param $params         The parameters for the tag
         *  @param $node_object    A reference to the node object.
         *
         *  @returns    The html version of the tag.
         */
        function doTagUrl( $action, $attributes, $content, $params, $node_object ) {
            if ( $action == 'validate' ) {
                return true;
            } else {
                if ( isset( $attributes['default'] ) && ! empty( $attributes['default'] ) ) {
                    return '<a href="' . $attributes['default'] . '">' . $content . "</a>";
                } else {
                    return '<a href="' . $content . '">' . $content . "</a>";
                }
            }
        }

        /**
         *  Function to handle the email tag.
         *
         *  @param $action         The action that is executed.
         *  @param $attributes     The attributes specified in the tag
         *  @param $content        The content value of the tag.
         *  @param $params         The parameters for the tag
         *  @param $node_object    A reference to the node object.
         *
         *  @returns    The html version of the tag.
         */
        function doTagEmail( $action, $attributes, $content, $params, $node_object ) {
            if ( $action == 'validate' ) {
                return true;
            } else {
                if ( isset( $attributes['default'] ) && ! empty( $attributes['default'] ) ) {
                    return '<a href="mailto:' . $attributes['default'] . '">' . $content . "</a>";
                } else {
                    return '<a href="mailto:' . $content . '">' . $content . "</a>";
                }
            }
        }

        /**
         *  Function to handle the img tag.
         *
         *  @param $action         The action that is executed.
         *  @param $attributes     The attributes specified in the tag
         *  @param $content        The content value of the tag.
         *  @param $params         The parameters for the tag
         *  @param $node_object    A reference to the node object.
         *
         *  @returns    The html version of the tag.
         */
        function doTagImg( $action, $attributes, $content, $params, $node_object ) {
            if ( $action == 'validate' ) {
                return true;
            } else {
                if ( isset( $attributes['default'] ) && ! empty( $attributes['default'] ) ) {
                    $url = $attributes['default'];
                } else {
                    $url = $content;
                }
                return '<img src="' . preg_replace( '/\[[A-Za-z]+\]/i', '', $url ) . '" alt="' . $content . '" />';
            }
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
         *	@param $convertBr		(optional) Boolean to indicate that new lines should be converted to &lt;br/&gt; tags.
         *							This is turned on by default.
         *	@param $convertTags		(optional) Boolean to indicate that tags should be converted to HTML. This is turned
         *							on by default.
         *	@param $convertLinks	(optional) Boolean to indicate if links should be automatically highlighted or not.
         *							This is turned on by default.
         *  @param $baseUrl         (optional) If you give this a non-null value, it will convert all links to absolute
         *                          links using this url as the base url.
         *
         *	@returns	The HTML equivalent of the string with all the BBCode's converted according to the conversion table
         *				of this class.
         */
        function toHtml( $data, $convertBr=true, $convertTags=true, $convertLinks=true, $baseUrl=null ) {

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

            // Fix common problems
            $data = str_replace( "\r\n", "\n", $data );
            $data = str_replace( "\r", "\n", $data );
            $data = str_replace( "[/quote]\n\n", "[/quote]\n", $data );

            // Convert the tags
            $data = $this->parser->parse( $data );

            // Strip the leftover tags
            $data = trim( preg_replace( '/\[\/[a-z]+\]/i', '', $data ) );

            // Open http links in a new window
            $data = str_replace( ' href="http://', ' target="_blank" href="http://', $data );

            // Convert tags if needed
            if ( $convertBr === true ) {
                $data = nl2br( trim( $data ) );
            }

            // Convert smilies if needed
            $smilies_path = YDConfig::get( 'YD_BBCODE_SMILIES_DIR', '' );
            $smilies_url = YDConfig::get( 'YD_BBCODE_SMILIES_URL', '' );
            if ( is_dir( $smilies_path ) ) {
                foreach ( $this->smilies as $smilie=>$file ) {
                    $data = str_replace( $smilie, '<img src="' . $smilies_url . '/' . $file . '" width="15" height="15" />', $data );
                }
            }

            // Make links absolute if needed
            if ( ! is_null( $baseUrl ) ) {
                include_once( dirname( __FILE__ ) . '/../../YDClasses/YDUrl.php' );
                $data = YDUrl::makeLinksAbsolute( $data, $baseUrl );
            }

            // Return the data
            return $data;

        }

    }

?>
