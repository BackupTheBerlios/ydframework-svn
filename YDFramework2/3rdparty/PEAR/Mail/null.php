<?php

class Mail_null extends Mail {

    function send($recipients, $headers, $body)
    {
        return true;
    }

}
