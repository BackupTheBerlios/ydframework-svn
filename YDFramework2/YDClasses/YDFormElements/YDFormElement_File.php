<?php

	// Yellow Duck Framework version 2.0
	// (c) copyright 2004 Pieter Claerhout, pieter@yellowduck.be

	if ( ! defined( 'YD_FW_NAME' ) ) {
		die( 'Yellow Duck Framework is not loaded.' );
	}

	require_once( 'YDFormElement.php' );

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
			return is_uploaded_file( $_FILES[ $this->_form . '_' . $this->_name ]['tmp_name'] );
		}

		/**
		 *	This function will move the uploaded file to the specified directory.
		 *
		 *	@param $dir	(optional) The directory to move the file to. Defaults to the current directory.
		 *
		 *	@returns	Boolean indicating if the move was succesful or not.
		 */
		function moveUpload( $dir='.' ) {
			$result = move_uploaded_file(
				$_FILES[ $this->_form . '_' . $this->_name ]['tmp_name'],
				realpath( $dir ) . '/' . $_FILES[ $this->_form . '_' . $this->_name ]['name']
			);
			@chmod( realpath( $dir ), 0777 );
			@chmod( realpath( $dir ) . '/' . $_FILES[ $this->_form . '_' . $this->_name ]['name'], 0666 );
			return $result;
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