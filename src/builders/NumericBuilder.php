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
    private $bytes;
    
    public function __construct( $bytes ) {
        $this->bytes = $bytes;
    }
    
    public final function write( $subtype, $nativeData ){
        $data = $this->createBinnDataStringForNativeData( $nativeData );
        $type = $subtype;
        $binnContainer = $type . $data;
        return $binnContainer;
    }
    
    abstract protected function createBinnDataStringForNativeData( $nativeData );
    
    protected function getByteLength(){
        return $this->bytes;
    }
    
    /**
     * Public for testing purposes
     * @param type $binaryString
     * @return type
     */
    public function isNegative( $binaryString ){
        $firstBit = $binaryString[0];
        $negativeFlag = $firstBit & "\x80";
        $isNegative = ord($negativeFlag) == 128;
        return $isNegative;
    }
    
    protected function getTwosComplement( $binaryString ){
        $flipped = ~$binaryString;
        $result = $this->addOneToBinaryString( $flipped );
        return $result;
    }
    
    protected function convertBinaryToInteger( $data ){
        $value = 0;
        $length = strlen( $data );
        for($i=0;$i<$length;$i++){
            $char = $data[$i];
            $value <<= 8;
            $value += ord( $char );
        }
        return $value;
    }
    
    protected function convertPositiveIntegerToHex( $integer ){
        $hexString = "";
        while( $integer > 0 ){
            $remainder = $integer % 256;
            $char = chr( $remainder );
            $hexString = $char . $hexString;
            $integer -= $remainder;
            $integer /= 256;
        }
        if( $hexString == "" ){
            $hexString = "\x00";
        }
        return $hexString;
    }

    private function addOneToBinaryString($binaryString) {
        $carry = 1;
        for( $i = strlen( $binaryString ) - 1; $i >= 0; $i-- ){
            $currentByte = $binaryString[$i];
            $currentByte = $this->addOneToBytePreserveCarry( $currentByte, $carry );
            $binaryString[$i] = $currentByte;
        }
        unset( $carry ); //discard last carry bit if one exists.
        return $binaryString;
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
    
    private function calculateBias( $nBits ){
        $bias = pow(2, ($nBits-1)) - 1;
        return $bias;
    }
    
    protected function expandHexToByteLength($hex) {
        $longHex = $this->growHexToMaxByteSize( $hex );
        $correctHex = $this->truncateHex($longHex);
        return $correctHex;
    }

    private function truncateHex($longHex) {
        $byteLength = $this->getByteLength();
        return substr( $longHex, -1 * $byteLength );
    }

    private function growHexToMaxByteSize($hex) {
        $length = $this->getByteLength();
        while( strlen( $hex ) < $length ){
            $hex = "\x00" . $hex;
        }
        return $hex;
    }

}
