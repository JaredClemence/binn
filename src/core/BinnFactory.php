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
        }else if(is_numeric($data) ){
            return $this->determineNumericSubType( $data );
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

    private function determineNumericSubType($data) {
        if( is_float( $data ) == true && !is_integer( $data ) ){
            return $this->determineDecimalSubType( $data );
        }else{
            return $this->determineIntegerSubType( $data );
        }
    }

    private function determineDecimalSubType($data) {
        //no differentiation in PHP between float and double
        //return float
        return "\x62";
    }

    private function determineIntegerSubType($data) {
        if( $data < 0 ){
            return $this->determineSignedIntegerSubType( $data );
        }
        else{
            return $this->determineUnsignedIntegerSubType( $data );
        }
    }

    private function determineSignedIntegerSubType($data) {
        $absData = abs( $data );
        if( $absData < 0xFF ){
            return "\x21";
        }
        else if( $absData < 0xFFFF ){
            return "\x41";
        }
        else if( $absData < 0xFFFFFFFF ){
            return "\x61";
        }
        else{
            return "\x81";
        }
    }

    private function determineUnsignedIntegerSubType($data) {
        $absData = abs( $data );
        if( $absData <= 0xFF ){
            return "\x20";
        }
        else if( $absData <= 0xFFFF ){
            return "\x40";
        }
        else if( $absData <= 0xFFFFFFFF ){
            return "\x60";
        }
        else{
            return "\x80";
        }
    }

}
