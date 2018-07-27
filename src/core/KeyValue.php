<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace JRC\binn\core;

/**
 * Description of KeyValue
 *
 * @author jaredclemence
 */
class KeyValue {

    private $sizeBytes;
    private $keyString;
    private $mixedValue;

    public function __construct( $size, $keyString, $mixedValue ){
        $this->sizeBytes = $size;
        $this->keyString = $keyString;
        $this->mixedValue = $mixedValue;
    }
    public function getIndexValue(){
        return $this->mixedValue;
    }
    public function getLength(){
        return strlen( $this->sizeBytes . $this->keyString );
    }
    public function __toString() {
        return $this->sizeBytes . $this->keyString;
    }
}
