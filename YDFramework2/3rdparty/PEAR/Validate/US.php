<?php

require_once 'PEAR.php';
require_once 'File.php';

class Validate_US
{

    function ssn($ssn, $high_groups = null)
    {
        $ssn = str_replace(array('-','/',' ',"\t","\n"), '', $ssn);

        if (!is_numeric($ssn) || !(strlen($ssn) == 9)) {
            return false;
        }
        $area   = intval(substr($ssn, 0, 3));
        $group  = intval(substr($ssn, 3, 2));
        $serial = intval(substr($ssn, 5, 4));

        if (is_null($high_groups)) {
            $high_groups = Validate_US::ssnGetHighGroups();
        }
        return Validate_US::ssnCheck($area, $group, $serial, $high_groups);
    }

    function ssnGroupRange($groupNumber)
    {
        if(is_array($groupNumber)){
            extract($groupNumber);
        }
        if ($groupNumber < 10) {
            if ($groupNumber % 2) {
                return 1;
            } else {
                return 3;
            }
        } else {
            if ($groupNumber % 2) {
                return 4;
            } else {
                return 2;
            }
        }
    }

    function ssnCheck($ssnCheck, $group, $serial, $high_groups)
    {
        if(is_array($ssnCheck)){
            extract($ssnCheck);
        }
        if (!($area && $group && $serial)) {
            return false;
        }

        if (!($high_group = $high_groups[$area])) {
            return false;
        }

        $high_group_range = Validate_US::ssnGroupRange($high_group);
        $group_range = Validate_US::ssnGroupRange($group);

        if ($high_group_range > $group_range) {
            return true;
        } else {
            if ($high_group_range < $group_range) {
                return false;
            } else {
                if ($high_group >= $group) {
                    return true;
                } else {
                    return false;
                }
            }
        }
    }

    function ssnGetHighGroups($uri = 'http://www.ssa.gov/foia/highgroup.htm',
                              $is_text = false)
    {
        if (!$is_text) {
            if (!$fd = @fopen($uri, 'r')) {
                trigger_error("Could not access the SSA High Groups file", E_USER_WARNING);
                return array();
            }
            $source = '';
            while ($data = fread($fd, 2048)) {
                $source .= $data;
            }
            fclose($fd);
        }

        $search = array ("'<script[^>]*?>.*?</script>'si",
                         "'<[\/\!]*?[^<>]*?>'si",
                         "'([\r\n])[\s]+'",
                         "'\*'si");

        $replace = array ('','','\\1','');

        $lines = explode("\n", preg_replace($search, $replace, $source));
        $high_groups = array();
        foreach ($lines as $line) {
            $line = trim($line);
            if ((strlen($line) == 3) && is_numeric($line)) {
                $current_group = $line;
            } elseif ((strlen($line) == 2) && is_numeric($line)) {
                $high_groups[$current_group] = $line;
            }
        }
        return $high_groups;
    }
}
?>