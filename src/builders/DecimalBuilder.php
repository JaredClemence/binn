<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace JRC\binn\builders;

use JRC\binn\builders\NumericBuilder;
use JRC\binn\core\BinaryStringAtom;
use JRC\binn\builders\binary\BinaryDecimalBuilder;

/**
 * Description of DecimalBuilder
 *
 * @author jaredclemence
 */
abstract class DecimalBuilder extends NumericBuilder {

    private $signBitLength;

    /**
     *
     * @var int The bits in this field are set to all 1's to represent either Infinity (mantissa is >0) or NaN (mantissa == 0).
     */
    private $exponentBitLength;
    private $mantissaBitLength;

    public function __construct($dataByteLength) {
        parent::__construct($dataByteLength);
        $bitLength = $dataByteLength * 8;
        $this->signBitLength = 1;
        $this->exponentBitLength = $width = $this->calculateExponentWidth($dataByteLength);
        $this->mantissaBitLength = $bitLength - 1 - $width;
    }

    private function calculateExponentWidth($byteLength) {
        $bits = $byteLength * 8;
        $width = round(4 * log($bits, 2)) - 13;
        //IEEE 754-1985 provides exceptions for 32-bit and 16-bit formats.
        if ($bits == 32) {
            $width = 8;
        } else if ($bits == 16) {
            $width = 5;
        }
        return $width;
    }

    protected function createBinnDataStringForNativeData($nativeData) {
        list( $binaryDecimalString, $exponent) = $this->createBase2ScientificNotation($nativeData);
        $mantissa = $this->convertReadableBinaryStringToMantissa($binaryDecimalString);
        $exponentBitString = $this->makeExponentBitString( $exponent );
        $signByte = $this->getSignFlag($nativeData);
        $binnContainer = $this->createBinnContainerFromComponents($signByte, $exponentBitString, $mantissa);
        return $binnContainer;
    }

    private function createBase2ScientificNotation($nativeData) {
        $positiveData = abs($nativeData);
        $base2Decimal = $this->calculateBase2Decimal($positiveData, 2 * $this->mantissaBitLength);
        return $this->findDigitAndExponent($base2Decimal);
    }

    private function calculateBase2Decimal($value, $bitLength) {
        $posVal = abs( $value );
        $wholePart = floor($posVal);
        $fraction = $posVal - $wholePart;
        $builder = new BinaryDecimalBuilder($bitLength);
        $builder->setWholePart($wholePart);
        $builder->addFractionPart($fraction);
        $binaryString = $builder->getBinaryString();
        unset( $builder );
        return $binaryString;
    }

    private function findDigitAndExponent($decimal) {
        $exponent = 0;
        $parts = \explode(".", $decimal);
        $wholePart = $parts[0];
        $fractionPart = $parts[1];
        if ((int) $wholePart > 0) {
            //find positive exponent
            while ((int) $wholePart > 1) {
                $mostSigDigits = substr($wholePart, 0, strlen($wholePart) - 1);
                $leastSigDigit = str_replace($mostSigDigits, "", $wholePart);
                $fractionPart = $leastSigDigit . $fractionPart;
                $wholePart = $mostSigDigits;
                $exponent++;
            }            

        } else {
            //find negative exponent
            while ((int) $wholePart == 0) {
                $wholePart = $fractionPart[0];
                $fractionPart = substr($fractionPart, 1);
                $exponent--;
            }
        }
        $digit = "1." . $fractionPart;
        return [$digit, $exponent];
    }

    public function make() {
        $data = $this->getData();
        $mantissa = $this->extractMantissa($data);
        $biasedExponent = $this->extractBiasedExponent($data);
        $bias = $this->calculateBias();
        $exponent = $biasedExponent - $bias;
        $value = $this->convertBinaryFractionToDecimalFraction($mantissa, $exponent);
        $multiplier = 1;
        if ($this->isNegative($data)) {
            $multiplier = -1;
        }
        return $value * $multiplier;
    }

    private function calculateBias() {
        $bitLength = $this->exponentBitLength;
        $bias = pow(2, $bitLength - 1) - 1;
        return $bias;
    }

    /**
     * Public for unit test
     */
    public function extractMantissa($data) {
        $signAndExponent = $this->extractSignAndExponentFromData($data);
        $mask = ~$signAndExponent;
        $mantissa = $data & $mask;
        return $mantissa;
    }

    private function extractBiasedExponent($data) {
        $signAndExponent = $this->extractSignAndExponentFromData($data);
        $exponentOnly = $this->removeSignBit($signAndExponent);
        //convert exponent to integer value
        $paddedExponentValue = $this->convertBinaryToInteger($exponentOnly);
        //right align exponent to get biased Exponent
        $biasedExponent = $paddedExponentValue >> $this->mantissaBitLength;
        return $biasedExponent;
    }

    private function extractSignAndExponentFromData($data) {
        $maskLength = $this->signBitLength + $this->exponentBitLength;
        $byteLength = $this->getByteLength();
        $mask = $this->makeFrontSideMask($maskLength, $byteLength);
        $signAndExponent = $data & $mask;
        return $signAndExponent;
    }

