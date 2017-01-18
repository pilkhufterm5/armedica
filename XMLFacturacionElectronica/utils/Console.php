<?php
/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Console
 *
 * @author roberto
 */
class Console {
    //put your code here
    public static function execOutput($command, $errorMessage='') {
        //$command .= ' 2>&1';
        $output;
        $error;
        exec($command, $output, $error);
        if($error){
            $consoleOutput = '';
            for($i = 0; $i < count($output); $i++){
                $consoleOutput .= " (" . ($i+1) . "):" .  $output[$i];
            }
            throw new Exception("El comando '$command' se ejecuto con errores. Codigo de error del comando: " . ($errorMessage?($errorMessage . ' (php)'):($error . ' (linux)')) . ($consoleOutput?(' Salida de consola: ' . $consoleOutput):''));
        }
        return @$output[0];
    }
    
}
?>
