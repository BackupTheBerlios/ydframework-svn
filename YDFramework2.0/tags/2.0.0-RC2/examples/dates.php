<?php

    // Standard include
    include_once( dirname( __FILE__ ) . '/../YDFramework2/YDF2_init.php' );

    // Includes
    YDInclude( 'YDRequest.php' );
    YDInclude( 'YDDateUtil.php' );

    // Class definition
    class dates extends YDRequest {

        // Class constructor
        function dates() {
            $this->YDRequest();
        }

        // Default action
        function actionDefault() {

            // Static validation
            YDDebugUtil::dump( YDDate::get( "HUM", time() ), 'YDDate::get( "HUM", time() )' );
            YDDebugUtil::dump( YDDate::get( "EUN_DATE", time() ), 'YDDate::get( "EUN_DATE", time() )' );
            YDDebugUtil::dump(
                YDDate::get("HUM_DATE", "2004-03-12", "ISO" ), 'YDDate::get( "HUM_DATE", "2004-03-12", "ISO" )'
            );

            YDDebugUtil::dump( YDDate::now(), 'YDDate::now()' );
            YDDebugUtil::dump( YDDate::now( "EUN_DATE" ), 'YDDate::now( "EUN_DATE" )' );
            YDDebugUtil::dump( YDDate::now( "ISO_TIME" ), 'YDDate::now( "ISO_TIME" )' );
            YDDebugUtil::dump( YDDate::now( "HUM" ), 'YDDate::now( "HUM" )' );

            YDDebugUtil::dump( YDDate::isValid( "" ), 'YDDate::isValid( "" )' );
            YDDebugUtil::dump( YDDate::isValid(
                "0000-00-00 00:00:00", "ISO" ), 'YDDate::isValid( "0000-00-00 00:00:00", "ISO" )'
            );
            YDDebugUtil::dump( YDDate::isValid(
                "0000-00-00 00:00:00", "ISO", true, false ),
                'YDDate::isValid( "0000-00-00 00:00:00", "ISO", true, false )'
            );
            YDDebugUtil::dump( YDDate::isValid( "2005-5-5" ), 'YDDate::isValid( "2005-5-5" )' );
            YDDebugUtil::dump( YDDate::isValid( "2005 5 15" ), 'YDDate::isValid( "2005 5 15" )' );
            YDDebugUtil::dump( YDDate::isValid( "20050515", "SQL" ), 'YDDate::isValid( "20050515", "SQL" )' );
            YDDebugUtil::dump( YDDate::isValid( "15 5 2005", "EUN" ), 'YDDate::isValid( "15 5 2005", "EUN" )' );
            YDDebugUtil::dump( YDDate::isValid( "15 May 2005", "HUM" ), 'YDDate::isValid( "15 May 2005", "HUM" )' );
            YDDebugUtil::dump( YDDate::isValid( "5.15.2005", "USA" ), 'YDDate::isValid( "5.15.2005", "USA" )' );
            YDDebugUtil::dump( YDDate::isValid( "5.15.2005", "EUN" ), 'YDDate::isValid( "5.15.2005", "EUN" )' );
            YDDebugUtil::dump( YDDate::isValid( "5.15.2005", "HUM" ), 'YDDate::isValid( "5.15.2005", "HUM" )' );
            YDDebugUtil::dump( YDDate::isValid( "5.15.2005", "ISO" ), 'YDDate::isValid( "5.15.2005", "ISO" )' );
            YDDebugUtil::dump( YDDate::isValid( "5.15.2005", "SQL" ), 'YDDate::isValid( "5.15.2005", "SQL" )' );

            $date = new YDDateUtil();

            // Setting an empty date
            $date->set( "0000-00-00 00:00:00" );
            YDDebugUtil::dump( $date->get(), '$date->set( "0000-00-00 00:00:00" )' );

            // Setting Unix epoch
            $date->set( 0 );
            YDDebugUtil::dump( $date->get(), '$date->set( 0 )' );

            // Setting in SQL format
            $date->set( "20050515", "SQL" );
            YDDebugUtil::dump( $date->get(), '$date->set( "20050515", "SQL" )' );

            // Setting in ISO format
            $date->set( "2005-5-5" );
            YDDebugUtil::dump( $date->get(), '$date->set( "2005-5-15" )' );

            $date->set( "2005 5 15" );
            YDDebugUtil::dump( $date->get(), '$date->set( "2005 5 15" )' );

            // Setting in EUN format
            $date->set( "15 5 2005", "EUN" );
            YDDebugUtil::dump( $date->get(), '$date->set( "15 5 2005", "EUN" )' );

            // Setting in HUM format
            $date->set( "15 May 2005", "HUM" );
            YDDebugUtil::dump( $date->get(), '$date->set( "15 May 2005", "HUM" )' );

            // Setting in USA format
            $date->set( "5.15.2005", "USA" );
            YDDebugUtil::dump( $date->get(), '$date->set( "5.15.2005", "USA" )' );

            // Setting a custom format
            YDDebugUtil::dump(
                YDDateFormat::setString( "custom format", "%a %A %b %B %d %m %Y %H %M %S %T %w" ),
                'YDDateFormat::setString( "custom format", "%a %A %b %B %d %m %Y %H %M %S %T %w" )'
            );

            // Returning the date with a custom format
            YDDebugUtil::dump( $date->get( "custom format" ), '$date->get( "custom format" )' );

            // Setting the date to today
            $date->set();
            YDDebugUtil::dump( $date->get(), '$date->set()' );

            // Getting date info
            YDDebugUtil::dump( $date->get(), '$date->get()' );
            YDDebugUtil::dump( $date->isToday(), '$date->isToday()' );
            YDDebugUtil::dump( $date->isTomorrow(), '$date->isTomorrow()' );
            YDDebugUtil::dump( $date->isYesterday(), '$date->isYesterday()' );

            YDDebugUtil::dump( $date->isMonday(), '$date->isMonday()' );
            YDDebugUtil::dump( $date->isTuesday(), '$date->isTuesday()' );
            YDDebugUtil::dump( $date->isWednesday(), '$date->isWednesday()' );
            YDDebugUtil::dump( $date->isThursday(), '$date->isThursday()' );
            YDDebugUtil::dump( $date->isFriday(), '$date->isFriday()' );

            YDDebugUtil::dump( $date->isSaturday(), '$date->isSaturday()' );
            YDDebugUtil::dump( $date->isSunday(), '$date->isSunday()' );
            YDDebugUtil::dump( $date->isWeekend(), '$date->isWeekend()' );

            YDDebugUtil::dump( $date->isCurrentHour(), '$date->isCurrentHour()' );
            YDDebugUtil::dump( $date->isCurrentMinute(), '$date->isCurrentMinute()' );
            YDDebugUtil::dump( $date->isCurrentMonth(), '$date->isCurrentMonth()' );
            YDDebugUtil::dump( $date->isCurrentYear(), '$date->isCurrentYear()' );

            YDDebugUtil::dump( $date->getDayName(), '$date->getDayName()' );
            YDDebugUtil::dump( $date->getDayName( true ), '$date->getDayName( true )' );
            YDDebugUtil::dump( $date->getMonthName(), '$date->getMonthName()' );
            YDDebugUtil::dump( $date->getMonthName( true ), '$date->getMonthName( true )' );

            // Getting date info with different locale
            YDDebugUtil::dump( YDLocale::set( "ptb" ), 'YDLocale::set( "ptb" )' );
            YDDebugUtil::dump( $date->getDayName(), '$date->getDayName()' );
            YDDebugUtil::dump( $date->getDayName( true ), '$date->getDayName( true )' );
            YDDebugUtil::dump( $date->getMonthName(), '$date->getMonthName()' );
            YDDebugUtil::dump( $date->getMonthName( true ), '$date->getMonthName( true )' );

            // Comparing two dates
            $date2 = $date;
            $date2->addHour( 10 );
            $date2->addDay( 35 );

            YDDebugUtil::dump( $date->get(), 'Date 1' );
            YDDebugUtil::dump( $date2->get(), 'Date 2' );
            YDDebugUtil::dump( $date->getDifference( $date2 ), 'Difference between Dates 1 and 2' );

            // Moving
            YDDebugUtil::dump( $date->get(), '$date->get()' );
            $date->nextDay();
            YDDebugUtil::dump( $date->get(), '$date->nextDay()' );
            $date->prevDay();
            YDDebugUtil::dump( $date->get(), '$date->prevDay()' );

            // Adding values
            YDDebugUtil::dump( $date->addSecond( 70 ), '$date->addSecond( 70 )' );
            YDDebugUtil::dump( $date->addMinute( 80 ), '$date->addMinute( 80 )' );
            YDDebugUtil::dump( $date->addHour( 27 ), '$date->addHour( 27 )' );
            YDDebugUtil::dump( $date->addDay( 40 ), '$date->addDay( 40 )' );
            YDDebugUtil::dump( $date->addDay( -3 ), '$date->addDay( -3 )' );

            // Getting an array
            YDDebugUtil::dump( $date->toArray(), '$date->toArray()' );

            $date->set( "1981-11-20" );
            YDDebugUtil::dump( $date->get(), '$date->set( "1981-11-20" )' );
            YDDebugUtil::dump( $date->getYearsToToday(), '$date->getYearsToToday()' );
            
            // Should return an error
            YDDebugUtil::dump( $date->set( "no_date" ), '$date->set( "no_date" )' );

        }

    }

    // Process the request
    YDInclude( 'YDF2_process.php' ); 

?>