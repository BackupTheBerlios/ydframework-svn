<?php

class Mail_mail extends Mail
{

    var $_params = '';

    function Mail_mail($params = '')
    {

        if (is_array($params)) {
            $this->_params = join(' ', $params);
        } else {
            $this->_params = $params;
        }

        $this->sep = (strstr(PHP_OS, 'WIN')) ? "\r\n" : "\n";
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

        if (empty($this->_params) || ini_get('safe_mode')) {
            $result = mail($recipients, $subject, $body, $text_headers);
        } else {
            $result = mail($recipients, $subject, $body, $text_headers,
                           $this->_params);
        }

        if ($result === false) {
            $result = PEAR::raiseError('mail() returned failure',
                                       PEAR_MAIL_FAILED);
        }

        return $result;
    }
}
