<?php

require_once 'PEAR.php';

class Mail
{

    var $sep = "\r\n";

    function factory($driver, $params = array())
    {
        $driver = strtolower($driver);
        @include_once 'Mail/' . $driver . '.php';
        $class = 'Mail_' . $driver;
        if (class_exists($class)) {
            return new $class($params);
        } else {
            return PEAR::raiseError('Unable to find class for driver ' . $driver);
        }
    }

    function send($recipients, $headers, $body)
    {
        if (is_array($recipients)) {
            $recipients = implode(', ', $recipients);
        }

        $subject = '';
        if (isset($headers['Subject'])) {
            $subject = $headers['Subject'];
            unset($headers['Subject']);
        }

        list(,$text_headers) = Mail::prepareHeaders($headers);

        return mail($recipients, $subject, $body, $text_headers);

    }

    function prepareHeaders($headers)
    {
        $lines = array();
        $from = null;

        foreach ($headers as $key => $value) {
            if ($key === 'From') {
                include_once 'Mail/RFC822.php';

                $addresses = Mail_RFC822::parseAddressList($value, 'localhost',
                                                           false);
                $from = $addresses[0]->mailbox . '@' . $addresses[0]->host;

                if (strstr($from, ' ')) {
                    return false;
                }

                $lines[] = $key . ': ' . $value;
            } elseif ($key === 'Received') {
                array_unshift($lines, $key . ': ' . $value);
            } else {
                $lines[] = $key . ': ' . $value;
            }
        }

        return array($from, join($this->sep, $lines) . $this->sep);
    }

    function parseRecipients($recipients)
    {
        include_once 'Mail/RFC822.php';

        if (is_array($recipients)) {
            $recipients = implode(', ', $recipients);
        }

        $addresses = Mail_RFC822::parseAddressList($recipients, 'localhost', false);
        $recipients = array();
        if (is_array($addresses)) {
            foreach ($addresses as $ob) {
                $recipients[] = $ob->mailbox . '@' . $ob->host;
            }
        }

        return $recipients;
    }

}
?>
