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
    include_once( dirname( __FILE__ ) . '/YDDate.php');

    /**
     *  This class defines a YDDateUtil object.
     *
     *  @author  David Bittencourt <muitocomplicado@hotmail.com>
     */
    class YDDateUtil extends YDDate {
        
        /**
         *  The constructor.
         *
         *  @param $date    (Optional) A YDDate object, timestamp, array or string.
         *                             If null, the current date.
         *  @param $format  (Optional) The format name. Default: "ISO".
         */
        function YDDateUtil( $date=null, $format="ISO" ) {
            $this->YDDate( $date, $format );
        }

        /**
         *  This function sets the date to tomorrow.
         */
        function nextDay() {
            $this->addDay( 1 );
        }
        
        /**
         *  This function sets the date to yesterday.
         */
        function prevDay() {
            $this->addDay( -1 );
        }
        
        /**
         *  This function returns a YDDate object of the next day.
         *
         *  @returns  A YDDate object of the next day.
         */
        function getNextDay() {
            
            $date = new YDDateUtil( $this );
            $date->nextDay();
            return $date;
        
        }
        
        /**
         *  This function returns a YDDate object of the previous day.
         *
         *  @returns  A YDDate object of the previous day.
         */
        function getPrevDay() {
            
            $date = new YDDateUtil( $this );
            $date->prevDay();
            return $date;
        
        }
        
        /**
         *  This function sets the date to the next month.
         */
        function nextMonth() {
            $this->addMonth( 1 );
        }
        
        /**
         *  This function sets the date to the previous month.
         */
        function prevMonth() {
            $this->addMonth( -1 );
        }
        
        /**
         *  This function returns a YDDate object of the next month.
         *
         *  @returns  A YDDate object of the next month.
         */
        function getNextMonth() {
            
            $date = new YDDateUtil( $this );
            $date->nextMonth();
            return $date;
        
        }
        
        /**
         *  This function returns a YDDate object of the previous month.
         *
         *  @returns  A YDDate object of the previous month.
         */
        function getPrevMonth() {
            
            $date = new YDDateUtil( $this );
            $date->prevMonth();
            return $date;
        
        }
        
        /**
         *  This function sets the date to the next year.
         */
        function nextYear() {
            $this->addYear( 1 );
        }
        
        /**
         *  This function sets the date to the previous year.
         */
        function prevYear() {
            $this->addYear( -1 );
        }
        
        /**
         *  This function returns a YDDate object of the next year.
         *
         *  @returns  A YDDate object of the next year.
         */
        function getNextYear() {
            
            $date = new YDDateUtil( $this );
            $date->nextYear();
            return $date;
        
        }
        
        /**
         *  This function returns a YDDate object of the previous year.
         *
         *  @returns  A YDDate object of the previous year.
         */
        function getPrevYear() {
            
            $date = new YDDateUtil( $this );
            $date->prevYear();
            return $date;
        
        }
        
        /**
         *  This function returns a YDDate object of tomorrow.
         *
         *  @returns  A YDDate object of tomorrow.
         *
         *  @static
         */
        function tomorrow() {
            
            $date = new YDDateUtil();
            $date->addDay( 1 );
            return $date;
            
        }
        
        /**
         *  This function returns a YDDate object of yesterday.
         *
         *  @returns  A YDDate object of yesterday.
         *
         *  @static
         */
        function yesterday() {
            
            $date = new YDDateUtil();
            $date->addDay( -1 );
            return $date;
            
        }

        /**
         *  This function returns a boolean indicating if the date is today.
         *
         *  @returns  True if today, false otherwise.
         */
        function isToday() {
            
            $this->reset();
            
            $today = $this->year . $this->month_with_zero . $this->day_with_zero;
            
            if ( $today == strftime( '%Y%m%d' ) ) {
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
            
            $date = new YDDateUtil();
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
            
            $date = new YDDateUtil();
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
            if ( $this->year == (int) strftime( "%Y" ) ) {
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
            if ( $this->month == (int) strftime( "%m" ) && $this->isCurrentYear() ) {
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
            if ( $this->hours == (int) strftime( "%H" ) && $this->isToday() ) {
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
            if ( $this->minutes == (int) strftime( "%M" ) && $this->isToday() && $this->isCurrentHour() ) {
                return true;
            }
            return false;
        }

        /**
         *  This function returns if the date is in a sunday.
         *
         *  @returns  True if the date is in a sunday, false otherwise.
         */
        function isSunday() {

            if ( $this->getWeekDay() == 0 ) {
                return true;
            }
            return false;
            
        }
        
        /**
         *  This function returns if the date is in a saturday.
         *
         *  @returns  True if the date is in a saturday, false otherwise.
         */
        function isSaturday() {
            
            if ( $this->getWeekDay() == 6 ) {
                return true;
            }
            return false;
            
        }
        
        /**
         *  This function returns if the date is in a friday.
         *
         *  @returns  True if the date is in a friday, false otherwise.
         */
        function isFriday() {
            
            if ( $this->getWeekDay() == 5 ) {
                return true;
            }
            return false;
            
        }
        
        /**
         *  This function returns if the date is in a thursday.
         *
         *  @returns  True if the date is in a thursday, false otherwise.
         */
        function isThursday() {
            
            if ( $this->getWeekDay() == 4 ) {
                return true;
            }
            return false;
            
        }
        
        /**
         *  This function returns if the date is in a wednesday.
         *
         *  @returns  True if the date is in a wednesday, false otherwise.
         */
        function isWednesday() {
            
            if ( $this->getWeekDay() == 3 ) {
                return true;
            }
            return false;
            
        }
        
        /**
         *  This function returns if the date is in a tuesday.
         *
         *  @returns  True if the date is in a tuesday, false otherwise.
         */
        function isTuesday() {
            
            if ( $this->getWeekDay() == 2 ) {
                return true;
            }
            return false;
            
        }
        
        /**
         *  This function returns if the date is in a monday.
         *
         *  @returns  True if the date is in a monday, false otherwise.
         */
        function isMonday() {
            
            if ( $this->getWeekDay() == 1 ) {
                return true;
            }
            return false;
            
        }
        
        /**
         *  This function returns if the date is in a weekend.
         *
         *  @returns  True if the date is in a weekend, false otherwise.
         */
        function isWeekend() {
            
            if ( $this->isSunday() || $this->isSaturday() ) {
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
         *  This function returns a boolean indicating if the date
         *  is at the end of the year.
         *
         *  @returns  True if at the end of the year, false otherwise.
         */
        function isEndOfYear() {
            
            if ( $this->month === 12 && $this->day === 31 ) {
                return true;
            }
            return false;
            
        }
        
        /**
         *  This function returns a boolean indicating if the day
         *  is at the end of the quarter.
         *
         *  @returns  True if at the end of the quarter, false otherwise.
         */
        function isEndOfQuarter() {
            
            $quarter = $this->getQuarter();
            $month   = 3 * $quarter;
            
            if ( $this->month == $month && $this->isEndOfMonth() ) {
                return true;
            }
            return false;
            
        }
        
        /**
         *  This function returns a boolean indicating if the date
         *  is at the end of the month.
         *
         *  @returns  True if at the end of the month, false otherwise.
         */
        function isEndOfMonth() {
            
            if ( $this->day === $this->getDaysInMonth( $this->month, $this->year ) ) {
                return true;
            }
            return false;
            
        }
        
        /**
         *  This function sets the date to the beginning of the month.
         */
        function beginOfMonth() {
            
            $this->day = 1;
            $this->reset();
            
        }

        /**
         *  This function sets the date to the end of the month.
         */
        function endOfMonth() {
            
            $this->day = $this->getDaysInMonth( $this->month, $this->year );
            $this->reset();
            
        }
        
        /**
         *  This function sets the date to the beginning of the year.
         */
        function beginOfYear() {
            
            $this->day = 1;
            $this->month = 1;
            $this->reset();
            
        }
        
        /**
         *  This function sets the date to the end of the year.
         */
        function endOfYear() {
            $this->day = 31;
            $this->month = 12;
            $this->reset();
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
                $yday += YDDateUtil::getDaysInMonth( $i, $date['year'] );
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
            
            if ( $value == 0 ) {
                return $this->get();
            }
            
            if ( $this->isDateEmpty() ) {
                trigger_error( 'Cannot make calculations with an empty date', YD_ERROR );
            }

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
            
            $lastday = YDDateUtil::getDaysInMonth( $this->month, $this->year );
            
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
            
            if ( $value == 0 ) {
                return $this->get();
            }
            
            if ( $this->isDateEmpty() ) {
                trigger_error( 'Cannot make calculations with an empty date', YD_ERROR );
            }
            
            if ( ! is_int( $value ) ) {
                $value = intval( $value );
            }
            
            $this->hours += $value;
            
            $days = floor( $this->hours / 24 );
            $this->hours = $this->hours % 24;
            
            if ( $this->hours < 0 ) {
                $this->hours = 24 + $this->hours;
            }
           
            if ( $days ) {
                $this->addDay( $days );
            }
            
            $this->reset();
            
            return $this->get();
            
        }
        
        /**
         *  This function adds a number of minutes to the date.
         *
         *  @param $value (Optional) The number of minutes. Default: 1.
         *
         *  @returns  The date in the "ISO" format.
         */
        function addMinute( $value=1 ) {
            
            if ( $value == 0 ) {
                return $this->get();
            }
            
            if ( $this->isDateEmpty() ) {
                trigger_error( 'Cannot make calculations with an empty date', YD_ERROR );
            }
            
            if ( ! is_int( $value ) ) {
                $value = intval( $value );
            }
            
            $this->minutes += $value;
            
            $hours = floor( $this->minutes / 60 );
            $this->minutes = $this->minutes % 60;
            
            if ( $this->minutes < 0 ) {
                $this->minutes = 60 + $this->minutes;
            }

            if ( $hours ) {
                $this->addHour( $hours );
            }
            
            $this->reset();
            
            return $this->get();
            
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
            
            $minutes = floor( $this->seconds / 60 );
            $this->seconds = $this->seconds % 60;
            
            if ( $this->seconds < 0 ) {
                $this->seconds = 60 + $this->seconds;
            }

            if ( $minutes ) {
                $this->addMinute( $minutes );
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
            $end   = new YDDateUtil( $date );
            
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
            $weekend    = $s->isWeekend();
            $monthend   = $s->isEndOfMonth();
            $quarterend = $s->isEndOfQuarter();
            $yearend    = $s->isEndOfYear();
            
            while ( $count > 0 ) {
                
                if ( ! $s->isWeekend() ) {
                    if ( $weekend ) {
                        $weeks++;
                    }
                    $weekdays++;
                    $weekend = false;
                } else {
                    $weekend = true;
                }
                
                if ( ! $s->isEndOfMonth() ) {
                    if ( $monthend ) {
                        $months++;
                    }
                    $monthend = false;
                } else {
                    $monthend = true;
                }
                
                if ( ! $s->isEndOfQuarter() ) {
                    if ( $quarterend ) {
                        $quarters++;
                    }
                    $quarterend = false;
                } else {
                    $quarterend = true;
                }
                
                if ( ! $s->isEndOfYear() ) {
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

?>