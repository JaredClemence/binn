<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace JRC\binn;
use JRC\binn\BinaryStringAtom;
/**
 * Description of Type
 *
 * @author jaredclemence
 */
class Type extends BinaryStringAtom  {

    public $storage_type; //3 bits first byte
    public $sub_type_size; //4th bit first byte
    public $sub_type; //4 or 12 bits

    public function __construct() {
        $this->storage_type = "";
        $this->sub_type_size = "";
        $this->sub_type = "";
    }

    public function getByteString() {
        $byteString = "";
        $firstByte = $this->storage_type | $this->sub_type_size;
        if( strlen( $this->sub_type_size ) == 2 ){
            $twoByte = $firstByte << 8;
            $twoByte |= $this->sub_type_size;
            $byteString = $twoByte | $this->sub_type;
        }else{
            $firstByte |= $this->sub_type_size;
            $byteString = $firstByte | $this->sub_type;
        }
        return $byteString;
    }

    public function setByteString($byteString) {
        if( is_numeric( $byteString ) ) $byteString = $this->getBinaryStringFromInt ($byteString, 1);
        $firstByte = substr($byteString, 0, 1);
        $this->storage_type = $firstByte & "\xE0";
        $this->sub_type_size = $firstByte & "\x10";
        if( ord( $this->sub_type_size ) > 0 ){
            $twoBytes = substr( $byteString, 0, 2 );
            $this->sub_type = $twoBytes & "\x0F\xFF";
        }else{
            $this->sub_type = $firstByte & "\x0F";
        }
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
            case StorageType::NOBYTES:
                $length = 0;
                break;
            case StorageType::BYTE:
                $length = 1;
                break;
            case StorageType::WORD:
                $length = 2;
                break;
            case StorageType::DWORD:
                $length = 4;
                break;
            case StorageType::QWORD:
                $length = 8;
                break;
        }
        return $length;
    }
    
    /**
     * Use StorageType constants as the input.
     * 
     * @param int $typeMask  \\JRC\\bin\\StorageType::{value}
     * @return bool
     */
    public function isType( $typeMask ){
        $filtered = $this->storage_type & "\xE0";
        return $typeMask == $filtered;
    }

}