    /**
     * This function makes a mask that has one's in the highest bit-values, and 0's in the lower bit-values.
     * 
     * For example, when passed the parameters (3,1) it produces a binary string 0b11100000;
     * 
     * Public function for testing.
     * 
     * @param int $maskLength
     * @param int $bitLength
     * @return string
     */
    public function makeFrontSideMask($maskLength, $byteLength) {
        $maskValue = 0;
        $bitLength = $byteLength * 8;
        $maskString = "";
        $valueStore = 0;
        for ($i = 0; $i < $bitLength; $i++) {
            $maskValue <<= 1;
            if ($i < $maskLength) {
                $maskValue += 1;
            }
            $valueStore++;
            $binRep = BinaryStringAtom::createHumanReadableBinaryRepresentation($maskValue);
            if ($valueStore == 8) {
                $char = chr($maskValue);
                $maskString .= $char;
                $maskValue = 0;
                $valueStore = 0;
            }
        }
        return $maskString;
    }

    private function removeSignBit($byteString) {
        $firstByte = $byteString[0];
        $signRemoved = $firstByte & "\x7F";
        $byteString[0] = $signRemoved;
        return $byteString;
    }

    /**
     * Pubic for unit testing only.
     * 
     * @param type $mantissaByteString
     * @param type $integerExponent
     */
    public function convertBinaryFractionToDecimalFraction($mantissaByteString, $integerExponent) {
        $bits = $this->mantissaBitLength;
        $value = pow(2, $integerExponent);
        for ($i = 0; $i < $bits; $i++) {
            $curExponent = $integerExponent - 1 - $i;
            $bitPosition = $bits - $i - 1;
            $bitValue = $this->readBit($mantissaByteString, $bitPosition);
            $decValue = pow(2, $curExponent) * $bitValue;
            $value += $decValue;
        }
        return $value;
    }

    /**
     * Public for testing
     * @param type $byteString
     * @param type $bitPosition
     * @return type
     */
    public function readBit($byteString, $bitPosition) {
        $byteNumber = $this->findByteNumberForBitPosition($bitPosition);
        $bitPositionInByte = $bitPosition % 8;
        $pos = strlen($byteString) - 1 - $byteNumber;
        $targetByte = $byteString[$pos];
        $mask = chr(1 << $bitPositionInByte);
        $filteredByte = $mask & $targetByte;
        $bitValue = 0;
        if (ord($filteredByte) > 0) {
            $bitValue = 1;
        }
        return $bitValue;
    }

    /**
     * Public for unit test
     */
    public function findByteNumberForBitPosition($bitPosition) {
        $float = $bitPosition / 8;
        $bitNumber = (int) floor($float);
        return $bitNumber;
    }

    private function convertReadableBinaryStringToMantissa($binaryDecimalString) {
        $parts = explode( ".", $binaryDecimalString );
        $mantissaString = $this->fitMantissaStringToMantissaBitLength( $parts[1] );
        $mantissa = "";
        $byteLength = ceil( $this->mantissaBitLength / 8 );
        for( $i = 0; $i < $byteLength; $i++ ){
            $lastByte = substr( $mantissaString, -8 );
            $mantissaString = substr( $mantissaString, 0, strlen( $mantissaString ) - 8 );
            while( strlen( $lastByte ) < 8 ){
                $lastByte = "0" . $lastByte;
            }
            $decValue = bindec( $lastByte );
            $char = chr( $decValue );
            $mantissa = $char . $mantissa;
        }
        return $mantissa;
    }

    private function fitMantissaStringToMantissaBitLength($mantissaString) {
        while( strlen( $mantissaString ) < $this->mantissaBitLength ) {
            $mantissaString .= "0";
        }
        $mantissaString = substr( $mantissaString, 0, $this->mantissaBitLength );
        return $mantissaString;
    }

    private function makeExponentBitString($exponent) {
        $bias = $this->calculateBias();
        $biasedExponent = $exponent + $bias;
        $exponentBitString = $this->convertBiasedExponentToHex($biasedExponent);
        $lastBitShift = $this->calculateExponentLastBitShift();
        return $this->shiftLeftXBits( $exponentBitString, $lastBitShift );
    }

    private function calculateExponentLastBitShift() {
        $mantissaBytes = ceil( $this->mantissaBitLength / 8 );
        $shift = $this->mantissaBitLength - $mantissaBytes * 8;
        if( $shift < 0 ) $shift += 8;
        return $shift;
    }

    private function shiftLeftXBits($exponentBitString, $lastBitShift) {
        $value = $this->convertBinaryToInteger($exponentBitString);
        $value <<= $lastBitShift;
        $newBitString = $this->convertPositiveIntegerToHex($value);
        return $newBitString;
    }

    public function createBinnContainerFromComponents($signByte, $exponentBitString, $mantissa) {
        $exponentBitString[0] = $exponentBitString[0] | $signByte;
        $binnContainer = $exponentBitString . $mantissa;
        if( $this->calculateExponentLastBitShift() > 0 ){
            $exponentStart = substr( $exponentBitString, 0, strlen( $exponentBitString ) - 1 );
            $exponentEnd = substr( $exponentBitString, -1 );
            $mantissa[0] = $mantissa[0] | $exponentEnd;
            $binnContainer = $exponentStart . $mantissa;
        }
        return $binnContainer;
    }

    private function convertBiasedExponentToHex($biasedExponent) {
        return $this->convertPositiveIntegerToHex($biasedExponent);
    }

    private function getSignFlag($nativeData) {
        if( $nativeData < 0 ){
            return "\x80";
        }else{
            return "\x00";
        }
    }

}
