<?php

    YDInclude( 'user.php' );
    
    class admin extends user {
    
        function admin() {    
            $this->user();
            $this->registerProtected( 'is_admin', 1 );
        }
        
    }


?>