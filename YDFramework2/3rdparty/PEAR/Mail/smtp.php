<?php

class Mail_smtp extends Mail {

    var $host = 'localhost';
    var $port = 25;
    var $auth = false;
    var $username = '';
    var $password = '';
    var $localhost = 'localhost';

    function Mail_smtp($params)
    {
        if (isset($params['host'])) $this->host = $params['host'];
        if (isset($params['port'])) $this->port = $params['port'];
        if (isset($params['auth'])) $this->auth = $params['auth'];
        if (isset($params['username'])) $this->username = $params['username'];
        if (isset($params['password'])) $this->password = $params['password'];
        if (isset($params['localhost'])) $this->localhost = $params['localhost'];
    }

    function send($recipients, $headers, $body)
    {
        include_once 'Net/SMTP.php';

        if (!($smtp = new Net_SMTP($this->host, $this->port, $this->localhost))) {
            return new PEAR_Error('unable to instantiate Net_SMTP object');
        }

        if (PEAR::isError($smtp->connect())) {
            return new PEAR_Error('unable to connect to smtp server ' .
                                  $this->host . ':' . $this->port);
        }

        if ($this->auth) {
            $method = is_string($this->auth) ? $this->auth : '';

            if (PEAR::isError($smtp->auth($this->username, $this->password,
                              $method))) {
                return new PEAR_Error('unable to authenticate to smtp server');
            }
        }

        list($from, $text_headers) = $this->prepareHeaders($headers);

        if (!empty($headers['Return-Path'])) {
            $from = $headers['Return-Path'];
        }

        if (!isset($from)) {
            return new PEAR_Error('No from address given');
        }

        if (PEAR::isError($smtp->mailFrom($from))) {
            return new PEAR_Error('unable to set sender to [' . $from . ']');
        }

        $recipients = $this->parseRecipients($recipients);
        foreach($recipients as $recipient) {
            if (PEAR::isError($res = $smtp->rcptTo($recipient))) {
                return new PEAR_Error('unable to add recipient [' .
                                      $recipient . ']: ' . $res->getMessage());
            }
        }

        if (PEAR::isError($smtp->data($text_headers . "\r\n" . $body))) {
            return new PEAR_Error('unable to send data');
        }

        $smtp->disconnect();
        return true;
    }
}

?>
