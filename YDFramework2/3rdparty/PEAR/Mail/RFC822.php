<?php

class Mail_RFC822 {

    var $address = '';
    var $default_domain = 'localhost';
    var $nestGroups = true;
    var $validate = true;
    var $addresses = array();
    var $structure = array();
    var $error = null;
    var $index = null;
    var $num_groups = 0;
    var $mailRFC822 = true;
    var $limit = null;

    function Mail_RFC822($address = null, $default_domain = null, $nest_groups = null, $validate = null, $limit = null)
    {
        if (isset($address))        $this->address        = $address;
        if (isset($default_domain)) $this->default_domain = $default_domain;
        if (isset($nest_groups))    $this->nestGroups     = $nest_groups;
        if (isset($validate))       $this->validate       = $validate;
        if (isset($limit))          $this->limit          = $limit;
    }

    function parseAddressList($address = null, $default_domain = null, $nest_groups = null, $validate = null, $limit = null)
    {

        if (!isset($this->mailRFC822)) {
            $obj = new Mail_RFC822($address, $default_domain, $nest_groups, $validate, $limit);
            return $obj->parseAddressList();
        }

        if (isset($address))        $this->address        = $address;
        if (isset($default_domain)) $this->default_domain = $default_domain;
        if (isset($nest_groups))    $this->nestGroups     = $nest_groups;
        if (isset($validate))       $this->validate       = $validate;
        if (isset($limit))          $this->limit          = $limit;

        $this->structure  = array();
        $this->addresses  = array();
        $this->error      = null;
        $this->index      = null;

        while ($this->address = $this->_splitAddresses($this->address)) {
            continue;
        }

        if ($this->address === false || isset($this->error)) {
            require_once 'PEAR.php';
            return PEAR::raiseError($this->error);
        }

        for ($i = 0; $i < count($this->addresses); $i++) {
            if (($return = $this->_validateAddress($this->addresses[$i])) === false
                || isset($this->error)) {
                require_once 'PEAR.php';
                return PEAR::raiseError($this->error);
            }

            if (!$this->nestGroups) {
                $this->structure = array_merge($this->structure, $return);
            } else {
                $this->structure[] = $return;
            }
        }

        return $this->structure;
    }

    function _splitAddresses($address)
    {

        if (!empty($this->limit) AND count($this->addresses) == $this->limit) {
            return '';
        }

        if ($this->_isGroup($address) && !isset($this->error)) {
            $split_char = ';';
            $is_group   = true;
        } elseif (!isset($this->error)) {
            $split_char = ',';
            $is_group   = false;
        } elseif (isset($this->error)) {
            return false;
        }

        $parts  = explode($split_char, $address);
        $string = $this->_splitCheck($parts, $split_char);

        if ($is_group) {

            if (strpos($string, ':') === false) {
                $this->error = 'Invalid address: ' . $string;
                return false;
            }

            if (!$this->_splitCheck(explode(':', $string), ':')) {
                return false;
            }

            $this->num_groups++;
        }

        $this->addresses[] = array(
                                   'address' => trim($string),
                                   'group'   => $is_group
                                   );

        $address = trim(substr($address, strlen($string) + 1));

        if ($is_group && substr($address, 0, 1) == ','){
            $address = trim(substr($address, 1));
            return $address;

        } elseif (strlen($address) > 0) {
            return $address;

        } else {
            return '';
        }

        return false;
    }

    function _isGroup($address)
    {
        $parts  = explode(',', $address);
        $string = $this->_splitCheck($parts, ',');
        if (count($parts = explode(':', $string)) > 1) {
            $string2 = $this->_splitCheck($parts, ':');
            return ($string2 !== $string);
        } else {
            return false;
        }
    }

    function _splitCheck($parts, $char)
    {
        $string = $parts[0];

        for ($i = 0; $i < count($parts); $i++) {
            if ($this->_hasUnclosedQuotes($string)
                || $this->_hasUnclosedBrackets($string, '<>')
                || $this->_hasUnclosedBrackets($string, '[]')
                || $this->_hasUnclosedBrackets($string, '()')
                || substr($string, -1) == '\\') {
                if (isset($parts[$i + 1])) {
                    $string = $string . $char . $parts[$i + 1];
                } else {
                    $this->error = 'Invalid address spec. Unclosed bracket or quotes';
                    return false;
                }
            } else {
                $this->index = $i;
                break;
            }
        }

        return $string;
    }

    function _hasUnclosedQuotes($string)
    {
        $string     = explode('"', $string);
        $string_cnt = count($string);

        for ($i = 0; $i < (count($string) - 1); $i++)
            if (substr($string[$i], -1) == '\\')
                $string_cnt--;

        return ($string_cnt % 2 === 0);
    }

