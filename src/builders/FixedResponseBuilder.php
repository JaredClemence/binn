<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace JRC\binn\builders;
use JRC\binn\builders\NativeBuilder;

/**
 * The FixedResponseBuilder is an intermediary object that provides a single value based 
 * on the container type regardless of the [size], [count] or [data] values.
 * 
 * This is primarily intended for all the NOBYTE types which have value communicated in the 
 * type setting itself.
 *
 * @author jaredclemence
 */
abstract class FixedResponseBuilder extends NativeBuilder {
    private $value;

    public function setFixedResponse( $value ){
        $this->value = $value;
    }

    public function make() {
        return $this->value;
    }
    
    public function write( $subtype, $data ){
        return $subtype;
    }
}
