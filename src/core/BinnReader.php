<?php

namespace JRC\binn\core;

use JRC\binn\core\BinnContainer;
use JRC\binn\core\StorageType;
use JRC\binn\core\BinnNumber;
use JRC\binn\core\Type;

/**
 * BinnReader separates a binary string into its four components of [type] [size] [count] and [data].
 * 
 * No further processing is performed on [data], which means that [data] will 
 * contain other unprocessed data.
 *
 * @author jaredclemence
 */
class BinnReader {

    /**
     * This method breaks a binary string into an array of BinnContainers.
     * 
     * Each container contains a segment of the string appropriate to the type and size indicated at the container header.
     * 
     * This method is useful in constructing list items.
     * 
     * @param string $byteString
     * @return array BinnContainer
     */
    public function readAll($byteString) {
        $saftey = 0;
        $results = [];
        do {
            $container = $this->readNext($byteString);
            $containerString = $container->getByteString();
            $containerStringLength = strlen($containerString);
            $byteString = substr($byteString, $containerStringLength);
            $results[] = $container;
        } while ($saftey++ < 100000 && strlen($byteString) > 0);
        return $results;
    }

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
    public function readNext($byteString) {
        return $this->read($byteString);
    }

    private function identifyTypeString($byteString) {
        $typeString = "";
        $oneByteString = substr($byteString, 0, 1);
        $twoByteString = substr($byteString, 0, 2);
        $twoByteFlag = $oneByteString & "\x10";
        $hasTwoByteFlag = $twoByteFlag > 0;
        if ($hasTwoByteFlag == true) {
            $typeString = $twoByteString;
        } else {
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

    private function determineIfHasSize($typeString) {
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
        if ($type->isType(StorageType::CONTAINER)) {
            $hasSize = true;
        }
        return $hasSize;
    }

    private function identifyDataString($byteString, $typeString, $sizeString, $countString) {
        $size = $this->getBinnContainerStringSizeByTypeStringAndSizeString($typeString, $sizeString);
        $fullString = substr($byteString, 0, $size);
        $prefix = $typeString . $sizeString . $countString;
        $data = substr($fullString, strlen($prefix));
        return $data;
    }

    private function getBinnContainerStringSizeByTypeStringAndSizeString($typeString, $sizeString) {
        if ($sizeString) {
            $sizeNumber = new BinnNumber();
            $sizeNumber->setByteString($sizeString);
            $containerSize = $sizeNumber->getValue();
            if ($this->isStringOrBlobContainer($typeString) == true) {
                //if the size is a string, then it describes ONLY the length of the string WITHOUT the null byte.
                $sizeOfString = $containerSize;
                $containerSize = $this->getContainerSizeForStringOrBlob($sizeOfString);
            }
        } else {
            $type = new Type();
            $type->setByteString($typeString);
            $dataSize = $type->getDefaultDataByteLength();
            $containerSize = 1 /* type */ + 0 /* size */ + 0 /* count */ + $dataSize;
        }
        return $containerSize;
    }

    private function isStringOrBlobContainer($typeString) {
        $type = new Type();
        $type->setByteString($typeString);
        $majorType = $type->getContainerType();
        $isString = ($majorType == StorageType::STRING);
        $isBlob = ($majorType == StorageType::BLOB);
        $isStringOrBlob = $isBlob || $isString;
        return $isStringOrBlob;
    }

    private function getContainerSizeForStringOrBlob($sizeOfStringWithoutNull) {
        $nullByteLength = 1;
        $dataLength = $sizeOfStringWithoutNull + $nullByteLength;
        
        $typeLength = 1;
        $countLength = 0; //strings and blobs have no count
        $sizeLength = 1;
        
        $containerSize = $typeLength + $sizeLength + $countLength + $dataLength;
        
        return $containerSize;
    }

}
