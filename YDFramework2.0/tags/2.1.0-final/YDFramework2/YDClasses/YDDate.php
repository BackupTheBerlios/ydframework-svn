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

    // Custom formats
    YDConfig::set( 'YD_DATE_FORMATS', array(), false );    
    
    /**
     *  This class defines a YDDateFormat object.
     *
     *  @author  David Bittencourt <muitocomplicado@hotmail.com>
     */
    class YDDateFormat extends YDBase {
        
        /**
         *  The constructor.
         */
        function YDDateFormat() {
            $this->YDBase();
        }

        /**
         *  This function sets the value of a part of the format.
         *
         *  @param  $name    The format name.
         *  @param  $part    The part name: string, parts, regexes or empty.
         *  @param  $value   The part value.
         *
         *  @static
         */
        function set( $name, $part='string', $value ) {
            
            $all  = YDConfig::get( 'YD_DATE_FORMATS' );
            $name = strtoupper( $name );
            
            if ( ! isset( $all[ $name ] ) ) {
                $all[ $name ] = array( 'string' => '',
                                       'parts' => array(),
                                       'regexes' => array(),
                                       'empty' => '' );
            }
            
            $all[ $name ][ $part ] = $value;
            
            YDConfig::set( 'YD_DATE_FORMATS', $all );
            
        }
        
        /**
         *  This function returns the value of a part of the format.
         *
         *  @param  $name    The format name.
         *  @param  $part    The part name: string, parts, regexes or empty.
         *
         *  @returns         The part value.
         *
         *  @static
         */
        function get( $name, $part ) {

            $all  = YDConfig::get( 'YD_DATE_FORMATS' );
            $name = strtoupper( $name );
            
            if ( isset( $all[ $name ][ $part ] ) ) {
                return $all[ $name ][ $part ];
            }
            return;
        }
        
        /**
         *  This function sets the format string. The following specifiers
         *  are recognized in the format string:
         *
         *  %a - abbreviated weekday name
         *  %A - weekday name
         *  %b - abbreviated month name
         *  %B - month name
         *  %d - day (with zero)
         *  %m - month (with zero)
         *  %Y - year (4 digits)
         *  %H - hours (with zero)
         *  %M - minutes (with zero)
         *  %S - seconds (with zero)
         *  %w - weekday number (0 = sunday, 6 = saturday)
         *  %T - equal to %H:%M:%S
         *
         *  @param  $name    The format name.
         *  @param  $value   The format string.
         *  
         *  @static
         */
        function setString( $name, $value ) {
            YDDateFormat::set( $name, 'string', $value );
        }
        
        /**
         *  This function sets the format parts.
         *
         *  @param  $name    The format name.
         *  @param  $value   (Optional) The format parts array. Default: array()
         *  
         *  @static
         */
        function setParts( $name, $value=array() ) {
            YDDateFormat::set( $name, 'parts', $value );
        }
        
        /**
         *  This function sets the format regexes.
         *
         *  @param  $name    The format name.
         *  @param  $value   (Optional) The format regexes array. Default: array()
         *  
         *  @static
         */
        function setRegexes( $name, $value=array() ) {
            YDDateFormat::set( $name, 'regexes', $value );
        }
        
        /**
         *  This function sets the format empty string.
         *
         *  @param  $name    The format name.
         *  @param  $value   (Optional) The format empty string. Default: ""
         *  
         *  @static
         */
        function setEmpty( $name, $value='' ) {
            YDDateFormat::set( $name, 'empty', $value );
        }
        
    }
    
    /**
     *  This class defines a YDDate object.
     *
     *  @author  David Bittencourt <muitocomplicado@hotmail.com>
     */
    class YDDate extends YDBase {
        
        var $year;
        var $month;
        var $day;
        var $hours;
        var $minutes;
        var $seconds;

        var $day_with_zero;
        var $month_with_zero;
        var $hours_with_zero;
        var $minutes_with_zero;
        var $seconds_with_zero;

        var $day_name;
        var $day_name_abbr;
        var $month_name;
        var $month_name_abbr;

        var $quarter;
        var $weekday;
        var $leap;
        
        var $timestamp;
        var $timestamp_string;
        
        /**
         *  The constructor.
         *
         *  @param $date    (Optional) A YDDate object, timestamp, array or string.
         *                             If null, the current date.
         *  @param $format  (Optional) The format name. Default: 'ISO'.
         */
        function YDDate( $date=null, $format='ISO' ) {
            
            $this->YDBase();
            $this->set( $date, $format );
            
        }

        /**
         *  This function returns the date as a formatted string defined by the
         *  custom formats (e.g. 'ISO', 'EUN', 'USA', 'SQL', 'HUM' ).
         *
         *  @param $format     (Optional) The format to be returned. Default: 'ISO'.
         *  @param $date       (Optional) A date to be converted to another format.
         *                     Default: current object.
         *  @param $format_in  (Optional) The format of $date, if passed. Default: 'SQL'.
         *  
         *  @returns  A string with the formatted result.
         *
         *  @static  If $date is passed.
         */
        function get( $format='ISO', $date=null, $format_in='SQL' ) {
            
            if ( $date !== null ) {
                $date = new YDDate( $date, $format_in );
                return $date->get( $format );
            }
            
            if ( $this->isDateEmpty() && $this->isTimeEmpty() ) {
                return YDDateFormat::get( $format, 'empty' );
            }
            
            $string = YDDateFormat::get( $format, 'string' );
            
            if ( is_null( $string ) ) {
                trigger_error( 'The format "' . $name . '" is not defined', YD_ERROR );
            }
            
            return $this->getCustom( $string, $date, $format_in );
        
        }
        
        /**
         *  This function returns the current date/time formatted.
         *
         *  @param $format    (Optional) The format. Default: 'ISO'.
         *  
         *  @returns  A string with the formatted result.
         *
         *  @static
         */
        function now( $format='ISO' ) {
            $date = new YDDate();
            return $date->get( $format );
        }
        
        /**
         *  This function returns the current date/time as a custom format string.
         *
         *  @param $format    The format string.
         *  
         *  @returns  A string with the formatted result.
         *
         *  @static
         */
        function nowCustom( $format ) {
            $date = new YDDate();
            return $date->getCustom( $format );
        }

        /**
         *  This function returns the date as a custom format string.
         *
         *  @param $format     The format string.
         *  @param $date       (Optional) A date to be converted to another format.
         *                     Default: current object.
         *  @param $format_in  (Optional) The format of $date, if passed. Default: 'SQL'.
         *  
         *  @returns  A string with the formatted result.
         *
         *  @static  If $date is passed.
         */
        function getCustom( $format, $date=null, $format_in='SQL' ) {
            
            if ( $date !== null ) {
                $date = new YDDate( $date, $format_in );
                return $date->getCustom( $format );
            }
            
            // date
            $format = str_replace( '%a', $this->day_name_abbr,      $format );
            $format = str_replace( '%A', $this->day_name,           $format );
            $format = str_replace( '%b', $this->month_name_abbr,    $format );
            $format = str_replace( '%B', $this->month_name,         $format );
            $format = str_replace( '%d', $this->day_with_zero,      $format );
            $format = str_replace( '%m', $this->month_with_zero,    $format );
            $format = str_replace( '%Y', $this->year,               $format );
            $format = str_replace( '%w', $this->weekday,            $format );
            
            // time
            $format = str_replace( '%T', '%H:%M:%S',                $format );
            $format = str_replace( '%H', $this->hours_with_zero,    $format );
            $format = str_replace( '%M', $this->minutes_with_zero,  $format );
            $format = str_replace( '%S', $this->seconds_with_zero,  $format );
            
            return $format;
            
        }
        
        /**
         *  This function returns the timestamp of the object date.
         *
         *  @returns  A unix timestamp if valid, -1 otherwise.
         */
        function getTimestamp() {
            
            $this->reset();
            
            return @mktime( $this->hours,
                            $this->minutes,
                            $this->seconds,
                            $this->month,
                            $this->day,
                            $this->year );
        }
        
        /**
         *  This function returns a boolean indicating if the date and time are empty.
         *
         *  @returns  A boolean.
         */
        function isEmpty() {
            
            if ( $this->isDateEmpty() && $this->isTimeEmpty() ) {
                return true;
            }
            return false;
            
        }
        
        /**
         *  This function returns a boolean indicating if the date part if empty.
         *
         *  @returns  A boolean.
         */
        function isDateEmpty() {
            if ( ! $this->year && ! $this->month && ! $this->day ) {
                return true;
            }
            return false;
        }

        /**
         *  This function returns a boolean indicating if the time part if empty.
         *
         *  @returns  A boolean.
         */
        function isTimeEmpty() {
            if ( ! $this->hours && ! $this->minutes && ! $this->seconds ) {
                return true;
            }
            return false;
        }

        /**
         *  This function sets the date.
         *
         *  @param $date    (Optional) A YDDate object, timestamp, array or
         *                  string. If null, the current date. Default: null.
         *  @param $format  (Optional) The format name. Default: 'ISO'.
         *  @param $family  (Optional) If true, parses in all the format family. Default: true.
         */
        function set( $date=null, $format='ISO', $family=true ) {
            
            if ( $date === null ) {
                $date = time();
            }
            
            $date = $this->parse( $date, $format, $family, true );
            
            $this->year              = $date['year'];
            $this->month             = $date['month'];
            $this->day               = $date['day'];
            $this->hours             = $date['hours'];
            $this->minutes           = $date['minutes'];
            $this->seconds           = $date['seconds'];
            
            $this->day_with_zero     = substr( "00" . $this->day,     -2 );
            $this->month_with_zero   = substr( "00" . $this->month,   -2 );
            $this->hours_with_zero   = substr( "00" . $this->hours,   -2 );
            $this->minutes_with_zero = substr( "00" . $this->minutes, -2 );
            $this->seconds_with_zero = substr( "00" . $this->seconds, -2 );
            
            $this->timestamp         = @mktime( $this->hours,
                                                $this->minutes,
                                                $this->seconds,
                                                $this->month,
                                                $this->day,
                                                $this->year );
                                               
            $this->timestamp_string  = $this->year . 
                                       $this->month_with_zero .  
                                       $this->day_with_zero   . 
                                       $this->hours_with_zero   . 
                                       $this->minutes_with_zero . 
                                       $this->seconds_with_zero;
            
            if ( ! (int) $this->timestamp_string ) {
                $this->timestamp_string = '';
            }
            
            $this->weekday           = $this->getWeekDay();
            $this->quarter           = $this->getQuarter();
            $this->day_name          = $this->getDayName();
            $this->day_name_abbr     = $this->getDayName( true );
            $this->month_name        = $this->getMonthName();
            $this->month_name_abbr   = $this->getMonthName( true );
            $this->leap              = (int) $this->isLeapYear();

        }
        
        
        /**
         *  This function parses a date by it's format and returns an
         *  array with the date values.
         *
         *  @param $date    A YDDate object, timestamp, array or string.
         *                  If null, the object date. Default: null.
         *  @param $format  (Optional) The format name. Default: 'ISO'.
         *  @param $family  (Optional) If true, parses in all the format family. Default: true.
         *  @param $error   (Optional) If true, errors are triggered. Default: true.
         *
         *  @returns  An array with the date values.
         *
         *  @static  If $date is passed.
         */
        function parse( $date=null, $format='ISO', $family=true, $error=true ) {
            
            if ( $date === null ) {
                return $this->toArray();
            }
            
            $old_date = $date;
            $match = false;
            $valid_date = true;
            $valid_time = true;
            
            $month_name = null;
            
            $result = array();
            $result['year']    = $year    = 0;
            $result['month']   = $month   = 0;
            $result['day']     = $day     = 0;
            $result['hours']   = $hours   = 0;
            $result['minutes'] = $minutes = 0;
            $result['seconds'] = $seconds = 0;
            
            if ( is_string( $date ) && $date == '__NOW__' ) {
                $date = time();
            }
            
            if ( is_int( $date ) ) {
                
                $result['year']    = (int) strftime( '%Y', $date );
                $result['month']   = (int) strftime( '%m', $date );
                $result['day']     = (int) strftime( '%d', $date );
                $result['hours']   = (int) strftime( '%H', $date );
                $result['minutes'] = (int) strftime( '%M', $date );
                $result['seconds'] = (int) strftime( '%S', $date );
                
                return $result;
                
            } else if ( is_object( $date ) && is_a( $date, 'YDDate' ) ) {
                
                if ( $date->isDateEmpty() && $date->isTimeEmpty() ) {
                    return $result;
                }
                return $date->toArray();
            
            } else if ( is_array( $date ) ) {
                
                if ( empty( $date ) ) {
                    return $result;
                }
                
                if ( isset( $date['day'] ) && isset( $date['month'] ) && isset( $date['year'] ) ) {
                    
                    $day   = $date['day'];
                    $month = $date['month'];
                    $year  = $date['year'];
                    $match = true;
                    
                    if ( isset( $date['hours'] ) && isset( $date['minutes'] ) && isset( $date['seconds'] ) ) {
                        $hours    = $date['hours'];
                        $minutes  = $date['minutes'];
                        $seconds  = $date['seconds'];
                    }
                }
                
            }
            
            if ( is_string( $date ) ) {
                
                $date = trim( $date );
                
                if ( $date === YDDateFormat::get( $format, 'empty' ) ) {
                    return $result;
                }
                
                $fam = array();
                $fam[] = $format;
                
                if ( $family ) {
                    
                    $formats = YDConfig::get( 'YD_DATE_FORMATS' );
                    
                    foreach ( $formats as $name => $arr ) {
                        
                        if ( empty( $arr['parts'] ) ) {
                            continue;
                        }
                        
                        if ( preg_match( '/^' . $format . '(.+)/i', $name, $parts ) ) {
                            $fam[] = $format . $parts[1];
                        }

                    }
                }
                
                foreach ( $fam as $name ) {
                
                    $p = (array) YDDateFormat::get( $name, 'parts' );
                    $r = (array) YDDateFormat::get( $name, 'regexes' );
                    
                    if ( preg_match( YDDate::getRegex( $name, $r ), $date, $date_parts ) ) {
                        
                        foreach ( $p as $var => $num ) {
                             $$var = isset( $date_parts[ $num ] ) ? $date_parts[ $num ] : 0;
                        }
                        
                        $match = true;
                    
                        if ( $month_name ) {
                        
                            for ( $i=1; $i<=12; $i++ ) {
                                
                                $month_abbr = strtolower( strftime( '%b', mktime( 0, 0, 0, $i, 1, 2000 ) ) );
                                if ( substr( strtolower( $month_name ), 0, 3 ) == substr( $month_abbr, 0, 3 ) ) {
                                    $month = $i;
                                    break;
                                }
                            }
                        }
                        
                        break;
                    }
                }
            }
            
            $valid_date = YDDate::isValidDate( $day,   $month,   $year );
            $valid_time = YDDate::isValidTime( $hours, $minutes, $seconds );
            
            if ( ! $match || ! $valid_date || ! $valid_time ) {
                if ( $error ) {
                    trigger_error( 'The date "' . $old_date . '" is not valid', YD_ERROR );
                }
                return false;
            }
            
            $result['year']    = (int) $year;
            $result['month']   = (int) $month;
            $result['day']     = (int) $day;
            $result['hours']   = (int) $hours;
            $result['minutes'] = (int) $minutes;
            $result['seconds'] = (int) $seconds;
            
            return $result;
            
        }
        
        /**
         *  This function returns the month name.
         *
         *  @param $abbr   (Optional) If true, returns the abbreviated month name. Default: false.
         *  @param $date   (Optional) A YDDate object, timestamp, array or string.
         *                            If null, the object. Default: null.
         *  @param $format (Optional) The format name. Default: 'ISO'.
         *  
         *  @returns  The month name.
         *
         *  @static   If $date is passed.
         */
        function getMonthName( $abbr=false, $date=null, $format='ISO' ) {
            
            if ( $date === null ) {
                $date = $this->parse( $date, $format );
            } else {
                $date = YDDate::parse( $date, $format );
            }
            
            if ( ! $date['month'] ) {
                return '';
            }
            
            if ( $abbr ) {
                return ucfirst( strftime( '%b', mktime( 0, 0, 0, $date['month'], 1, 2000 ) ) );
            }
            return ucfirst( strftime( '%B', mktime( 0, 0, 0, $date['month'], 1, 2000 ) ) );
        
        }
        
        /**
         *  This function returns the weekday name.
         *
         *  @param $abbr   (Optional) If true, returns the abbreviated weekday name. Default: false.
         *  @param $date   (Optional) A YDDate object, timestamp, array or string.
         *                            If null, the date of the object. Default: null.
         *  @param $format (Optional) The format name. Default: 'ISO'.
         *  
         *  @returns  The weekday name.
         *
         *  @static   If $date is passed.
         */
        function getDayName( $abbr=false, $date=null, $format='ISO' ) {
            
            if ( $date === null ) {
                $weekday = $this->getWeekDay( $date, $format );
            } else {
                $weekday = YDDate::getWeekDay( $date, $format );
            }
            
            for ( $i=1; $i<=7; $i++ ) {
                
                $wday = strftime( '%w', mktime( 0, 0, 0, 1, $i, 2000 ) ) ;
                
                if ( $wday == $weekday ) {
                    if ( $abbr ) {
                        return ucfirst( strftime( '%a', mktime( 0, 0, 0, 1, $i, 2000 ) ) );
                    }
                    return ucfirst( strftime( '%A', mktime( 0, 0, 0, 1, $i, 2000 ) ) );
                }
                
            }
            
            return '';
            
        }
        
        /**
         *  This function returns a boolean indicating if the year
         *  is a leap year.
         *
         *  @param $year (Optional) If empty, object's year. Default: empty.
         *
         *  @returns  True if leap year, false otherwise.
         *
         *  @static  If $year is passed.
         */
        function isLeapYear( $year='' ) {
            
            if ( ! $year ) { $year = $this->year; }
            
            if ( $year % 4 == 0 ) {
                if ( $year % 100 == 0 ) {
                    if ( $year % 400 == 0 ) {
                        return true;
                    }
                } else {
                    return true;
                }
            }
            return false;
        }
        
        /**
         *  This function returns a boolean indicating if a date is
         *  valid by it's format.
         *
         *  @param $date   (Optional) A YDDate object, timestamp, array or string.
         *  @param $format (Optional) The format name. Default: 'ISO'.
         *  @param $family (Optional) If true, checks in all the format family. Default: true.
         *  @param $empty  (Optional) If false, empty dates are not valid. Default: true.
         *
         *  @returns  True if the date is valid, false otherwise.
         *
         *  @static
         */
        function isValid( $date, $format='ISO', $family=true, $empty=true ) {
            
            if ( ! YDDate::parse( $date, $format, $family, false ) ) {
                return false;
            }
            if ( ! $empty && $date === YDDateFormat::get( $format, 'empty' ) ) {
                return false;
            }
            return true;
            
        }

        /**
         *  This function returns a boolean indicating if the time passed is valid.
         *
         *  @returns  True if valid, false otherwise.
         */
        function isValidTime( $hours, $minutes, $seconds ) {
            
            if ( $hours < 0 && $hours > 23 ) {
                return false;
            }
            if ( $minutes < 0 && $minutes > 59 ) {
                return false;
            }
            if ( $seconds < 0 && $seconds > 59 ) {
                return false;
            }
            return true;
            
        }
        
        /**
         *  This function returns a boolean indicating if the date passed is valid.
         *
         *  @returns  True if valid, false otherwise.
         */
        function isValidDate( $day, $month, $year ) {
            
            if ( ! checkdate( $month, $day, $year ) ) {
                return false;
            }
            return true;
            
        }
        
        /**
         *  This function returns the weekday as an integer.
         *  0 = Sunday - 6 = Saturday
         *
         *  @param $date   (Optional) A YDDate object, timestamp, array or string.
         *                            If null, the date of the object. Default: null.
         *  @param $format (Optional) The format. Default: 'ISO'.
         *
         *  @returns  The weeday number.
         *
         *  @static   If $date is passed.
         */
        function getWeekDay( $date=null, $format='ISO' ) {
            
            if ( $date === null ) {
                $date = $this->parse( $date, $format );
            } else {
                $date = YDDate::parse( $date, $format );
            }
            
            if ( ! $date['day'] && ! $date['month'] && ! $date['year'] ) {
                return null;
            }
            
            $arr = array( 0, 6, 2, 2, 5, 0, 3, 5, 1, 4, 6, 2, 4 );
            
            $y = substr( $date['year'], -2 );
            $a = $date['day'] + $y + ( floor( $y/4 ) ) + $arr[ $date['month'] ];
            
            if ( YDDate::isLeapYear( $date['year'] ) && $date['month'] <= 2 && $date['day'] < 29 ) {
                $a--;
            }
            while ( $a > 7 ) {
                $a -= 7;
            }
            
            if ( $date['year'] != 2000 && $date['year'] % 100 == 0 ) {
                if ( $date['year'] < 2000 ) {
                    $a += ( 2000 - $date['year'] ) / 100;
                } else {
                    $a -= 2 * ( ( $date['year'] - 2000 ) / 100 );
                }
            }
            
            return ( $a == 7 ) ? 0 : $a;
            
        }
        
        /**
         *  This function returns the quarter of the date.
         *
         *  @param $date   (Optional) A YDDate object, timestamp, array or string.
         *                            If null, the date of the object. Default: null.
         *  @param $format (Optional) The format name. Default: 'ISO'.
         *
         *  @returns  The quarter.
         *
         *  @static   If $date is passed.
         */
        function getQuarter( $date=null, $format='ISO' ) {
            
            if ( $date === null ) {
                $date = $this->parse( $date, $format );
            } else {
                $date = YDDate::parse( $date, $format );
            }
            
            switch ( $date['month'] ) {
                case 1:
                case 2:
                case 3:
                    return 1;
                case 4:
                case 5:
                case 6:
                    return 2;
                case 7:
                case 8:
                case 9:
                    return 3;
                case 10:
                case 11:
                case 12:
                    return 4;
            }
            
            return 0;
            
        }
        
        /**
         *  This function returns a regex string of the format.
         *
         *  @param $format   The format name.
         *  @param $regexes  (Optional) Regexes replacements.
         *
         *  @returns     The regex string.
         *
         *  @static
         */
        function getRegex( $format, $regexes=array() ) {
            
            $s = '([0-5]{1}[0-9]{1}|[0-9]{1})';           // seconds and minutes
            $h = '([0-1]{1}[0-9]{1}|2[0-3]{1}|[0-9]{1})'; // hours
            $d = '([0-2]{1}[0-9]{1}|30|31|[0-9]{1})';     // day
            $m = '(0[0-9]{1}|1[0-2]{1}|[0-9]{1})';        // month
            $y = '([0-9]{4})';                            // year 4 digits
            $t = '([\-\/\.\,\: ])';                       // separators
            $a = '([a-zA-Z]+)';                           // alpha
            $w = '([0-9]+)';                              // numbers
            
            // replace regexes
            if ( $regexes ) {
                foreach ( $regexes as $k => $v ) {
                    $$k = $v;
                }
            }
            
            // format string
            $string = YDDateFormat::get( $format, 'string' );
            
            // time representation
            $string = str_replace( '%T', '%H:%M:%S', $string );
            
            // separators
            $string = preg_replace( $t, $t, $string );
            
            // date
            $string = str_replace( '%a', $a, $string );
            $string = str_replace( '%A', $a, $string );
            $string = str_replace( '%b', $a, $string );
            $string = str_replace( '%B', $a, $string );
            $string = str_replace( '%d', $d, $string );
            $string = str_replace( '%m', $m, $string );
            $string = str_replace( '%Y', $y, $string );
            $string = str_replace( '%w', $w, $string );
            
            // time
            $string = str_replace( '%H', $h, $string );
            $string = str_replace( '%M', $s, $string );
            $string = str_replace( '%S', $s, $string );
            
            return '/^' . $string . '$/i';
            
        }
        
        /**
         *  This function sets the date object based on it's values
         *  in case they were modified.
         *
         *  @internal
         */
        function reset() {
            
            $set = false;
            if ( (int) $this->day_with_zero != (int) $this->day ) {
                $set = true;
            }
            if ( (int) $this->month_with_zero != (int) $this->month ) {
                $set = true;
            }
            if ( (int) $this->hours_with_zero != (int) $this->hours ) {
                $set = true;
            }
            if ( (int) $this->minutes_with_zero != (int) $this->minutes ) {
                $set = true;
            }
            if ( (int) $this->seconds_with_zero != (int) $this->seconds ) {
                $set = true;
            }
            
            if ( $set ) {
            
                $this->set( (int) $this->year  .'-'. (int) $this->month .'-'  . (int) $this->day . ' ' . 
                            (int) $this->hours .':'. (int) $this->minutes .':'. (int) $this->seconds );
            
            }
            
        }
        
        /**
         *  This function returns a boolean indicating if the date is today.
         *
         *  @returns  True if today, false otherwise.
         */
        function isToday() {
            
            $this->reset();
            
            $today = $this->year . $this->month_with_zero . $this->day_with_zero;
            
            if ( $today == YDDate::getCustom( "%Y%m%d" ) ) {
                return true;
            }
            return false;
        }
        
        /**
         *  This function returns a boolean indicating if the date is tomorrow.
         *
         *  @returns  True if tomorrow, false otherwise.
         */
        function isTomorrow() {
            
            $date = new YDDate();
            $date->addDay( 1 );
            
            $this->reset();
            
            $t = $this->year . $this->month_with_zero . $this->day_with_zero;
            $d = $date->year . $date->month_with_zero . $date->day_with_zero;
            
            if ( $t == $d ) {
                return true;
            }
            return false;
        }
        
        /**
         *  This function returns a boolean indicating if the date is yesterday.
         *
         *  @returns  True if yesterday, false otherwise.
         */
        function isYesterday() {
            
            $date = new YDDate();
            $date->addDay( -1 );
            
            $this->reset();
            
            $t = $this->year . $this->month_with_zero . $this->day_with_zero;
            $d = $date->year . $date->month_with_zero . $date->day_with_zero;
            
            if ( $t == $d ) {
                return true;
            }
            return false;
        }
        
        /**
         *  This function returns a boolean indicating if the date
         *  is in the current year.
         *
         *  @returns  True if current year, false otherwise.
         */
        function isCurrentYear() {
            if ( $this->year == YDDate::getCustom( "%Y" ) ) {
                return true;
            }
            return false;
        }
        
        /**
         *  This function returns a boolean indicating if the date
         *  is in the current month.
         *
         *  @returns  True if current month, false otherwise.
         */
        function isCurrentMonth() {
            if ( $this->month == YDDate::getCustom( "%m" ) && $this->isCurrentYear() ) {
                return true;
            }
            return false;
        }
        
        /**
         *  This function returns a boolean indicating if the date
         *  is in the current hour.
         *
         *  @returns  True if current hour, false otherwise.
         */
        function isCurrentHour() {
            if ( $this->hours == YDDate::getCustom( "%H" ) && $this->isToday() ) {
                return true;
            }
            return false;
        }
        
        /**
         *  This function returns a boolean indicating if the date
         *  is in the current minute.
         *
         *  @returns  True if current minute, false otherwise.
         */
        function isCurrentMinute() {
            if ( $this->minutes == YDDate::getCustom( "%M" ) && $this->isToday() && $this->isCurrentHour() ) {
                return true;
            }
            return false;
        }

        /**
         *  This function returns the number of complete years to the current year.
         *
         *  @returns  The number of years to today.
         */
        function getYearsToToday() {
            
            $date = new YDDate();
            
            $this->reset();
            
            $e = & $date;
            $s = & $this;
            
            if ( $this->timestamp_string > $date->timestamp_string ) {
                $s = & $date;
                $e = & $this;
            }
            
            $years  = $e->year - $s->year;
            if ( $e->month < $s->month ) {
                $years--;
            } else if ( $e->month == $s->month && $e->day < $s->day ) {
                $years--;
            }
            return $years;
            
        }
        
        /**
         *  This function sets the date to the beginning of the week.
         */
        function beginOfWeek() {
            
            $wday = $this->getWeekDay();
            if ( $wday == 0 ) {
                $this->addDay( -6 );
            } else {
                $this->addDay( -$wday+1 );
            }
            
        }
        
        /**
         *  This function sets the date to the end of the week.
         */
        function endOfWeek() {
            
            $wday = $this->getWeekDay();
            $this->addDay( 7-$wday );
            
        }
        
        /**
         *  This function returns the year day of the date.
         *
         *  @param $date   (Optional) A YDDate object, timestamp, array or string.
         *                            If null, the date of the object. Default: null.
         *  @param $format (Optional) The format name. Default: "ISO".
         *
         *  @returns  The year day.
         *
         *  @static   If $date is passed.
         */
        function getYearDay( $date=null, $format="ISO" ) {
            
            if ( $date === null ) {
                $date = $this->parse( $date, $format );
            } else {
                $date = YDDate::parse( $date, $format );
            }
            
            $yday = 0;
            for ( $i=1; $i < $date['month']; $i++ ) {
                $yday += YDDate::getDaysInMonth( $i, $date['year'] );
            }
            
            return $yday + $date['day'];
            
        }
        
        /**
         *  This function returns the number of days in the year.
         *
         *  @param $year  (Optional) If empty, object's year. Default: null.
         *
         *  @returns  The number of days in the year.
         *
         *  @static   If $year is passed.
         */
        function getDaysInYear( $year=null ) {
            
            if ( is_null( $year ) ) { $year  = $this->year;  }
            
            return YDDate::isLeapYear( $year ) ? 366 : 365;
        
        }

        /**
         *  This function returns the number of days in the month.
         *
         *  @param $month (Optional) If empty, object's month. Default: null.
         *  @param $year  (Optional) If empty, object's year. Default: null.
         *
         *  @returns  The number of days in the month.
         *
         *  @static  If $month and $year are passed.
         */
        function getDaysInMonth( $month=null, $year=null ) {
            
            if ( is_null( $month ) ) { $month = $this->month; }
            if ( is_null( $year  ) ) { $year  = $this->year;  }
            
            switch ( (int) $month ) {
                
                case 1:  return 31;
                case 2:  return YDDate::isLeapYear( $year ) ? 29 : 28;
                case 3:  return 31;
                case 4:  return 30;
                case 5:  return 31;
                case 6:  return 30;
                case 7:  return 31;
                case 8:  return 31;
                case 9:  return 30;
                case 10: return 31;
                case 11: return 30;
                case 12: return 31;
                
            }
            
            return 0;
            
        }
        
        /**
         *  This function adds a number of years to the date.
         *
         *  If the resulting day is not valid (e.g 30 Feb), the last day of the
         *  resulting month will be selected.
         *
         *  @param $value (Optional) The number of years. Default: 1.
         *
         *  @returns  The date in the "ISO" format.
         */
        function addYear( $value=1 ) {
            return $this->addMonth( $value * 12 );
        }

        /**
         *  This function adds a number of months to the date.
         *
         *  If the resulting day is not valid (e.g 30 Feb), the last day of the
         *  resulting month will be selected.
         *
         *  @param $value (Optional) The number of months. Default: 1.
         *
         *  @returns  The date in the "ISO" format.
         */
        function addMonth( $value=1 ) {
            
            if ( $value == 0 ) {
                return $this->get();
            }
            
            if ( $this->isDateEmpty() ) {
                trigger_error( 'Cannot make calculations with an empty date', YD_ERROR );
            }
            
            if ( ! is_int( $value ) ) {
                $value = intval( $value );
            }

            $this->month += $value;
            
            $this->year += floor( $this->month / 12 );
            $this->month = $this->month % 12;
            
            if ( $this->month % 12 == 0 ){
                $this->month = 12;
                $this->year--;
            }
            
            if ( $this->month < 0 ) {
                $this->month = 12 + $this->month;
            }
            
            $lastday = YDDate::getDaysInMonth( $this->month, $this->year );
            
            if ( $this->day > $lastday ) {
                $this->day = $lastday;
            }
            
            $this->reset();
            
            return $this->get();
            
        }

        /**
         *  This function adds a number of days to the date.
         *
         *  @param $value (Optional) The number of days. Default: 1.
         *
         *  @returns  The date in the 'ISO' format.
         */
        function addDay( $value=1 ) {
            
            if ( $value == 0 ) {
                return $this->get();
            }
            
            if ( ! extension_loaded( 'calendar' ) ) {
                trigger_error( 'The PHP calendar extension must be enabled', YD_ERROR );
            }
            if ( $this->isDateEmpty() ) {
                trigger_error( 'Cannot make calculations with an empty date', YD_ERROR );
            }
            
            if ( ! is_int( $value ) ) {
                $value = intval( $value );
            }
            
            $julian = GregoriantoJD( $this->month, $this->day, $this->year );
            $julian += $value;
               
            $gregorian = JDtoGregorian( $julian ); 
               
            list ( $month, $day, $year ) = split ( '[/]', $gregorian );
            
            $this->day   = $day;
            $this->month = $month;
            $this->year  = $year;
            
            $this->reset();
            
            return $this->get();
           
        }

        /**
         *  This function adds a number of hours to the date.
         *
         *  @param $value (Optional) The number of hours. Default: 1.
         *
         *  @returns  The date in the "ISO" format.
         */
        function addHour( $value=1 ) {
            return $this->addMinute( $value * 60 );
        }
        
        /**
         *  This function adds a number of minutes to the date.
         *
         *  @param $value (Optional) The number of minutes. Default: 1.
         *
         *  @returns  The date in the "ISO" format.
         */
        function addMinute( $value=1 ) {
            return $this->addSecond( $value * 60 );
        }
        
        /**
         *  This function adds a number of seconds to the date.
         *
         *  @param $value (Optional) The number of seconds. Default: 1.
         *
         *  @returns  The date in the "ISO" format.
         */
        function addSecond( $value=1 ) {
            
            if ( $value == 0 ) {
                return $this->get();
            }
            
            if ( $this->isDateEmpty() ) {
                trigger_error( 'Cannot make calculations with an empty date', YD_ERROR );
            }
            
            if ( ! is_int( $value ) ) {
                $value = intval( $value );
            }
            
            $this->seconds += $value;
            $days    = 0;
            $hours   = 0;
            $minutes = 0;
            
            if ( abs( $this->seconds ) >= 86400 ) {
                $days = floor( $this->seconds / 86400 );
                $this->seconds -= $days * 86400;
            }
            
            if ( abs( $this->seconds ) >= 3600 ) {
                $hours = floor( $this->seconds / 3600 );
                $this->seconds -= $hours * 3600;
            }
            
            if ( abs( $this->seconds ) >= 60 ) {
                $minutes = floor( $this->seconds / 60 );
                $this->seconds -= $minutes * 60;
            }
            
            if ( $this->seconds < 0 ) {
                $this->seconds = 60 + $this->seconds;
            }
            
            if ( $minutes != 0 ) {
                $this->minutes += $minutes;
                $hours += floor( $this->minutes / 60 );
                $this->minutes = $this->minutes % 60;
                
                if ( $this->minutes < 0 ) {
                    $this->minutes = 60 + $this->minutes;
                }
            }
            
            if ( $hours != 0 ) {
                $this->hours += $hours;
                $days += floor( $this->hours / 24 );
                $this->hours = $this->hours % 24;
                
                if ( $this->hours < 0 ) {
                    $this->hours = 24 + $this->hours;
                }
            }
            
            if ( $days != 0 ) {
                $this->addDay( $days );
            }
            
            $this->reset();
            
            return $this->get();
            
        }
        
        /**
         *  This function calculates the difference in years, quarters, months, 
         *  weeks, weekdays, days, hours, minutes and seconds between the object
         *  and another date passed.
         *
         *  @param  $date  The other date to be compared. A YDDate object,
         *                 timestamp, array or string. If null, the current date.
         *
         *  @returns       An array with all the difference information.
         *
         *  @todo          Not very efficient for big differences.
         */
        function getDifference( $date ) {
            
            if ( ! extension_loaded( 'calendar' ) ) {
                trigger_error( 'The PHP calendar extension must be enabled', YD_ERROR );
            }
            if ( $this->isDateEmpty() ) {
                trigger_error( 'Cannot make calculations with an empty date', YD_ERROR );
            }
            
            $start = $this;
            $end   = new YDDate( $date );
            
            $span = array();
            $span['years']    = 0;
            $span['quarters'] = 0;
            $span['months']   = 0;
            $span['weeks']    = 0;
            $span['days']     = 0;
            $span['weekdays'] = 0;
            $span['hours']    = 0;
            $span['minutes']  = 0;
            $span['seconds']  = 0;
            
            if ( $start->timestamp_string == $end->timestamp_string ) {
                return $span;
            }
            
            $julian_start = GregoriantoJD( $start->month, $start->day, $start->year );
            $julian_end   = GregoriantoJD( $end->month,   $end->day,   $end->year );
            
            $days     = abs( $julian_end - $julian_start );
            
            $years    = 0;
            $quarters = 0;
            $months   = 0;
            $weeks    = 0;
            $weekdays = 0;
            $hours    = 0;
            $minutes  = 0;
            $seconds  = 0;
            
            $neg = 1;
            $s = & $start;
            $e = & $end;
            if ( $start->timestamp_string > $end->timestamp_string ) {
                $neg = -1;
                $s = & $end;
                $e = & $start;
            } 
            
            $hours   += $e->hours - $s->hours;
            $minutes += $e->minutes - $s->minutes;
            $seconds += $e->seconds - $s->seconds;

            $hours   += $days * 24;
            $minutes += $hours * 60;
            $seconds += $minutes * 60;
            
            $count      = $days;
            $weekend    = ( $s->getWeekDay() == 0 || $s->getWeekDay() == 6 ) ? true : false;
            $monthend   = ( $s->day == YDDate::getDaysInMonth( $s->month, $s->year ) ) ? true : false;
            $quarterend = ( $s->month == 3 * $s->getQuarter() && $monthend ) ? true : false;
            $yearend    = ( $s->month === 12 && $s->day === 31 ) ? true : false;
            
            while ( $count > 0 ) {
                
                if ( ! ( $s->getWeekDay() == 0 || $s->getWeekDay() == 6 ) ) {
                    if ( $weekend ) {
                        $weeks++;
                    }
                    $weekdays++;
                    $weekend = false;
                } else {
                    $weekend = true;
                }
                
                if ( ! ( $s->day == YDDate::getDaysInMonth( $s->month, $s->year ) ) ) {
                    if ( $monthend ) {
                        $months++;
                    }
                    $monthend = false;
                } else {
                    $monthend = true;
                }
                
                if ( ! ( $s->month == 3 * $s->getQuarter() && $monthend ) ) {
                    if ( $quarterend ) {
                        $quarters++;
                    }
                    $quarterend = false;
                } else {
                    $quarterend = true;
                }
                
                if ( ! ( $s->month === 12 && $s->day === 31 ) ) {
                    if ( $yearend ) {
                        $years++;
                    }
                    $yearend = false;
                } else {
                    $yearend = true;
                }
                
                $s->addDay( 1 );
                $count--;
            }
            
            $span['years']    = $neg * $years;
            $span['quarters'] = $neg * $quarters;
            $span['months']   = $neg * $months;
            $span['weeks']    = $neg * $weeks;
            $span['days']     = $neg * $days;
            $span['weekdays'] = $neg * $weekdays;
            $span['hours']    = $neg * $hours;
            $span['minutes']  = $neg * $minutes;
            $span['seconds']  = $neg * $seconds;
            
            return $span;
            
        }
        
    }
    
    // Setting default custom output formats
    YDDateFormat::setString( 'ISO', '%Y-%m-%d %T' );
    YDDateFormat::setEmpty(  'ISO', '0000-00-00 00:00:00' );
    YDDateFormat::setParts(  'ISO', array(
        'day'     => 5,
        'month'   => 3,
        'year'    => 1,
        'hours'   => 7,
        'minutes' => 9,
        'seconds' => 11 ) );

    YDDateFormat::setString( 'ISO_DATE', '%Y-%m-%d' );
    YDDateFormat::setEmpty(  'ISO_DATE', '0000-00-00' );
    YDDateFormat::setParts(  'ISO_DATE', array(
        'day'     => 5,
        'month'   => 3,
        'year'    => 1 ) );

    YDDateFormat::setString( 'ISO_TIME', '%T' );
    YDDateFormat::setEmpty(  'ISO_TIME', '00:00:00' );
    YDDateFormat::setParts(  'ISO_TIME', array(
        'hours'     => 1,
        'minutes'   => 3,
        'seconds'   => 5 ) );

    YDDateFormat::setString( 'USA', '%m/%d/%Y %T' );
    YDDateFormat::setParts(  'USA', array(
        'day'     => 3,
        'month'   => 1,
        'year'    => 5,
        'hours'   => 7,
        'minutes' => 9,
        'seconds' => 11 ) );

    YDDateFormat::setString( 'USA_DATE', '%m/%d/%Y' );
    YDDateFormat::setParts(  'USA_DATE', array(
        'day'     => 3,
        'month'   => 1,
        'year'    => 5 ) );

    YDDateFormat::setString( 'USA_TIME', '%T' );
    YDDateFormat::setEmpty(  'USA_TIME', '00:00:00' );
    YDDateFormat::setParts(  'USA_TIME', array(
        'hours'     => 1,
        'minutes'   => 3,
        'seconds'   => 5 ) );

    YDDateFormat::setString( 'EUN', '%d.%m.%Y %T' );
    YDDateFormat::setParts(  'EUN', array(
        'day'     => 1,
        'month'   => 3,
        'year'    => 5,
        'hours'   => 7,
        'minutes' => 9,
        'seconds' => 11 ) );

    YDDateFormat::setString( 'EUN_DATE', '%d.%m.%Y' );
    YDDateFormat::setParts(  'EUN_DATE', array(
        'day'     => 1,
        'month'   => 3,
        'year'    => 5 ) );

    YDDateFormat::setString( 'EUN_TIME', '%T' );
    YDDateFormat::setEmpty(  'EUN_TIME', '00:00:00' );
    YDDateFormat::setParts(  'EUN_TIME', array(
        'hours'     => 1,
        'minutes'   => 3,
        'seconds'   => 5 ) );

    YDDateFormat::setString( 'SQL', '%Y%m%d%H%M%S' );
    YDDateFormat::setEmpty(  'SQL', '00000000000000' );
    YDDateFormat::setParts(  'SQL', array(
        'day'     => 3,
        'month'   => 2,
        'year'    => 1,
        'hours'   => 4,
        'minutes' => 5,
        'seconds' => 6 ) );
        
    YDDateFormat::setRegexes( 'SQL', array(
        's'  => '([0-5]{1}[0-9]{1})',           // seconds and minutes
        'h'  => '([0-1]{1}[0-9]{1}|2[0-3]{1})', // hours
        'd'  => '([0-2]{1}[0-9]{1}|30|31)',     // day
        'm'  => '(0[0-9]{1}|1[0-2]{1})',        // month
        ) );

    YDDateFormat::setString( 'SQL_NOSEC', '%Y%m%d%H%M' );
    YDDateFormat::setEmpty(  'SQL_NOSEC', '000000000000' );
    YDDateFormat::setParts(  'SQL_NOSEC', array(
        'day'     => 3,
        'month'   => 2,
        'year'    => 1,
        'hours'   => 4,
        'minutes' => 5 ) );
        
    YDDateFormat::setRegexes( 'SQL_NOSEC', array(
        's'  => '([0-5]{1}[0-9]{1})',           // seconds and minutes
        'h'  => '([0-1]{1}[0-9]{1}|2[0-3]{1})', // hours
        'd'  => '([0-2]{1}[0-9]{1}|30|31)',     // day
        'm'  => '(0[0-9]{1}|1[0-2]{1})',        // month
        ) );

    YDDateFormat::setString( 'SQL_DATE', '%Y%m%d' );
    YDDateFormat::setEmpty(  'SQL_DATE', '00000000' );
    YDDateFormat::setParts(  'SQL_DATE', array(
        'day'     => 3,
        'month'   => 2,
        'year'    => 1 ) );
        
    YDDateFormat::setRegexes( 'SQL_DATE', array(
        'd'  => '([0-2]{1}[0-9]{1}|30|31)',     // day
        'm'  => '(0[0-9]{1}|1[0-2]{1})',        // month
        ) );

    YDDateFormat::setString( 'SQL_TIME', '%H%M%S' );
    YDDateFormat::setEmpty(  'SQL_TIME', '000000' );
    YDDateFormat::setParts(  'SQL_TIME', array(
        'hours'   => 1,
        'minutes' => 2,
        'seconds' => 3 ) );
        
    YDDateFormat::setRegexes( 'SQL_TIME', array(
        's'  => '([0-5]{1}[0-9]{1})',           // seconds and minutes
        'h'  => '([0-1]{1}[0-9]{1}|2[0-3]{1})', // hours
        ) );


    YDDateFormat::setString( 'HUM', '%a, %d %b %Y %T' );
    YDDateFormat::setParts(  'HUM', array(
        'day'        => 3,
        'month_name' => 5,
        'year'       => 7,
        'hours'      => 9,
        'minutes'    => 11,
        'seconds'    => 13 ) );

    YDDateFormat::setString( 'HUM_NOWEEK', '%d %b %Y %T' );
    YDDateFormat::setParts(  'HUM_NOWEEK', array(
        'day'        => 1,
        'month_name' => 3,
        'year'       => 5,
        'hours'      => 7,
        'minutes'    => 9,
        'seconds'    => 11 ) );

    YDDateFormat::setString( 'HUM_DATE', '%a, %d %b %Y' );
    YDDateFormat::setParts(  'HUM_DATE', array(
        'day'        => 3,
        'month_name' => 5,
        'year'       => 7 ) );

    YDDateFormat::setString( 'HUM_DATE_NOWEEK', '%d %b %Y' );
    YDDateFormat::setParts(  'HUM_DATE_NOWEEK', array(
        'day'        => 1,
        'month_name' => 3,
        'year'       => 5 ) );

    YDDateFormat::setString( 'HUM_TIME', '%T' );
    YDDateFormat::setEmpty(  'HUM_TIME', '00:00:00' );
    YDDateFormat::setParts(  'HUM_TIME', array(
        'hours'     => 1,
        'minutes'   => 3,
        'seconds'   => 5 ) );

?>