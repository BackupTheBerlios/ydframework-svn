<?php

	function smarty_modifier_implode( $array, $separator=', ' ) {
		return implode( $separator, $array );
	}

?>
