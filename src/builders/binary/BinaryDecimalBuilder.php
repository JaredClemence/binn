<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace JRC\binn\builders\binary;

/**
 * This builder is unlike the other builders. It calculates binary data using strings of 1's and 0's.
 *
 * @author jaredclemence
 */
class BinaryDecimalBuilder {

    private $binaryString;
    private $maxDigitLength;
    private $sigBitLength;
    private $countSigBits;

    public function __construct($bitLength) {
        $this->binaryString = "";
        $this->maxDigitLength = $bitLength;
    }

    public function setWholePart($base10value) {
        $value = abs( $base10value );
        if ($value == 0) {
            $this->disableSigBitTracking();
        } else {
            $this->enableSigBitTracking();
        }
        $this->binaryString = decbin($value) . ".";
        $this->initializeSigBitCount();
    }

    public function addFractionPart($base10fraction) {
        $value = abs( $base10fraction );
        if( $value > 1 ){
            throw new \Exception("Fraction part must be less than 1.");
        }
        $value = $this->convertScientificNotationBase10ToStandardNotationString( $value );
        while( $this->sigBitLength < $this->maxDigitLength && bccomp( $value, "0", 100 ) == 1){
            $value = \bcmul($value, "2", 100);
            if( bccomp( $value, "1", 100 ) == 1 ){
                $this->pushBinaryValue( "1" );
                $value = \bcsub($value, "1", 100);
            }else{
                $this->pushBinaryValue( "0" );
            }
        }
    }
    
    public function getBinaryString(){
        return $this->binaryString;
    }

    private function disableSigBitTracking() {
        $this->countSigBits = false;
    }

    private function enableSigBitTracking() {
        $this->countSigBits = true;
    }

    private function pushBinaryValue($char) {
        $this->binaryString .= $char;
        if( $char == "1" ) $this->enableSigBitTracking();
        $this->incrementSigBitLength();
    }

    private function incrementSigBitLength() {
        if( $this->countSigBits == true ){
            $this->sigBitLength++;
        }
    }

    private function initializeSigBitCount() {
        $this->sigBitLength = 0;
        if( $this->countSigBits == true ){
            $this->sigBitLength = strlen( $this->binaryString ) - 1; //subtract decimal point
        }
    }

    private function convertScientificNotationBase10ToStandardNotationString($value) {
        $value = \strtoupper((string)$value);
        if( strpos($value, "E")!==false ){
            $parts = explode("E", $value );
            $exponent = (int)$parts[1];
            $sigValue = str_replace(".", "", $parts[0] );
            $padCount = abs($exponent) - 1;
            $padding = $this->repeatString("0", $padCount );
            if( $exponent < 0 ){
                $sigValue = "0." . $padding . $sigValue;
            }else{
                $sigValue .= $padding . ".";
            }
            $value = $sigValue;
        }
        return $value;
    }

    private function repeatString($string, $count) {
        $result = "";
        while( strlen( $result ) < $count ){
            $result .= $string;
        }
        return $result;
    }

}
