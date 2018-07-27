<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace JRC\binn\builders;

use JRC\binn\builders\ContainerBuilder;
use JRC\binn\core\ObjectContainerKey;
use JRC\binn\core\Size;

use Exception;

/**
 * Description of ObjectBuilder
 *
 * @author jaredclemence
 */
class ObjectBuilder extends ContainerBuilder {
    protected function addElementAtKey(&$object, $key, $value) {
        $object->{$key} = $value;
    }

    protected function createEmptyContainer() {
        return new \stdClass();
    }

    /**
     * Extracts next field identifier from data string from lastPosition.
     * 
     * Keys are NOT null terminated. (This is different from STRING containers)
     * Keys are stored with a key length in one byte stored in front of the key.
     * 
     * @param string $data
     * @param int $lastPosition
     * @return arary [ $stringKey, $nextPosition ] 
     */
    protected function extractKey($data, $lastPosition) : ObjectContainerKey {
        $substring = substr( $data, $lastPosition );
        
        $containerKey = new ObjectContainerKey();
        $containerKey->readKeySize( $substring, 1 );
        
        $keyValueLength = $containerKey->getKeyValueLength();
        $keySizeLength = $containerKey->getKeySizeByteLength();
        
        $keyText = substr( $substring, $keySizeLength, $keyValueLength );
        $containerKey->setKeyValue( $keyText );
        $containerKey->setKey( $keyText );
        return $containerKey;
    }
    
    /**
     * BinnSpecification notes that Object keys are limited to 255 bytes in length.
     * This makes the size field unusual. The full 8 bits are used to indicate size.
     * This size field is NOT a standard BinnNumber which can be one byte OR four bytes.
     * @param string $key
     * @return string
     */
    protected function convertKeyToKeyByteString( $key ){
        $stringKey = (string) $key;
        $length = strlen( $stringKey );
        $keySizeByte = chr( $length );
        if( $length > 255 ){
            throw new Exception("Unable to write binary data in Binn format for objects with attribute names that are longer than 255 characters.");
        }
        $keyByteString = $keySizeByte . $stringKey; // no null byte on keys
        return $keyByteString;
    }
}
