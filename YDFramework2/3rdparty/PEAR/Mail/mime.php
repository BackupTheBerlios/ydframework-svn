<?php

require_once 'PEAR.php';
require_once 'Mail/mimePart.php';

class Mail_mime
{

    var $_txtbody;
    var $_htmlbody;
    var $_mime;
    var $_multipart;
    var $_html_images = array();
    var $_parts = array();
    var $_build_params = array();
    var $_headers = array();

    function Mail_mime($crlf = "\r\n")
    {
        if (!defined('MAIL_MIME_CRLF')) {
            define('MAIL_MIME_CRLF', $crlf, true);
        }

        $this->_boundary = '=_' . md5(uniqid(time()));

        $this->_build_params = array(
                                     'text_encoding' => '7bit',
                                     'html_encoding' => 'quoted-printable',
                                     '7bit_wrap'     => 998,
                                     'html_charset'  => 'ISO-8859-1',
                                     'text_charset'  => 'ISO-8859-1',
                                     'head_charset'  => 'ISO-8859-1'
                                    );
    }

    function setTXTBody($data, $isfile = false, $append = false)
    {
        if (!$isfile) {
            if (!$append) {
                $this->_txtbody = $data;
            } else {
                $this->_txtbody .= $data;
            }
        } else {
            $cont = $this->_file2str($data);
            if (PEAR::isError($cont)) {
                return $cont;
            }
            if (!$append) {
                 $this->_txtbody = $cont;
             } else {
                 $this->_txtbody .= $cont;
             }
        }
        return true;
    }

    function setHTMLBody($data, $isfile = false)
    {
        if (!$isfile) {
            $this->_htmlbody = $data;
        } else {
            $cont = $this->_file2str($data);
            if (PEAR::isError($cont)) {
                return $cont;
            }
            $this->_htmlbody = $cont;
        }

        return true;
    }

    function addHTMLImage($file, $c_type='application/octet-stream', $name = '', $isfilename = true)
    {
        $filedata = ($isfilename === true) ? $this->_file2str($file) : $file;
        $filename = ($isfilename === true) ? basename($file) : basename($name);
        if (PEAR::isError($filedata)) {
            return $filedata;
        }
        $this->_html_images[] = array(
                                      'body'   => $filedata,
                                      'name'   => $filename,
                                      'c_type' => $c_type,
                                      'cid'    => md5(uniqid(time()))
                                     );
        return true;
    }

    function addAttachment($file, $c_type='application/octet-stream', $name = '', $isfilename = true, $encoding = 'base64')
    {
        $filedata = ($isfilename === true) ? $this->_file2str($file) : $file;
        if ($isfilename === true) {
            $filename = (!empty($name)) ? $name : $file;
        } else {
            $filename = $name;
        }
        if (empty($filename)) {
            return PEAR::raiseError('The supplied filename for the attachment can\'t be empty');
        }
        $filename = basename($filename);
        if (PEAR::isError($filedata)) {
            return $filedata;
        }

        $this->_parts[] = array(
                                'body'     => $filedata,
                                'name'     => $filename,
                                'c_type'   => $c_type,
                                'encoding' => $encoding
                               );
        return true;
    }

    function & _file2str($file_name)
    {
        if (!is_readable($file_name)) {
            return PEAR::raiseError('File is not readable ' . $file_name);
        }
        if (!$fd = fopen($file_name, 'rb')) {
            return PEAR::raiseError('Could not open ' . $file_name);
        }
        $cont = fread($fd, filesize($file_name));
        fclose($fd);
        return $cont;
    }

    function &_addTextPart(&$obj, $text){

        $params['content_type'] = 'text/plain';
        $params['encoding']     = $this->_build_params['text_encoding'];
        $params['charset']      = $this->_build_params['text_charset'];
        if (is_object($obj)) {
            return $obj->addSubpart($text, $params);
        } else {
            return new Mail_mimePart($text, $params);
        }
    }

    function &_addHtmlPart(&$obj){

        $params['content_type'] = 'text/html';
        $params['encoding']     = $this->_build_params['html_encoding'];
        $params['charset']      = $this->_build_params['html_charset'];
        if (is_object($obj)) {
            return $obj->addSubpart($this->_htmlbody, $params);
        } else {
            return new Mail_mimePart($this->_htmlbody, $params);
        }
    }

    function &_addMixedPart(){

        $params['content_type'] = 'multipart/mixed';
        return new Mail_mimePart('', $params);
    }

    function &_addAlternativePart(&$obj){

        $params['content_type'] = 'multipart/alternative';
        if (is_object($obj)) {
            return $obj->addSubpart('', $params);
        } else {
            return new Mail_mimePart('', $params);
        }
    }

    function &_addRelatedPart(&$obj){

        $params['content_type'] = 'multipart/related';
        if (is_object($obj)) {
            return $obj->addSubpart('', $params);
        } else {
            return new Mail_mimePart('', $params);
        }
    }

    function &_addHtmlImagePart(&$obj, $value){

        $params['content_type'] = $value['c_type'];
        $params['encoding']     = 'base64';
        $params['disposition']  = 'inline';
        $params['dfilename']    = $value['name'];
        $params['cid']          = $value['cid'];
        $obj->addSubpart($value['body'], $params);
    }

    function &_addAttachmentPart(&$obj, $value){

        $params['content_type'] = $value['c_type'];
        $params['encoding']     = $value['encoding'];
        $params['disposition']  = 'attachment';
        $params['dfilename']    = $value['name'];
        $obj->addSubpart($value['body'], $params);
    }

