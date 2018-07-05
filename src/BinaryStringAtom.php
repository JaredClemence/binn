<?php

namespace JRC\binn;

/**
 * Description of BinaryStringAtom
 *
 * @author jaredclemence
 */
abstract class BinaryStringAtom {
    protected static function getBinaryStringFromInt( $size, $minSize = 1 ){
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
    
    public static function createHumanReadableHexRepresentation( $value, $minBytes = 1 ){
        $binaryString = self::convertValueToBinaryString( $value );
        while( strlen( $binaryString ) < $minBytes ){
            $binaryString = "\x00" . $binaryString;
        }
        $length = strlen( $binaryString );
        $hexRepresentation = "";
        for( $i = 0; $i < $length; $i++ ){
            $char = $binaryString[$i];
            $val = ord( $char );
            $hexString = dechex($val);
            while( strlen( $hexString ) < 2 ){
                $hexString = "0" . $hexString;
            }
            $hexRepresentation .= $hexString . " ";
        }
        $hexRepresentation = trim( $hexRepresentation );
        return $hexRepresentation;
    }

    private static function convertValueToBinaryString($value) {
        if( is_numeric($value) ){ $value = self::getBinaryStringFromInt($value, 1); }
        return $value;
    }

}