    function _hasUnclosedBrackets($string, $chars)
    {
        $num_angle_start = substr_count($string, $chars[0]);
        $num_angle_end   = substr_count($string, $chars[1]);

        $this->_hasUnclosedBracketsSub($string, $num_angle_start, $chars[0]);
        $this->_hasUnclosedBracketsSub($string, $num_angle_end, $chars[1]);

        if ($num_angle_start < $num_angle_end) {
            $this->error = 'Invalid address spec. Unmatched quote or bracket (' . $chars . ')';
            return false;
        } else {
            return ($num_angle_start > $num_angle_end);
        }
    }

    function _hasUnclosedBracketsSub($string, &$num, $char)
    {
        $parts = explode($char, $string);
        for ($i = 0; $i < count($parts); $i++){
            if (substr($parts[$i], -1) == '\\' || $this->_hasUnclosedQuotes($parts[$i]))
                $num--;
            if (isset($parts[$i + 1]))
                $parts[$i + 1] = $parts[$i] . $char . $parts[$i + 1];
        }

        return $num;
    }

    function _validateAddress($address)
    {
        $is_group = false;
        $addresses = array();

        if ($address['group']) {
            $is_group = true;

            $parts     = explode(':', $address['address']);
            $groupname = $this->_splitCheck($parts, ':');
            $structure = array();

            if (!$this->_validatePhrase($groupname)){
                $this->error = 'Group name did not validate.';
                return false;
            } else {
                if ($this->nestGroups) {
                    $structure = new stdClass;
                    $structure->groupname = $groupname;
                }
            }

            $address['address'] = ltrim(substr($address['address'], strlen($groupname . ':')));
        }

        if ($is_group) {
            while (strlen($address['address']) > 0) {
                $parts       = explode(',', $address['address']);
                $addresses[] = $this->_splitCheck($parts, ',');
                $address['address'] = trim(substr($address['address'], strlen(end($addresses) . ',')));
            }
        } else {
            $addresses[] = $address['address'];
        }

        if (!count($addresses)){
            $this->error = 'Empty group.';
            return false;
        }

        for ($i = 0; $i < count($addresses); $i++) {
            $addresses[$i] = trim($addresses[$i]);
            if (!$this->validateMailbox($addresses[$i])) {
                if (empty($this->error)) {
                    $this->error = 'Validation failed for "' . $addresses[$i] . '"';
                }
                return false;
            }
        }

        if ($this->nestGroups) {
            if ($is_group) {
                $structure->addresses = $addresses;
            } else {
                $structure = $addresses[0];
            }

        } else {
            if ($is_group) {
                $structure = array_merge($structure, $addresses);
            } else {
                $structure = $addresses;
            }
        }

        return $structure;
    }

    function _validatePhrase($phrase)
    {
        $parts = preg_split('/[ \\x09]+/', $phrase, -1, PREG_SPLIT_NO_EMPTY);

        $phrase_parts = array();
        while (count($parts) > 0){
            $phrase_parts[] = $this->_splitCheck($parts, ' ');
            for ($i = 0; $i < $this->index + 1; $i++)
                array_shift($parts);
        }

        for ($i = 0; $i < count($phrase_parts); $i++) {
            if (substr($phrase_parts[$i], 0, 1) == '"') {
                if (!$this->_validateQuotedString($phrase_parts[$i])) {
                    return false;
                }
                continue;
            }

            if (!$this->_validateAtom($phrase_parts[$i])) return false;
        }

        return true;
    }

    function _validateAtom($atom)
    {
        if (!$this->validate) {
            return true;
        }
        if (!preg_match('/^[\\x00-\\x7E]+$/i', $atom, $matches)) {
            return false;
        }
        if (preg_match('/[][()<>@,;\\:". ]/', $atom)) {
            return false;
        }
        if (preg_match('/[\\x00-\\x1F]+/', $atom)) {
            return false;
        }
        return true;
    }

    function _validateQuotedString($qstring)
    {
        $qstring = substr($qstring, 1, -1);
        return !(preg_match('/(.)[\x0D\\\\"]/', $qstring, $matches) && $matches[1] != '\\');
    }

    function validateMailbox(&$mailbox)
    {
        $phrase  = '';
        $comment = '';
        $comments = array();

        $_mailbox = $mailbox;
        while (strlen(trim($_mailbox)) > 0) {
            $parts = explode('(', $_mailbox);
            $before_comment = $this->_splitCheck($parts, '(');
            if ($before_comment != $_mailbox) {
                $comment    = substr(str_replace($before_comment, '', $_mailbox), 1);
                $parts      = explode(')', $comment);
                $comment    = $this->_splitCheck($parts, ')');
                $comments[] = $comment;
                $_mailbox   = substr($_mailbox, strpos($_mailbox, $comment)+strlen($comment)+1);
            } else {
                break;
            }
        }

        foreach ($comments as $comment) {
            $mailbox = str_replace("($comment)", '', $mailbox);
        }

        $mailbox = trim($mailbox);

        if (substr($mailbox, -1) == '>' && substr($mailbox, 0, 1) != '<') {
            $parts  = explode('<', $mailbox);
            $name   = $this->_splitCheck($parts, '<');

            $phrase     = trim($name);
            $route_addr = trim(substr($mailbox, strlen($name.'<'), -1));

            if ($this->_validatePhrase($phrase) === false || ($route_addr = $this->_validateRouteAddr($route_addr)) === false) {
                return false;
            }

        } else {
            if (substr($mailbox, 0, 1) == '<' && substr($mailbox, -1) == '>') {
                $addr_spec = substr($mailbox, 1, -1);
            } else {
                $addr_spec = $mailbox;
            }
            if (($addr_spec = $this->_validateAddrSpec($addr_spec)) === false) {
                return false;
            }
        }

        $mbox = new stdClass();

        $mbox->personal = $phrase;
        $mbox->comment  = isset($comments) ? $comments : array();

        if (isset($route_addr)) {
            $mbox->mailbox = $route_addr['local_part'];
            $mbox->host    = $route_addr['domain'];
            $route_addr['adl'] !== '' ? $mbox->adl = $route_addr['adl'] : '';
        } else {
            $mbox->mailbox = $addr_spec['local_part'];
            $mbox->host    = $addr_spec['domain'];
        }

        $mailbox = $mbox;
        return true;
    }

