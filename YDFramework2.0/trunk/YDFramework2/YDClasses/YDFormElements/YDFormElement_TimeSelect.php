<?php

	// Yellow Duck Framework version 2.0
	// (c) copyright 2004 Pieter Claerhout, pieter@yellowduck.be

	if ( ! defined( 'YD_FW_NAME' ) ) {
		die( 'Yellow Duck Framework is not loaded.' );
	}

	require_once( 'YDForm.php' );
	require_once( 'YDFormElements/YDFormElement_Select.php' );

	/**
	 *	This is the class that define a time select form element.
	 */
	class YDFormElement_TimeSelect extends YDFormElement {

		/**
		 *	This is the class constructor for the YDFormElement_TimeSelect class.
		 *
		 *	@param $form		The name of the form to which this element is connected.
		 *	@param $name		The name of the form element.
		 *	@param $label		(optional) The label for the form element.
		 *	@param $attributes	(optional) The attributes for the form element.
		 *	@param $options		(optional) The options for the elment.
		 */
		function YDFormElement_TimeSelect( $form, $name, $label='', $attributes=array(), $options=array() ) {

			// Initialize the parent
			$this->YDFormElement( $form, $name, $label, $attributes, $options );

			// The default value is an array
			$this->_value = array();

			// Set the type
			$this->_type = 'timeselect';

			// Fill in the default date
			if ( $this->_value == array() ) {
				$now = getdate();
				$this->_value['hours'] = $now['hours'];
				$this->_value['minutes'] = $now['minutes'];
			}

			// Get the names of the hours
			$hours = array();
			for ( $i = 0; $i <= 23; $i++ ) {
				$hours[$i] = ( strlen( $i ) == 1 ) ? '0' . $i : $i;
			}

			// Get the names of the minutes
			$minutes = array();
			for ( $i = 0; $i <= 59; $i++ ) {
				$minutes[$i] = ( strlen( $i ) == 1 ) ? '0' . $i : $i;
			}

			// Add the different elements
			$this->hours = new YDFormElement_Select( 
				$this->_form, $this->_name . '[hours]', '', $this->_attributes, $hours
			);
			$this->minutes = new YDFormElement_Select(
				$this->_form, $this->_name . '[minutes]', '', $this->_attributes, $minutes
			);

		}

		/**
		 *	This will return a unix timestamp of the selected time.
		 *
		 *	@param $format	(optional) The format if you want to get a formatted date/time. If null, an integer is
		 *					returned. The syntax is the one as used by the strftime function.
		 *
		 *	@returns	An integer with the current date/time stamp.
		 */
		function getTimeStamp( $format=null ) {
			$tstamp = mktime( $this->_value['hours'], $this->_value['minutes'], 0, 1, 1, 1970 );
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

			// Fix up the value
			if ( ! is_array( $this->_value ) ) {
				if ( is_int( $this->_value ) ) {
					$value = getdate( $this->_value );
				} else {
					$value = getdate( strtotime( $this->_value ) );
				}
				$this->_value = array();
				$this->_value['hours'] = $value['hours'];
				$this->_value['minutes'] = $value['minutes'];
			}

			// Convert to HTML
			$this->hours->_value = isset( $this->_value['hours'] ) ? $this->_value['hours'] : '';
			$this->minutes->_value = isset( $this->_value['minutes'] ) ? $this->_value['minutes'] : '';
			return $this->hours->toHtml() . ' : ' . $this->minutes->toHtml();

		}

	}

?>