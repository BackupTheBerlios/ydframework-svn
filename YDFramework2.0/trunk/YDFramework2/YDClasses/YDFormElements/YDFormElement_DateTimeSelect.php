<?php

	// Yellow Duck Framework version 2.0
	// (c) copyright 2004 Pieter Claerhout, pieter@yellowduck.be

	if ( ! defined( 'YD_FW_NAME' ) ) {
		die( 'Yellow Duck Framework is not loaded.' );
	}

	require_once( 'YDForm.php' );
	require_once( 'YDFormElements/YDFormElement_Select.php' );

	/**
	 *	This is the class that define a datetime select form element.
	 */
	class YDFormElement_DateTimeSelect extends YDFormElement {

		/**
		 *	This is the class constructor for the YDFormElement_DateTimeSelect class.
		 *
		 *	@param $form		The name of the form to which this element is connected.
		 *	@param $name		The name of the form element.
		 *	@param $label		(optional) The label for the form element.
		 *	@param $attributes	(optional) The attributes for the form element.
		 *	@param $options		(optional) The options for the elment.
		 */
		function YDFormElement_DateTimeSelect( $form, $name, $label='', $attributes=array(), $options=array() ) {

			// Initialize the parent
			$this->YDFormElement( $form, $name, $label, $attributes, $options );

			// The default value is an array
			$this->_value = array();

			// Set the type
			$this->_type = 'datetimeselect';

			// Fill in the default date
			if ( $this->_value == array() ) {
				$now = getdate();
				$this->_value['day'] = $now['mday'];
				$this->_value['month'] = $now['mon'];
				$this->_value['year'] = $now['year'];
				$this->_value['hours'] = $now['hours'];
				$this->_value['minutes'] = $now['minutes'];
			}

			// Get the names of the days
			$days = array();
			for ( $i = 1; $i <= 31; $i++ ) {
				$days[$i] = ( strlen( $i ) == 1 ) ? '0' . $i : $i;
			}

			// Get the names of the months
			$months = array();
			for ( $i = 1; $i <= 12; $i++ ) {
				$months[$i] = strftime( '%B', mktime( 0, 0, 0, $i, 1, 2000 ) );
			}

			// Get the names of the years
			$now = getdate();
			$years = array();
			for ( $i = $now['year'] - 5 ; $i <= $now['year'] + 5; $i++ ) {
				$years[$i] = $i;
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
			$this->day = new YDFormElement_Select(
				$this->_form, $this->_name . '[day]', '', $this->_attributes, $days
			);
			$this->month = new YDFormElement_Select(
				$this->_form, $this->_name . '[month]', '', $this->_attributes, $months
			);
			$this->year = new YDFormElement_Select(
				$this->_form, $this->_name . '[year]', '', $this->_attributes, $years
			);
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
			$tstamp = mktime(
				$this->_value['hours'], $this->_value['minutes'], 0,
				$this->_value['month'], $this->_value['day'], $this->_value['year']
			);
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
			$this->day->_value = isset( $this->_value['day'] ) ? $this->_value['day'] : '';
			$this->month->_value = isset( $this->_value['month'] ) ? $this->_value['month'] : '';
			$this->year->_value = isset( $this->_value['year'] ) ? $this->_value['year'] : '';
			$this->hours->_value = isset( $this->_value['hours'] ) ? $this->_value['hours'] : '';
			$this->minutes->_value = isset( $this->_value['minutes'] ) ? $this->_value['minutes'] : '';
			return $this->day->toHtml() . ' / ' . $this->month->toHtml() . ' / ' . $this->year->toHtml() . ' ' . $this->hours->toHtml() . ' : ' . $this->minutes->toHtml();
		}

	}

?>