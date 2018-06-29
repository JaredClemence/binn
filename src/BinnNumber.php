<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace JRC\binn;

use JRC\binn\BinaryStringAtom;
/**
 * A bin number is one or four bytes. The smallest byte size should be used. To indicate 
 * whether four bytes are expected, the first bit is used as a flag.
 * 
 * Note: If the first bit is 0, and the value is 0, then the value is stored in 4 bytes.
 * //this is an exception
 *
 * @author jaredclemence
 */
class BinnNumber extends BinaryStringAtom {

    public $firstBit;
    public $size;

    public function getByteString() {
        if( $this->size == 0 ) return "";
        
        $this->determinePropperFirstBitSetting();
        return $this->constructByteString();
    }

    private function determinePropperFirstBitSetting() {
        if( $this->size <= 127 ){
            $this->firstBit = 0;
        }else{
            $this->firstBit = 1;
        }
    }

    private function constructByteString() {
        $firstBit = $this->firstBit;
        $byteString = "";
        if ($firstBit) {
            //use 4 bytes
            $firstBitString = $this->getBinaryStringFromInt($firstBit << 31, 4);
            $binarySize = $this->getBinaryStringFromInt($this->size, 4);
            $size = $binarySize & ("\x7F\xFF\xFF\xFF");
            $byteString = $firstBitString | $size;
        } else {
            $firstBitString = $this->getBinaryStringFromInt($firstBit << 7, 1);
            $binarySize = $this->getBinaryStringFromInt($this->size, 1);
            $size = $binarySize & ("\x7F");
            $byteString = $firstBitString | $size;
        }
        return $byteString;
    }

    public function setByteString($bytes) {
        if( is_numeric($bytes) ) $bytes = $this->getBinaryStringFromInt($bytes, $minSize);
        $firstByte = substr($bytes, 0, 1);
        $firstBit = $firstByte & "\x80";
        $firstBit = (ord( $firstBit ) >> 7 );
        $this->firstBit = $firstBit;
        $force4Byte = false;
        if ($firstBit == 0) {
            //use short size
            $this->size = ord( "\x7F" & $firstByte );
            if( $this->size == 0 ) $force4Byte = true;
        }
        
        if( $firstBit != 0 || $force4Byte == true )
        {
            //use long size
            $fourBytes = substr($bytes, 0, 4);
            $this->size = "\x7F\xFF\xFF\xFF" & $fourBytes;
        }
        
        if( $this->size == "" ) $this->size = 0;
        $this->convertSizeToInt();
    }
    
    private function convertSizeToInt(){
        if( is_string( $this->size ) ){
            $chars = $this->size;
            $int = 0;
            for( $i = 0; $i < strlen( $chars ); $i++ ){
                $char = $chars[$i];
                $ord = ord( $char );
                $int <<= 8;
                $int += $ord;
            }
            $this->size = $int;
        }
    }

    public function getValue() {
        return $this->size;
    }

}
