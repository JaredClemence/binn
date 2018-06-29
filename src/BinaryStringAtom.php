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
    
    
}
