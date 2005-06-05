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
    YDInclude( YD_DIR_3RDP . YD_DIRDELIM . 'dateclass' . YD_DIRDELIM . 'dateclass.php' );

    /**
     *  This class defines a YDDate object and is a wrapper of the DateClass class
     *  from Steve Powell.
     */
    class YDDate extends DateClass {

        var $format;

        /**
         *  The constructor.
         *
         *  @param $datetime  (optional) A YDDate object, timestamp or string of a date.
         *                    If empty, the current time.
         */
        function YDDate( $datetime='' ) {
            $this->DateClass( $datetime );
            $this->setFormat();
        }

        /**
         *  This function sets the date of the object.
         *
         *  @param $datetime  (optional) A YDDate object, timestamp or string of a date.
         *
         *  @returns  This object.
         */
        function setDate( $datetime='' ) {
            
            $oldStamp = $this->TimeStamp();
            
            if ( $datetime == '' ) {
                $this->_Date = time();
            } else {
                $this->_Date = $this->_parseDate( $datetime );
            }
            
            if ( $oldStamp != $this->TimeStamp() ) {
                $this->_datePart = getdate( $this->TimeStamp() );
            }
            return $this;
        }

        /**
         *  This function adds a number of days to the current date.
         *
         *  @param  $days  (optional) Number of days. Default: 1.
         *  @param  $skip  (optional) Skips weekends. Default: false.
         *
         *  @returns       A string of the datetime.
         */
        function addDay( $value=1, $skip=false ) {
            
            if ( $skip ) {
                
                $datetime1 = new DateClass( $this->getTimeStamp() );
                $datetime2 = $datetime1;
                $datetime2->Add( 'day', $value );
                
                $span = new DateSpanClass( $datetime1, $datetime2 );
                $weekdays = $span->WeekDays();
                
                $value += ( $value - $weekdays );
            }
            
            $this->Add( 'day', $value );
            return $this->toString();
        }

        /**
         *  This function adds a number of months to the current date.
         *
         *  @param  $months  (optional) Number of months. Default: 1.
         *
         *  @returns         A string of the datetime.
         */
        function addMonth( $value=1 ) {
            $this->Add( 'month', $value );
            return $this->toString();
        }

        /**
         *  This function adds a number of years to the current date.
         *
         *  @param  $years  (optional) Number of years. Default: 1.
         *
         *  @returns         A string of the datetime.
         */
        function addYear( $value=1 ) {
            $this->Add( 'year', $value );
            return $this->toString();
        }

        /**
         *  This function adds a number of hours to the current date.
         *
         *  @param  $hours  (optional) Number of hours. Default: 1.
         *
         *  @returns         A string of the datetime.
         */
        function addHour( $value=1 ) {
            $this->Add( 'hour', $value );
            return $this->toString();
        }

        /**
         *  This function adds a number of minutes to the current date.
         *
         *  @param  $minutes  (optional) Number of minutes. Default: 1.
         *
         *  @returns         A string of the datetime.
         */
        function addMinute( $value=1 ) {
            $this->Add( 'minute', $value );
            return $this->toString();
        }

        /**
         *  This function adds a number of seconds to the current date.
         *
         *  @param  $seconds  (optional) Number of seconds. Default: 1.
         *
         *  @returns         A string of the datetime.
         */
        function addSecond( $value=1 ) {
            $this->Add( 'second', $value );
            return $this->toString();
        }

        /**
         *  This function returns the timestamp of the datetime.
         *
         *  @param $datetime  (optional) A YDDate object, timestamp or string of a date.
         *
         *  @returns  The timestamp of the datetime.
         */
        function getTimeStamp( $datetime='' ) {
            return $this->TimeStamp( $datetime );
        }

        /**
         *  This function returns a MySQL timestamp string of the datetime - YYYYMMDDHHMMSS.
         *
         *  @param $datetime  (optional) A YDDate object, timestamp or string of a date.
         *
         *  @returns  A timestamp string of the datetime.
         */
        function getTimeStampString( $datetime='' ) {
            if ( $datetime ) { $this->setDate( $datetime ); }
            
            return $this->getPart( 'year' ) .
                   $this->getPart( 'month_with_zero' ) .
                   $this->getPart( 'day_with_zero' ) .
                   $this->getPart( 'hours_with_zero' ) .
                   $this->getPart( 'minutes_with_zero' ) .
                   $this->getPart( 'seconds_with_zero' );
        }

        /**
         *  This function returns a MySQL timestamp string of the time - HHMMSS.
         *
         *  @param $datetime  (optional) A YDDate object, timestamp or string of a date.
         *
         *  @returns  A timestamp string of the time.
         */
        function getTimeStampStringTime( $datetime='' ) {
            if ( $datetime ) { $this->setDate( $datetime ); }
            
            return $this->getPart( 'hours_with_zero' ) .
                   $this->getPart( 'minutes_with_zero' ) .
                   $this->getPart( 'seconds_with_zero' );
        }

        /**
         *  This function returns a MySQL timestamp string of the date - YYYYMMDD.
         *
         *  @param $datetime  (optional) A YDDate object, timestamp or string of a date.
         *
         *  @returns  A timestamp string of the date.
         */
        function getTimeStampStringDate( $datetime='' ) {
            if ( $datetime ) { $this->setDate( $datetime ); }
            
            return $this->getPart( 'year' ) . 
                   $this->getPart( 'month_with_zero' ) .
                   $this->getPart( 'day_with_zero' );
        }

        /**
         *  This function sets the default format of the class.
         *
         *  @param $format  (optional) A format accepted by PHP function strftime.
         */
        function setFormat( $format='' ) {
            if ( ! $format ) {
                $format = '%Y-%m-%d %H:%M:%S';
            }
            $this->format = $format;
        }

        /**
         *  This function parses the datetime passed and returns a timestamp.
         *
         *  @param $datetime  (optional) A YDDate object, timestamp or string of a date.
         *
         *  @returns  A timestamp of the date passed.
         *
         *  @internal
         */
        function _parseDate( $dateTime="" ) {
            $ts = 0;
            switch( gettype($dateTime) ) {
                case "string": 
                    $ts = strtotime( $dateTime ); 
                    break;
                case "integer":
                    $ts = $dateTime;
                    break;
                case "object":
                    if ( is_a( $dateTime, "dateclass" ) ) {
                        $ts = $dateTime->TimeStamp();
                    }
            } 
            return $ts;
        }

        /**
         *  This function returns a string representation of the date based
         *  on a format passed as parameter or by the default format.
         *
         *  @param $format    (optional) A format accepted by PHP function strftime.
         *  @param $datetime  (optional) A YDDate object, timestamp or string of a date.
         *
         *  @returns  A string of the date.
         */
        function toString( $format='', $datetime='' ) {
            if ( $datetime ) { $this->setDate( $datetime ); }
            if ( ! $format ) { $format = $this->format; }
            return strftime( $format, $this->getTimeStamp() );
        }

        /**
         *  This function returns a part of the date information.
         *
         *  year, month, month_with_zero, month_name, month_name_abbr, day,
         *  day_with_zero, day_name, day_name_abbr, hours, hours_with_zero, minutes,
         *  minutes_with_zero, seconds, seconds_with_zero, weekday, yearday, timestamp.
         *
         *  @param $part      The part of the date information.
         *  @param $datetime  (optional) A YDDate object, timestamp or string of a date.
         *
         *  @returns  The part of the date information.
         */
        function getPart( $part, $datetime='' ) {
            
            if ( $datetime ) { $this->setDate( $datetime ); }
            
            switch ( $part ) {
                case 'year':
                    return (int) strftime( '%Y', $this->getTimeStamp() );
                case 'month':
                    return (int) strftime( '%m', $this->getTimeStamp() );
                case 'month_with_zero':
                    return strftime( '%m', $this->getTimeStamp() );
                case 'month_name':
                    return ucfirst( strftime( '%B', $this->getTimeStamp() ) );
                case 'month_name_abbr':
                    return ucfirst( strftime( '%b', $this->getTimeStamp() ) );
                case 'day':
                    return (int) strftime( '%d', $this->getTimeStamp() );
                case 'day_with_zero':
                    return strftime( '%d', $this->getTimeStamp() );
                case 'day_name':
                    return ucfirst( strftime( '%A', $this->getTimeStamp() ) );
                case 'day_name_abbr':
                    return ucfirst( strftime( '%a', $this->getTimeStamp() ) );
                case 'hours':
                    return (int) strftime( '%H', $this->getTimeStamp() );
                case 'hours_with_zero':
                    return strftime( '%H', $this->getTimeStamp() );
                case 'minutes':
                    return (int) strftime( '%M', $this->getTimeStamp() );
                case 'minutes_with_zero':
                    return strftime( '%M', $this->getTimeStamp() );
                case 'seconds':
                    return (int) strftime( '%S', $this->getTimeStamp() );
                case 'seconds_with_zero':
                    return strftime( '%S', $this->getTimeStamp() );
                case 'weekday':
                    return (int) strftime( '%w', $this->getTimeStamp() );
                case 'yearday':
                    return (int) strftime( '%j', $this->getTimeStamp() );
                case 'timestamp':
                    return $this->getTimeStamp();
            }
            return false;
        }

        /**
         *  This function returns an array with all the date information.
         *
         *  @param $datetime  (optional) A YDDate object, timestamp or string of a date.
         *
         *  @returns  An array with all the date information.
         */
        function toArray( $datetime='' ) {
            
            if ( $datetime ) { $this->setDate( $datetime ); }
            
            $parts = array( 'year', 'month', 'day', 'month_with_zero', 'day_with_zero', 'month_name', 'month_name_abbr', 'day_name', 'day_name_abbr', 'yearday', 'weekday', 'hours', 'minutes', 'seconds', 'hours_with_zero', 'minutes_with_zero', 'seconds_with_zero', 'timestamp' );
            
            $arr = array();
            foreach ( $parts as $part ) {
                $arr[ $part ] = $this->getPart( $part );
            }
            
            $arr['timestamp_string']      = $this->getTimeStampString();
            $arr['timestamp_string_date'] = $this->getTimeStampStringDate();
            $arr['timestamp_string_time'] = $this->getTimeStampStringTime();
            
            return $arr;
            
        }

        /**
         *  This function returns the second.
         *
         *  @returns  The second.
         */
        function getSeconds() {
            return $this->Seconds();
        }

        /**
         *  This function returns the minute.
         *
         *  @returns  The minute.
         */
        function getMinutes() {
            return $this->Minutes();
        }

        /**
         *  This function returns the hour.
         *
         *  @returns  The hour.
         */
        function getHours() {
            return $this->Hours();
        }

        /**
         *  This function returns the day.
         *
         *  @returns  The day.
         */
        function getDay() {
            return $this->Day();
        }
        
        /**
         *  This function returns the weekday name.
         *
         *  @returns  The weekday name.
         */
        function getDayName() {
            return $this->getPart( 'day_name' );
        }

        /**
         *  This function returns the abbreviated weekday name.
         *
         *  @returns  The weekday name.
         */
        function getDayNameAbbr() {
            return $this->getPart( 'day_name_abbr' );
        }

        /**
         *  This function returns the weekday. 0 = Sunday, 6 = Saturday
         *
         *  @returns  The weekday.
         */
        function getWeekDay() {
            return $this->getPart( 'weekday' );
        }

        /**
         *  This function returns the month.
         *
         *  @returns  The month.
         */
        function getMonth() {
            return $this->Month();
        }

        /**
         *  This function returns the month name.
         *
         *  @returns  The month name.
         */
        function getMonthName() {
            return $this->getPart( 'month_name' );
        }


        /**
         *  This function returns the abbreviated month name based on the locale settings.
         *
         *  @returns  The abbreviated month name.
         */
        function getMonthNameAbbr() {
            return $this->getPart( 'month_name_abbr' );
        }

        /**
         *  This function returns the year.
         *
         *  @returns  The year.
         */
        function getYear() {
            return $this->Year();
        }

        /**
         *  This function returns the quarter.
         *
         *  @returns  The quarter.
         */
        function getQuarter() {
            return $this->Quarter();
        }

        /**
         *  This function returns a boolean indicating if the date is today.
         *
         *  @returns  A boolean indicating if the date is today.
         */
        function isToday() {
            if ( $this->toString( '%Y-%m-%d' ) === date( 'Y-m-d' ) ) {
                return true;
            }
            return false;
        }

        /**
         *  This function returns a boolean indicating if the date is tomorrow.
         *
         *  @returns  A boolean indicating if the date is tomorrow.
         */
        function isTomorrow() {
            if ( $this->toString( '%Y-%m-%d' ) === date( 'Y-m-d', time()+(60*60*24) ) ) {
                return true;
            }
            return false;
        }

        /**
         *  This function returns a boolean indicating if the date is yesterday.
         *
         *  @returns  A boolean indicating if the date is yesterday.
         */
        function isYesterday() {
            if ( $this->toString( '%Y-%m-%d' ) === date( 'Y-m-d', time()-(60*60*24) ) ) {
                return true;
            }
            return false;
        }

        /**
         *  This function returns a boolean indicating if the date
         *  is in the current month.
         *
         *  @returns  A boolean indicating if the date is in the current month.
         */
        function isCurrentMonth() {
            if ( $this->toString( '%Y-%m' ) === date( 'Y-m' ) ) {
                return true;
            }
            return false;
        }

        /**
         *  This function returns a boolean indicating if the date
         *  is in the current year.
         *
         *  @returns  A boolean indicating if the date is in the current year.
         */
        function isCurrentYear() {
            if ( $this->toString( '%Y' ) === date( 'Y' ) ) {
                return true;
            }
            return false;
        }

        /**
         *  This function returns a boolean indicating if the time
         *  is in the current hour.
         *
         *  @returns  A boolean indicating if the time is in the current hour.
         */
        function isCurrentHour() {
            if ( $this->toString( '%Y-%m-%d %H' ) === date( 'Y-m-d H' ) ) {
                return true;
            }
            return false;
        }

        /**
         *  This function returns a boolean indicating if the time
         *  is in the current minute.
         *
         *  @returns  A boolean indicating if the time is in the current minute.
         */
        function isCurrentMinute() {
            if ( $this->toString( '%Y-%m-%d %H:%M' ) === date( 'Y-m-d H:i' ) ) {
                return true;
            }
            return false;
        }

        /**
         *  This function returns a boolean indicating if the day
         *  is a friday.
         *
         *  @returns  A boolean indicating if the day is a friday.
         */
        function isFriday() {
            if ( $this->getWeekDay() == 5 ) {
                return true;
            }
            return false;
        }

        /**
         *  This function returns a boolean indicating if the day
         *  is a saturday.
         *
         *  @returns  A boolean indicating if the day is a saturday.
         */
        function isSaturday() {
            if ( $this->getWeekDay() == 6 ) {
                return true;
            }
            return false;
        }

        /**
         *  This function returns a boolean indicating if the day
         *  is a sunday.
         *
         *  @returns  A boolean indicating if the day is a sunday.
         */
        function isSunday() {
            if ( $this->getWeekDay() == 0 ) {
                return true;
            }
            return false;
        }

        /**
         *  This function returns a boolean indicating if the date
         *  is in a weekend.
         *
         *  @returns  A boolean indicating if the date is in a weekend.
         */
        function isWeekend() {
            return ( $this->isSaturday() || $this->isSunday() );
        }

        /**
         *  This function returns a YDDate object of the previous day.
         *
         *  @returns  A YDDate object of the previous day.
         */
        function getPrevDay() {
            $date = new YDDate();
            $date->addDay( -1 );
            return $date;
        }

        /**
         *  This function returns a YDDate object of the next day.
         *
         *  @returns  A YDDate object of the next day.
         */
        function getNextDay() {
            $date = new YDDate();
            $date->addDay( +1 );
            return $date;
        }

        /**
         *  This function sets the date to the previous day.
         *
         *  @returns  A formatted string of the datetime.
         */
        function prevDay() {
            return $this->addDay( -1 );
        }

        /**
         *  This function sets the date to the next day.
         *
         *  @returns  A formatted string of the datetime.
         */
        function nextDay() {
            return $this->addDay( +1 );
        }
        
        /**
         *  This function returns the difference between the current object
         *  and another datetime.
         *
         *  @param $datetime  A YDDate object, a timestamp or a string.
         *
         *  @returns  An array with the number of years, months, days, weeks, 
         *            weekdays, quarters, hours, minutes and seconds 
         *            between the dates.
         */
        function getDifference( $datetime ) {
            
            $datetime_start = new DateClass( $this->getTimeStamp() );
            if ( is_object( $datetime ) && is_a( $datetime, 'dateclass' ) ) {
                $datetime_end = new DateClass( $datetime->TimeStamp() );
            } else {
                $datetime_end = new DateClass( $datetime );
            }
            
            $span = new DateSpanClass( $datetime_start, $datetime_end );
            
            $arr = array();
            $arr['years']    = round( $span->Years() );
            $arr['months']   = round( $span->Months() );
            $arr['weeks']    = round( $span->Weeks() );
            $arr['weekdays'] = round( $span->WeekDays() );
            $arr['days']     = round( $span->Days() );
            $arr['quarters'] = round( $span->Quarters() );
            $arr['hours']    = round( $span->Hours() );
            $arr['minutes']  = round( $span->Minutes() );
            $arr['seconds']  = round( $span->Seconds() );
            
            return $arr;
        }
        
    }

?>