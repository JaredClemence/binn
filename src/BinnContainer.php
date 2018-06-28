<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace JRC\binn;

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
}
