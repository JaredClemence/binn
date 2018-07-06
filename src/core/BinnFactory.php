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
        }else if(is_numeric($data) && !is_string( $data )){
            return $this->determineNumericSubType( $data );
        }else if(is_string($data) ){
            return $this->determineStringSubType( $data );
        }else if(is_a( $data, \DateTime::class ) ){
            return $this->determineDateTimeSubType( $data );
        }else if(is_object( $data ) || is_array($data) ){
            return $this->determineSubTypeOfIndexedContainers($data);
        }
        //unable to get here.
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

    private function determineStringSubType($data) {
        if(is_numeric( $data ) ){
            return "\xA4";
        }
        else {
            return "\xA0";
        }
    }

    private function determineDateTimeSubType($data) {
        //default to DateTime container. User can specify Date or Time containers in a controlled construct.
        return "\xA1";
    }

    /**
     * We select the container subtype that uses the lowest number of bytes to get 
     * the job done.
     * 
     * To do this, we determine:
     *   1. Are ALL indexes numeric?  If yes, then the object is a LIST or a MAP; if no, then the object is an OBJECT
     *   2. Are ALL indexes in sequence starting at 0? If yes, then the object is a LIST. If no, the object is a MAP.
     * @param array|object $data
     * @return string
     */
    private function determineSubTypeOfIndexedContainers($data) {
        list( $allNumeric, $inSequence ) = $this->detectContainerKeyFlags( $data );
        if( $allNumeric == false ){
            return "\xE2";
        }else if( $inSequence ){
            return "\xE0";
        }else{
            return "\xE1";
        }
    }
    
    /**
     * Detect the status of keys to determine whether they are all numeric and in sequence from zero.
     * 
     * [ 0=>..., 1=>..., 2=>... ] === in sequence from zero
     * [ 1=>..., 15=>..., 19=>... ] === all numeric
     * [ "index1"=>..., "index2"=>... ] === neither numeric or in sequence
     * 
     * @param array|object $data
     * @return array
     */
    private function detectContainerKeyFlags($data) {
        $allNumeric = true;
        $inSequence = true;
        $expectedIndex = 0;
        foreach( $data as $key=>$value ){
            if( $key == $expectedIndex ){
                $expectedIndex++;
            }else{
                $inSequence = false;
            }
            if( is_numeric($key) == false ){
                $allNumeric = false;
            }
        }
        return [ $allNumeric, $inSequence ];
    }

}
