<?php

class Mail_sendmail extends Mail {
    
    var $sendmail_path = '/usr/sbin/sendmail';
    var $sendmail_args = '';
    
    function Mail_sendmail($params)
    {
        if (isset($params['sendmail_path'])) $this->sendmail_path = $params['sendmail_path'];
        if (isset($params['sendmail_args'])) $this->sendmail_args = $params['sendmail_args'];
        $this->sep = (strstr(PHP_OS, 'WIN')) ? "\r\n" : "\n";
    }
    
    function send($recipients, $headers, $body)
    {
        $recipients = escapeShellCmd(implode(' ', $this->parseRecipients($recipients)));
        
        list($from, $text_headers) = $this->prepareHeaders($headers);
        if (!isset($from)) {
            return new PEAR_Error('No from address given.');
        } elseif (strstr($from, ' ') ||
                  strstr($from, ';') ||
                  strstr($from, '&') ||
                  strstr($from, '`')) {
            return new PEAR_Error('From address specified with dangerous characters.');
        }
        
        $result = 0;
        if (@is_executable($this->sendmail_path)) {
            $from = escapeShellCmd($from);
            $mail = popen($this->sendmail_path . (!empty($this->sendmail_args) ? ' ' . $this->sendmail_args : '') . " -f$from -- $recipients", 'w');
            fputs($mail, $text_headers);
            fputs($mail, $this->sep);
            fputs($mail, $body);
            $result = pclose($mail) >> 8 & 0xFF;
        } else {
            return new PEAR_Error('sendmail [' . $this->sendmail_path . '] not executable');
        }
        
        if ($result != 0) {
            return new PEAR_Error('sendmail returned error code ' . $result);
        }
        
        return true;
    }
    
}
?>
