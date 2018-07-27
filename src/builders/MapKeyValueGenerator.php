<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace JRC\binn\builders;
use JRC\binn\builders\KeyValueByteGenerator;
/**
 * Description of MapKeyValueGenerator
 *
 * @author jaredclemence
 */
class MapKeyValueGenerator extends KeyValueByteGenerator{
    protected function makeKeyString($key): string {
        $numeric = (int)$key;
        $readableHex = dechex($numeric);
        if( strlen($readableHex )%2 ==1 ){
            $readableHex = "0" .$readableHex;
        }
        $byteRepresentation = hex2bin( $readableHex );
        while( strlen( $byteRepresentation ) < 4 ){
            $byteRepresentation = "\x00" . $byteRepresentation;
        }
        if( strlen( $byteRepresentation ) > 4 ){
            throw new \Exception("The Binn Format restricts numeric indexes to a 4-byte representation. The object provided exceeds this limit." );
        }
        return $byteRepresentation;
    }
}
