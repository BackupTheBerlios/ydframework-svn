<?php

	// Yellow Duck Framework version 2.0
	// (c) copyright 2004 Pieter Claerhout, pieter@yellowduck.be

	if ( ! defined( 'YD_FW_NAME' ) ) {
		die( 'Yellow Duck Framework is not loaded.' );
	}

	require_once( 'YDForm.php' );
	require_once( 'YDFormElements/YDFormElement_Select.php' );

	/**
	 *	This is the class that define a text form element.
	 */
	class YDFormElement_DateSelect extends YDFormElement {

		/**
		 *	This is the class constructor for the YDFormElement_Text class.
		 *
		 *	@param $form		The name of the form to which this element is connected.
		 *	@param $name		The name of the form element.
		 *	@param $label		(optional) The label for the form element.
		 *	@param $attributes	(optional) The attributes for the form element.
		 *	@param $options		(optional) The options for the elment.
		 */
		function YDFormElement_DateSelect( $form, $name, $label='', $attributes=array(), $options=array() ) {

			// Initialize the parent
			$this->YDFormElement( $form, $name, $label, $attributes, $options );

			// The default value is an array
			$this->_value = array();

			// Set the type
			$this->_type = 'dateselect';

			// Fill in the default date
			if ( $this->_value == array() ) {
				$now = getdate();
				$this->_value['d'] = $now['mday'];
				$this->_value['m'] = $now['mon'];
				$this->_value['y'] = $now['year'];
			}

		}

		/**
		 *	This will return a unix timestamp of the selected date/time.
		 *
		 *	@param $format	(optional) The format if you want to get a formatted date/time. If null, an integer is
		 *					returned. The syntax is the one as used by the strftime function.
		 *
		 *	@returns	An integer with the current date/time stamp.
		 */
		function getTimeStamp( $format=null ) {
			$tstamp = mktime( 0, 0, 0, $this->_value['m'], $this->_value['d'], $this->_value['y'] );
			if ( is_null( $format ) ) {
				return $tstamp;
			} else {
				return strftime( $format, $tstamp );
			}
		}

		/**
		 *	This function will return the element as HTML.
		 *
		 *	@returns	The form element as HTML text.
		 */
		function toHtml() {

			// Start with an empty out variable
			$out = '';

			// Get the names of the days
			$days = array();
			for ( $i = 1; $i <= 31; $i++ ) {
				$days[$i] = $i;
			}

			// Add the list of days
			$item = new YDFormElement_Select( $this->_form, $this->_name . '[d]', '', $this->_attributes, $days );
			$item->_value = isset( $this->_value['d'] ) ? $this->_value['d'] : '';
			$out .= $item->toHtml() . ' ';

			// Get the names of the months
			$months = array();
			for ( $i = 1; $i <= 12; $i++ ) {
				$months[$i] = strftime( '%B', mktime( 0, 0, 0, $i, 1, 2000 ) );
			}

			// Add the list of months
			$item = new YDFormElement_Select( $this->_form, $this->_name . '[m]', '', $this->_attributes, $months );
			$item->_value = isset( $this->_value['m'] ) ? $this->_value['m'] : '';
			$out .= $item->toHtml() . ' ';

			// Get the names of the years
			$now = getdate();
			$years = array();
			for ( $i = $now['year'] - 5 ; $i <= $now['year'] + 5; $i++ ) {
				$years[$i] = $i;
			}

			// Add the list of days
			$item = new YDFormElement_Select( $this->_form, $this->_name . '[y]', '', $this->_attributes, $years );
			$item->_value = isset( $this->_value['y'] ) ? $this->_value['y'] : '';
			$out .= $item->toHtml() . ' ';

			// Get the HTML
			return $out;

		}

	}

?>