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

	// The definitions for the regexp that parses the date strings
	define(
		'YD_DATE_PREG_1',
		'/^(\d{4})[-\/ ]?(\d{1,2})[-\/ ]?(\d{1,2})([T\s]?(\d{2}):?(\d{2}):?(\d{2})(\.\d+)?(Z|[\+\-]\d{2}:?\d{2})?)?$/i'
	);
	define(
		'YD_DATE_PREG_2',
		'/^(\d{1,2})[-\/ ]?(\d{1,2})[-\/ ]?(\d{4})([T\s]?(\d{2}):?(\d{2}):?(\d{2})(\.\d+)?(Z|[\+\-]\d{2}:?\d{2})?)?$/i'
	);
	define(
		'YD_DATE_PREG_3',
		'/^(\d{1,2})[-\/ ]?(\d{1,2})[-\/ ]?(\d{2})([T\s]?(\d{2}):?(\d{2}):?(\d{2})(\.\d+)?(Z|[\+\-]\d{2}:?\d{2})?)?$/i'
	);

	/**
	 *	This is the YDDate class which allows you to work with dates.
	 *
	 *	@warning
	 *		This class is not final yet and is most likely going to change. Be careful with using this module.
	 *
	 *	@todo
	 *		Move the date parsing to a static function in the YDDateUtil class.
	 *
	 *	@todo
	 *		Add date calculations to the YDDateUtil class.
	 *
	 *	@todo
	 *		Add a getter method to get a specific part of the date.
	 *
	 *	@todo
	 *		Add better handling of times. Either we make a class called YDTime and YDDateTime to do all this, or we put
	 *		everything in one class.
	 *
	 *	@todo
	 *		Add documentation about this class in the userguide.
	 *
	 *	@todo
	 *		Evaluate if it's easier to store the timestamp or the parsed date.
	 *
	 *	@todo
	 *		Improve error handling. If you try to instantiate the class with an invalid date, a fatal error should be
	 *		raised.
	 *
	 *	@todo
	 *		Any non numeric character should be allowed as a separator. The separator should be optional though.
	 */
	class YDDate extends YDBase {

		/**
		 *	This is the class constructor for the YDDate class. If no date is given, the current date and time will be
		 *	used.
		 *
		 *	@param	$date	(optional) The date to use for this object. The default is the current date and time.
		 */
		function YDDate( $date=null ) {

			// If no date, use the current date
			if ( is_null( $date ) ) {
				$date = time();
			}

			// Keep track of the date
			$this->date = null;

			// Parse the date
			$this->parseDate( $date );

		}

		/**
		 *	This function parse the date which can be specified as a string. If you run this function statically, you
		 *	can use it to check if a date is correct by checking the result from this function.
		 *
		 *	@param	$date	The date to parse. This can be both a string or a unix timestamp.
		 *
		 *	@returns	The parsed date on success, false otherwise.
		 */
		function parseDate( $date ) {

			// Parse the date
			if ( is_numeric( $date ) ) {
				return $this->parseDate( date( 'Y-m-d H:i:s', $date ) );
			} elseif ( preg_match( YD_DATE_PREG_1 , $date, $regs ) ) {
				$year		= $regs[1];
				$month		= $regs[2];
				$day		= $regs[3];
				$hour		= isset( $regs[5] ) ? $regs[5] : 0;
				$minute		= isset( $regs[6] ) ? $regs[6] : 0;
				$second		= isset( $regs[7] ) ? $regs[7] : 0;
				$partsecond	= isset( $regs[8] ) ? (float) $regs[8] : (float) 0;
			} elseif ( preg_match( YD_DATE_PREG_2 , $date, $regs ) ) {
				$year		= $regs[3];
				$month		= $regs[2];
				$day		= $regs[1];
				$hour		= isset( $regs[5] ) ? $regs[5] : 0;
				$minute		= isset( $regs[6] ) ? $regs[6] : 0;
				$second		= isset( $regs[7] ) ? $regs[7] : 0;
				$partsecond	= isset( $regs[8] ) ? (float) $regs[8] : (float) 0;
			} elseif ( preg_match( YD_DATE_PREG_3 , $date, $regs ) ) {
				$year		= substr( date( 'Y' ), 0, 2 ) . $regs[3];
				$month		= $regs[2];
				$day		= $regs[1];
				$hour		= isset( $regs[5] ) ? $regs[5] : 0;
				$minute		= isset( $regs[6] ) ? $regs[6] : 0;
				$second		= isset( $regs[7] ) ? $regs[7] : 0;
				$partsecond	= isset( $regs[8] ) ? (float) $regs[8] : (float) 0;
			}

			// Check if it's valid
			if ( ! @checkdate( $month, $day, $year ) ) {
				return false;
			}

			// Check the hour
			if ( intval( $hour ) < 0 || intval( $hour ) > 23 ) {
				return false;
			}

			// Check the minute
			if ( intval( $minute ) < 0 || intval( $minute ) > 59 ) {
				return false;
			}

			// Check the minute
			if ( intval( $second ) < 0 || intval( $second ) > 59 ) {
				return false;
			}

			// All checks passed
			$this->date['year'] = intval( $year );
			$this->date['month'] = intval( $month );
			$this->date['day'] = intval( $day );
			$this->date['hour'] = intval( $hour );
			$this->date['minute'] = intval( $minute );
			$this->date['second'] = intval( $second );
			$this->date['partsecond'] = intval( $partsecond );

			// Return success
			return ( is_null( $this->date ) ) ? false : true;

		}

		/**
		 *	This function returns the timestamp for the current date object.
		 *
		 *	@returns	Integer containing the unix timestamp.
		 */
		function getTimestamp() {
			return mktime(
				$this->date['hour'], $this->date['minute'], $this->date['second'],
				$this->date['month'], $this->date['day'], $this->date['year']
			);
		}

		/**
		 *	Returns a formatted date formatted using the date function.
		 *
		 *	@param	$fmt	(optional) The format to use. Defaults to 'Y-m-d H:i:s'.
		 *
		 *	@returns	The formatted date.
		 */
		function getFormatDate( $fmt='Y-m-d H:i:s' ) {
			return date( $fmt, $this->getTimestamp() );
		}

		/**
		 *	Returns a formatted date formatted using the strftime function.
		 *
		 *	@param	$fmt	(optional) The format to use. Defaults to '%Y-%m-%d %H:%M:%S'.
		 *
		 *	@returns	The formatted date.
		 */
		function getFormatStrftime( $fmt='%Y-%m-%d %H:%M:%S' ) {
			return strftime( $fmt, $this->getTimestamp() );
		}

		/**
		 *	Checks if the date is in the future or not.
		 *
		 *	@returns	Boolean indicating if the date is in the future or not.
		 */
		function isFuture() {
			return ( $this->getTimeStamp() > time() ) ? true : false;
		}

		/**
		 *	Checks if the date is in the past or not.
		 *
		 *	@returns	Boolean indicating if the date is in the past or not.
		 */
		function isPast() {
			return ( $this->getTimeStamp() < time() ) ? true : false;
		}

		/**
		 *	Returns the day of week as a number where monday is 1 and sunday is 7.
		 *
		 *	@returns	The day of week as a number.
		 */
		function getDayOfWeek() {
			$date = getdate( $this->getTimeStamp() );
			return ( $date['wday'] == 0 ) ? 7 : $date['wday'];
		}

		/**
		 *	Returns the number of the next day of week as a number where monday is 1 and sunday is 7.
		 *
		 *	@returns	The next day of week as a number.
		 */
		function getNextWeekDay() {
			if ( $this->getDayOfWeek()+1 > 7 ) {
				return 1;
			} else {
				return $this->getDayOfWeek()+1;
			}
		}

		/**
		 *	Returns the number of the previous day of week as a number where monday is 1 and sunday is 7.
		 *
		 *	@returns	The previous day of week as a number.
		 */
		function getPreviousWeekDay() {
			if ( $this->getDayOfWeek()-1 < 1 ) {
				return 7;
			} else {
				return $this->getDayOfWeek()-1;
			}
		}

		/**
		 *	Returns a YDDate object pointing to the next day.
		 *
		 *	@returns	A YDDate object pointing to the next day.
		 */
		function getNextDay() {
			return new YDDate( $this->getTimeStamp() + 86400 );
		}

		/**
		 *	Returns a YDDate object pointing to the previous day.
		 *
		 *	@returns	A YDDate object pointing to the previous day.
		 */
		function getPreviousDay() {
			return new YDDate( $this->getTimeStamp() - 86400 );
		}

	}

?>
