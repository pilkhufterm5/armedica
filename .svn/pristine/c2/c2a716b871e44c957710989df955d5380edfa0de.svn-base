<?php
/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of JSON
 *
 * @author roberto
 */
class Json {
    //put your code here
    /**
     * Convert an object into an associative array
     *
     * This function converts an object into an associative array by iterating
     * over its public properties. Because this function uses the foreach
     * construct, Iterators are respected. It also works on arrays of objects.
     *
     * @return array
     */
    public static function object_to_array($var) {
        $result = array();
        $references = array();

        // loop over elements/properties
        foreach ($var as $key => $value) {
            // recursively convert objects
            if (is_object($value) || is_array($value)) {
                // but prevent cycles
                if (!in_array($value, $references)) {
                    $result[$key] = Json::object_to_array($value);
                    $references[] = $value;
                }
            } else {
                // simple values are untouched
                $result[$key] = $value;
            }
        }
        return $result;
    }

    //Igual que la funcion anterior pero esta si incluye propiedades privadas y protegidas
    public static function objectArray( $object ) {

        if ( is_array( $object ))
            return $object ;

        if ( !is_object( $object ))
            return false ;

        $serial = serialize( $object ) ;
        $serial = preg_replace( '/O:\d+:".+?"/' ,'a' , $serial ) ;
        if( preg_match_all( '/s:\d+:"\\0.+?\\0(.+?)"/' , $serial, $ms, PREG_SET_ORDER )) {
            foreach( $ms as $m ) {
                $serial = str_replace( $m[0], 's:'. strlen( $m[1] ) . ':"'.$m[1] . '"', $serial ) ;
            }
        }

        return @unserialize( $serial ) ;

    }

    /**
     * Convert a value to JSON
     *
     * This function returns a JSON representation of $param. It uses json_encode
     * to accomplish this, but converts objects and arrays containing objects to
     * associative arrays first. This way, objects that do not expose (all) their
     * properties directly but only through an Iterator interface are also encoded
     * correctly.
     */
    public static function json_encode2($param) {
        if (is_object($param) || is_array($param)) {
            $param = Json::object_to_array($param);
        }
        return json_encode($param);
    }
}
?>
