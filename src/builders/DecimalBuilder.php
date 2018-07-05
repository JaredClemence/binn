<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace JRC\binn\builders;

use JRC\binn\builders\NumericBuilder;
use JRC\binn\BinaryStringAtom;

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

}
