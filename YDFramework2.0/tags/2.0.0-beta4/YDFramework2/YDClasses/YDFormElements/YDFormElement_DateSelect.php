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

	if ( ! defined( 'YD_FW_NAME' ) ) {
		die( 'Yellow Duck Framework is not loaded.' );
	}

	YDInclude( 'YDForm.php' );
	YDInclude( 'YDFormElements/YDFormElement_Select.php' );

	/**
	 *	This is the class that define a date select form element.
	 */
	class YDFormElement_DateSelect extends YDFormElement {

		/**
		 *	This is the class constructor for the YDFormElement_DateSelect class.
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
				$this->_value['day'] = $now['mday'];
				$this->_value['month'] = $now['mon'];
				$this->_value['year'] = $now['year'];
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
			$tstamp = mktime( 0, 0, 0, $this->_value['month'], $this->_value['day'], $this->_value['year'] );
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
				$this->_value['day'] = $value['mday'];
				$this->_value['month'] = $value['mon'];
				$this->_value['year'] = $value['year'];
			}

			// Convert to HTML
			$this->day->_value = isset( $this->_value['day'] ) ? $this->_value['day'] : '';
			$this->month->_value = isset( $this->_value['month'] ) ? $this->_value['month'] : '';
			$this->year->_value = isset( $this->_value['year'] ) ? $this->_value['year'] : '';
			return $this->day->toHtml() . ' / ' . $this->month->toHtml() . ' / ' . $this->year->toHtml();

		}

	}

?>