<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace JRC\binn;

/**
 * A bin number is one or four bytes. The smallest byte size should be used. To indicate 
 * whether four bytes are expected, the first bit is used as a flag.
 * 
 * Note: If the first bit is 0, and the value is 0, then the value is stored in 4 bytes.
 * //this is an exception
 *
 * @author jaredclemence
 */
class BinnNumber {

    public $firstBit;
    public $size;

    public function getByteString() {
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
        $firstBit = $this->size & 0x01;
        $byteString = null;
        if ($firstBit) {
            //use 4 bytes
            $firstBit = $firstBit << 31;
            $size = $this->size & 0x7FFFFFFF;
            $byteString = $firstBit | $size;
        } else {
            $firstBit = $firstBIt << 7;
            $size = $this->size & 0x7F;
            $byteString = $firstBit | $size;
        }
        return $byteString;
    }

    public function setByteString($bytes) {
        $firstByte = substr($bytes, 0, 1);
        $firstBit = ($firstByte >> 7) & 0x01;
        $this->firstBit = $firstBit;
        $force4Byte = false;
        if ($firstBit == 0) {
            //use short size
            $this->size = 0x7F & $firstByte;
            if( $this->size == 0 ) $force4Byte = true;
        }
        
        if( $firstBit != 0 || $force4Byte == true )
        {
            //use long size
            $this->size = 0x7FFFFFFF & substr($bytes, 0, 4);
        }
    }

}
