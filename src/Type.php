<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace JRC\binn;

/**
 * Description of Type
 *
 * @author jaredclemence
 */
class Type {

    public $storage_type; //3 bits first byte
    public $sub_type_size; //4th bit first byte
    public $sub_type; //4 or 12 bits

    public function __construct($storageType, $subTypeSize, $subType) {
        $this->storage_type = $storageType;
        $this->sub_type_size = $subTypeSize;
        $this->sub_type = $subType;
    }

    public function getByteString() {
        $typeCode = ( $this->storage_type << 1 );
        $subTypeLength = $this->sub_type_size & 0x01;
        $firstFourBits = $typeCode | $subTypeLength;
        $byteString = null;
        if ($this->sub_type_size & 0x01) {
            //long subtype
            $lastTwelveBits = 0x0FFF & $this->sub_type;
            $byteString = ( $firstFourBits << 12 ) | $lastTwelveBits;
        } else {
            //short subtype
            $lastFourBits = 0x0F & $this->sub_type;
            $byteString = ( $firstFourBits << 4 ) | $lastFourBits;
        }
        return $byteString;
    }

    public function setByteString($byte) {
        $firstByte = substr($byte, 0, 1);
        $secondByte = substr($byte, 1, 1);
        $subType = 0x0F & $firstByte;
        $subTypeLength = ($firstByte >> 4) & 0x01;
        $storageType = ($firstByte >> 5 ) & 0x07;
        if ($subTypeLength & 0x01) {
            $subType = ($subType << 8) | $secondByte;
        }
        $this->storage_type = $storageType;
        $this->sub_type_size = $subTypeLength;
        $this->sub_type = $subType;
    }
    
    /**
     * Certain storage container types do not require a data length, because it is 
     * predefined.
     * 
     * This function returns -1 if the storage type does not have a data length;
     * If -1 is returned look for a size field.
     * 
     * @return int  number of bytes in data packet length or -1
     */
    public function getDefaultDataByteLength() {
        $default = -1;
        $length = $default;
        switch( $this->storage_type ) {
            case StorageType::$NOBYTES:
                $length = 0;
                break;
            case StorageType::$BYTE:
                $length = 1;
                break;
            case StorageType::$WORD:
                $length = 2;
                break;
            case StorageType::$DWORD:
                $length = 4;
                break;
            case StorageType::$QWORD:
                $length = 8;
                break;
        }
        return $length;
    }

}
