<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace JRC\binn;
use JRC\binn\Type;
use JRC\binn\Size;
use JRC\binn\Count;
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
        $this->size = $size;
    }
    public function setCount( $count ){
        $this->count = $count;
    }
    public function setData( $data ){
        $this->data = $data;
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
}
