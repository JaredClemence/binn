<?php

namespace JRC\binn\core;
use JRC\binn\core\Type;
use JRC\binn\core\Size;
use JRC\binn\core\Count;
/**
 * Description of BinnContainer
 *
 * @author jaredclemence
 */
class BinnContainer {
    public $type;
    public $size;
    public $count;
    public $data;
    
    public function __construct() {
        $this->type ="";
        $this->size = "";
        $this->count = "";
        $this->data = "";
    }
    
    public function setType( $type ){
        $this->type = $type;
    }
    public function setSize( $size ){
        if( is_numeric( $size ) ){
            //size will convert values to bytestrings automatically
            $sizeAtom = new Size();
            $sizeAtom->setByteString($size);
            $size = $sizeAtom->getByteString();
        }
        $this->size = $size;
    }
    
    public function setCount( $count ){
        if( is_numeric( $count ) ){
            //size will convert values to bytestrings automatically
            $countAtom = new Count();
            $countAtom->setByteString($count);
            $count = $countAtom->getByteString();
        }
        $this->count = $count;
    }
    
    public function setData( $data ){
        $this->data = $data;
    }
    
    public function __toString() {
        return $this->getByteString();
    }
    
    public function getByteString(){
        $type = new Type();
        $type->setByteString($this->type);
        $size = new Size();
        $size->setByteString($this->size);
        $count = new Count();
        $count->setByteString($this->count);
        return $type->getByteString() . $size->getByteString() . $count->getByteString() . $this->data;
    }

    public function getType() {
        return $this->type;
    }

    public function getData() {
        return $this->data;
    }

    public function getCount() {
        return $this->count;
    }
    
    public function dumpHex(){
        $std = new \stdClass();
        $vars = get_object_vars($this);
        foreach( $vars as $key=>$value ){
            $std->$key = BinaryStringAtom::createHumanReadableHexRepresentation($value);
        }
        $count = $this->count;
        $data = $this->data;
        $size = $this->size;
        $type = $this->type;
        $hexType = BinaryStringAtom::createHumanReadableHexRepresentation($type);
        $hexSize = BinaryStringAtom::createHumanReadableHexRepresentation($size);
        $hexCount = BinaryStringAtom::createHumanReadableHexRepresentation($count);
        $hexData = BinaryStringAtom::createHumanReadableHexRepresentation($data);
        echo <<<DUMPHEX
BinnContainer:
    Type: $hexType
    Size: $hexSize
    Count: $hexCount
    Data: $hexData
DUMPHEX;
    }

}
