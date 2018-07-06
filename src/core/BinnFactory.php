<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace JRC\binn\core;
use JRC\binn\core\NativeFactory;
/**
 * Description of BinnFactory
 *
 * @author jaredclemence
 */
class BinnFactory {
    public function blindWrite( $data ){
        $subtype = $this->determineContainerSubType( $data );
        return $this->writeDataAsSubtype( $data, $subtype );
    }

    public function writeDataAsSubtype($data, $subtype) {
        $builder = $this->selectBuilderBySubtype( $subtype );
        $binnString = $builder->write( $subtype, $data );
        return $binnString;
    }

    private function selectBuilderBySubtype($subtype) {
        $factory = new NativeFactory();
        return $factory->selectBuilderByRegisterredSubtype($subtype);
    }

    private function determineContainerSubType($data) {
        if( is_null( $data ) ){
            return "\x00";
        }else if( is_bool( $data ) ){
            return $this->determineBoolSubType( $data );
        }
    }

    private function determineBoolSubType($data) {
        if( $data ){
            return "\x01";
        }
        else {
            return "\x02";
        }
    }

}
