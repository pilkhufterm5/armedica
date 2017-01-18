<?php
/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Openssl
 *
 * @author roberto
 */
class Openssl {
    //put your code here
    private static function monthToNumber($month){
        switch($month){
            case "Jan":
                return '01';
            break;
            case "Feb":
                return '02';
            break;
            case "Mar":
                return '03';
            break;
            case "Apr":
                return '04';
            break;
            case "May":
                return '05';
            break;
            case "Jun":
                return '06';
            break;
            case "Jul":
                return '07';
            break;
            case "Aug":
                return '08';
            break;
            case "Sep":
                return '09';
            break;
            case "Oct":
                return '10';
            break;
            case "Nov":
                return '11';
            break;
            case "Dec":
                return '12';
            break;
        }
    }

    public static function dateToPHPDate($date){
        $date = preg_split ("/\s+/", $date);
        $day = $date[1];
        $month = Openssl::monthToNumber($date[0]);
        $year = $date[3];
        $time = explode(':', $date[2]);
        $hour = $time[0];
        $minute = $time[1];
        $second = $time[2];
        //"d/m/Y : H:i:s"
        $date = "$year-$month-$day : $hour:$minute:$second";
        $date = str_replace(': ', '', $date);
        $date = strtotime($date);
        return $date;
    }
    
}
?>
