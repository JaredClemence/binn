<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace JRC\binn\builders;
use JRC\binn\builders\KeyValueByteGenerator;
use JRC\binn\core\KeyValue;

/**
 * Description of ObjectKeyValueGenerator
 *
 * @author jaredclemence
 */
class ObjectKeyValueGenerator extends KeyValueByteGenerator{
    /**
     * Object containers have string keys. The string key is composed of two parts.
     * The key has a 1-byte integer size and a variable length string value with no null-terminator.
     * @param type $key
     */
    protected function makeKeyString($key): string {
        //convert numbers to strings.
        $stringValue = (string)$key;
        $length = strlen( $stringValue );
        if( $length == 0 ){
            throw new \Exception("Key values must be string values of at least 1 character. Empty strings cannot be used as indices in Binn format object containers." );
        }
        if( $length > 255 ){
            throw new \Exception("Unable to write this object in Binn Format. The Binn format OBJECT container is limited to string key lengths of 255 characters or less; one of the keys in this object violate this length rule.");
        }
        $byteSize = chr( $length );
        return $byteSize . $stringValue;
    }

    protected function extractKeyBytes($truncatedString) : KeyValue {
        $size = substr( $truncatedString, 0, 1 );
        $stringLength = ord( $size );
        $keyString = substr( $truncatedString, 1, $stringLength );
        return new KeyValue( $size, $keyString, $keyString );
    }

}
