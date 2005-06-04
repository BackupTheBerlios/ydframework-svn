<?php

    // Standard include
    include_once( dirname( __FILE__ ) . '/../YDFramework2/YDF2_init.php' );

    // Includes
    YDInclude( 'YDRequest.php' );

 
    YDInclude( 'YDDate2.php' );
       
    // Class definition
    class dates extends YDRequest {

        // Class constructor
        function dates() {
            $this->YDRequest();
        }
        
        // Default action
        function actionDefault() {
            
            $date = new YDDate();
            
            YDDebugUtil::dump( $date->toString(), '$date->toString()' );
            YDDebugUtil::dump( $date->getYear(), '$date->getYear()' );
            YDDebugUtil::dump( $date->getMonth(), '$date->getMonth()' );
            YDDebugUtil::dump( $date->getQuarter(), '$date->getQuarter()' );
            YDDebugUtil::dump( $date->getDay(), '$date->getDay()' );
            YDDebugUtil::dump( $date->getHours(), '$date->getHours()' );
            YDDebugUtil::dump( $date->getMinutes(), '$date->getMinutes()' );
            YDDebugUtil::dump( $date->getSeconds(), '$date->getSeconds()' );
            
            YDDebugUtil::dump( $date->isToday(), '$date->isToday()' );
            YDDebugUtil::dump( $date->isTomorrow(), '$date->isTomorrow()' );
            YDDebugUtil::dump( $date->isYesterday(), '$date->isYesterday()' );
            YDDebugUtil::dump( $date->isFriday(), '$date->isFriday()' );
            YDDebugUtil::dump( $date->isSaturday(), '$date->isSaturday()' );
            YDDebugUtil::dump( $date->isSunday(), '$date->isSunday()' );
            YDDebugUtil::dump( $date->isWeekend(), '$date->isWeekend()' );
            
            YDDebugUtil::dump( $date->isCurrentHour(), '$date->isCurrentHour()' );
            YDDebugUtil::dump( $date->isCurrentMinute(), '$date->isCurrentMinute()' );
            YDDebugUtil::dump( $date->isCurrentMonth(), '$date->isCurrentMonth()' );
            YDDebugUtil::dump( $date->isCurrentYear(), '$date->isCurrentYear()' );
            
            
            YDDebugUtil::dump( ucfirst( $date->getMonthName() ), '$date->getMonthName()' );
            YDDebugUtil::dump( ucfirst( $date->getDayName() ), '$date->getDayName()' );
            YDDebugUtil::dump( ucfirst( $date->getMonthNameAbbr() ), '$date->getMonthNameAbbr()' );
            YDDebugUtil::dump( ucfirst( $date->getDayNameAbbr() ), '$date->getDayNameAbbr()' );
            
            YDDebugUtil::dump( YDLocale::set( 'ptb' ), 'YDLocale::set( \'ptb\' )' );
            YDDebugUtil::dump( ucfirst( $date->getMonthName() ), '$date->getMonthName()' );
            YDDebugUtil::dump( ucfirst( $date->getDayName() ), '$date->getDayName()' );
            YDDebugUtil::dump( ucfirst( $date->getMonthNameAbbr() ), '$date->getMonthNameAbbr()' );
            YDDebugUtil::dump( ucfirst( $date->getDayNameAbbr() ), '$date->getDayNameAbbr()' );
            
            YDDebugUtil::dump( $date->toString(), '$date->toString()' );
            YDDebugUtil::dump( $date->addSecond( 70 ), '$date->addSecond( 70 )' );
            YDDebugUtil::dump( $date->addMinute( 80 ), '$date->addMinute( 80 )' );
            YDDebugUtil::dump( $date->addHour( 27 ), '$date->addHour( 27 )' );
            YDDebugUtil::dump( $date->addDay( 40 ), '$date->addDay( 40 )' );
            YDDebugUtil::dump( $date->addMonth( 15 ), '$date->addMonth( 15 )' );
            YDDebugUtil::dump( $date->addYear( 3 ), '$date->addYear( 3 )' );
            
            YDDebugUtil::dump( $date->toArray(), '$date->toArray()' );
            
            $date2 = $date;
            $date2->addDay( 2, true );
            
            YDDebugUtil::dump( $date->toString(), '$date->toString()' );
            YDDebugUtil::dump( $date2->toString(), '$date2->toString()' );
            
            YDDebugUtil::dump( $date->getDifference( $date2 ), '$date->getDifference( $date2 )' );
            

        }

    }

    
    // Process the request
    YDInclude( 'YDF2_process.php' ); 

?>
