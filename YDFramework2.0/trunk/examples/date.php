<?php

	// Standard include
	require_once( dirname( __FILE__ ) . '/../YDFramework2/YDF2_init.php' );

	// Includes
	YDInclude( 'YDRequest.php' );
	YDInclude( 'YDDate.php' );

	// Class definition
	class date extends YDRequest {

		// Class constructor
		function date() {
			$this->YDRequest();
		}

		// Default action
		function actionDefault() {

			$tests = array(
				null,
				time(),
				'12/feb/2000 10:10:10',
				'12/10/2000 10:10:10',
				'12-10-2000 10:10:10',
				'12 10 2000 10:10:10',
				'2-10-2000 10:10:10',
				'10-2000 10:10:10',
				'31-02-2000 10:10:10',
				'12-10-2000 99:00:00',
				'12-10-2000 00:99:00',
				'12-10-2000 00:00:99',
				'2-10-04 10:10:10',
				'02-10-04 10:10:10',
				'23:00',
			);

			foreach ( $tests as $test ) {
				YDDebugUtil::dump( new YDDate( $test ), 'Object: ' . var_export( $test, 1 ) );
			}

			$date =  new YDDate();
			foreach ( $tests as $test ) {
				YDDebugUtil::dump( $date->parseDate( $test ), 'parseDate: ' . var_export( $test, 1 ) );
			}

			foreach ( $tests as $test ) {
				$date =  new YDDate();
				if ( $date->parseDate( $test ) ) {
					$tstamp = $date->getTimeStamp() . ' ' . $date->getFormatDate();
				} else {
					$tstamp = null;
				}
				YDDebugUtil::dump( $tstamp, 'getFormatDate: ' . var_export( $test, 1 ) );
			}

			foreach ( $tests as $test ) {
				$date =  new YDDate();
				if ( $date->parseDate( $test ) ) {
					$tstamp = $date->getTimeStamp() . ' ' . $date->getFormatStrftime();
				} else {
					$tstamp = null;
				}
				YDDebugUtil::dump( $tstamp, 'getFormatStrftime: ' . var_export( $test, 1 ) );
			}

			$date =  new YDDate();
			YDDebugUtil::dump( $date->getDayOfWeek(), 'getDayOfWeek' );
			YDDebugUtil::dump( $date->isFuture(), 'isFuture' );
			YDDebugUtil::dump( $date->isPast(), 'isPast' );
			YDDebugUtil::dump( $date->getNextWeekDay(), 'getNextWeekDay' );
			YDDebugUtil::dump( $date->getPreviousWeekDay(), 'getPreviousWeekDay' );
			YDDebugUtil::dump( $date->getNextDay(), 'getNextDay' );
			YDDebugUtil::dump( $date->getPreviousDay(), 'getPreviousDay' );

		}

	}

	// Process the request
	YDInclude( 'YDF2_process.php' );

?>