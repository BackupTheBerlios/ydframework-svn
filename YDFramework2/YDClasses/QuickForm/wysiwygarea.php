<?php

    /*
     *  Yellow Duck Framework version 2.0
     *  (c) copyright 2004 Pieter Claerhout, pieter@yellowduck.be
     */

    require_once( 'HTML/QuickForm/element.php' );

    require_once( 'fckeditor.php' ) ;

    class HTML_QuickForm_wysiwygarea extends HTML_QuickForm_element {

        var $_value = null;

        function HTML_QuickForm_wysiwygarea(
            $elementName=null, $elementLabel=null, $attributes=null
        ) {
            HTML_QuickForm_element::HTML_QuickForm_element(
                $elementName, $elementLabel, $attributes
            );
            $this->_persistantFreeze = true;
            $this->_type = 'wysiwygarea';
        }

        function setName( $name ) {
            $this->updateAttributes( array( 'name'=>$name ) );
        }

        function getName() {
            return $this->getAttribute( 'name' );
        }

        function setValue( $value ) {
            $this->_value = $value;
        }
    
        function getValue() {
            return $this->_value;
        }

        function toHtml() {
            if ( $this->_flagFrozen ) {
                return $this->getFrozenHtml();
            } else {

                ob_start();
                $oFCKeditor = new FCKeditor();
                $oFCKeditor->ToolbarSet = 'Basic';
                $oFCKeditor->Value = $this->getValue();
                $oFCKeditor->CreateFCKeditor( $this->getName(), 300, 120 );
                $result = ob_get_contents();
                ob_end_clean();
                
                return $result;

            }
        }
        
        function getFrozenHtml() {
            $value = htmlspecialchars( $this->getValue() );
            if ( $this->getAttribute( 'wrap' ) == 'off' ) {
                $html = $this->_getTabs() . '<pre>' . $value."</pre>\n";
            } else {
                $html = nl2br( $value )."\n";
            }
            return $html . $this->_getPersistantData();
        }

    }

?>
