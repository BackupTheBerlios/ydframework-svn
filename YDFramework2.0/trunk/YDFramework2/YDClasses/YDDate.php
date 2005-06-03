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
    YDInclude( 'phpDateTime' . YD_DIRDELIM . 'DateTime.class.php' );

    /**
     *  This class defines a YDDateTime object and it's a wrapper around the
     *  DateTime class of the phpDateTime package. More information can be found on:
     *  http://sourceforge.net/projects/phpdatetime/
     */
    class YDDateTime extends DateTime {

        /**
         *  The YDDateTime constructor.
         *
         *  @param  $datetime  (optional) A YDDateTime object or a date in form
         *                     YYYY-MM-DD HH:MM:SS. Default: null = today.
         *  @param  $time      (optional) A YDTime object or a time in form HH:MM:SS.
         *                     Default: null = current time.
         */
        function YDDateTime( $datetime=null, $time=null ) {
            if ( $datetime === null && $time === null ) {
                $datetime = time();
            }
            $this->DateTime( $datetime, $time );
        }
        
        /**
         *  This function sets the time.
         *
         *  @param  $time  (optional) A YDTime object, a number of seconds or
         *                 a time in form HH:MM:SS. Default: null = current time.
         */
        function setTime( $time=null ) {
            $this->time = new YDTime($time);
        }

        /**
         *  This function sets the date.
         *
         *  @param  $date  (optional) A YDDate object, a number of seconds or
         *                 a date in form YYYY-MM-DD. Default: null = today.
         */
        function setDate( $date=null ) {
            $this->date = new YDDate($date);
        }
        
        /**
         *  This function adds a number of days to the current date.
         *
         *  @param  $days  (optional) Number of days. Default: 1.
         *
         *  @returns       A string of the datetime.
         */
        function addDay( $days=1 ) {
            $seconds = mktime( $this->time->getHours(),
                               $this->time->getMinutes(),
                               $this->time->getSeconds(),
                               $this->date->getMonth(),
                               $this->date->getDay() + $days,
                               $this->date->getYear()
                       );
                       
            $this->set( $seconds );
            return $this->get();
        }

        /**
         *  This function adds a number of months to the current date.
         *
         *  @param  $months  (optional) Number of months. Default: 1.
         *
         *  @returns         A string of the datetime.
         */
        function addMonth( $months=1 ) {
            $seconds = mktime( $this->time->getHours(),
                               $this->time->getMinutes(),
                               $this->time->getSeconds(),
                               $this->date->getMonth() + $months,
                               $this->date->getDay(),
                               $this->date->getYear()
                       );
                       
            $this->set( $seconds );
            return $this->get();
        }

        /**
         *  This function adds a number of years to the current date.
         *
         *  @param  $years  (optional) Number of years. Default: 1.
         *
         *  @returns         A string of the datetime.
         */
        function addYear( $years=1 ) {
            $seconds = mktime( $this->time->getHours(),
                               $this->time->getMinutes(),
                               $this->time->getSeconds(),
                               $this->date->getMonth(),
                               $this->date->getDay(),
                               $this->date->getYear() + $years
                       );
                       
            $this->set( $seconds );
            return $this->get();
        }

        /**
         *  This function adds a number of hours to the current date.
         *
         *  @param  $hours  (optional) Number of hours. Default: 1.
         *
         *  @returns         A string of the datetime.
         */
        function addHour( $hours=1 ) {
            $seconds = mktime( $this->time->getHours() + $hours,
                               $this->time->getMinutes(),
                               $this->time->getSeconds(),
                               $this->date->getMonth(),
                               $this->date->getDay(),
                               $this->date->getYear()
                       );
                       
            $this->set( $seconds );
            return $this->get();
        }

        /**
         *  This function adds a number of minutes to the current date.
         *
         *  @param  $minutes  (optional) Number of minutes. Default: 1.
         *
         *  @returns         A string of the datetime.
         */
        function addMinute( $minutes=1 ) {
            $seconds = mktime( $this->time->getHours(),
                               $this->time->getMinutes() + $minutes,
                               $this->time->getSeconds(),
                               $this->date->getMonth(),
                               $this->date->getDay(),
                               $this->date->getYear()
                       );
                       
            $this->set( $seconds );
            return $this->get();
        }

        /**
         *  This function adds a number of seconds to the current date.
         *
         *  @param  $seconds  (optional) Number of seconds. Default: 1.
         *
         *  @returns         A string of the datetime.
         */
        function addSecond( $seconds=1 ) {
            $seconds = mktime( $this->time->getHours(),
                               $this->time->getMinutes(),
                               $this->time->getSeconds() + $seconds,
                               $this->date->getMonth(),
                               $this->date->getDay(),
                               $this->date->getYear()
                       );
                       
            $this->set( $seconds );
            return $this->get();
        }

        /**
         *  This function returns a reference to the date object of the datetime.
         *  
         *  @returns  A reference to a YDDate object.
         */
        function & getDate() {
            return $this->date;
        }

        /**
         *  This function returns a reference to the time object of the datetime.
         *  
         *  @returns  A reference to a YDTime object.
         */
        function & getTime() {
            return $this->time;
        }

        /**
         *  This function sets the seconds.
         *
         *  @param  $seconds  The number of seconds.
         */
        function setSeconds( $seconds ) {
            $this->time->setSeconds( $seconds );
        }

        /**
         *  This function returns the second.
         *
         *  @returns  The second.
         */
        function getSeconds() {
            return $this->time->getSeconds();
        }

        /**
         *  This function sets the minute.
         *
         *  @param  $minutes  The minute.
         */
        function setMinutes( $minutes ) {
            $this->time->setMinutes( $minutes );
        }

        /**
         *  This function returns the minute.
         *
         *  @returns  The minute.
         */
        function getMinutes() {
            return $this->time->getMinutes();
        }

        /**
         *  This function sets the hour.
         *
         *  @param  $hours  The hour.
         */
        function setHours( $hours ) {
            $this->time->setHours( $hours );
        }

        /**
         *  This function returns the hour.
         *
         *  @returns  The hour.
         */
        function getHours() {
            return $this->time->getHours();
        }

        /**
         *  This function sets the day.
         *
         *  @param  $day  The day.
         */
        function setDay( $day ) {
            $this->date->setDay( $day );
        }

        /**
         *  This function returns the day.
         *
         *  @returns  The day.
         */
        function getDay() {
            return $this->date->getDay();
        }

        /**
         *  This function returns the weekday. 0 = Sunday, 1 = Monday, etc.
         *
         *  @returns  The weekday.
         */
        function getWeekDay() {
            return $this->date->getWeekDay();
        }

        /**
         *  This function sets the month.
         *
         *  @param  $month  The month.
         */
        function setMonth( $month ) {
            $this->date->setMonth( $month );
        }

        /**
         *  This function returns the month.
         *
         *  @returns  The month.
         */
        function getMonth() {
            return $this->date->getMonth();
        }

        /**
         *  This function sets the year.
         *
         *  @param  $year  The year.
         */
        function setYear( $year ) {
            $this->date->setYear( $year );
        }

        /**
         *  This function returns the year.
         *
         *  @returns  The year.
         */
        function getYear() {
            return $this->date->getYear();
        }

        /**
         *  This function returns the month name based on the locale settings.
         *
         *  @returns  The month name.
         */
        function getMonthName() {
            return $this->date->getMonthName();
        }

        /**
         *  This function returns the abbreviated month name based on the locale settings.
         *
         *  @returns  The abbreviated month name.
         */
        function getMonthNameAbbr() {
            return $this->date->getMonthNameAbbr();
        }

        /**
         *  This function returns the weekday name based on the locale settings.
         *
         *  @returns  The weekday name.
         */
        function getDayName() {
            return $this->date->getDayName();
        }

        /**
         *  This function returns the abbreviated weekday name based on the locale settings.
         *
         *  @returns  The abbreviated weekday name.
         */
        function getDayNameAbbr() {
            return $this->date->getDayNameAbbr();
        }

        /**
         *  This function returns a boolean indicating if the date is today.
         *
         *  @returns  A boolean indicating if the date is today.
         */
        function isToday() {
            return $this->date->isToday();
        }

        /**
         *  This function returns a boolean indicating if the date is tomorrow.
         *
         *  @returns  A boolean indicating if the date is tomorrow.
         */
        function isTomorrow() {
            return $this->date->isTomorrow();
        }

        /**
         *  This function returns a boolean indicating if the date is yesterday.
         *
         *  @returns  A boolean indicating if the date is yesterday.
         */
        function isYesterday() {
            return $this->date->isYesterday();
        }

        /**
         *  This function returns a boolean indicating if the date is in the current month.
         *
         *  @returns  A boolean indicating if the date is in the current month.
         */
        function isCurrentMonth() {
            return $this->date->isCurrentMonth();
        }

        /**
         *  This function returns a boolean indicating if the date is in the current year.
         *
         *  @returns  A boolean indicating if the date is in the current year.
         */
        function isCurrentYear() {
            return $this->date->isCurrentYear();
        }

        /**
         *  This function returns a boolean indicating if the time is in the current hour.
         *
         *  @returns  A boolean indicating if the time is in the current hour.
         */
        function isCurrentHour() {
            if ( $this->time->isCurrentHour() && $this->isToday() &&
                 $this->isCurrentMonth() && $this->isCurrentYear() ) {
                return true;
            }
            return false;
        }

        /**
         *  This function returns a boolean indicating if the time is in the current minute.
         *
         *  @returns  A boolean indicating if the time is in the current minute.
         */
        function isCurrentMinute() {
            if ( $this->time->isCurrentMinute() && $this->time->isCurrentHour() &&
                 $this->isToday() && $this->isCurrentMonth() && $this->isCurrentYear() ) {
                return true;
            }
            return false;
        }

        /**
         *  This function returns a YDDateTime object of the previous day.
         *
         *  @returns  A YDDateTime object of the previous day.
         */
        function getPrevDay() {
            $datetime = new YDDateTime();
            $datetime->setDate( $this->date->prevDay() );
            $datetime->setTime( $this->time );
            return $datetime;
        }

        function getNextDay() {
            $datetime = new YDDateTime();
            $datetime->setDate( $this->date->nextDay() );
            $datetime->setTime( $this->time );
            return $datetime;
        }

        /**
         *  This function sets the date to the previous day.
         *
         *  @returns  A formatted string of the datetime.
         */
        function prevDay() {
            $this->setDate( $this->date->getPrevDay() );
            return $this->get();
           
        }

        /**
         *  This function sets the date to the next day.
         *
         *  @returns  A formatted string of the datetime.
         */
        function nextDay() {
            $this->setDate( $this->date->getNextDay() );
            return $this->get();
        }

    }

    /**
     *  This class defines a YDDate object and it's a wrapper around the
     *  Date class of the phpDateTime package. More information can be found on:
     *  http://sourceforge.net/projects/phpdatetime/
     */
    class YDDate extends Date {

        /**
         *  The class constructor.
         *
         *  @param  $date  (optional) A YDDate object, a number of seconds or a string
         *                 in the form YYYY-MM-DD.
         */
        function YDDate( $date=null ) {
            $this->Date( $date );
        }

        /**
         *  This function adds a number of days to the date. 
         *
         *  @param  $months  (optional) The number of days. Default: 1.
         *  @param  $date    (optional) If defined with a YDDate object, number of
         *                   seconds or a string in the form YYYY-MM-DD, the method
         *                   can be called statically. Default: null.
         *  
         *  @returns  A formatted string of the date.
         *
         *  @static  If called with both arguments.
         */
        function addDay( $days=1, $date=null ) {
            if ( null === $date ) {
                $ts = mktime(0, 0, 0, $this->getMonth(), $this->getDay() + $days, $this->getYear());
                $this->setFromUnixTimestamp( $ts );
                return $this->get();
            }
            
            $date = new YDDate( $date );
            return $date->addDay( $days, null );
        }

        /**
         *  This function adds a number of months to the date. 
         *
         *  @param  $months  (optional) The number of months. Default: 1.
         *  @param  $date    (optional) If defined with a YDDate object, number of
         *                   seconds or a string in the form YYYY-MM-DD, the method
         *                   can be called statically. Default: null.
         *  
         *  @returns  A formatted string of the date.
         *
         *  @static  If called with both arguments.
         */
        function addMonth( $months=1, $date=null ) {
            if ( null === $date ) {
                $ts = mktime(0, 0, 0, $this->getMonth() + $months, $this->getDay(), $this->getYear());
                $this->setFromUnixTimestamp( $ts );
                return $this->get();
            }
            
            $date = new YDDate( $date );
            return $date->addMonth( $months, null );
        }

        /**
         *  This function adds a number of years to the date. 
         *
         *  @param  $years  (optional) The number of years. Default: 1.
         *  @param  $date    (optional) If defined with a YDDate object, number of
         *                   seconds or a string in the form YYYY-MM-DD, the method
         *                   can be called statically. Default: null.
         *  
         *  @returns  A formatted string of the date.
         *
         *  @static  If called with both arguments.
         */
        function addYear( $years=1, $date=null ) {
            if ( null === $date ) {
                $ts = mktime(0, 0, 0, $this->getMonth(), $this->getDay(), $this->getYear() + $years);
                $this->setFromUnixTimestamp( $ts );
                return $this->get();
            }
            
            $date = new YDDate( $date );
            return $date->addYear( $years, null );
        }

        /**
         *  This function returns a boolean indicating if the date is in the current year.
         *
         *  @returns  A boolean indicating if the date is in the current year.
         */
        function isCurrentYear() {
            if ( $this->getYear() == date('Y') ) {
                return true;
            }
            return false;
        }

        /**
         *  This function returns the month name based on the locale settings.
         *
         *  @returns  The month name.
         */
        function getMonthName() {
            return $this->getAsLcStr('%B');
        }
        
        /**
         *  This function returns a boolean indicating if the date is in a weekend.
         *
         *  @returns  A boolean indicating if the date is in a weekend.
         */
        function isWeekend() {
                
            if ( $this->isSaturday() || $this->isSunday() )
            {
                return true;
            }
            
            return false;
        }

        /**
         *  This function returns a YDDate object of the previous day. It's an alias
         *  of the previousDay method.
         *
         *  @returns  A YDDate object of the previous day.
         */
        function prevDay() {
            return $this->previousDay();
        }
        
        /**
         *  This function sets the date of the object.
         *
         *  @param  $date  (optional) A YDDate object, a number of seconds or a string
         *                 in the form YYYY-MM-DD. Default: null = today.
         *
         *  @returns     Returns
         */
        function set( $date = NULL ) {
            if ( NULL === $date ) {
                return $this->setFromTs( time() );
            }
            
            if ( is_numeric( $date ) ) {
                return $this->setFromTs( $date );
            }
            
            if ( is_object( $date ) && is_a( $date, 'date' ) ) {
                return $this->setFromIso( $date->getAsIso() );
            }
            
            if ( preg_match( '|\.|', $date ) ) {
                // date in form d.m.y
                return $this->setFromDin( $date );
            }
            
            if ( preg_match( '|\/|', $date ) ) {
                // date is in form m/d/y
                return $this->setFromAmi( $date );
            }
            
            if ( preg_match( '|\-|', $date ) ) {
                // date is in form YYYY-MM-DD
                return $this->setFromIso( $date );
            }
            
            if ( empty( $date ) ) {
                // date is '', so we use 0000-00-00
                return $this->setFromIso( '0000-00-00' );
            }
            
            trigger_error( 'unknown date-format: ' . var_export( $date, true ) . '(' . $_SERVER['REQUEST_URI'] . ')', E_USER_WARNING );
            
            return $this->setFromTs( time() );
        }
        
    }

    /**
     *  This class defines a YDTime object and it's a wrapper around the
     *  Time class of the phpDateTime package. More information can be found on:
     *  http://sourceforge.net/projects/phpdatetime/
     */
    class YDTime extends Time {

        /**
         *  The class constructor.
         *
         *  @param  $time  (optional) A YDTime object, a number of seconds or a string
         *                 in the form HH:MM:DD. Default: null = current time.
         */
        function YDTime( $time=null ) {
            $this->Time( $time );
        }

        /**
         *  This function adds a number of hours to the time. 
         *
         *  @param  $hours  (optional) The number of hours. Default: 1.
         *  @param  $date    (optional) If defined with a YDTime object, number of
         *                   seconds or a string in the form HH:MM:DD, the method
         *                   can be called statically. Default: null.
         *  
         *  @returns  A formatted string of the time.
         *
         *  @static  If called with both arguments.
         */
        function addHour( $hours=1, $time=null ) {
            if ( null === $time ) {
                $ts = mktime( $this->getHours() + $hours,
                              $this->getMinutes(),
                              $this->getSeconds(),
                              date("m"),
                              date("d"),
                              date("Y"));
                $this->set( $ts );
                return $this->get();
            }
            
            $time = new YDTime( $time );
            return $time->addHour( $hours, null );
        }

        /**
         *  This function adds a number of minutes to the time. 
         *
         *  @param  $minutes  (optional) The number of minutes. Default: 1.
         *  @param  $date    (optional) If defined with a YDTime object, number of
         *                   seconds or a string in the form HH:MM:DD, the method
         *                   can be called statically. Default: null.
         *  
         *  @returns  A formatted string of the time.
         *
         *  @static  If called with both arguments.
         */
        function addMinute( $minutes=1, $time=null ) {
            if ( null === $time ) {
                $ts = mktime( $this->getHours(),
                              $this->getMinutes() + $minutes,
                              $this->getSeconds(),
                              date("m"),
                              date("d"),
                              date("Y"));
                $this->set( $ts );
                return $this->get();
            }
            
            $time = new YDTime( $time );
            return $time->addMinute( $minutes, null );
        }

        /**
         *  This function adds a number of seconds to the time. 
         *
         *  @param  $seconds  (optional) The number of seconds. Default: 1.
         *  @param  $date    (optional) If defined with a YDTime object, number of
         *                   seconds or a string in the form HH:MM:DD, the method
         *                   can be called statically. Default: null.
         *  
         *  @returns  A formatted string of the time.
         *
         *  @static  If called with both arguments.
         */
        function addSecond( $seconds=1, $time=null ) {
            if ( null === $time ) {
                $ts = mktime( $this->getHours(),
                              $this->getMinutes(),
                              $this->getSeconds() + $seconds,
                              date("m"),
                              date("d"),
                              date("Y"));
                $this->set( $ts );
                return $this->get();
            }
            
            $time = new YDTime( $time );
            return $time->addSecond( $seconds, null );
        }

        /**
         *  This function adds a time string to the current time.
         *
         *  @param  $time  The time string in the form HH:MM:SS.
         *
         *  @returns  The formatted string of the time.
         */
        function addTime( $time ) {
            $result = time_add( $this->get(), $time );
            $this = new YDTime( $result );
            return $this->get();
        }

        /**
         *  This function adds one time string into another and returns
         *  a new YDTime object of the result.
         *
         *  @param  $time1  The first time string in the form HH:MM:SS.
         *  @param  $time2  The second time string in the form HH:MM:SS.
         *
         *  @returns  A YDTime object of the result.
         */
        function addTimes( $time1, $time2 ) {
            $result = time_add( $time1, $time2 );
            return new YDTime( $result );
        }

        /**
         *  This function subtracts a time string from the current time.
         *
         *  @param  $time  The time string in the form HH:MM:SS.
         *
         *  @returns  The formatted string of the time.
         */
        function subTime( $time ) {
            $result = time_sub( $this->get(), $time );
            $this = new YDTime( $result );
        }

        /**
         *  This function subtracts one time string into another and returns
         *  a new YDTime object of the result.
         *
         *  @param  $time1  The first time string in the form HH:MM:SS.
         *  @param  $time2  The second time string in the form HH:MM:SS.
         *
         *  @returns  A YDTime object of the result.
         */
        function subTimes( $time1, $time2 ) {
            $result = time_sub( $time1, $time2 );
            return new YDTime( $result );
        }

        /**
         *  This function returns a boolean indicating if the time is in the current hour.
         *
         *  @returns  A boolean indicating if the time is in the current hour.
         */
        function isCurrentHour() {
            if ( $this->hours == date( "H" ) ) {
                return true;
            }
            return false;
        }

        /**
         *  This function returns a boolean indicating if the time is in the current minute.
         *
         *  @returns  A boolean indicating if the time is in the current minute.
         */
        function isCurrentMinute() {
            if ( $this->minutes == date( "i" ) && $this->isCurrentHour() ) {
                return true;
            }
            return false;
        }

        /**
         *  This function sets the time.
         *
         *  @param  $time  (optional) A YDTime object, a number of seconds or a string
         *                 in the form HH:MM:DD. Default: null = current time.
         */
        function set( $time = null ) {
            if ( is_object($time) && is_a( $time, 'time' ) ) {
                return $this->setTimeFromSeconds($time->getAsSeconds());
            }
            
            if ( is_numeric($time) ) {
                return $this->setTimeFromSeconds($time);
            }
            
            return $this->setTimeFromString($time);
        }
        
    }

?>