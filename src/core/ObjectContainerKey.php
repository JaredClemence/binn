<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace JRC\binn\core;
use JRC\binn\core\Size;

/**
 * Description of ObjectContainerKey
 *
 * @author jaredclemence
 */
class ObjectContainerKey {
    public $sizeString;
    public $keyString;
    public $mixedKey;
    
    public function __construct() {
        $this->sizeString = "";
        $this->keyString = "";
        $this->mixedKey = null;
    }
    
    public function __toString() {
        return $this->sizeString . $this->keyString;
    }
    public function setSizeBytes( string $size ){
        $this->sizeString = $size;
    }
    public function setKeyBytes( string $key ){
        $this->keyString = $key;
    }

    public function readKeySize($substring, $defaultLength) {
        if( strlen( $substring ) ){
            $char = $substring[0];
            $length = $defaultLength;
            if( "\x80" & $char ){
                //4 byte string
                $length = 4;
            }
            $keyByteString = substr($substring, 0, $length);
            $this->setSizeBytes($keyByteString);
        }
    }

    public function getKeyValueLength() {
        $size = new Size();
        $size->setByteString($this->sizeString);
        return $size->getValue();
    }
    
    public function getKeySizeByteLength(){
        return strlen( $this->sizeString );
    }

    public function setKeyValue($keyText) {
        $this->keyString = $keyText;
    }
    
    public function setKey( $value ){
        $this->mixedKey = $value;
    }
    
    public function getKey(){
        return $this->mixedKey;
    }

}
