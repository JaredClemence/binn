<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace JRC\binn;

use JRC\binn\BinnContainer;
use JRC\binn\NativeFactory;
use JRC\binn\StorageType;
use JRC\binn\BinnNumber;
use JRC\binn\Type;
/**
 * BinnReader separates a binary string into its four components of [type] [size] [count] and [data].
 * 
 * No further processing is performed on [data], which means that [data] will 
 * contain other unprocessed data.
 *
 * @author jaredclemence
 */
class BinnReader {

    public function read($byteString) {
        $type = $this->identifyTypeString($byteString);
        $size = $this->identifySizeString($byteString, $type);
        $count = $this->identifyCountString($byteString, $type, $size);
        $data = $this->identifyDataString($byteString, $type, $size, $count);
        $container = new BinnContainer();
        $container->setType($type);
        $container->setSize($size);
        $container->setCount($count);
        $container->setData($data);
        return $container;
    }
    
    /**
     * This function exists just to improve readability in other methods. It executes 
     * the read method.
     * 
     * @param type $byteString
     * @return type
     */
    public function readNext( $byteString ){
        return $this->read( $byteString );
    }

    private function identifyTypeString($byteString) {
        $typeString = "";
        $oneByteString = substr( $byteString, 0, 1 );
        $twoByteString = substr( $byteString, 0, 2 );
        $twoByteFlag = $oneByteString & "\x10";
        $hasTwoByteFlag = $twoByteFlag > 0;
        if( $hasTwoByteFlag == true ){
            $typeString = $twoByteString;
        }else{
            $typeString = $oneByteString;
        }
        return $typeString;
    }

    private function identifySizeString($byteString, $type) {
        $size = "";
        $hasSize = $this->determineIfHasSize($type);
        if ($hasSize == true) {
            $typeLength = strlen($type);
            $remaining = substr($byteString, $typeLength);
            $oneByteSize = substr($remaining, 0, 1);
            $fourByteSize = substr($remaining, 0, 4);
            if (ord($oneByteSize & "\x7F") == 0 || ord($oneByteSize) >> 7 == 1) {
                $size = $fourByteSize;
            } else {
                $size = $oneByteSize;
            }
        }
        return $size;
    }
    
    private function determineIfHasSize( $typeString ){
        $type = new Type();
        $type->setByteString($typeString);
        $size = $type->getDefaultDataByteLength();
        $hasDefaultSize = $size >= 0;
        return !$hasDefaultSize;
    }

    private function identifyCountString($byteString, $type, $size) {
        $count = "";
        $hasCount = $this->determineIfHasCount($type);
        if ($hasCount == true) {
            $prefixLength = strlen($type . $size);
            $remaining = substr($byteString, $prefixLength);
            $oneByteSize = substr($remaining, 0, 1);
            $fourByteSize = substr($remaining, 0, 4);
            if (ord($oneByteSize & "\x7F") == 0 || ord($oneByteSize) >> 7 == 1) {
                $count = $fourByteSize;
            } else {
                $count = $oneByteSize;
            }
        }
        return $count;
    }

    private function determineIfHasCount($typeString) {
        $type = new Type();
        $type->setByteString($typeString);
        $hasSize = false;
        if( $type->isType( StorageType::CONTAINER ) ){
            $hasSize = true;
        }
        return $hasSize;
    }

    private function identifyDataString($byteString, $typeString, $sizeString, $countString) {
        $size = $this->getTypeSize( $typeString, $sizeString );
        $fullString = substr( $byteString, 0, $size );
        $prefix = $typeString . $sizeString . $countString;
        $data = substr( $fullString, strlen( $prefix ) );
        return $data;
    }

    private function getTypeSize($typeString, $sizeString) {
        if( $sizeString ){
            $sizeNumber = new BinnNumber();
            $sizeNumber->setByteString($sizeString);
            $size = $sizeNumber->getValue();
        }else{
            $type = new Type();
            $type->setByteString($typeString);
            $size = $type->getDefaultDataByteLength();
        }
        return $size;
    }

}