    function _validateRouteAddr($route_addr)
    {
        if (strpos($route_addr, ':') !== false) {
            $parts = explode(':', $route_addr);
            $route = $this->_splitCheck($parts, ':');
        } else {
            $route = $route_addr;
        }
        if ($route === $route_addr){
            unset($route);
            $addr_spec = $route_addr;
            if (($addr_spec = $this->_validateAddrSpec($addr_spec)) === false) {
                return false;
            }
        } else {
            if (($route = $this->_validateRoute($route)) === false) {
                return false;
            }
            $addr_spec = substr($route_addr, strlen($route . ':'));
            if (($addr_spec = $this->_validateAddrSpec($addr_spec)) === false) {
                return false;
            }
        }

        if (isset($route)) {
            $return['adl'] = $route;
        } else {
            $return['adl'] = '';
        }

        $return = array_merge($return, $addr_spec);
        return $return;
    }

    function _validateRoute($route)
    {
        $domains = explode(',', trim($route));

        for ($i = 0; $i < count($domains); $i++) {
            $domains[$i] = str_replace('@', '', trim($domains[$i]));
            if (!$this->_validateDomain($domains[$i])) return false;
        }

        return $route;
    }

    function _validateDomain($domain)
    {
        $subdomains = explode('.', $domain);

        while (count($subdomains) > 0) {
            $sub_domains[] = $this->_splitCheck($subdomains, '.');
            for ($i = 0; $i < $this->index + 1; $i++)
                array_shift($subdomains);
        }

        for ($i = 0; $i < count($sub_domains); $i++) {
            if (!$this->_validateSubdomain(trim($sub_domains[$i])))
                return false;
        }

        return $domain;
    }

    function _validateSubdomain($subdomain)
    {
        if (preg_match('|^\[(.*)]$|', $subdomain, $arr)){
            if (!$this->_validateDliteral($arr[1])) return false;
        } else {
            if (!$this->_validateAtom($subdomain)) return false;
        }

        return true;
    }

    function _validateDliteral($dliteral)
    {
        return !preg_match('/(.)[][\x0D\\\\]/', $dliteral, $matches) && $matches[1] != '\\';
    }

    function _validateAddrSpec($addr_spec)
    {
        $addr_spec = trim($addr_spec);

        if (strpos($addr_spec, '@') !== false) {
            $parts      = explode('@', $addr_spec);
            $local_part = $this->_splitCheck($parts, '@');
            $domain     = substr($addr_spec, strlen($local_part . '@'));
        } else {
            $local_part = $addr_spec;
            $domain     = $this->default_domain;
        }

        if (($local_part = $this->_validateLocalPart($local_part)) === false) return false;
        if (($domain     = $this->_validateDomain($domain)) === false) return false;

        return array('local_part' => $local_part, 'domain' => $domain);
    }

    function _validateLocalPart($local_part)
    {
        $parts = explode('.', $local_part);
        $words = array();

        while (count($parts) > 0){
            $words[] = $this->_splitCheck($parts, '.');
            for ($i = 0; $i < $this->index + 1; $i++) {
                array_shift($parts);
            }
        }

        for ($i = 0; $i < count($words); $i++) {
            if ($this->_validatePhrase(trim($words[$i])) === false) return false;
        }

        return $local_part;
    }

    function approximateCount($data)
    {
        return count(preg_split('/(?<!\\\\),/', $data));
    }

    function isValidInetAddress($data, $strict = false)
    {
        $regex = $strict ? '/^([.0-9a-z_-]+)@(([0-9a-z-]+\.)+[0-9a-z]{2,4})$/i' : '/^([*+!.&#$|\'\\%\/0-9a-z^_`{}=?~:-]+)@(([0-9a-z-]+\.)+[0-9a-z]{2,4})$/i';
        if (preg_match($regex, trim($data), $matches)) {
            return array($matches[1], $matches[2]);
        } else {
            return false;
        }
    }

}
