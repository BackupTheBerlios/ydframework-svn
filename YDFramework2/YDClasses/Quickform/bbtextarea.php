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

            // The list of buttons
            $this->_buttons = array();

            // Add the default buttons
            $this->addModifier( 'b', 'bold' );
            $this->addModifier( 'i', 'italic' );
            $this->addModifier( 'u', 'underline' );
            $this->addSimplePopup( 'url', 'url', 'Enter the url:', 'http://' );

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
            $attrib['type'] = 'modifier';
            $attrib['label'] = $label;
            $attrib['text'] = $text;

            // Add it to the list
            array_push( $this->_buttons, $attrib );
        
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
        function addSimplePopup(
            $name, $label, $question, $default='', $text='' 
        ) {

            // Create a simple array for this
            $attrib = array();
            $attrib['name'] = $name;
            $attrib['type'] = 'simplepopup';
            $attrib['label'] = $label;
            $attrib['question'] = $question;
            $attrib['default'] = $default;
            $attrib['text'] = $text;

            // Add it to the list
            array_push( $this->_buttons, $attrib );

        }

        /**
         *  This function will add a popup window. This is a popup window that
         *  will point to a URL. This window can do whatever it wants and can
         *  change values in the main window.
         *
         *  @param $url      The url for the popup window.
         *  @param $label    The label of the popup window.
         *  @param $name     (optional) The JavaScript name of the popup window.
         *  @param $params   (optional) The JavaScript parameters for the popup
         *                   window.
         */
        function addPopupWindow( 
            $url, $label, $name='', $params='' 
        ) {

            // Create a simple array for this
            $attrib = array();
            $attrib['url'] = $url;
            $attrib['type'] = 'popupwindow';
            $attrib['label'] = $label;
            $attrib['name'] = $name;
            if ( empty( $params ) ) {
                $attrib['params'] = 'width=500,height=320,location=0,menubar=0,resizable=1,scrollbars=1,status=1,titlebar=1';
            } else {
                $attrib['params'] = $params;
            }

            // Add it to the list
            //array_push( $this->_popupWindows, $attrib );
            array_push( $this->_buttons, $attrib );

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

                // Empty out variable
                $out = '';

                // Only if something is in there
                if ( sizeof( $this->_buttons ) > 0 ) {

                    // Add the AddText function if needed
                    if ( ! defined( 'YD_BBTA_MAINSCRIPT' ) ) {

                        // Create the AddText script
                        $out .= '<script language="JavaScript">';
                        $out .= '    function AddText( element, startTag, defaultText, endTag ) {';
                        $out .= '        objElement = document.getElementById( element );';
                        $out .= '        if ( objElement.createTextRange ) {';
                        $out .= '            var text;';
                        $out .= '            objElement.focus( objElement.caretPos);';
                        $out .= '            objElement.caretPos = document.selection.createRange().duplicate();';
                        $out .= '            if ( objElement.caretPos.text.length > 0 ) {' . "\n";
                        $out .= '                objElement.caretPos.text = startTag + objElement.caretPos.text + endTag;';
                        $out .= '            } else {';
                        $out .= '                objElement.caretPos.text = startTag + defaultText + endTag;';
                        $out .= '            }';
                        $out .= '        } else {';
                        $out .= '            objElement.value += startTag + defaultText + endTag;';
                        $out .= '        }';
                        $out .= '    }';
                        $out .= '    function openWin( url, name, opts ) {';
                        $out .= '        window.open( url, name, opts );';
                        $out .= '    }';
                        $out .= '</script>';

                        // Define that we added it
                        define( 'YD_BBTA_MAINSCRIPT', 1 );

                    }

                    // Create the JavaScript
                    $out .= '<script language="JavaScript">';
                    $out .= '    function doButton( element, name ) {';
                    foreach ( $this->_buttons as $button ) {
                        if ( $button['type'] == 'modifier' )  {
                            $out .= 'if ( name == \'' . addslashes( $button['name'] ) . '\' ) {';
                            $out .= '    AddText ( element, \'[' . addslashes( $button['name'] ) . ']\',\'' . addslashes( $button['text'] ) . '\',\'[/' . addslashes( $button['name'] ) . ']\');';
                            $out .= '}';
                        }
                        if ( $button['type'] == 'simplepopup' )  {
                            $out .= 'if ( name == \'' . addslashes( $button['name'] ) . '\' ) {';
                            $out .= '    data = prompt( "' . addslashes( $button['question'] ) . '", "' . addslashes( $button['default'] ) . '" );';
                            $out .= '    if ( data == null ) return;';
                            $out .= '    AddText( element, \'[' . addslashes( $button['name'] ) . '=\' + data + \']\',\'' . addslashes( $button['text'] ) . '\',\'[/' . addslashes( $button['name'] ) . ']\');';
                            $out .= '}';
                        }
                    }
                    $out .= '    }';
                    $out .= '</script>';

                    // Add the form itself
                    $out .= '<table border="0" cellpadding="0" cellspacing="0" width="' . $this->getAttribute( 'width' ) . '">';
                    $out .= '<tr>';
                    $out .= '<td class="bbtoolbar">';
                    foreach ( $this->_buttons as $button ) {
                        if ( $button['type'] == 'modifier' )  {
                            $out .= '<a href="javascript:void( doButton( \'' . addslashes( $this->getName() ) . '\', \'' . addslashes( $button['name'] ) . '\') );">[ ' . $button['label'] . ' ]</a> ';
                        }
                        if ( $button['type'] == 'simplepopup' )  {
                            $out .= '<a href="javascript:void( doButton( \'' . addslashes( $this->getName() ) . '\', \'' . addslashes( $button['name'] ) . '\') );">[ ' . $button['label'] . ' ]</a> ';
                        }
                        if ( $button['type'] == 'popupwindow' )  {
                            $out .= '<a href="javascript:void( openWin( \'' . addslashes( $button['url'] ) . '\', \'' . addslashes( $button['name'] ) . '\', \'' . addslashes( $button['params'] ) . '\' ) );">[ ' . $button['label'] . ' ]</a> ';
                        }
                    }
                    $out .= '</td>';
                    $out .= '</tr>';
                    $out .= '<tr>';
                    $out .= '<td><textarea id="' . $this->getName() . '" name="' . $this->getName() . '" class="bbtextarea" width="100%">' . $this->getValue() . '</textarea></td>';
                    $out .= '</tr>';
                    $out .= '</table>';
                } else {
                    $out .= '<textarea name="' . $this->getName() . '" class="bbtextarea" width="' . $this->getAttribute( 'width' ) . '">' . $this->getValue() . '</textarea>';
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
