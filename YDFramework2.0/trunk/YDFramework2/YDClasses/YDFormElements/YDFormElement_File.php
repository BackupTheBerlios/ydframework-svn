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

    // Includes
    YDInclude( 'YDForm.php' );

    /**
     *	This is the class that define a file upload form element.
     */
    class YDFormElement_File extends YDFormElement {

        /**
         *	This is the class constructor for the YDFormElement_File class.
         *
         *	@param $form		The name of the form to which this element is connected.
         *	@param $name		The name of the form element.
         *	@param $label		(optional) The label for the form element.
         *	@param $attributes	(optional) The attributes for the form element.
         *	@param $options		(optional) The options for the elment.
         */
        function YDFormElement_File( $form, $name, $label='', $attributes=array(), $options=array() ) {

            // Initialize the parent
            $this->YDFormElement( $form, $name, $label, $attributes, $options );

            // Set the type
            $this->_type = 'file';

        }

        /**
         *	This function will tell if the file was actually uploaded or not.
         *
         *	@returns	Boolean indicating if the file was uploaded or not.
         */
        function isUploaded() {
            return is_uploaded_file( $_FILES[ $this->_form . '_' . $this->_name ]['tmp_name'] )
                    && ( filesize( $_FILES[ $this->_form . '_' . $this->_name ]['tmp_name'] ) > 0 );
        }

        /**
         *	This function will move the uploaded file to the specified directory.
         *
         *	@param $dir	(optional) The directory to move the file to. Defaults to the current directory.
         *
         *	@returns	Boolean indicating if the move was succesful or not.
         */
        function moveUpload( $dir='.' ) {
            if ( filesize( $_FILES[ $this->_form . '_' . $this->_name ]['tmp_name'] ) == 0 ) {
                return false;
            }
            $result = move_uploaded_file(
                $_FILES[ $this->_form . '_' . $this->_name ]['tmp_name'],
                realpath( $dir ) . '/' . $_FILES[ $this->_form . '_' . $this->_name ]['name']
            );
            @chmod( realpath( $dir ), 0777 );
            @chmod( realpath( $dir ) . '/' . $_FILES[ $this->_form . '_' . $this->_name ]['name'], 0666 );
            return $result;
        }

        /**
         *	This function will return the actual name of the uploaded file.
         *
         *	@returns	Name of the uploaded file.
         */
        function getName() {
            return basename( $_FILES[ $this->_form . '_' . $this->_name ]['name'] );
        }

        /**
         *	This function will return the element as HTML.
         *
         *	@returns	The form element as HTML text.
         */
        function toHtml() {

            // Create the list of attributes
            $attribs = array( 'type' => $this->_type, 'name' => $this->_form . '_' . $this->_name );
            $attribs = array_merge( $this->_attributes, $attribs );

            // Get the HTML
            return '<input' . YDForm::_convertToHtmlAttrib( $attribs ) . '>';

        }

    }

?>