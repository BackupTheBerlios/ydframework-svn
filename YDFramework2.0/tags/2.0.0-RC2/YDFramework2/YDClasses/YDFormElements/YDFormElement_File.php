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
    include_once( dirname( __FILE__ ) . '/../YDForm.php');

    /**
     *	This is the class that define a file upload form element.
     */
    class YDFormElement_File extends YDFormElement {

        var $fileo;

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
            if ( ! isset( $_FILES[ $this->_form . '_' . $this->_name ]['tmp_name'] ) ) {
                return false;
            }
            return is_uploaded_file( $_FILES[ $this->_form . '_' . $this->_name ]['tmp_name'] )
                    && ( filesize( $_FILES[ $this->_form . '_' . $this->_name ]['tmp_name'] ) > 0 );
        }
        
        /**
         *      This function returns a boolean indicating if the element value was
         *      modified from it's default value.
         *
         *      @returns        Boolean indicating if the element was modified.
         */
        function isModified() {
            if ( $this->isUploaded() ) {
                return true;
            }
            return false;
        }

        /**
         *  This function will move the uploaded file to the specified directory.
         *
         *  @param $dir (optional) The directory to move the file to. Defaults to the current directory.
         *  @param $fname (optional) File name to use. Prevents e.g. the file hits the FS with unwanted chars in it
         *  @param $retainExt (optional) Retain file name extension or not
         *  @returns  Boolean indicating if the move was succesful or not.
         */
        function moveUpload( $dir='.', $fname='', $retainExt=false ) {
            if ( filesize( $_FILES[ $this->_form . '_' . $this->_name ]['tmp_name'] ) == 0 ) {
                return false;
            }

            // Fetch extension if it is to be retained, set empty if otherwise
            $retainExt AND $ext = '.' . YDPath::getExtension( $_FILES[ $this->_form . '_' . $this->_name ]['name'] ) OR $ext='';

            // Create (new) file name
            $fname = ($fname=='' ? $_FILES[ $this->_form . '_' . $this->_name ]['name'] : $fname . $ext );

            // Compile path
            $path = realpath( $dir ) . '/' . $fname;

            // Move file to temp location
            // plz point out in the docs this dir is _temporary_, as it is automatically made 0777!
            $result = move_uploaded_file(
                $_FILES[ $this->_form . '_' . $this->_name ]['tmp_name'],
                $path
            );

            @chmod( realpath( $dir ), 0700 );
            @chmod( $path, 0700 );

            // Provide an interface to some more useful information on the file
            $this->fileo = new YDFSFile( $path );

            return $result;
        }

        /**
         *  This function will return the actual size of the uploaded file.
         *
         *  @returns  Size of the uploaded file in Bytes
         */
        function getSize() {
            return is_object($this->fileo) ? $this->fileo->getSize() : '';
        }

        /**
         *  This function will return the actual path of the uploaded file.
         *
         *  @returns  Path of the uploaded file.
         */
        function getPath() {
            return is_object($this->fileo) ? $this->fileo->getPath() : '';
        }

        /**
         *	This function will return the actual name of the uploaded file.
         *  In case there is no file object, fetch the name from $_FILES
         *
         *	@returns	Name of the uploaded file.
         */
        function getBasename() {
            return is_object($this->fileo) ? $this->fileo->getBasename() : $_FILES[ $this->_form . '_' . $this->_name ]['name'];
        }

        /**
         *  This function will return the actual extension of the uploaded file.
         *
         *  @returns  Extension of the uploaded file.
         */
        function getExtension() {
          return is_object($this->fileo) ? $this->fileo->getExtension() : '';
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
            return '<input' . YDForm::_convertToHtmlAttrib( $attribs ) . ' />';

        }

    }

?>