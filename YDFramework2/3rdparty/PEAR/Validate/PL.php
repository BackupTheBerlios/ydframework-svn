<?php

class Validate_PL
{

    function nip($nip)
    {
        static $weights_nip = array(6,5,7,2,3,4,5,6,7);
        $nip = str_replace(array('-','/',' ',"\t","\n"), '', $nip);
        if (!is_numeric($nip) || strlen($nip) != 10) {
            return false;
        }
        return Validate::_check_control_number($nip, $weights_nip, 11);
    }

    function bank_branch($number)
    {
        static $weights_bank_branch = array(7,1,3,9,7,11,3);
        $number = str_replace(array('-','/',' ',"\t","\n"), '', $number);
        if (!is_numeric($number) || strlen($number) != 8) {
            return false;
        }
        return Validate::_check_control_number($number, $weights_bank_branch, 10);
    }

    function pesel($pesel, &$birth)
    {
        static $weights_pesel = array(1,3,7,9,1,3,7,9,1,3);

        $birth = array(false,false);

        $pesel = str_replace(array('-','/',' ',"\t","\n"), '', $pesel);

        if (!is_numeric($pesel) || strlen($pesel) != 11) {
            return false;
        }

        if (Validate::_check_control_number($pesel, $weights_pesel, 10, 10) === false)
            return false;

        $vy = substr($pesel,0,2);
        $vm = substr($pesel,2,2);
        $vd = substr($pesel,4,2);

        if ($vm < 20)
            $vy += 1900;
        elseif ($vm < 40)
            $vy += 2000;
        elseif ($vm < 60)
            $vy += 2100;
        elseif ($vm < 80)
            $vy += 2200;
        else
            $vy += 1800;
        $vm %= 20;
        $birth[0] = "$vy-$vm-$vd";

        $gender = substr($pesel,9,1) % 2;
        $birth[1] = ($gender % 2 == 0) ? 'female' : 'male';

        return true;
    }

    function regon($regon)
    {
        static $weights_regon = array(8,9,2,3,4,5,6,7);
        static $weights_regon_local = array(2,4,8,5,0,9,7,3,6,1,2,4,8);

        $regon = str_replace(array('-','/',' ',"\t","\n"), '', $regon);

        if (!is_numeric($regon) || (strlen($regon) != 9 && strlen($regon) != 14)) {
            return false;
        }

        if (Validate::_check_control_number($regon, $weights_regon, 11) === false)
          return false;

        if (strlen($regon) == 14)
        {
            return Validate::_check_control_number($regon, $weights_regon_local, 11);
        }

        return true;
    }
}
?>
