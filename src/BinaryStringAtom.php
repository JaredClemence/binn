<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace JRC\binn;

/**
 * Description of BinaryStringAtom
 *
 * @author jaredclemence
 */
abstract class BinaryStringAtom {
    protected function getBinaryStringFromInt( $size, $minSize = 1 ){
        $binaryString = "";
        while( $size > 0 ){
            $temp = $size;
            $temp >>= 8;
            $temp <<= 8;
            $nextChar = $size - $temp;
            $char = chr( $nextChar );
            $binaryString = $char . $binaryString;
            $size >>= 8;
        }
        $nullByte = "\x00";
        while( strlen( $binaryString ) < $minSize ){
            $binaryString = $nullByte . $binaryString;
        }
        return $binaryString;
    }
    
    public static function createHumanReadableBinaryRepresentation( $value, $minBytes = 1 ){
        $binaryString = self::convertValueToBinaryString( $value );
        while( strlen( $binaryString ) < $minBytes ){
            $binaryString = "\x00" . $binaryString;
        }
        $length = strlen( $binaryString );
        $byteRepresentation = "";
        for( $i = 0; $i < $length; $i++ ){
            $char = $binaryString[$i];
            $val = ord( $char );
            $binString = decbin( $val );
            while( strlen( $binString ) < 8 ){
                $binString = "0" . $binString;
            }
            $byteRepresentation .= $binString . " ";
        }
        $byteRepresentation = trim( $byteRepresentation );
        return $byteRepresentation;
    }

    private static function convertValueToBinaryString($value) {
        if( is_numeric($value) ){ $value = $this->getBinaryStringFromInt($value, 1); }
        return $value;
    }

}
