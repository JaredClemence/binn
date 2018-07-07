<?php


namespace JRC\binn;

use JRC\binn\core\BinnFactory;
use JRC\binn\core\NativeFactory;

/**
 * Main interface for working with BinnContainers.
 * 
 * This class provides a simple user interface for encoding BinnObjects and decoding them
 * again.
 * 
 * In this implementation, any "Object" subtypes are always returned as a stdClass 
 * implementation with public attributes. All "List" and "Map" types are returned as
 * arrays. Similarly, any array with a string index is written as an Object to conform 
 * with the Binn specification.
 * 
 * Due to limitations of the PHP language, some defaults have been selected for 
 * writing blindly (letting the system auto detect container subtypes). For example, 
 * PHP does not handle 64-bit decimals accurately, so this specification defaults to 
 * the FLOAT type over DOUBLES. Additionally, \DateTime objects are automatically 
 * set to the DATETIME subtype rather than the DATE or the TIME subtypes.
 * 
 * Future implementations will allow you to construct a container using specific 
 * subtypes and data.
 *
 * @author jaredclemence
 */
class BinnSpecification {
    public function write( $nativePHPData ){
        $factory = new BinnFactory();
        return $factory->blindWrite($nativePHPData);
    }
    public function read( $binnContainerString ){
        $factory = new NativeFactory();
        return $factory->read($binnContainerString);
    }
}
