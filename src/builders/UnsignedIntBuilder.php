<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace JRC\binn\builders;
use JRC\binn\builders\NumericBuilder;
/**
 * Description of UnsignedIntBuilder
 *
 * @author jaredclemence
 */
class UnsignedIntBuilder extends NumericBuilder {
    public function make(){
        $data = $this->getData();
        $int = $this->convertBinaryToInteger( $data );
        return $int;
    }

    protected function createBinnDataStringForNativeData($nativeData) {
        $value = abs( $nativeData );
        $hex = $this->convertPositiveIntegerToHex( $value );
        $longHex = $this->expandHexToByteLength( $hex );
        return $longHex;
    }

}
