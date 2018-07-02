<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace JRC\binn\builders;
use JRC\binn\builders\NumericBuilder;
/**
 * Description of IntBuilder
 *
 * @author jaredclemence
 */
class IntBuilder extends NumericBuilder {
    public function make(){
        $data = $this->getData();
        $multiplier = 1;
        if( $this->isNegative( $data ) ){
            $data = $this->getTwosComplement($data);
            $multiplier = -1;
        }
        $value = $this->convertBinaryToInteger($data);
        $signedValue = $multiplier * $value;
        return $signedValue;
    }
}
