<?php

class Validate_ptBR
{

    function cep($cep)
    {
        return (ereg('(^[0-9]{5})-([0-9]{3})$', $cep));
    }

    function postcode($postcode, $strong=false)
    {
        return cep($postcode);
    }

    function cpf($cpf)
    {
        $cleaned = "";
        for ($i=0; $i<strlen($cpf); $i++) {
            $num = substr($cpf, $i, 1);
            if (ord($num) >= 48 && ord($num) <= 57) {
                $cleaned .= $num;
            }
        }
        $cpf = $cleaned;

        if (strlen($cpf) != 11) {
            return false;
        } elseif ($cpf == "00000000000") {
            return false;
        } else {
            $number[0]  = intval(substr($cpf, 0, 1));
            $number[1]  = intval(substr($cpf, 1, 1));
            $number[2]  = intval(substr($cpf, 2, 1));
            $number[3]  = intval(substr($cpf, 3, 1));
            $number[4]  = intval(substr($cpf, 4, 1));
            $number[5]  = intval(substr($cpf, 5, 1));
            $number[6]  = intval(substr($cpf, 6, 1));
            $number[7]  = intval(substr($cpf, 7, 1));
            $number[8]  = intval(substr($cpf, 8, 1));
            $number[9]  = intval(substr($cpf, 9, 1));
            $number[10] = intval(substr($cpf, 10, 1));

            $sum = 10*$number[0]+9*$number[1]+8*$number[2]+7*$number[3]+
                6*$number[4]+5*$number[5]+4*$number[6]+3*$number[7]+
                2*$number[8];

            $sum -= (11*(intval($sum/11)));

            if ($sum == 0 || $sum == 1) {
                $result1 = 0;
            } else {
                $result1 = 11-$sum;
            }

            if ($result1 == $number[9]) {
                $sum = $number[0]*11+$number[1]*10+$number[2]*9+$number[3]*8+
                        $number[4]*7+$number[5]*6+$number[6]*5+$number[7]*4+
                        $number[8]*3+$number[9]*2;
                $sum -= (11*(intval($sum/11)));

                if ($sum == 0 || $sum == 1) {
                    $result2 = 0;
                } else {
                    $result2 = 11-$sum;
                }

                if ($result2 == $number[10]) {
                    return true;
                } else {
                    return false;
                }
            } else {
                return false;
            }
        }
    }

    function cnpj($cnpj)
    {
        $cleaned = "";
        for ($i=0; $i<strlen($cnpj); $i++) {
            $num = substr($cnpj,$i,1);
            if (ord($num) >= 48 && ord($num) <= 57) {
                $cleaned .= $num;
            }
        }
        $cnpj = $cleaned;
        if (strlen($cnpj) != 14) {
            return false;
        } elseif ($cnpj == "00000000000000") {
            return false;
        } else {
            $number[0]  = intval(substr($cnpj, 0, 1));
            $number[1]  = intval(substr($cnpj, 1, 1));
            $number[2]  = intval(substr($cnpj, 2, 1));
            $number[3]  = intval(substr($cnpj, 3, 1));
            $number[4]  = intval(substr($cnpj, 4, 1));
            $number[5]  = intval(substr($cnpj, 5, 1));
            $number[6]  = intval(substr($cnpj, 6, 1));
            $number[7]  = intval(substr($cnpj, 7, 1));
            $number[8]  = intval(substr($cnpj, 8, 1));
            $number[9]  = intval(substr($cnpj, 9, 1));
            $number[10] = intval(substr($cnpj, 10, 1));
            $number[11] = intval(substr($cnpj, 11, 1));
            $number[12] = intval(substr($cnpj, 12, 1));
            $number[13] = intval(substr($cnpj, 13, 1));

            $sum = $number[0]*5+$number[1]*4+$number[2]*3+$number[3]*2+
                   $number[4]*9+$number[5]*8+$number[6]*7+$number[7]*6+
                   $$number[8]*5+$number[9]*4+$number[10]*3+$number[11]*2;

            $sum -= (11*(intval($sum/11)));

            if ($sum == 0 || $sum == 1) {
                $result1 = 0;
            } else {
                $result1 = 11-$sum;
            }

            if ($result1 == $number[12]) {
                $sum = $number[0]*6+$number[1]*5+$number[2]*4+$number[3]*3+
                        $number[4]*2+$number[5]*9+$number[6]*8+$number[7]*7+
                        $number[8]*6+$number[9]*5+$number[10]*4+$number[11]*3+
                        $number[12]*2;
                $sum -= (11*(intval($sum/11)));
                if ($sum == 0 || $sum == 1) {
                    $result2 = 0;
                } else {
                    $result2 = 11-$sum;
                }

                if ($result2 == $number[13]) {
                    return true;
                } else {
                    return false;
                }
            } else {
                return false;
            }
        }
    }
}
?>