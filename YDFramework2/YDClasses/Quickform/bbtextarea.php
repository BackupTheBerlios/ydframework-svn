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
    require_once( 'YDBBCode.php' );
    require_once( 'HTML/QuickForm/element.php' );

    /**
     *  This class defines a text area field that has support for a toolbar. 
     *  This widget can add BBCode style tags to the text in the textarea. It is
     *  created as a standard HTML_QuickForm element.
     *
     *  @todo
     *      Add a function called getValueAsHtml.
     *
     *  @todo
     *      The parser indicated in the class constructor should be the one that
     *      gets used instead of the default one. We should check if it's one of
     *      the right type. Put this in a function called _getParser().
     */
    class HTML_QuickForm_bbtextarea extends HTML_QuickForm_element {

        var $_value = null;

        /**
         *  This is the class constructor for the bbtextarea element.
         *
         *  @param $elementName  The name of the element.
         *  @param $elementLabel The label for the element.
         *  @param $attributes   The attributes for the element.
         */
        function HTML_QuickForm_bbtextarea(
            $elementName=null, $elementLabel=null, $attributes=null
        ) {

            // Initialize the parent element
            HTML_QuickForm_element::HTML_QuickForm_element(
                $elementName, $elementLabel, $attributes
            );

            // Allow persistant freeze
            $this->_persistantFreeze = true;

            // The short name of the element
            $this->_type = 'bbtextarea';

            // The list of modifiers
            $this->_modifiers = array(
                array(
                    'name' => 'b', 'label' => 'bold', 'text' => ''
                ),
                array(
                    'name' => 'i', 'label' => 'italic', 'text' => ''
                ),
                array(
                    'name' => 'u', 'label' => 'underline', 'text' => ''
                ),
                array(
                    'name' => 'quote', 'label' => 'quote', 'text' => ''
                ),
            );

            // The list of popup text elements
            $this->_simplepops = array(
                array(
                    'name' => 'url', 'question' => 'Enter the url:',
                    'default' => 'http://', 'label' => 'url'
                ),
            );

            // The list of popup windows elements
            //$this->_windowpops = array();

            // The YDBBCode parser to use
            $this->_parser = 'YDBBCode';

        }

        /**
         *  Function to set the name of the element.
         *
         *  @param $name The name of the element.
         */
        function setName( $name ) {
            $this->updateAttributes( array( 'name' => $name ) );
        }

        /**
         *  Function to get the name of the element.
         *
         *  @returns The name of the element.
         */
        function getName() {
            return $this->getAttribute( 'name' );
        }

        /**
         *  Function to set the value of the element.
         *
         *  @param $name The value of the element.
         */
        function setValue( $value ) {
            $this->_value = $value;
        }
        
        /**
         *  Function to get the value of the element.
         *
         *  @returns The value of the element.
         */
        function getValue() {
            return $this->_value;
        }

        /**
         *  This function will return the html value of the element.
         *
         *  @returns The HTML value of the elements.
         */
        function getValueAsHtml() {
            $parser = new YDBBCode();
            return $parser->toHtml( $this->getValue() );
        }

        /**
         *  Function to set the wrap attribute of the element.
         *
         *  @param $name The wrap attribute of the element.
         */
        function setWrap( $wrap ) {
            $this->updateAttributes( array( 'wrap' => $wrap ) );
        }

        /**
         *  Function to set the row count of the element.
         *
         *  @param $name The row count of the element.
         */
        function setRows( $rows ) {
            $this->updateAttributes( array( 'rows' => $rows ) );
        }

        /**
         *  Function to set the column count of the element.
         *
         *  @param $name The column count of the element.
         */
        function setCols( $cols ) {
            $this->updateAttributes( array( 'cols' => $cols ) );
        }

        /**
         *  This function will add a modifier.
         *
         *  @param $name  The name of the modifier.
         *  @param $label The label of the modifier.
         *  @param $text  (optional) The initial text for the modifier.
         */
        function addModifier( $name, $label, $text='' ) {

            // Create a simple array for this
            $attrib = array();
            $attrib['name'] = $name;
            $attrib['label'] = $label;
            $attrib['text'] = $text;

            // Add it to the list
            array_push( $this->_modifiers, $attrib );

        }

        /**
         *  This function will add a simple popup window.
         *
         *  @param $name     The name of the modifier.
         *  @param $label    The label of the simple popup.
         *  @param $question The question for in the popup window.
         *  @param $default  (optional) The default text for in the popup window.
         *  @param $text  (optional) The initial text for the modifier.
         */
        function addSimplePopup( $name, $label, $question, $default='', $text='' ) {

            // Create a simple array for this
            $attrib = array();
            $attrib['name'] = $name;
            $attrib['label'] = $label;
            $attrib['question'] = $question;
            $attrib['default'] = $default;
            $attrib['text'] = $text;

            // Add it to the list
            array_push( $this->_simplepops, $attrib );

        }

        /**
         *  This function will clear the list of modifiers.
         */
        function clearModifiers() {
            $this->_modifiers = array();
        }

        /**
         *  This function will clear the list of popup dialogs.
         */
        function clearSimplePopups() {
            $this->_simplepops = array();
        }

        // Function to get the HMTL
        function toHtml() {

            // If frozen, return the frozen value
            if ( $this->_flagFrozen ) {

                return $this->getFrozenHtml();

            // Return the actual element
            } else {

                // Check if there are any buttons defined
                $toolbarLength = sizeof( $this->_modifiers ) + sizeof( $this->_simplepops );

                // Empty out variable
                $out = '';

                // Only if something is in there
                if ( $toolbarLength > 0 ) {

                    // Create the JavaScript
                    $out .= '<script language="JavaScript">';
                    $out .= '    function doButton( name ) {';
                    foreach ( $this->_modifiers as $mod ) {
                        $out .= 'if ( name == \'' . addslashes( $mod['name'] ) . '\' ) {';
                        $out .= '    AddText (\'[' . addslashes( $mod['name'] ) . ']\',\'' . addslashes( $mod['text'] ) . '\',\'[/' . addslashes( $mod['name'] ) . ']\');';
                        $out .= '}';
                    }
                    foreach ( $this->_simplepops as $pop ) {
                        $out .= 'if ( name == \'' . addslashes( $pop['name'] ) . '\' ) {';
                        $out .= '    data = prompt( "' . addslashes( $pop['question'] ) . '", "' . addslashes( $pop['default'] ) . '" );';
                        $out .= '    if ( data == null ) return;';
                        $out .= '    AddText(\'[' . addslashes( $pop['name'] ) . '=\' + data + \']\',\'' . addslashes( $pop['text'] ) . '\',\'[/' . addslashes( $pop['name'] ) . ']\');';
                        $out .= '}';
                    }
                    $out .= '    }';
                    $out .= '    function AddText(startTag,defaultText,endTag) {';
                    $out .= '        if ( document.getElementById( \'' . addslashes( $this->getName() ) . '\' ).createTextRange ) {';
                    $out .= '            var text;';
                    $out .= '            document.getElementById( \'' . addslashes( $this->getName() ) . '\' ).focus(document.getElementById( \'' . addslashes( $this->getName() ) . '\' ).caretPos);';
                    $out .= '            document.getElementById( \'' . addslashes( $this->getName() ) . '\' ).caretPos = document.selection.createRange().duplicate();';
                    $out .= '            if(document.getElementById( \'' . addslashes( $this->getName() ) . '\' ).caretPos.text.length>0){';
                    $out .= '                document.getElementById( \'' . addslashes( $this->getName() ) . '\' ).caretPos.text = startTag + document.getElementById( \'' . addslashes( $this->getName() ) . '\' ).caretPos.text + endTag;';
                    $out .= '            } else {';
                    $out .= '                document.getElementById( \'' . addslashes( $this->getName() ) . '\' ).caretPos.text = startTag+defaultText+endTag;';
                    $out .= '            }';
                    $out .= '        } else {';
                    $out .= '            document.getElementById( \'' . addslashes( $this->getName() ) . '\' ).value += startTag+defaultText+endTag;';
                    $out .= '        }';
                    $out .= '    }';
                    $out .= '</script>';

                    // Add the form itself
                    $out .= '<table border="0" cellpadding="0" cellspacing="0" width="' . $this->getAttribute( 'width' ) . '">';
                    $out .= '<tr>';
                    $out .= '<td class="bbtoolbar">';
                    foreach ( $this->_modifiers as $mod ) {
                        $out .= '<a href="javascript:void( doButton(\'' . addslashes( $mod['name'] ) . '\') );">[ ' . $mod['label'] . ' ]</a> ';
                    }
                    foreach ( $this->_simplepops as $pop ) {
                        $out .= '<a href="javascript:void( doButton(\'' . addslashes( $pop['name'] ) . '\') );">[ ' . $pop['label'] . ' ]</a> ';
                    }
                    $out .= '</td>';
                    $out .= '</tr>';
                    $out .= '<tr>';
                    $out .= '<td><textarea id="' . $this->getName() . '" name="' . $this->getName() . '" class="bbtextarea" width="100%">' . $this->getValue() . '</textarea></td>';
                    $out .= '</tr>';
                    $out .= '</table>';
                } else {
                    $out .= '<textarea name="' . $this->getName() . '" class="bbtextarea" width="100%">' . $this->getValue() . '</textarea>';
                }

                // Return the HTML code
                return $out;

            }
        }

        // Function to get the frozen HTML
        function getFrozenHtml() {

            // Get the value
            $value = htmlspecialchars( $this->getValue() );

            // Convert the value to html
            $parser = new YDBBCode();
            $value = $parser->toHtml( $parser );

            // Check if we need to do wrapping or not
            if ( $this->getAttribute( 'wrap' ) == 'off' ) {
                $html = $this->_getTabs() . '<pre>' . $value."</pre>\n";
            } else {
                $html = nl2br($value)."\n";
            }

            // Return the frozen data
            return $html . $this->_getPersistantData();

        }

       /**
        *   Returns a 'safe' element's value
        *
        *   @param  array   array of submitted values to search
        *   @param  bool    whether to return the value as associative array
        *   @access public
        *   @return mixed
        */
        function exportValue( &$submitValues, $assoc = false ) {

            // Get the value
            $value = $this->_findValue( $submitValues );
            if ( null === $value ) {
                $value = $this->getValue();
            }

            // Convert the value to BBCode
            $parser = new YDBBCode();
            $value = $parser->toHtml( $parser );


            // Prepare the value and return it
            return $this->_prepareValue( $value, $assoc );

        }

    }

?>
