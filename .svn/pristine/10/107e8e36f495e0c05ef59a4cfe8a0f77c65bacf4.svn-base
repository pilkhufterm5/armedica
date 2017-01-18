<?php
/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Converter
 *
 * @author roberto
 */
class Converter {
    //put your code here
    public static function hex2Ascii($str){
        $p = '';
        for ($i=0; $i < strlen($str); $i=$i+2)
            $p .= chr(hexdec(substr($str, $i, 2)));
        return $p;
    }


    public static function phpToMysqlTimestamp($timestampInSeconds){
        $mysqlTimestamp= date("Y-m-d H:i:s", $timestampInSeconds);
        return $mysqlTimestamp;
    }
}
?>
