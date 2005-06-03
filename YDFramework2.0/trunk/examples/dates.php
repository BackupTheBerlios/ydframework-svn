<?php

    // Standard include
    include_once( dirname( __FILE__ ) . '/../YDFramework2/YDF2_init.php' );

    // Includes
    YDInclude( 'YDRequest.php' );
    YDInclude( 'YDDate.php' );

    // Class definition
    class dates extends YDRequest {

        // Class constructor
        function dates() {
            $this->YDRequest();
        }

        // Default action
        function actionDefault() {

            $datetime = new YDDateTime();
            $date = & $datetime->getDate();
            $time = & $datetime->getTime();
            
            YDDebugUtil::dump( $datetime->get(), '$datetime->get()' );
            YDDebugUtil::dump( $datetime->getYear(), '$datetime->getYear()' );
            YDDebugUtil::dump( $datetime->getMonth(), '$datetime->getMonth()' );
            YDDebugUtil::dump( $datetime->getDay(), '$datetime->getDay()' );
            YDDebugUtil::dump( $datetime->getHours(), '$datetime->getHours()' );
            YDDebugUtil::dump( $datetime->getMinutes(), '$datetime->getMinutes()' );
            YDDebugUtil::dump( $datetime->getSeconds(), '$datetime->getSeconds()' );
            
            YDDebugUtil::dump( $datetime->isToday(), '$datetime->isToday()' );
            YDDebugUtil::dump( $datetime->isTomorrow(), '$datetime->isTomorrow()' );
            YDDebugUtil::dump( $datetime->isYesterday(), '$datetime->isYesterday()' );
            
            YDDebugUtil::dump( $datetime->isCurrentHour(), '$datetime->isCurrentHour()' );
            YDDebugUtil::dump( $datetime->isCurrentMinute(), '$datetime->isCurrentMinute()' );
            YDDebugUtil::dump( $datetime->isCurrentMonth(), '$datetime->isCurrentMonth()' );
            YDDebugUtil::dump( $datetime->isCurrentYear(), '$datetime->isCurrentYear()' );
            
            
            YDDebugUtil::dump( ucfirst( $datetime->getMonthName() ), '$datetime->getMonthName()' );
            YDDebugUtil::dump( ucfirst( $datetime->getDayName() ), '$datetime->getDayName()' );
            YDDebugUtil::dump( ucfirst( $datetime->getMonthNameAbbr() ), '$datetime->getMonthNameAbbr()' );
            YDDebugUtil::dump( ucfirst( $datetime->getDayNameAbbr() ), '$datetime->getDayNameAbbr()' );
            
            YDLocale::set( 'ptb' );
            YDDebugUtil::dump( ucfirst( $datetime->getMonthName() ), '$datetime->getMonthName()' );
            YDDebugUtil::dump( ucfirst( $datetime->getDayName() ), '$datetime->getDayName()' );
            YDDebugUtil::dump( ucfirst( $datetime->getMonthNameAbbr() ), '$datetime->getMonthNameAbbr()' );
            YDDebugUtil::dump( ucfirst( $datetime->getDayNameAbbr() ), '$datetime->getDayNameAbbr()' );
            
            YDDebugUtil::dump( $datetime->addSecond( 1 ), '$datetime->addSecond( 1 )' );
            YDDebugUtil::dump( $datetime->addMinute( 2 ), '$datetime->addMinute( 2 )' );
            YDDebugUtil::dump( $datetime->addHour( 3 ), '$datetime->addHour( 3 )' );
            YDDebugUtil::dump( $datetime->addDay( 4 ), '$datetime->addDay( 4 )' );
            YDDebugUtil::dump( $datetime->addMonth( 5 ), '$datetime->addMonth( 5 )' );
            YDDebugUtil::dump( $datetime->addYear( 6 ), '$datetime->addYear( 6 )' );
            
            YDDebugUtil::dump( $datetime->nextDay(), '$datetime->nextDay()' );
            YDDebugUtil::dump( $datetime->prevDay(), '$datetime->prevDay()' );
            
            

        }

    }

    // Process the request
    YDInclude( 'YDF2_process.php' );

?>
