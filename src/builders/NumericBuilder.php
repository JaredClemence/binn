<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace JRC\binn\builders;
use JRC\binn\builders\NativeBuilder;
/**
 * Description of NumericBuilder
 *
 * @author jaredclemence
 */
abstract class NumericBuilder extends NativeBuilder {
    protected function isNegative( $binaryString ){
        $firstByte = substr( $binaryString, 0, 1 );
        $negativeFlag = $binaryString & "\x80";
        $isNegative = ord($negativeFlag) == 128;
        return $isNegative;
    }
    protected function getTwosComplement( $binaryString ){
        $length = strlen( $binaryString );
        $positive = ~$binaryString;
        $result = $this->addOneToBinaryString( $positive );
        return $result;
    }

    private function addOneToBinaryString($binaryString) {
        $carry = 1;
        for( $i = strlen( $binaryString ) - 1; $i >= 0; $i-- ){
            $currentByte = $binaryString[$i];
            $currentByte = $this->addOneToBytePreserveCarry( $currentByte, $carry );
            $binaryString[$i] = $currentByte;
        }
    }

    /**
     * We add one by inserting the 1 into the first carry bit.
     * 
     * This is public for testing only.
     * 
     * @param type $currentByte
     * @param type $carry
     */
    public function addOneToBytePreserveCarry($currentByte, &$carry) {
        for( $i = 0; $i < 8; $i++ ){
            $bitMaskValue = pow( 2, $i );
            $mask = chr( $bitMaskValue );
            $filteredBit = $currentByte & $mask;
            if( $carry == 1 && ord( $filteredBit ) == 0 ){
                $currentByte |= $mask;
                $carry = 0;
            }else if($carry == 1 && ord( $filteredBit ) >= 1){
                $flipMask = ~$mask;
                $currentByte &= $flipMask;
                //carry bit remains unchanged
            } 
        }
        return $currentByte;
    }

}