    function &get($build_params = null)
    {
        if (isset($build_params)) {
            while (list($key, $value) = each($build_params)) {
                $this->_build_params[$key] = $value;
            }
        }

        if (!empty($this->_html_images) AND isset($this->_htmlbody)) {
            foreach ($this->_html_images as $value) {
                $this->_htmlbody = str_replace($value['name'], 'cid:'.$value['cid'], $this->_htmlbody);
            }
        }

        $null        = null;
        $attachments = !empty($this->_parts)                ? TRUE : FALSE;
        $html_images = !empty($this->_html_images)          ? TRUE : FALSE;
        $html        = !empty($this->_htmlbody)             ? TRUE : FALSE;
        $text        = (!$html AND !empty($this->_txtbody)) ? TRUE : FALSE;

        switch (TRUE) {
            case $text AND !$attachments:
                $message =& $this->_addTextPart($null, $this->_txtbody);
                break;

            case !$text AND !$html AND $attachments:
                $message =& $this->_addMixedPart();

                for ($i = 0; $i < count($this->_parts); $i++) {
                    $this->_addAttachmentPart($message, $this->_parts[$i]);
                }
                break;

            case $text AND $attachments:
                $message =& $this->_addMixedPart();
                $this->_addTextPart($message, $this->_txtbody);

                for ($i = 0; $i < count($this->_parts); $i++) {
                    $this->_addAttachmentPart($message, $this->_parts[$i]);
                }
                break;

            case $html AND !$attachments AND !$html_images:
                if (isset($this->_txtbody)) {
                    $message =& $this->_addAlternativePart($null);
                       $this->_addTextPart($message, $this->_txtbody);
                    $this->_addHtmlPart($message);

                } else {
                    $message =& $this->_addHtmlPart($null);
                }
                break;

            case $html AND !$attachments AND $html_images:
                if (isset($this->_txtbody)) {
                    $message =& $this->_addAlternativePart($null);
                    $this->_addTextPart($message, $this->_txtbody);
                    $related =& $this->_addRelatedPart($message);
                } else {
                    $message =& $this->_addRelatedPart($null);
                    $related =& $message;
                }
                $this->_addHtmlPart($related);
                for ($i = 0; $i < count($this->_html_images); $i++) {
                    $this->_addHtmlImagePart($related, $this->_html_images[$i]);
                }
                break;

            case $html AND $attachments AND !$html_images:
                $message =& $this->_addMixedPart();
                if (isset($this->_txtbody)) {
                    $alt =& $this->_addAlternativePart($message);
                    $this->_addTextPart($alt, $this->_txtbody);
                    $this->_addHtmlPart($alt);
                } else {
                    $this->_addHtmlPart($message);
                }
                for ($i = 0; $i < count($this->_parts); $i++) {
                    $this->_addAttachmentPart($message, $this->_parts[$i]);
                }
                break;

            case $html AND $attachments AND $html_images:
                $message =& $this->_addMixedPart();
                if (isset($this->_txtbody)) {
                    $alt =& $this->_addAlternativePart($message);
                    $this->_addTextPart($alt, $this->_txtbody);
                    $rel =& $this->_addRelatedPart($alt);
                } else {
                    $rel =& $this->_addRelatedPart($message);
                }
                $this->_addHtmlPart($rel);
                for ($i = 0; $i < count($this->_html_images); $i++) {
                    $this->_addHtmlImagePart($rel, $this->_html_images[$i]);
                }
                for ($i = 0; $i < count($this->_parts); $i++) {
                    $this->_addAttachmentPart($message, $this->_parts[$i]);
                }
                break;

        }

        if (isset($message)) {
            $output = $message->encode();
            $this->_headers = array_merge($this->_headers, $output['headers']);

            return $output['body'];

        } else {
            return FALSE;
        }
    }

    function &headers($xtra_headers = null)
    {
        $headers['MIME-Version'] = '1.0';
        if (isset($xtra_headers)) {
            $headers = array_merge($headers, $xtra_headers);
        }
        $this->_headers = array_merge($headers, $this->_headers);

        return $this->_encodeHeaders($this->_headers);
    }

    function txtHeaders($xtra_headers = null)
    {
        $headers = $this->headers($xtra_headers);
        $ret = '';
        foreach ($headers as $key => $val) {
            $ret .= "$key: $val" . MAIL_MIME_CRLF;
        }
        return $ret;
    }

    function setSubject($subject)
    {
        $this->_headers['Subject'] = $subject;
    }

    function setFrom($email)
    {
        $this->_headers['From'] = $email;
    }

    function addCc($email)
    {
        if (isset($this->_headers['Cc'])) {
            $this->_headers['Cc'] .= ", $email";
        } else {
            $this->_headers['Cc'] = $email;
        }
    }

    function addBcc($email)
    {
        if (isset($this->_headers['Bcc'])) {
            $this->_headers['Bcc'] .= ", $email";
        } else {
            $this->_headers['Bcc'] = $email;
        }
    }
    
    function _encodeHeaders($input)
    {
        foreach ($input as $hdr_name => $hdr_value) {
            preg_match_all('/(\w*[\x80-\xFF]+\w*)/', $hdr_value, $matches);
            foreach ($matches[1] as $value) {
                $replacement = preg_replace('/([\x80-\xFF])/e', '"=" . strtoupper(dechex(ord("\1")))', $value);
                $hdr_value = str_replace($value, '=?' . $this->_build_params['head_charset'] . '?Q?' . $replacement . '?=', $hdr_value);
            }
            $input[$hdr_name] = $hdr_value;
        }
        
        return $input;
    }

}
?>
