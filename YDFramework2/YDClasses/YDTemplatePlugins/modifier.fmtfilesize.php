<?php

	require_once( 'YDStringUtil.php' );

	function smarty_modifier_fmtfileize( $size ) {
		return YDStringUtil::formatFileSize( $size );
	}

?>
